(function() {
  var APP_URLS = {
    production: 'https://simple-cv-builder.com',
    testing: 'https://lightcoral-raccoon-941077.hostingersite.com'
  };

  var baseUrlInput = document.getElementById('baseUrl');
  var tokenInput = document.getElementById('token');
  var saveBtn = document.getElementById('save-settings-btn');
  var messageEl = document.getElementById('message');
  var tokenLink = document.getElementById('token-link');

  chrome.storage.sync.get({ baseUrl: '', token: '' }, function(items) {
    baseUrlInput.value = items.baseUrl || APP_URLS.production;
    tokenInput.value = items.token || '';
  });

  document.getElementById('use-production-btn').addEventListener('click', function() {
    baseUrlInput.value = APP_URLS.production;
  });
  document.getElementById('use-testing-btn').addEventListener('click', function() {
    baseUrlInput.value = APP_URLS.testing;
  });

  if (!saveBtn) return;
  saveBtn.addEventListener('click', function() {
    var baseUrl = (baseUrlInput.value || '').trim().replace(/\/+$/, '');
    var token = (tokenInput.value || '').trim();
    if (!baseUrl || !token) {
      showMessage('Please set both Site URL and Save token.', true);
      return;
    }
    chrome.storage.sync.set({ baseUrl: baseUrl, token: token }, function() {
      showMessage('Settings saved.');
    });
  });

  function showMessage(text, isError) {
    messageEl.textContent = text;
    messageEl.className = 'message ' + (isError ? 'error' : 'success');
    messageEl.style.display = 'block';
    setTimeout(function() { messageEl.style.display = 'none'; }, 4000);
  }

  // Link to app's save token page (default to production if no base URL yet)
  chrome.storage.sync.get({ baseUrl: '' }, function(items) {
    var base = (items.baseUrl || '').trim() || APP_URLS.production;
    tokenLink.href = base.replace(/\/+$/, '') + '/save-job-token.php';
    tokenLink.target = '_blank';
  });
})();
