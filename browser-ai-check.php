<?php
/**
 * Public Browser AI Compatibility Check
 * Lets anyone verify their browser supports Browser-Based AI (no login required)
 */

require_once __DIR__ . '/php/helpers.php';

$pageTitle = 'Browser AI Compatibility Check';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php partial('head', ['pageTitle' => $pageTitle]); ?>
</head>
<body class="bg-gray-50">
    <?php partial('header'); ?>
    <main id="main-content" class="max-w-3xl mx-auto px-4 py-10 sm:py-12">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-8 sm:px-8 sm:py-10">
                <h1 class="text-2xl font-bold text-gray-900">Browser AI compatibility check</h1>
                <p class="mt-2 text-gray-600">
                    Check whether your current browser can run our Browser-Based AI (no API keys or cloud required). This uses the same checks we use when you use AI features in your account.
                </p>

                <div class="mt-6">
                    <button type="button" id="run-check-btn" class="inline-flex items-center justify-center px-4 py-2.5 bg-purple-600 text-white rounded-lg hover:bg-purple-700 font-medium text-sm transition-colors">
                        <svg id="run-check-icon" class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span id="run-check-text">Check my browser</span>
                    </button>
                    <div id="check-loading" class="hidden mt-4 flex items-center text-sm text-gray-500">
                        <svg class="animate-spin h-5 w-5 mr-2 text-purple-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Checking…
                    </div>
                </div>

                <div id="check-result" class="hidden mt-6 space-y-4"></div>
            </div>
        </div>

        <div class="mt-8 bg-white rounded-xl shadow-sm border border-gray-200 px-6 py-6">
            <h2 class="text-lg font-semibold text-gray-900">What matters for Browser AI</h2>
            <p class="mt-2 text-sm text-gray-600">
                Besides using a supported browser, your <strong>device RAM</strong> and <strong>browser storage</strong> affect whether models run smoothly:
            </p>
            <ul class="mt-3 space-y-1.5 text-sm text-gray-700 list-disc list-inside">
                <li><strong>RAM:</strong> Models typically use 2–8GB while running. We recommend at least <strong>4GB free RAM</strong> (close other apps/tabs if you have less).</li>
                <li><strong>Browser storage:</strong> The first time you use a model, it downloads (often 2–5GB) and is cached. We recommend at least <strong>4GB free</strong> in your browser’s storage (not the same as general disk space).</li>
                <li><strong>GPU:</strong> Optional but helps. WebGPU/WebGL use your graphics hardware when available; otherwise the CPU is used and may be slower.</li>
            </ul>
            <p class="mt-3 text-sm text-gray-500">
                The check above shows WebGPU/WebGL and storage when your browser reports them. RAM is not always reported by browsers.
            </p>
        </div>

        <div class="mt-8 bg-white rounded-xl shadow-sm border border-gray-200 px-6 py-6">
            <h2 class="text-lg font-semibold text-gray-900">Recommended browsers</h2>
            <p class="mt-2 text-sm text-gray-600">
                Browser-Based AI needs <strong>WebGPU</strong> or <strong>WebGL 2</strong> support. Use a recent version of one of these:
            </p>
            <ul class="mt-3 space-y-1.5 text-sm text-gray-700 list-disc list-inside">
                <li><strong>Chrome:</strong> 113+ (WebGPU) or 56+ (WebGL 2)</li>
                <li><strong>Edge:</strong> 113+ (WebGPU) or 79+ (WebGL 2)</li>
                <li><strong>Firefox:</strong> 141+ on Windows (WebGPU), or 51+ (WebGL 2) on all platforms. WebLLM is not supported in Firefox; use Chrome, Edge, or Safari for full Browser AI.</li>
                <li><strong>Safari:</strong> 16.4+ on macOS 13.3+ (WebGPU), or 15.2+ (WebGL 2)</li>
                <li><strong>Opera / Brave:</strong> Recent versions based on Chromium</li>
            </ul>
        </div>

        <div class="mt-6 text-center">
            <a href="/" class="text-blue-600 hover:text-blue-800 text-sm font-medium">← Back to home</a>
            <?php if (!isLoggedIn()): ?>
                <span class="mx-2 text-gray-400">|</span>
                <a href="/?register=1" data-open-register class="text-blue-600 hover:text-blue-800 text-sm font-medium">Create free account</a>
            <?php else: ?>
                <span class="mx-2 text-gray-400">|</span>
                <a href="/ai-settings.php" class="text-blue-600 hover:text-blue-800 text-sm font-medium">AI Settings</a>
            <?php endif; ?>
        </div>
    </main>
    <?php partial('footer'); ?>

    <script>
    (function() {
        var btn = document.getElementById('run-check-btn');
        var icon = document.getElementById('run-check-icon');
        var text = document.getElementById('run-check-text');
        var loading = document.getElementById('check-loading');
        var result = document.getElementById('check-result');

        function checkSupportSync() {
            var s = {
                webgpu: false,
                webgl: false,
                indexeddb: false,
                required: false
            };
            if (typeof navigator !== 'undefined' && navigator.gpu) {
                s.webgpu = true;
                s.required = true;
            }
            var canvas = document.createElement('canvas');
            var gl = canvas.getContext('webgl') || canvas.getContext('experimental-webgl');
            if (gl) {
                s.webgl = true;
                if (!s.webgpu) s.required = true;
            }
            if (typeof window !== 'undefined' && window.indexedDB) {
                s.indexeddb = true;
            }
            return s;
        }

        function getDeviceMemory() {
            if (typeof navigator !== 'undefined' && navigator.deviceMemory) {
                return navigator.deviceMemory + ' GB';
            }
            return null;
        }

        function getStorageEstimate() {
            return new Promise(function(resolve) {
                if (typeof navigator !== 'undefined' && navigator.storage && navigator.storage.estimate) {
                    navigator.storage.estimate().then(function(est) {
                        var quota = est.quota != null ? (est.quota / (1024 * 1024 * 1024)).toFixed(2) : null;
                        var usage = est.usage != null ? (est.usage / (1024 * 1024 * 1024)).toFixed(2) : null;
                        var available = (quota != null && usage != null) ? (parseFloat(quota) - parseFloat(usage)).toFixed(2) : null;
                        resolve({ quota: quota, usage: usage, available: available });
                    }).catch(function() { resolve(null); });
                } else {
                    resolve(null);
                }
            });
        }

        function showResult(support, deviceMemory, storage) {
            var isFirefox = navigator.userAgent.toLowerCase().indexOf('firefox') !== -1;
            var ready = support.required && (support.webgpu || support.webgl);
            if (isFirefox) ready = false; // WebLLM not supported in Firefox

            var html = '';
            if (ready) {
                html += '<div class="p-4 rounded-lg bg-green-50 border border-green-200">';
                html += '<div class="flex items-start"><svg class="w-6 h-6 text-green-600 flex-shrink-0 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
                html += '<div><h3 class="font-semibold text-green-900">Your browser is ready for Browser AI</h3>';
                html += '<p class="mt-1 text-sm text-green-800">You can use Browser-Based AI features (CV tailoring, quality assessment, cover letters) without API keys. Sign in or create an account to get started.</p></div></div></div>';
            } else {
                html += '<div class="p-4 rounded-lg bg-amber-50 border border-amber-200">';
                html += '<div class="flex items-start"><svg class="w-6 h-6 text-amber-600 flex-shrink-0 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>';
                html += '<div><h3 class="font-semibold text-amber-900">Your browser may not support Browser AI</h3>';
                if (isFirefox) {
                    html += '<p class="mt-1 text-sm text-amber-800">Browser-Based AI (WebLLM) is not supported in Firefox. Please use Chrome, Edge, Brave, Opera, or Safari to use Browser AI.</p>';
                } else if (!support.webgpu && !support.webgl) {
                    html += '<p class="mt-1 text-sm text-amber-800">WebGPU and WebGL were not detected. Try updating your browser or using Chrome, Edge, or Safari (see recommended versions below).</p>';
                } else {
                    html += '<p class="mt-1 text-sm text-amber-800">One or more checks did not pass. Try updating your browser or use a recommended browser below.</p>';
                }
                html += '</div></div></div>';
            }

            html += '<div class="p-4 rounded-lg bg-gray-50 border border-gray-200">';
            html += '<h4 class="font-medium text-gray-900 mb-2">Details</h4>';
            html += '<dl class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-sm">';
            html += '<dt class="text-gray-600">WebGPU:</dt><dd>' + (support.webgpu ? '<span class="text-green-700 font-medium">Supported</span>' : '<span class="text-red-700 font-medium">Not supported</span>') + '</dd>';
            html += '<dt class="text-gray-600">WebGL:</dt><dd>' + (support.webgl ? '<span class="text-green-700 font-medium">Supported</span>' : '<span class="text-red-700 font-medium">Not supported</span>') + '</dd>';
            html += '<dt class="text-gray-600">IndexedDB:</dt><dd>' + (support.indexeddb ? '<span class="text-green-700 font-medium">Available</span>' : '<span class="text-amber-700 font-medium">Not available</span>') + '</dd>';
            if (deviceMemory) {
                html += '<dt class="text-gray-600">Device RAM (reported):</dt><dd class="text-gray-900">' + deviceMemory + ' <span class="text-gray-500">(Chrome/Edge only; models need ~2–8GB)</span></dd>';
            }
            if (storage && storage.available != null) {
                var storageNote = parseFloat(storage.available) >= 4 ? 'text-green-700' : (parseFloat(storage.available) >= 2 ? 'text-amber-700' : 'text-red-700');
                html += '<dt class="text-gray-600">Browser storage free:</dt><dd class="' + storageNote + '">' + storage.available + ' GB <span class="text-gray-500">(models need several GB)</span></dd>';
            }
            html += '</dl></div>';

            result.innerHTML = html;
            result.classList.remove('hidden');
        }

        if (btn && result) {
            btn.addEventListener('click', function() {
                btn.disabled = true;
                text.textContent = 'Checking…';
                icon.classList.add('hidden');
                loading.classList.remove('hidden');
                result.classList.add('hidden');
                result.innerHTML = '';
                var support = checkSupportSync();
                var deviceMemory = getDeviceMemory();
                getStorageEstimate().then(function(storage) {
                    loading.classList.add('hidden');
                    btn.disabled = false;
                    text.textContent = 'Check again';
                    icon.classList.remove('hidden');
                    showResult(support, deviceMemory, storage);
                });
            });
        }
    })();
    </script>
</body>
</html>
