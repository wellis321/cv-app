/**
 * Content script for LinkedIn job view pages.
 * Listens for getJobTitle and returns the job title from the page DOM so the
 * extension can use it instead of the tab title (which is often just "LinkedIn").
 */
(function() {
  var SKIP_TITLES = /^(LinkedIn|Sign in|Log in|Join LinkedIn|Job details|Jobs|Home|Digital Senior Trainer in)?$/i;

  function cleanTitle(t) {
    if (!t || typeof t !== 'string') return '';
    t = t.trim();
    if (t.length < 2 || t.length > 400) return '';
    if (SKIP_TITLES.test(t)) return '';
    return t;
  }

  function queryDoc(root, selector) {
    try {
      var el = root.querySelector(selector);
      return el ? cleanTitle(el.textContent) : '';
    } catch (e) { return ''; }
  }

  function walkShadowRoots(root, selectors, depth) {
    if (!root || depth > 5) return '';
    for (var i = 0; i < selectors.length; i++) {
      var t = queryDoc(root, selectors[i]);
      if (t) return t;
    }
    var nodes = root.querySelectorAll('*');
    for (var j = 0; j < nodes.length; j++) {
      if (nodes[j].shadowRoot) {
        var t = walkShadowRoots(nodes[j].shadowRoot, selectors, depth + 1);
        if (t) return t;
      }
    }
    return '';
  }

  function getJobTitle() {
    var selectors = [
      'h1.job-details-jobs-unified-top-card__job-title',
      'h1.topcard__title',
      '.job-details-jobs-unified-top-card__job-title',
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
      // Newer LinkedIn selectors
      'h1[data-test-id="job-posting-title"]',
      '[data-test-id="job-posting-title"]',
      'h1.jobs-details-top-card__job-title',
      '.jobs-details-top-card__job-title',
      'h1[aria-label*="job title"]',
      'h1'
    ];
    for (var i = 0; i < selectors.length; i++) {
      try {
        var el = document.querySelector(selectors[i]);
        if (el) {
          var t = cleanTitle(el.textContent);
          if (t) return t;
        }
      } catch (e) {}
    }
    var fromShadow = walkShadowRoots(document.documentElement, selectors, 0);
    if (fromShadow) {
      return fromShadow;
    }
    var h1s = document.querySelectorAll('h1');
    for (var k = 0; k < h1s.length; k++) {
      var t = cleanTitle(h1s[k].textContent);
      if (t) {
        return t;
      }
    }
    var boldTitle = document.querySelector('[class*="job-title"], [class*="job_title"], [class*="topcard__title"]');
    if (boldTitle) {
      var t = cleanTitle(boldTitle.textContent);
      if (t) {
        return t;
      }
    }
    return '';
  }

  chrome.runtime.onMessage.addListener(function(request, sender, sendResponse) {
    if (request && request.action === 'getJobTitle') {
      var title = getJobTitle();
      if (title) {
        sendResponse({ jobTitle: title });
        return true;
      }
      setTimeout(function() {
        sendResponse({ jobTitle: getJobTitle() });
      }, 350);
      return true;
    }
    return true;
  });
})();
