/**
 * Content script to extract closing/end dates from job pages.
 * Searches for common date patterns and selectors used by job boards.
 */
(function() {
  // Common date label patterns
  var DATE_LABELS = [
    /closing\s+date/i,
    /end\s+date/i,
    /application\s+deadline/i,
    /deadline/i,
    /closes/i,
    /expires/i,
    /apply\s+by/i,
    /apply\s+before/i,
    /closing/i
  ];

  // Common date formats to parse
  var DATE_FORMATS = [
    /(\d{1,2})\/(\d{1,2})\/(\d{4})/,           // MM/DD/YYYY or DD/MM/YYYY
    /(\d{4})-(\d{1,2})-(\d{1,2})/,              // YYYY-MM-DD
    /(\d{1,2})\s+(Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec)[a-z]*\s+(\d{4})/i,  // DD Mon YYYY
    /(Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec)[a-z]*\s+(\d{1,2}),?\s+(\d{4})/i,  // Mon DD, YYYY
    /(\d{1,2})\s+(January|February|March|April|May|June|July|August|September|October|November|December)\s+(\d{4})/i  // DD Month YYYY
  ];

  var MONTHS = {
    'jan': '01', 'feb': '02', 'mar': '03', 'apr': '04',
    'may': '05', 'jun': '06', 'jul': '07', 'aug': '08',
    'sep': '09', 'oct': '10', 'nov': '11', 'dec': '12'
  };

  function parseDate(dateStr) {
    if (!dateStr || typeof dateStr !== 'string') return null;
    dateStr = dateStr.trim();
    if (!dateStr) return null;

    // Try ISO format first (YYYY-MM-DD)
    var isoMatch = dateStr.match(/^(\d{4})-(\d{1,2})-(\d{1,2})$/);
    if (isoMatch) {
      var year = parseInt(isoMatch[1], 10);
      var month = parseInt(isoMatch[2], 10);
      var day = parseInt(isoMatch[3], 10);
      if (year >= 2020 && year <= 2100 && month >= 1 && month <= 12 && day >= 1 && day <= 31) {
        return year + '-' + String(month).padStart(2, '0') + '-' + String(day).padStart(2, '0');
      }
    }

    // Try DD/MM/YYYY or MM/DD/YYYY
    var slashMatch = dateStr.match(/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/);
    if (slashMatch) {
      var d1 = parseInt(slashMatch[1], 10);
      var d2 = parseInt(slashMatch[2], 10);
      var year = parseInt(slashMatch[3], 10);
      // Assume DD/MM/YYYY if first part > 12, otherwise try both
      var day, month;
      if (d1 > 12) {
        day = d1;
        month = d2;
      } else if (d2 > 12) {
        day = d2;
        month = d1;
      } else {
        // Ambiguous - prefer DD/MM/YYYY (UK format) for UK sites
        // Check URL to determine format preference
        var url = window.location.href || '';
        var isUKSite = /\.(uk|scot\.nhs|gov\.uk|nhs\.uk)/i.test(url) || 
                       /jobs\.scot\.nhs/i.test(url) ||
                       /apply\.jobs\.scot\.nhs/i.test(url);
        if (isUKSite) {
          // UK sites use DD/MM/YYYY
          day = d1;
          month = d2;
        } else {
          // For other sites, try to validate both formats
          var testDate1 = new Date(year, d2 - 1, d1); // Try DD/MM/YYYY
          var testDate2 = new Date(year, d1 - 1, d2); // Try MM/DD/YYYY
          // Prefer the one that makes sense (not in the past if year is current/future)
          var currentYear = new Date().getFullYear();
          if (year >= currentYear) {
            // Future year - prefer the format that gives a future date
            if (testDate1.getTime() > Date.now() && testDate1.getFullYear() === year && testDate1.getMonth() === d2 - 1 && testDate1.getDate() === d1) {
              day = d1;
              month = d2;
            } else if (testDate2.getTime() > Date.now() && testDate2.getFullYear() === year && testDate2.getMonth() === d1 - 1 && testDate2.getDate() === d2) {
              day = d2;
              month = d1;
            } else {
              // Default to DD/MM/YYYY
              day = d1;
              month = d2;
            }
          } else {
            // Default to DD/MM/YYYY
            day = d1;
            month = d2;
          }
        }
      }
      if (year >= 2020 && year <= 2100 && month >= 1 && month <= 12 && day >= 1 && day <= 31) {
        return year + '-' + String(month).padStart(2, '0') + '-' + String(day).padStart(2, '0');
      }
    }

    // Try "DD Mon YYYY" format
    var monMatch = dateStr.match(/^(\d{1,2})\s+(Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec)[a-z]*\s+(\d{4})$/i);
    if (monMatch) {
      var day = parseInt(monMatch[1], 10);
      var monthName = monMatch[2].toLowerCase().substring(0, 3);
      var year = parseInt(monMatch[3], 10);
      var month = MONTHS[monthName];
      if (month && year >= 2020 && year <= 2100 && day >= 1 && day <= 31) {
        return year + '-' + month + '-' + String(day).padStart(2, '0');
      }
    }

    // Try "Mon DD, YYYY" format
    var monMatch2 = dateStr.match(/^(Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec)[a-z]*\s+(\d{1,2}),?\s+(\d{4})$/i);
    if (monMatch2) {
      var monthName = monMatch2[1].toLowerCase().substring(0, 3);
      var day = parseInt(monMatch2[2], 10);
      var year = parseInt(monMatch2[3], 10);
      var month = MONTHS[monthName];
      if (month && year >= 2020 && year <= 2100 && day >= 1 && day <= 31) {
        return year + '-' + month + '-' + String(day).padStart(2, '0');
      }
    }

    // Try native Date parsing as fallback
    try {
      var d = new Date(dateStr);
      if (!isNaN(d.getTime()) && d.getFullYear() >= 2020 && d.getFullYear() <= 2100) {
        var year = d.getFullYear();
        var month = String(d.getMonth() + 1).padStart(2, '0');
        var day = String(d.getDate()).padStart(2, '0');
        return year + '-' + month + '-' + day;
      }
    } catch (e) {}

    return null;
  }

  function findDateNearLabel(element) {
    var text = element.textContent || '';
    var lowerText = text.toLowerCase();

    // Check if this element or nearby elements contain date labels
    for (var i = 0; i < DATE_LABELS.length; i++) {
      if (DATE_LABELS[i].test(lowerText)) {
        // Look for date in this element or siblings
        var dateStr = extractDateFromText(text);
        if (dateStr) return dateStr;

        // Check next sibling
        var next = element.nextElementSibling;
        if (next) {
          dateStr = extractDateFromText(next.textContent || '');
          if (dateStr) return dateStr;
        }

        // Check parent's next sibling
        var parent = element.parentElement;
        if (parent && parent.nextElementSibling) {
          dateStr = extractDateFromText(parent.nextElementSibling.textContent || '');
          if (dateStr) return dateStr;
        }

        // Check data attributes (common in modern job boards)
        var dataDate = element.getAttribute('data-date') || 
                      element.getAttribute('data-closing-date') ||
                      element.getAttribute('data-end-date');
        if (dataDate) {
          var parsed = parseDate(dataDate);
          if (parsed) return parsed;
        }
      }
    }
    return null;
  }

  function extractDateFromText(text) {
    if (!text) return null;
    
    // Look for ordinal dates like "13th of February" or "13th February" with or without year
    // First try with year
    var ordinalMatch = text.match(/\b(\d{1,2})(?:st|nd|rd|th)?\s+(?:of\s+)?(Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec|January|February|March|April|May|June|July|August|September|October|November|December)[a-z]*\s+(\d{4})\b/i);
    if (ordinalMatch) {
      var day = parseInt(ordinalMatch[1], 10);
      var monthName = ordinalMatch[2].toLowerCase().substring(0, 3);
      var year = parseInt(ordinalMatch[3], 10);
      var month = MONTHS[monthName];
      if (month && year >= 2020 && year <= 2100 && day >= 1 && day <= 31) {
        var parsed = year + '-' + month + '-' + String(day).padStart(2, '0');
        return parsed;
      }
    }
    
    // Try without year - assume current year or next year if month has passed
    var ordinalMatchNoYear = text.match(/\b(\d{1,2})(?:st|nd|rd|th)?\s+(?:of\s+)?(Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec|January|February|March|April|May|June|July|August|September|October|November|December)[a-z]*\b/i);
    if (ordinalMatchNoYear) {
      var day = parseInt(ordinalMatchNoYear[1], 10);
      var monthName = ordinalMatchNoYear[2].toLowerCase().substring(0, 3);
      var month = MONTHS[monthName];
      if (month && day >= 1 && day <= 31) {
        var now = new Date();
        var currentYear = now.getFullYear();
        var currentMonth = now.getMonth() + 1; // 1-12
        var monthNum = parseInt(month, 10);
        // If the month has passed this year, assume next year
        var year = (monthNum < currentMonth || (monthNum === currentMonth && day < now.getDate())) ? currentYear + 1 : currentYear;
        var parsed = year + '-' + month + '-' + String(day).padStart(2, '0');
        return parsed;
      }
    }
    
    // Look for ISO format dates
    var isoMatch = text.match(/\b(\d{4}-\d{1,2}-\d{1,2})\b/);
    if (isoMatch) {
      var parsed = parseDate(isoMatch[1]);
      if (parsed) return parsed;
    }

    // Look for slash dates
    var slashMatch = text.match(/\b(\d{1,2}\/\d{1,2}\/\d{4})\b/);
    if (slashMatch) {
      var parsed = parseDate(slashMatch[1]);
      if (parsed) return parsed;
    }

    // Look for text dates (DD Mon YYYY)
    var textMatch = text.match(/\b(\d{1,2}\s+(Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec)[a-z]*\s+\d{4})\b/i);
    if (textMatch) {
      var parsed = parseDate(textMatch[1]);
      if (parsed) return parsed;
    }

    // Look for text dates (Mon DD, YYYY)
    var textMatch2 = text.match(/\b((Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec)[a-z]*\s+\d{1,2},?\s+\d{4})\b/i);
    if (textMatch2) {
      var parsed = parseDate(textMatch2[1]);
      if (parsed) return parsed;
    }

    // Look for full month names (DD Month YYYY)
    var textMatch3 = text.match(/\b(\d{1,2}\s+(January|February|March|April|May|June|July|August|September|October|November|December)\s+\d{4})\b/i);
    if (textMatch3) {
      var parsed = parseDate(textMatch3[1]);
      if (parsed) return parsed;
    }

    return null;
  }

  function getClosingDate() {
    
    // PRIORITY 1: Check for "Closes:" and "Apply Before" patterns first (common on job sites)
    // This should be checked before generic date extraction to avoid picking up "Published" dates
    try {
      var allText = document.body ? document.body.textContent || '' : '';
      // Look for "Apply Before: DD/MM/YYYY" pattern (Oracle Cloud jobs)
      var applyBeforeMatch = allText.match(/apply\s+before[:\s]+(\d{1,2}\/\d{1,2}\/\d{4}|\d{1,2}\s+\w+\s+\d{4}|\d{4}-\d{1,2}-\d{1,2})/i);
      if (applyBeforeMatch) {
        var parsed = parseDate(applyBeforeMatch[1].trim());
        if (parsed) {
          return parsed;
        }
      }
      // Look for "Closes: DD/MM/YYYY" pattern
      var closesMatch = allText.match(/closes[:\s]+(\d{1,2}\/\d{1,2}\/\d{4}|\d{1,2}\s+\w+\s+\d{4}|\d{4}-\d{1,2}-\d{1,2})/i);
      if (closesMatch) {
        var parsed = parseDate(closesMatch[1].trim());
        if (parsed) {
          return parsed;
        }
      }
    } catch (e) {}
    
    // STRICT MODE: Only return dates that are explicitly labeled as "Closing date"
    // Search for elements containing "Closing date" and extract the date from nearby text
    var strictMatchFound = false;
    var strictMatchDate = null;
    
    try {
      // First, try to find elements that contain "Closing date", "End Date", "Closes", etc.
      var allElements = document.querySelectorAll('*');
      var elementsWithClosingDate = [];
      
      for (var i = 0; i < allElements.length; i++) {
        var el = allElements[i];
        var text = el.textContent || '';
        var lowerText = text.toLowerCase();
        
        // Look for "Closing date", "End Date", "End date", "Application deadline", "Closes", "Apply Before", etc.
        // Prioritize "Closes" and "Closing" over "Published"
        var hasClosingLabel = lowerText.indexOf('closing date') !== -1 || 
            lowerText.indexOf('end date') !== -1 ||
            lowerText.indexOf('application deadline') !== -1 ||
            (lowerText.indexOf('deadline') !== -1 && lowerText.indexOf('application') !== -1) ||
            lowerText.indexOf('closes:') !== -1 ||
            lowerText.indexOf('closes') !== -1 ||
            lowerText.indexOf('apply before') !== -1 ||
            lowerText.indexOf('apply by') !== -1;
        
        // Exclude "Published" dates - they're not closing dates
        var hasPublishedLabel = lowerText.indexOf('published:') !== -1 || lowerText.indexOf('published') !== -1;
        
        if (hasClosingLabel && !hasPublishedLabel) {
          elementsWithClosingDate.push(el);
        }
      }
      
      
      // Process each element that contains "Closing date"
      for (var j = 0; j < elementsWithClosingDate.length; j++) {
        var el = elementsWithClosingDate[j];
        var text = el.textContent || '';
        var lowerText = text.toLowerCase();
        
        
        // Try regex match on the element text - check for multiple patterns
        var dateMatch = null;
        var dateText = '';
        
        // Pattern 1: "Closes: ..." (highest priority - most common on job sites)
        dateMatch = lowerText.match(/closes[:\s]+(.+?)(?:\s*$|\s*[,\n\r\.;]|$)/i);
        if (!dateMatch) {
          dateMatch = lowerText.match(/closes[:\s]+(.+)/i);
        }
        
        // Pattern 2: "Apply Before: ..." (Oracle Cloud jobs)
        if (!dateMatch) {
          dateMatch = lowerText.match(/apply\s+before[:\s\-]+(.+?)(?:\s*$|\s*[,\n\r\.;]|$)/i);
        }
        if (!dateMatch) {
          dateMatch = lowerText.match(/apply\s+before[:\s\-]+(.+)/i);
        }
        
        // Pattern 3: "Apply By: ..."
        if (!dateMatch) {
          dateMatch = lowerText.match(/apply\s+by[:\s\-]+(.+?)(?:\s*$|\s*[,\n\r\.;]|$)/i);
        }
        if (!dateMatch) {
          dateMatch = lowerText.match(/apply\s+by[:\s\-]+(.+)/i);
        }
        
        // Pattern 4: "Closing date: ..." or "Closing date - ..." or "Closing date- ..." (no space after dash)
        if (!dateMatch) {
          dateMatch = lowerText.match(/closing\s+date[:\s]*\-?\s*(.+?)(?:\s*$|\s*[,\n\r\.;]|$)/i);
        }
        if (!dateMatch) {
          dateMatch = lowerText.match(/closing\s+date[:\s]*\-?\s*(.+)/i);
        }
        
        // Pattern 5: "End Date: ..." or "End date: ..."
        if (!dateMatch) {
          dateMatch = lowerText.match(/end\s+date[:\s\-]+(.+?)(?:\s*\(|\s*$|\s*[,\n\r\.;]|$)/i);
        }
        if (!dateMatch) {
          dateMatch = lowerText.match(/end\s+date[:\s\-]+(.+)/i);
        }
        
        // Pattern 6: "Application deadline: ..."
        if (!dateMatch) {
          dateMatch = lowerText.match(/application\s+deadline[:\s\-]+(.+?)(?:\s*$|\s*[,\n\r\.;]|$)/i);
        }
        if (!dateMatch) {
          dateMatch = lowerText.match(/application\s+deadline[:\s\-]+(.+)/i);
        }
        
        if (dateMatch) {
          dateText = dateMatch[1].trim();
          // Remove trailing text like "(2 days left to apply)"
          dateText = dateText.replace(/\s*\([^)]*\)\s*$/, '').trim();
          if (dateText.length > 100) {
            dateText = dateText.substring(0, 100);
          }
          
          var dateStr = extractDateFromText(dateText);
          if (dateStr) {
            strictMatchFound = true;
            strictMatchDate = dateStr;
            return dateStr;
          }
        }
        
        // If regex didn't work, try extracting date from the entire element text
        // (in case "Closing date"/"End Date" and the date are in different child elements)
        // But prioritize "Closes" and "Apply Before" over other labels
        var dateStr = extractDateFromText(text);
        if (dateStr) {
          // Make sure this element actually contains closing/end date text
          // Prioritize "Closes" and "Apply Before" over other labels
          if (lowerText.indexOf('closes:') !== -1 || lowerText.indexOf('closes') !== -1) {
            strictMatchFound = true;
            strictMatchDate = dateStr;
            return dateStr;
          }
          if (lowerText.indexOf('apply before') !== -1 || lowerText.indexOf('apply by') !== -1) {
            strictMatchFound = true;
            strictMatchDate = dateStr;
            return dateStr;
          }
          if (lowerText.indexOf('closing date') !== -1 || 
              lowerText.indexOf('end date') !== -1 ||
              lowerText.indexOf('application deadline') !== -1) {
            strictMatchFound = true;
            strictMatchDate = dateStr;
            return dateStr;
          }
        }
        
        // Check parent element (in case "Closing date"/"End Date" label is separate from date)
        var parent = el.parentElement;
        if (parent) {
          var parentText = parent.textContent || '';
          var parentLowerText = parentText.toLowerCase();
          var parentDateStr = extractDateFromText(parentText);
          // Prioritize "Closes" and "Apply Before" over other labels
          if (parentDateStr && (parentLowerText.indexOf('closes:') !== -1 || parentLowerText.indexOf('closes') !== -1)) {
            strictMatchFound = true;
            strictMatchDate = parentDateStr;
            return parentDateStr;
          }
          if (parentDateStr && (parentLowerText.indexOf('apply before') !== -1 || parentLowerText.indexOf('apply by') !== -1)) {
            strictMatchFound = true;
            strictMatchDate = parentDateStr;
            return parentDateStr;
          }
          if (parentDateStr && (parentLowerText.indexOf('closing date') !== -1 || 
                                parentLowerText.indexOf('end date') !== -1 ||
                                parentLowerText.indexOf('application deadline') !== -1)) {
            strictMatchFound = true;
            strictMatchDate = parentDateStr;
            return parentDateStr;
          }
        }
      }
    } catch (e) {
      console.error('Error in closing date search:', e);
    }
    
    // If we found a strict match, return it and don't try other methods
    if (strictMatchFound && strictMatchDate) {
      return strictMatchDate;
    }
    
    // If we're on a Workday page and didn't find a strict match, return null
    // Don't trust generic selectors on Workday as they might pick up wrong dates
    if (window.location.href.indexOf('workdayjobs.com') !== -1 || window.location.href.indexOf('myworkdayjobs.com') !== -1) {
      return null;
    }
    
    // Check for "Closes:" pattern first (common on many job sites like myjobscotland)
    // This should be checked before generic date extraction to avoid picking up "Published" dates
    try {
      var allText = document.body ? document.body.textContent || '' : '';
      var closesMatch = allText.match(/closes[:\s]+(\d{1,2}\/\d{1,2}\/\d{4}|\d{1,2}\s+\w+\s+\d{4}|\d{4}-\d{1,2}-\d{1,2})/i);
      if (closesMatch) {
        var parsed = parseDate(closesMatch[1].trim());
        if (parsed) {
          return parsed;
        }
      }
    } catch (e) {}
    
    // LinkedIn-specific selectors (check first as they're common)
    // Note: LinkedIn often doesn't show closing dates, so this may return null
    if (window.location.href.indexOf('linkedin.com/jobs/view/') !== -1) {
      try {
        // LinkedIn job posting closing date selectors
        var linkedinSelectors = [
          '[data-test-id="job-posting-closing-date"]',
          '[class*="job-posting-closing-date"]',
          '[class*="closing-date"]',
          '[class*="job-details-jobs-unified-top-card__closing-date"]',
          '[class*="jobs-unified-top-card__closing-date"]',
          '[class*="topcard__closing-date"]',
          'span[class*="closing"]',
          'div[class*="closing"]',
          '[aria-label*="closing"]',
          '[aria-label*="deadline"]'
        ];
        
        for (var li = 0; li < linkedinSelectors.length; li++) {
          try {
            var linkedinEls = document.querySelectorAll(linkedinSelectors[li]);
            for (var li2 = 0; li2 < linkedinEls.length; li2++) {
              var linkedinText = linkedinEls[li2].textContent || '';
              var dateStr = extractDateFromText(linkedinText);
              if (dateStr) {
                return dateStr;
              }
              // Check for "Closes on [date]" pattern
              var closesOnMatch = linkedinText.match(/closes?\s+on\s+([^,\.\n]+)/i);
              if (closesOnMatch) {
                var parsed = parseDate(closesOnMatch[1].trim());
                if (parsed) {
                  return parsed;
                }
              }
            }
          } catch (e) {}
        }
      } catch (e) {}
    }

    // Workday-specific selectors (check before generic selectors)
    try {
      // First, try the most specific Workday selector
      var workdayExpiration = document.querySelector('[data-automation-id="jobPostingExpirationDate"]');
      if (workdayExpiration) {
        // Walk up the DOM tree to find the container with "Closing date" label
        var current = workdayExpiration;
        var foundClosingLabel = false;
        var containerText = '';
        
        // Check up to 5 levels up
        for (var level = 0; level < 5 && current; level++) {
          var text = current.textContent || '';
          var lowerText = text.toLowerCase();
          containerText = text;
          
          if (lowerText.indexOf('closing date') !== -1 || lowerText.indexOf('closing') !== -1 || lowerText.indexOf('expiration') !== -1 || lowerText.indexOf('deadline') !== -1) {
            foundClosingLabel = true;
            break;
          }
          current = current.parentElement;
        }
        
        if (foundClosingLabel) {
          var dateStr = extractDateFromText(containerText);
          if (dateStr) {
            return dateStr;
          }
        }
        
        // Fallback: check the element itself and immediate parent
        var expirationText = workdayExpiration.textContent || '';
        var expirationLabel = workdayExpiration.getAttribute('aria-label') || '';
        var parentText = workdayExpiration.parentElement ? (workdayExpiration.parentElement.textContent || '') : '';
        
        // Check data attributes - but ONLY if we found a closing label context
        // Don't trust data attributes without context as they might be other dates
        if (foundClosingLabel) {
          var dataDate = workdayExpiration.getAttribute('data-date') || 
                        workdayExpiration.getAttribute('data-closing-date') ||
                        workdayExpiration.getAttribute('data-end-date') ||
                        workdayExpiration.getAttribute('data-expiration-date');
          if (dataDate) {
            var parsed = parseDate(dataDate);
            if (parsed) {
              return parsed;
            }
          }
        }
      }
      
      // Try other Workday selectors
      var workdaySelectors = [
        '[data-automation-id*="expiration"]',
        '[data-automation-id*="closing"]',
        '[data-automation-id*="deadline"]',
        '[class*="expiration-date"]',
        '[class*="closing-date"]'
      ];
      
      for (var wd = 0; wd < workdaySelectors.length; wd++) {
        try {
          var workdayEls = document.querySelectorAll(workdaySelectors[wd]);
          for (var wd2 = 0; wd2 < workdayEls.length; wd2++) {
            var workdayText = workdayEls[wd2].textContent || '';
            var workdayLabel = workdayEls[wd2].getAttribute('aria-label') || '';
            var workdayParent = workdayEls[wd2].parentElement ? (workdayEls[wd2].parentElement.textContent || '') : '';
            var combinedText = (workdayLabel + ' ' + workdayParent + ' ' + workdayText).toLowerCase();
            // Only process if it mentions closing/expiration/deadline
            if (combinedText.indexOf('closing') === -1 && combinedText.indexOf('expiration') === -1 && combinedText.indexOf('deadline') === -1) {
              continue;
            }
            var dateStr = extractDateFromText(workdayParent || workdayText);
            if (dateStr) {
              return dateStr;
            }
            // Check data attributes - but only if we confirmed closing context
            // Skip data-date as it's too generic and might be other dates
            var dataDate = workdayEls[wd2].getAttribute('data-closing-date') ||
                          workdayEls[wd2].getAttribute('data-end-date') ||
                          workdayEls[wd2].getAttribute('data-expiration-date');
            if (dataDate) {
              var parsed = parseDate(dataDate);
              if (parsed) {
                return parsed;
              }
            }
          }
        } catch (e) {}
      }
    } catch (e) {}

    // Common selectors for closing dates on job boards
    // BUT: Only use if they explicitly mention "closing", "deadline", or "expiration"
    var selectors = [
      '[class*="closing-date"]',
      '[class*="closing_date"]',
      '[class*="end-date"]',
      '[class*="end_date"]',
      '[class*="deadline"]',
      '[class*="application-deadline"]',
      '[data-closing-date]',
      '[data-end-date]',
      '[data-deadline]',
      '[id*="closing"]',
      '[id*="deadline"]',
      '[id*="end-date"]'
    ];

    // Try selectors, but ONLY if they contain closing/deadline text
    for (var i = 0; i < selectors.length; i++) {
      try {
        var elements = document.querySelectorAll(selectors[i]);
        for (var j = 0; j < elements.length; j++) {
          var el = elements[j];
          var text = el.textContent || '';
          var label = el.getAttribute('aria-label') || '';
          var parentText = el.parentElement ? (el.parentElement.textContent || '') : '';
          var combinedText = (label + ' ' + parentText + ' ' + text).toLowerCase();
          
          // Skip if this doesn't mention closing/deadline/expiration
          if (combinedText.indexOf('closing') === -1 && combinedText.indexOf('deadline') === -1 && combinedText.indexOf('expiration') === -1 && combinedText.indexOf('closes') === -1) {
            continue;
          }
          
          
          var dateStr = extractDateFromText(text);
          if (dateStr) {
            return dateStr;
          }
          
          // Only use explicitly named closing date attributes, not generic data-date
          var dataDate = el.getAttribute('data-closing-date') ||
                        el.getAttribute('data-end-date') ||
                        el.getAttribute('data-deadline');
          if (dataDate) {
            var parsed = parseDate(dataDate);
            if (parsed) {
              return parsed;
            }
          }
        }
      } catch (e) {}
    }

    // Search all text for date labels - but ONLY return if it's explicitly a closing date
    var allElements = document.querySelectorAll('*');
    for (var k = 0; k < allElements.length; k++) {
      var el = allElements[k];
      var text = el.textContent || '';
      var lowerText = text.toLowerCase();
      
      // Only process if this element mentions closing/deadline
      if (lowerText.indexOf('closing') !== -1 || lowerText.indexOf('deadline') !== -1 || lowerText.indexOf('expiration') !== -1 || lowerText.indexOf('closes') !== -1) {
        var dateStr = findDateNearLabel(el);
        if (dateStr) {
          return dateStr;
        }
      }
    }


    return null;
  }

  // Listen for messages from extension
  chrome.runtime.onMessage.addListener(function(request, sender, sendResponse) {
    if (request && request.action === 'getClosingDate') {
      var date = getClosingDate();
      sendResponse({ closingDate: date });
      return true;
    }
    return true;
  });
})();
