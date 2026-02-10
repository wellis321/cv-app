// Context menu: "Save job to Simple CV Builder"
chrome.runtime.onInstalled.addListener(function() {
  chrome.contextMenus.create({
    id: 'save-job',
    title: 'Save job to Simple CV Builder',
    contexts: ['page']
  });
});

function isLinkedInJobView(url) {
  return url && url.indexOf('linkedin.com/jobs/view/') !== -1;
}

function isIndeedPage(url) {
  return url && (url.indexOf('indeed.com') !== -1 || url.indexOf('indeed.co.uk') !== -1 || url.indexOf('indeed.co') !== -1);
}

function getUrlForTab(tab, callback) {
  if (isIndeedPage(tab.url)) {
    // For Indeed, extract the "Apply now" button link from the right panel
    chrome.scripting.executeScript({
      target: { tabId: tab.id },
      func: function() {
        // Look for the Apply button in the main job view - prioritize smartapply links
        // First, try to find the main job view container to limit search scope
        var mainJobView = document.querySelector('[class*="jobsearch-ViewJob"], [class*="jobsearch-JobComponent"], [id*="jobsearch-ViewJob"], [class*="jobsearch-JobInfoHeader"]');
        var searchContainer = mainJobView || document;
        
        // Priority 1: Look specifically for smartapply links (most reliable)
        var smartApplyLinks = searchContainer.querySelectorAll('a[href*="smartapply.indeed.com"], a[href*="indeedapply"]');
        for (var s = 0; s < smartApplyLinks.length; s++) {
          try {
            var el = smartApplyLinks[s];
            var href = el.getAttribute('href');
            if (href) {
              // Exclude settings/account/profile links
              if (href.indexOf('settings') === -1 && href.indexOf('account') === -1 && href.indexOf('profile') === -1) {
                // Make sure it's not in the sidebar
                var isInSidebar = el.closest('[class*="jobsearch-ResultsList"], [class*="jobsearch-SerpJobResults"]');
                if (!isInSidebar) {
                  // Convert relative URLs to absolute
                  if (href.indexOf('http') !== 0) {
                    href = new URL(href, window.location.href).href;
                  }
                  return href;
                }
              }
            }
          } catch (e) {}
        }
        
        // Priority 2: Look for Apply buttons in the main job view
        var applySelectors = [
          '[class*="jobsearch-ApplyButton"] a',
          '[class*="jobsearch-ViewJob"] a[class*="apply"]',
          'a[class*="applyButton"]',
          'a[class*="ApplyButton"]',
          'a[data-testid*="apply"]',
          '[class*="jobsearch-JobInfoHeader"] a[class*="apply"]',
          '[class*="jobsearch-JobComponent"] a[class*="apply"]'
        ];
        
        for (var i = 0; i < applySelectors.length; i++) {
          try {
            var els = searchContainer.querySelectorAll(applySelectors[i]);
            for (var j = 0; j < els.length; j++) {
              var el = els[j];
              var href = el.getAttribute('href');
              if (href) {
                // Exclude settings links and other non-apply links
                if (href.indexOf('settings') !== -1 || href.indexOf('account') !== -1 || href.indexOf('profile') !== -1 || href.indexOf('secure.indeed.com/settings') !== -1) {
                  continue;
                }
                
                // Make sure it's not in the sidebar
                var isInSidebar = el.closest('[class*="jobsearch-ResultsList"], [class*="jobsearch-SerpJobResults"]');
                if (isInSidebar) continue;
                
                // Prefer smartapply links, but accept any apply link
                if (href.indexOf('smartapply') !== -1 || href.indexOf('indeedapply') !== -1 || href.indexOf('apply') !== -1) {
                  // Convert relative URLs to absolute
                  if (href.indexOf('http') !== 0) {
                    href = new URL(href, window.location.href).href;
                  }
                  return href;
                }
              }
            }
          } catch (e) {}
        }
        
        // Fallback: look for viewjob link in the URL or page
        var viewJobMatch = window.location.href.match(/vjk=([a-f0-9]+)/);
        if (viewJobMatch) {
          var baseUrl = window.location.origin;
          return baseUrl + '/viewjob?jk=' + viewJobMatch[1];
        }
        
        return '';
      }
    }, function(results) {
      var applyUrl = (results && results[0] && results[0].result) ? results[0].result.trim() : '';
      callback(applyUrl || tab.url || '');
    });
    return;
  }
  // For non-Indeed pages, use the tab URL
  callback(tab.url || '');
}

