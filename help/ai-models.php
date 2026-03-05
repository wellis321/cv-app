<?php
/**
 * AI Model Guide
 * Helps users choose the best models for CV rewriting, cover letters, application questions, and interview tasks.
 * Supports Ollama detection, LLMfit integration (paste output), and Browser AI guidance.
 */

require_once __DIR__ . '/../php/helpers.php';

requireAuth();

$userId = getUserId();
$userProfile = db()->fetchOne(
    "SELECT ollama_base_url, ollama_model, browser_ai_model, ai_service_preference FROM profiles WHERE id = ?",
    [$userId]
);
$ollamaUrl = $userProfile['ollama_base_url'] ?? 'http://localhost:11434';
$isSuperAdmin = isSuperAdmin($userId);

$serviceLabels = [
    'ollama' => 'Local Ollama',
    'browser' => 'Browser AI',
    'openai' => 'OpenAI',
    'anthropic' => 'Anthropic Claude',
    'gemini' => 'Google Gemini',
    'grok' => 'xAI Grok',
    'huggingface' => 'Hugging Face',
];
$browserModelLabels = [
    'llama3.2' => 'Llama 3.2',
    'mistral' => 'Mistral 7B',
    'phi3' => 'Phi-3 Mini',
    'gemma2' => 'Gemma 2',
];
$currentService = $userProfile['ai_service_preference'] ?? '';
$currentServiceLabel = $serviceLabels[$currentService] ?? ($currentService ? ucfirst($currentService) : 'Site default');
$currentModel = '';
if ($currentService === 'ollama') {
    $currentModel = $userProfile['ollama_model'] ?? '';
} elseif ($currentService === 'browser') {
    $bm = $userProfile['browser_ai_model'] ?? '';
    $currentModel = $browserModelLabels[$bm] ?? $bm ?: '(not set)';
}

$pageTitle = 'AI Model Guide | Simple CV Builder';
$metaDescription = 'Choose the best AI model for CV rewriting, cover letters, and application questions. Works with Ollama, LLMfit, and Browser AI.';
$canonicalUrl = APP_URL . '/help/ai-models.php';
$csrf = csrfToken();
?>
<!DOCTYPE html>
<html lang="en-GB">
<head>
    <?php partial('head', [
        'pageTitle' => $pageTitle,
        'metaDescription' => $metaDescription,
        'canonicalUrl' => $canonicalUrl,
        'metaNoindex' => true,
    ]); ?>
