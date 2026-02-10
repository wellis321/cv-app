(function() {
  var saveBtn = document.getElementById('save-btn');
  var statusEl = document.getElementById('status');
  var pageInfo = document.getElementById('page-info');

  function showStatus(text, type) {
    statusEl.textContent = text;
    statusEl.className = 'status ' + (type || '');
    statusEl.style.display = 'block';
  }

  function isLinkedInJobView(url) {
    return url && url.indexOf('linkedin.com/jobs/view/') !== -1;
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
    try {
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
    } catch (e) {
      safeCallback(null);
    }
    setTimeout(function() { safeCallback(null); }, 1500);
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

  function getTitleForTab(tab, callback) {
    if (isIndeedPage(tab.url)) {
      // Indeed-specific extraction: look for selected/featured job card on left sidebar
      chrome.scripting.executeScript({
        target: { tabId: tab.id },
        func: function() {
          // Indeed: Priority 1 - Look for job title in the right panel (main job view area)
          // This is the most reliable as it shows the currently viewed job
          // First, try to find the main job view container
          var mainJobView = document.querySelector('[class*="jobsearch-ViewJob"], [class*="jobsearch-JobComponent"], [id*="jobsearch-ViewJob"], [class*="jobsearch-JobInfoHeader"]');
          
          if (mainJobView) {
            // Look for title within the main job view container - be more specific
            // Prioritize selectors that are more likely to contain the actual job title
            var titleSelectors = [
              'h2[class*="jobTitle"]:not([class*="Details"])',
              'h2[data-testid="job-title"]',
              '[class*="jobsearch-JobInfoHeader-title"]',
              'h2.jobsearch-JobInfoHeader-title',
              '[id*="job-title"]',
              'h2[class*="jobsearch-JobInfoHeader"]:not([class*="Details"])'
            ];
            
            for (var k = 0; k < titleSelectors.length; k++) {
              try {
                var titleEl = mainJobView.querySelector(titleSelectors[k]);
                if (titleEl) {
                  var text = (titleEl.textContent || '').trim();
                  // Skip generic titles and make sure it's meaningful
                  // Also skip if it contains "job post" or "details" as those are usually headings
                  if (text && text.length > 2 && text.length < 200 && 
                      !/^(Home|Jobs|Search|Apply|Job Information|Indeed|Find jobs|Job Post Details|Details|Post Details)/i.test(text) &&
                      !/job\s+post\s+details/i.test(text) &&
                      !/^Details$/i.test(text)) {
                    return text;
                  }
                }
              } catch (e) {}
            }
            
            // Try h2 elements but filter more aggressively
            var h2s = mainJobView.querySelectorAll('h2');
            for (var h = 0; h < h2s.length; h++) {
              var text = (h2s[h].textContent || '').trim();
              // Skip if it's a generic heading or contains "job post details"
              if (text && text.length > 2 && text.length < 200 &&
                  !/^(Home|Jobs|Search|Apply|Job Information|Indeed|Find jobs|Job Post Details|Details|Post Details)$/i.test(text) &&
                  !/job\s+post\s+details/i.test(text) &&
                  !/^Details$/i.test(text) &&
                  // Make sure it looks like a job title (has some capitalization, not all caps)
                  text !== text.toUpperCase()) {
                return text;
              }
            }
          }
          
          // Priority 2: Look for job title in right panel using broader selectors
          var rightPanelSelectors = [
            'h2[class*="jobTitle"]',
            'h2[data-testid="job-title"]',
            '[class*="jobsearch-JobInfoHeader-title"]',
            'h2.jobsearch-JobInfoHeader-title',
            '[id*="job-title"]',
            '[class*="jobsearch-ViewJobHeader"] h2',
            '[class*="jobsearch-ViewJobHeader"] [class*="jobTitle"]',
            '[class*="jobsearch-JobComponent"] h2',
            'div[class*="jobsearch-JobInfoHeader"] h2'
          ];
          
          for (var j = 0; j < rightPanelSelectors.length; j++) {
            try {
              var el = document.querySelector(rightPanelSelectors[j]);
              if (el) {
                // Make sure it's not in the left sidebar (job list)
                var isInSidebar = el.closest('[class*="jobsearch-ResultsList"], [class*="jobsearch-SerpJobResults"]');
                if (isInSidebar) continue;
                
                var text = (el.textContent || '').trim();
                // Skip generic titles
                if (text && text.length > 2 && text.length < 200 && 
                    !/^(Home|Jobs|Search|Apply|Job Information|Indeed|Find jobs|Job Post Details|Details)/i.test(text)) {
                  return text;
                }
              }
            } catch (e) {}
          }
          
          // Priority 3: Look for selected/featured job card on left sidebar
          // But be more specific - look for elements with aria-selected or specific active states
          var selectedSelectors = [
            '[aria-selected="true"] [class*="jobTitle"]',
            '[aria-selected="true"] [data-testid*="job-title"]',
            'a[aria-current="true"] [class*="jobTitle"]',
            '[class*="job_selected"] [class*="jobTitle"]',
            '[class*="selected"] [class*="jobTitle"]',
            '[class*="featured"] [class*="jobTitle"]',
            '[class*="active"] [class*="jobTitle"]',
            '[data-testid*="job-title"][class*="selected"]',
            '[data-testid*="job-title"][class*="featured"]',
            'a[class*="selected"] [class*="jobTitle"]',
            'a[class*="featured"] [class*="jobTitle"]',
            '[data-jk][class*="selected"] [data-testid="job-title"]',
            '[data-jk][class*="featured"] [data-testid="job-title"]'
          ];
          
          for (var i = 0; i < selectedSelectors.length; i++) {
            try {
              var el = document.querySelector(selectedSelectors[i]);
              if (el) {
                var text = (el.textContent || '').trim();
                if (text && text.length > 2 && text.length < 200 &&
                    !/^(Home|Jobs|Search|Apply|Job Information|Indeed|Find jobs|Job Post Details|Details)/i.test(text)) {
                  return text;
                }
              }
            } catch (e) {}
          }
          
          return '';
        }
      }, function(results) {
        var extractedTitle = (results && results[0] && results[0].result) ? results[0].result.trim() : '';
        callback(extractedTitle || tab.title || '');
      });
      return;
    }
    
    if (!isLinkedInJobView(tab.url)) {
      // For non-LinkedIn pages, try to extract title from page DOM
      chrome.scripting.executeScript({
        target: { tabId: tab.id },
        func: function() {
          // Try to find h1 first (most common for job titles)
          var h1s = document.querySelectorAll('h1');
          for (var i = 0; i < h1s.length; i++) {
            var text = (h1s[i].textContent || '').trim();
            // Skip generic titles
            if (text && text.length > 2 && text.length < 200 && 
                !/^(Home|Jobs|Search|Apply|Job Information|NHS Scotland)/i.test(text)) {
              return text;
            }
          }
          // Try meta title and clean it up
          var metaTitle = document.querySelector('title');
          if (metaTitle) {
            var title = (metaTitle.textContent || '').trim();
            // Try to extract just the job title part (usually after "Apply for" or last part)
            var match = title.match(/Apply\s+for\s+(.+?)(?:\s*\||$)/i);
            if (match && match[1]) {
              return match[1].trim();
            }
            // If title has pipes, take the last meaningful part
            var parts = title.split('|').map(function(p) { return p.trim(); });
            for (var j = parts.length - 1; j >= 0; j--) {
              if (parts[j] && parts[j].length > 2 && parts[j].length < 200 &&
                  !/^(Home|Jobs|Search|Apply|Job Information|NHS Scotland)/i.test(parts[j])) {
                return parts[j];
              }
            }
          }
          return '';
        }
      }, function(results) {
        var extractedTitle = (results && results[0] && results[0].result) ? results[0].result.trim() : '';
        callback(extractedTitle || tab.title || '');
      });
      return;
    }
    var done = false;
    function finish(title) {
      if (done) return;
      done = true;
      callback(title || tab.title || '');
    }
    function injectGetTitle(cb) {
      // Fallback: inline extraction with comprehensive selectors
      chrome.scripting.executeScript({
        target: { tabId: tab.id },
        func: function() {
          var skip = /^(LinkedIn|Sign in|Log in|Join LinkedIn|Job details|Jobs|Home|Digital Senior Trainer in)?$/i;
          function clean(t) {
            if (!t || typeof t !== 'string') return '';
            t = t.trim();
            if (t.length < 2 || t.length > 400) return '';
            if (skip.test(t)) return '';
            return t;
          }
          // Comprehensive selector list matching linkedin-job-title.js
          var sel = [
            'h1.job-details-jobs-unified-top-card__job-title',
            '.job-details-jobs-unified-top-card__job-title',
            'h1.topcard__title',
            '.topcard__title',
            '[data-tracking-control-name="public_jobs_topcard-title"]',
            'h1[class*="top-card"]',
            'h1[class*="topcard"]',
            'h1[class*="job-title"]',
            'h1[class*="job_title"]',
            '.t-24.t-bold.jobs-unified-top-card__job-title',
            'h1.t-24',
            'h1.t-24.t-bold',
            '[class*="jobs-unified-top-card__job-title"]',
            '[class*="top-card__title"]',
            'h1[data-test-id="job-posting-title"]',
            '[data-test-id="job-posting-title"]',
            'h1.jobs-details-top-card__job-title',
            '.jobs-details-top-card__job-title',
            'h1[aria-label*="job title"]',
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
          // Check all h1s
          var h1s = document.querySelectorAll('h1');
          for (var j = 0; j < h1s.length; j++) {
            var t = clean(h1s[j].textContent);
            if (t) return t;
          }
          // Check for job title in common containers
          var containers = document.querySelectorAll('[class*="job-title"], [class*="job_title"], [class*="topcard__title"], [class*="top-card"]');
          for (var k = 0; k < containers.length; k++) {
            var t = clean(containers[k].textContent);
            if (t) return t;
          }
          return '';
        }
      }, function(results) {
        if (results && results[0] && results[0].result) cb(results[0].result);
        else cb('');
      });
    }
    // First try message (in case content script is already loaded)
    chrome.tabs.sendMessage(tab.id, { action: 'getJobTitle' }, function(response) {
      if (!chrome.runtime.lastError && response && response.jobTitle && response.jobTitle.trim()) {
        finish(response.jobTitle.trim());
        return;
      }
      // Content script not loaded, try injecting it
      chrome.scripting.executeScript({
        target: { tabId: tab.id },
        files: ['linkedin-job-title.js']
      }, function() {
        // Wait for script to load, then try message again
        setTimeout(function() {
          chrome.tabs.sendMessage(tab.id, { action: 'getJobTitle' }, function(response2) {
            if (!chrome.runtime.lastError && response2 && response2.jobTitle && response2.jobTitle.trim()) {
              finish(response2.jobTitle.trim());
              return;
            }
            injectGetTitle(function(title) {
              finish(title || tab.title || '');
            });
          });
        }, 200);
      });
    });
    setTimeout(function() { 
      finish(tab.title || ''); 
    }, 1200);
  }

  saveBtn.addEventListener('click', function() {
    chrome.storage.sync.get({ baseUrl: '', token: '' }, function(items) {
      var baseUrl = (items.baseUrl || '').trim().replace(/\/+$/, '');
      var token = (items.token || '').trim();
      if (!baseUrl || !token) {
        showStatus('Set your Site URL and token in extension options first.', 'warn');
        return;
      }
      saveBtn.disabled = true;
      chrome.tabs.query({ active: true, currentWindow: true }, function(tabs) {
        var tab = tabs[0];
        if (!tab || !tab.url) {
          showStatus('Could not get current tab.', 'error');
          saveBtn.disabled = false;
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
              if (!r.ok) throw new Error(data.error || 'Request failed');
              return data;
            });
          })
          .then(function(data) {
            showStatus('Saved to your job list.', 'success');
            setTimeout(function() { window.close(); }, 1200);
          })
          .catch(function(err) {
            showStatus(err.message || 'Failed to save.', 'error');
            saveBtn.disabled = false;
          });
            });
          });
        });
      });
    });
  });

  chrome.tabs.query({ active: true, currentWindow: true }, function(tabs) {
    if (tabs[0] && tabs[0].title) {
      var t = tabs[0].title;
      if (t.length > 50) t = t.slice(0, 47) + '...';
      pageInfo.textContent = 'Save: ' + t;
    }
  });
})();