function getClosingDateForTab(tab, callback) {
  var callbackCalled = false;
  function safeCallback(result) {
    if (callbackCalled) {
      return;
    }
    callbackCalled = true;
    callback(result);
  }
  
  // Try to send message to content script first
  chrome.tabs.sendMessage(tab.id, { action: 'getClosingDate' }, function(response) {
    if (!chrome.runtime.lastError && response && response.closingDate) {
      safeCallback(response.closingDate);
      return;
    }
    // If content script isn't available, inject it dynamically
    if (chrome.runtime.lastError && chrome.runtime.lastError.message.indexOf('Receiving end does not exist') !== -1) {
      // Inject the extraction script directly
      chrome.scripting.executeScript({
        target: { tabId: tab.id },
        files: ['extract-closing-date.js']
      }, function() {
        if (chrome.runtime.lastError) {
          safeCallback(null);
          return;
        }
        // Wait a bit for script to load, then try again
        setTimeout(function() {
          chrome.tabs.sendMessage(tab.id, { action: 'getClosingDate' }, function(response2) {
            if (!chrome.runtime.lastError && response2 && response2.closingDate) {
              safeCallback(response2.closingDate);
            } else {
              safeCallback(null);
            }
          });
        }, 200);
      });
    } else {
      safeCallback(null);
    }
  });
  setTimeout(function() { safeCallback(null); }, 1500);
}

function getTitleForTab(tab, callback) {
  if (!isLinkedInJobView(tab.url)) {
    callback(tab.title || '');
    return;
  }
  var done = false;
  function finish(title) {
    if (done) return;
    done = true;
    callback(title || tab.title || '');
  }
  chrome.tabs.sendMessage(tab.id, { action: 'getJobTitle' }, function(response) {
    if (!chrome.runtime.lastError && response && response.jobTitle && response.jobTitle.trim()) {
      finish(response.jobTitle.trim());
      return;
    }
    chrome.scripting.executeScript({
      target: { tabId: tab.id },
      func: function() {
        var skip = /^(LinkedIn|Sign in|Log in|Join LinkedIn|Job details|Jobs|Home)?$/i;
        function clean(t) {
          t = (t || '').trim();
          return (t.length > 1 && t.length < 400 && !skip.test(t)) ? t : '';
        }
        var sel = [
          'h1.job-details-jobs-unified-top-card__job-title',
          '.job-details-jobs-unified-top-card__job-title',
          'h1.topcard__title',
          '.topcard__title',
          '[class*="jobs-unified-top-card__job-title"]',
          '[class*="top-card__title"]',
          'h1.t-24',
          'h1'
        ];
        for (var i = 0; i < sel.length; i++) {
          try {
            var el = document.querySelector(sel[i]);
            if (el) {
              var t = clean(el.textContent);
              if (t) return t;
            }
          } catch (e) {}
        }
        var h1s = document.querySelectorAll('h1');
        for (var j = 0; j < h1s.length; j++) {
          var t = clean(h1s[j].textContent);
          if (t) return t;
        }
        return '';
      }
    }, function(results) {
      if (results && results[0] && results[0].result) finish(results[0].result);
      else finish(tab.title || '');
    });
  });
  setTimeout(function() { finish(tab.title || ''); }, 1200);
}

chrome.contextMenus.onClicked.addListener(function(info, tab) {
  if (info.menuItemId === 'save-job' && tab && tab.id) {
    chrome.storage.sync.get({ baseUrl: '', token: '' }, function(items) {
      var baseUrl = (items.baseUrl || '').trim().replace(/\/+$/, '');
      var token = (items.token || '').trim();
      if (!baseUrl || !token) {
        chrome.notifications && chrome.notifications.create({
          type: 'basic',
          title: 'Save job',
          message: 'Set your Site URL and token in extension options first.'
        });
        return;
      }
      getTitleForTab(tab, function(title) {
        getClosingDateForTab(tab, function(closingDate) {
          getUrlForTab(tab, function(jobUrl) {
            var payload = {
              url: jobUrl || tab.url,
              title: title || ''
            };
            if (closingDate) {
              payload.closing_date = closingDate;
            }
            var apiUrl = baseUrl + '/api/quick-add-job.php';
            fetch(apiUrl, {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + token
              },
              body: JSON.stringify(payload)
            })
        .then(function(r) {
          return r.json().then(function(data) {
            return { ok: r.ok, data: data };
          });
        })
        .then(function(res) {
          var msg = res.ok ? 'Saved to your job list.' : (res.data.error || 'Failed to save.');
          if (chrome.notifications) {
            chrome.notifications.create({
              type: 'basic',
              title: 'Save job',
              message: msg
            });
          }
        })
        .catch(function() {
          if (chrome.notifications) {
            chrome.notifications.create({
              type: 'basic',
              title: 'Save job',
              message: 'Failed to save. Check your options.'
            });
          }
        });
          });
        });
      });
    });
  }
});