</head>
<body class="bg-gray-50">
    <?php partial('header'); ?>

    <main id="main-content" class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <h1 class="text-4xl font-bold text-gray-900 mb-4">AI Model Guide</h1>
                <p class="text-lg text-gray-600">
                    Find the best model for your hardware and our AI features. Different tasks benefit from different model strengths.
                </p>
                <p class="mt-2 text-sm text-gray-600">
                    <a href="/ai-settings.php" class="text-blue-600 hover:text-blue-800 underline">Open AI Settings</a> to configure your chosen model.
                </p>
                <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <p class="text-sm font-medium text-gray-900">Currently using</p>
                    <p class="text-lg text-blue-800 font-semibold mt-0.5">
                        <?php echo e($currentServiceLabel); ?><?php if ($currentModel): ?> — <?php echo e($currentModel); ?><?php endif; ?>
                    </p>
                </div>
            </div>

            <!-- Task requirements table -->
            <section id="task-requirements" class="mb-10 bg-white rounded-lg shadow-sm border border-gray-200 p-6 scroll-mt-24">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">What each AI feature needs</h2>
                <p class="text-sm text-gray-600 mb-4">Use this to decide which model fits best. For writing tasks (CV, cover letters), prioritise quality. For quick tasks, speed matters more.</p>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="text-left py-3 px-2 font-medium text-gray-900">Feature</th>
                                <th class="text-left py-3 px-2 font-medium text-gray-900">Priority</th>
                                <th class="text-left py-3 px-2 font-medium text-gray-900">Min context</th>
                                <th class="text-left py-3 px-2 font-medium text-gray-900">Notes</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-700">
                            <tr class="border-b border-gray-100"><td class="py-2 px-2">CV rewriting</td><td class="py-2 px-2">Quality</td><td class="py-2 px-2">4K</td><td class="py-2 px-2">Long prompts, needs good prose</td></tr>
                            <tr class="border-b border-gray-100"><td class="py-2 px-2">Cover letters</td><td class="py-2 px-2">Quality</td><td class="py-2 px-2">4K</td><td class="py-2 px-2">Same</td></tr>
                            <tr class="border-b border-gray-100"><td class="py-2 px-2">Application questions</td><td class="py-2 px-2">Quality, Speed</td><td class="py-2 px-2">2K</td><td class="py-2 px-2">Shorter answers</td></tr>
                            <tr class="border-b border-gray-100"><td class="py-2 px-2">Interview task help</td><td class="py-2 px-2">Quality</td><td class="py-2 px-2">4K</td><td class="py-2 px-2">Outlines, examples</td></tr>
                            <tr class="border-b border-gray-100"><td class="py-2 px-2">Keyword extraction</td><td class="py-2 px-2">Speed</td><td class="py-2 px-2">2K</td><td class="py-2 px-2">Structured output</td></tr>
                        </tbody>
                    </table>
                </div>
                <p class="mt-4 text-xs text-gray-600">When using Qwen3 (e.g. <code>qwen3:4b-instruct</code>), we prepend <code>/think</code> to the prompt for complex tasks (CV rewrite, cover letters, interview help) and <code>/no_think</code> for quick tasks (keyword extraction). We use 32K context so the full CV and job description fit. To verify thinking is used: add <code>AI_DEBUG=1</code> to your <code>.env</code> and check your PHP error log when running a complex task.</p>
            </section>

            <!-- Ollama section -->
            <section id="ollama-models" class="mb-10 bg-white rounded-lg shadow-sm border border-gray-200 p-6 scroll-mt-24">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Ollama: Detect your models</h2>
                <p class="text-sm text-gray-600 mb-4">If you use local Ollama, we can list your installed models and suggest which to use for our features.</p>
                <?php if ($isSuperAdmin): ?>
                <div class="space-y-4">
                    <div>
                        <label for="ollama_url" class="block text-sm font-medium text-gray-700 mb-1">Ollama URL</label>
                        <input type="text" id="ollama_url" value="<?php echo e($ollamaUrl); ?>" placeholder="http://localhost:11434" class="w-full max-w-md px-3 py-2 border border-gray-300 rounded-lg text-sm">
                    </div>
                    <button type="button" id="detect-ollama-btn" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">
                        <span id="detect-ollama-text">Detect my models</span>
                        <span id="detect-ollama-loading" class="hidden ml-2">Checking...</span>
                    </button>
                    <div id="ollama-result" class="hidden mt-4"></div>
                </div>
                <?php else: ?>
                <p class="text-sm text-gray-600">Ollama is available for super administrators. <a href="/ai-settings.php" class="text-blue-600 hover:underline">Use Browser AI or cloud APIs</a> for model options.</p>
                <?php endif; ?>
            </section>

                <!-- LLMfit section -->
            <section id="llmfit" class="mb-10 bg-white rounded-lg shadow-sm border border-gray-200 p-6 scroll-mt-24" data-current-ollama-model="<?php echo e($currentService === 'ollama' ? ($userProfile['ollama_model'] ?? '') : ''); ?>">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">LLMfit: Find models that run on your hardware</h2>
                <p class="text-sm text-gray-600 mb-4">LLMfit scans your Mac and ranks 150+ models by Quality, Speed, and Fit. For our writing features, use the <strong>reasoning</strong> profile (prioritises quality).</p>
                <p class="text-xs text-amber-800 bg-amber-50 border border-amber-200 rounded p-2 mb-4">LLMfit uses HuggingFace-style names (e.g. <code>Qwen/Qwen3-4B-AWQ</code>); Ollama uses shorter names (e.g. <code>qwen3:4b-instruct</code>). They refer to the same family. If your current model works well, keep using it — it may not appear here because LLMfit only shows the top 20, or uses a different variant name.</p>

                <!-- Auto (local) -->
                <div class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                    <h3 class="text-sm font-semibold text-gray-900 mb-2">Option 1: Auto-detect (local development only)</h3>
                    <p class="text-xs text-gray-600 mb-3">When running the app locally, we can run LLMfit for you.</p>
                    <button type="button" id="llmfit-auto-btn" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm font-medium">
                        <span id="llmfit-auto-text">Run LLMfit</span>
                        <span id="llmfit-auto-loading" class="hidden ml-2">Running...</span>
                    </button>
                    <div id="llmfit-auto-result" class="hidden mt-4"></div>
                </div>

                <!-- Manual paste -->
                <div class="p-4 bg-blue-50 rounded-lg border border-blue-200">
                    <h3 class="text-sm font-semibold text-gray-900 mb-2">Option 2: Paste LLMfit output</h3>
                    <p class="text-xs text-gray-600 mb-2">Run in Terminal: <code class="bg-white px-1 py-0.5 rounded text-sm">llmfit recommend --json --limit 20</code></p>
                    <p class="text-xs text-gray-600 mb-3">Or for writing tasks: <code class="bg-white px-1 py-0.5 rounded text-sm">llmfit recommend --profile reasoning --json --limit 20</code></p>
                    <textarea id="llmfit-paste" rows="6" placeholder="Paste the JSON output from llmfit here..." class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm font-mono mb-2"></textarea>
                    <button type="button" id="llmfit-parse-btn" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">Parse and show recommendations</button>
                    <div id="llmfit-parse-result" class="hidden mt-4"></div>
                </div>
            </section>

            <!-- Browser AI section -->
            <section id="browser-ai" class="mb-10 bg-white rounded-lg shadow-sm border border-gray-200 p-6 scroll-mt-24">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Browser AI (WebLLM)</h2>
                <p class="text-sm text-gray-600 mb-4">Runs in your browser — no server, no API keys. Models are cached locally after first use.</p>
                <ul class="text-sm text-gray-700 space-y-1 list-disc list-inside">
                    <li><strong>Llama 3.2</strong> – Good balance for CV and cover letters</li>
                    <li><strong>Mistral 7B</strong> – Strong for writing tasks</li>
                    <li><strong>Phi-3 Mini</strong> – Faster, lighter</li>
                    <li><strong>Gemma 2</strong> – Smaller footprint</li>
                </ul>
                <p class="mt-3 text-xs text-gray-600">Choose in <a href="/ai-settings.php" class="text-blue-600 hover:underline">AI Settings</a> under Browser-Based AI.</p>
            </section>

            <div class="text-center">
                <a href="/ai-settings.php" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">Open AI Settings</a>
            </div>
        </div>
    </main>

    <?php partial('footer'); ?>

    <script>
    (function() {
        var csrf = '<?php echo e($csrf); ?>';
        var currentOllamaModel = '<?php echo e($currentService === 'ollama' ? ($userProfile['ollama_model'] ?? '') : ''); ?>';

        // Ollama detect
        var detectBtn = document.getElementById('detect-ollama-btn');
        if (detectBtn) {
            detectBtn.addEventListener('click', function() {
                var urlEl = document.getElementById('ollama_url');
                var url = urlEl ? urlEl.value.trim() || 'http://localhost:11434' : 'http://localhost:11434';
                var result = document.getElementById('ollama-result');
                var text = document.getElementById('detect-ollama-text');
                var loading = document.getElementById('detect-ollama-loading');
                detectBtn.disabled = true;
                text.classList.add('hidden');
                loading.classList.remove('hidden');
                result.classList.add('hidden');

                var fd = new FormData();
                fd.append('<?php echo CSRF_TOKEN_NAME; ?>', csrf);
                fd.append('base_url', url);

                fetch('/api/test-ollama-connection.php', { method: 'POST', body: fd, credentials: 'include' })
                    .then(function(r) { return r.json(); })
                    .then(function(data) {
                        result.classList.remove('hidden');
                        if (data.success && data.models && data.models.length > 0) {
                            var html = '<div class="p-4 bg-green-50 border border-green-200 rounded-lg"><p class="text-sm font-medium text-green-800 mb-2">' + (data.message || 'Found ' + data.models.length + ' model(s)') + '</p>';
                            html += '<table class="min-w-full text-sm"><thead><tr class="border-b"><th class="text-left py-1">Model</th><th class="text-left py-1">Suggested for</th></tr></thead><tbody>';
                            data.models.forEach(function(m) {
                                var name = m.name || m.model || 'Unknown';
                                var params = (name.match(/\d+b/i) || [])[0] || '';
                                var suggested = params && parseInt(params) >= 7 ? 'CV rewriting, cover letters, interview help' : (params ? 'Application questions, keywords' : 'General use');
                                html += '<tr class="border-b border-green-100"><td class="py-2 font-mono text-xs">' + escapeHtml(name) + '</td><td class="py-2 text-gray-700">' + escapeHtml(suggested) + '</td></tr>';
                            });
                            html += '</tbody></table></div>';
                            result.innerHTML = html;
                        } else if (data.success) {
                            result.innerHTML = '<div class="p-4 bg-amber-50 border border-amber-200 rounded-lg"><p class="text-sm text-amber-800">' + (data.message || 'No models found. Run <code>ollama pull llama3.2</code> to install one.') + '</p></div>';
                        } else {
                            result.innerHTML = '<div class="p-4 bg-red-50 border border-red-200 rounded-lg"><p class="text-sm text-red-800">' + (data.error || 'Connection failed') + '</p></div>';
                        }
                    })
                    .catch(function() {
                        result.classList.remove('hidden');
                        result.innerHTML = '<div class="p-4 bg-red-50 border border-red-200 rounded-lg"><p class="text-sm text-red-800">Request failed. Make sure Ollama is running.</p></div>';
                    })
                    .finally(function() {
                        detectBtn.disabled = false;
                        text.classList.remove('hidden');
                        loading.classList.add('hidden');
                    });
            });
        }

        // LLMfit auto
        var llmfitAutoBtn = document.getElementById('llmfit-auto-btn');
        if (llmfitAutoBtn) {
            llmfitAutoBtn.addEventListener('click', function() {
                var result = document.getElementById('llmfit-auto-result');
                var text = document.getElementById('llmfit-auto-text');
                var loading = document.getElementById('llmfit-auto-loading');
                llmfitAutoBtn.disabled = true;
                text.classList.add('hidden');
                loading.classList.remove('hidden');
                result.classList.add('hidden');

                var fd = new FormData();
                fd.append('<?php echo CSRF_TOKEN_NAME; ?>', csrf);

                fetch('/api/llmfit-recommend.php', { method: 'POST', body: fd, credentials: 'include' })
                    .then(function(r) { return r.json(); })
                    .then(function(data) {
                        result.classList.remove('hidden');
                        if (data.success && data.models && data.models.length > 0) {
                            result.innerHTML = renderLlmfitTable(data.models);
                        } else if (data.available === false) {
                            result.innerHTML = '<div class="p-4 bg-amber-50 border border-amber-200 rounded-lg"><p class="text-sm text-amber-800">' + (data.message || 'LLMfit not available. Use Option 2 to paste output.') + '</p></div>';
                        } else {
                            result.innerHTML = '<div class="p-4 bg-red-50 border border-red-200 rounded-lg"><p class="text-sm text-red-800">' + (data.error || 'Could not run LLMfit') + '</p></div>';
                        }
                    })
                    .catch(function() {
                        result.classList.remove('hidden');
                        result.innerHTML = '<div class="p-4 bg-red-50 border border-red-200 rounded-lg"><p class="text-sm text-red-800">Request failed. Use Option 2 to paste LLMfit output.</p></div>';
                    })
                    .finally(function() {
                        llmfitAutoBtn.disabled = false;
                        text.classList.remove('hidden');
                        loading.classList.add('hidden');
                    });
            });
        }

        // LLMfit parse paste
        var parseBtn = document.getElementById('llmfit-parse-btn');
        if (parseBtn) {
            parseBtn.addEventListener('click', function() {
                var textarea = document.getElementById('llmfit-paste');
                var result = document.getElementById('llmfit-parse-result');
                var raw = (textarea && textarea.value) ? textarea.value.trim() : '';
                result.classList.add('hidden');
                if (!raw) {
                    result.classList.remove('hidden');
                    result.innerHTML = '<div class="p-4 bg-amber-50 border border-amber-200 rounded-lg"><p class="text-sm text-amber-800">Paste LLMfit JSON output first.</p></div>';
                    return;
                }
                try {
                    var parsed = JSON.parse(raw);
                    var models = [];
                    if (Array.isArray(parsed)) {
                        models = parsed;
                    } else if (parsed.models && Array.isArray(parsed.models)) {
                        models = parsed.models;
                    } else if (parsed.recommendations && Array.isArray(parsed.recommendations)) {
                        models = parsed.recommendations;
                    } else if (typeof parsed === 'object') {
                        models = [parsed];
                    }
                    if (models.length > 0) {
                        result.classList.remove('hidden');
                        result.innerHTML = renderLlmfitTable(models);
                    } else {
                        result.classList.remove('hidden');
                        result.innerHTML = '<div class="p-4 bg-amber-50 border border-amber-200 rounded-lg"><p class="text-sm text-amber-800">Could not find model list in JSON. Expected an array or object with "models" or "recommendations".</p></div>';
                    }
                } catch (e) {
                    result.classList.remove('hidden');
                    result.innerHTML = '<div class="p-4 bg-red-50 border border-red-200 rounded-lg"><p class="text-sm text-red-800">Invalid JSON: ' + escapeHtml(e.message) + '</p></div>';
                }
            });
        }

        function renderLlmfitTable(models) {
            var sc, q, s, f, name, suggested;
            var html = '<div class="p-4 bg-green-50 border border-green-200 rounded-lg overflow-x-auto">';
            html += '<p class="text-xs text-gray-600 mb-2">Models are ranked by LLMfit (best first). Quality, Speed, and Fit are 0–100 scores.</p>';
            html += '<table class="min-w-full text-sm"><thead><tr class="border-b border-green-300"><th class="text-left py-2 px-2">Model</th><th class="text-left py-2 px-2">Quality</th><th class="text-left py-2 px-2">Speed</th><th class="text-left py-2 px-2">Fit</th><th class="text-left py-2 px-2">Suggested for</th></tr></thead><tbody>';
            if (currentOllamaModel) {
                html += '<tr class="border-b border-blue-200 bg-blue-50"><td class="py-2 px-2 font-mono text-xs font-medium text-blue-800">' + escapeHtml(currentOllamaModel) + ' <span class="text-blue-600">(your model)</span></td><td class="py-2 px-2">—</td><td class="py-2 px-2">—</td><td class="py-2 px-2">—</td><td class="py-2 px-2 text-gray-700">If it works well, keep using it</td></tr>';
            }
            models.slice(0, 20).forEach(function(m) {
                name = m.name || m.model || m.id || m.model_id || JSON.stringify(m).substring(0, 40);
                if (typeof name === 'object') name = name.display || name.name || JSON.stringify(name);
                sc = m.score_components || m.scores || {};
                q = m.quality != null ? m.quality : sc.quality;
                s = m.speed != null ? m.speed : sc.speed;
                f = m.fit != null ? m.fit : sc.fit;
                q = q != null ? (typeof q === 'number' ? q : parseFloat(q)) : '-';
                s = s != null ? (typeof s === 'number' ? s : parseFloat(s)) : '-';
                f = f != null ? (typeof f === 'number' ? f : parseFloat(f)) : '-';
                suggested = (q >= 70 || q === '-') ? 'CV, cover letters, interview help' : 'Application questions, keywords';
                html += '<tr class="border-b border-green-100"><td class="py-2 px-2 font-mono text-xs">' + escapeHtml(String(name)) + '</td><td class="py-2 px-2">' + q + '</td><td class="py-2 px-2">' + s + '</td><td class="py-2 px-2">' + f + '</td><td class="py-2 px-2 text-gray-700">' + escapeHtml(suggested) + '</td></tr>';
            });
            html += '</tbody></table></div>';
            return html;
        }

        function escapeHtml(s) {
            if (!s) return '';
            var d = document.createElement('div');
            d.textContent = s;
            return d.innerHTML;
        }
    })();
    </script>
</body>
</html>
