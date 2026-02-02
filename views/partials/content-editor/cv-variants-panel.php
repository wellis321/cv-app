<?php
/**
 * CV Variants Panel Component
 * Displays list of CV variants within content editor
 */

require_once __DIR__ . '/../../../php/cv-variants.php';
require_once __DIR__ . '/../../../php/cv-data.php';

$userId = getUserId();
$variants = getUserCvVariants($userId);
$csrf = csrfToken();

// Limited scope for local/browser AI: tailor one role or one project at a time
$pref = db()->fetchOne("SELECT ai_service_preference FROM profiles WHERE id = ?", [$userId]);
$ai_scope_limited = in_array($pref['ai_service_preference'] ?? '', ['ollama', 'browser']);
// Load variant data for Tailor dropdown when scope limited (need WE and projects per variant)
if ($ai_scope_limited) {
    foreach ($variants as &$v) {
        if (!$v['is_master'] && !empty($v['job_application_id'])) {
            $v['_data'] = loadCvVariantData($v['id']);
        }
    }
    unset($v);
}
?>
<div class="p-6 max-w-7xl mx-auto">
    <!-- Header: title and description full width above, button on its own row below -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">CV Variants</h1>
        <p class="mt-1 text-sm text-gray-500 max-w-3xl">Manage different versions of your CV for specific job applications. Open a job in Manage Jobs and use &ldquo;Generate AI CV for this job&rdquo; or &ldquo;Tailor CV for this job…&rdquo; to create a variant from that job.</p>
        <div class="mt-4 flex justify-end">
            <a href="#cv-variants&create=1" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors text-sm font-medium whitespace-nowrap">
                <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
                Create New CV with AI
            </a>
        </div>
    </div>

    <!-- Variants Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Variant Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Job Application</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($variants)): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                No CV variants found. Create your first variant using AI rewriting.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($variants as $variant): ?>
                            <tr class="hover:bg-gray-50" data-variant-id="<?php echo e($variant['id']); ?>">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">
                                                <?php echo e($variant['variant_name']); ?>
                                                <?php if ($variant['is_master']): ?>
                                                    <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">Master</span>
                                                <?php endif; ?>
                                                <?php if ($variant['ai_generated']): ?>
                                                    <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">AI</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php if ($variant['job_application_id']): ?>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 mr-1.5">Linked</span>
                                        <a href="#jobs&view=<?php echo e($variant['job_application_id']); ?>" class="text-blue-600 hover:text-blue-800">
                                            <?php echo e($variant['job_title'] ?? 'Untitled Job'); ?>
                                        </a>
                                        <div class="text-xs text-gray-400"><?php echo e($variant['company_name'] ?? ''); ?></div>
                                    <?php else: ?>
                                        <span class="text-gray-400">—</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php if ($variant['is_master']): ?>
                                        Master CV
                                    <?php elseif ($variant['ai_generated']): ?>
                                        AI-Generated
                                    <?php else: ?>
                                        Custom
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo date('d/m/Y', strtotime($variant['created_at'])); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-2">
                                        <a href="/cv.php?variant_id=<?php echo e($variant['id']); ?>" 
                                           target="_blank"
                                           class="text-blue-600 hover:text-blue-900" 
                                           title="View CV">
                                            View
                                        </a>
                                        <a href="#work-experience&variant_id=<?php echo e($variant['id']); ?>" 
                                           class="text-indigo-600 hover:text-indigo-900" 
                                           title="Edit this variant in the content editor">
                                            Edit
                                        </a>
                                        <a href="#ai-tools&variant_id=<?php echo e($variant['id']); ?>" 
                                           class="text-green-600 hover:text-green-900" 
                                           title="Assess Quality">
                                            Assess
                                        </a>
                                        <?php if (!$variant['is_master'] && !empty($variant['job_application_id'])): ?>
                                            <?php
                                            $tailorScopeLimited = $ai_scope_limited;
                                            $tailorWe = ($tailorScopeLimited && !empty($variant['_data']['work_experience'])) ? $variant['_data']['work_experience'] : [];
                                            $tailorProjects = ($tailorScopeLimited && !empty($variant['_data']['projects'])) ? $variant['_data']['projects'] : [];
                                            ?>
                                            <span class="inline-flex items-center gap-1">
                                                <select class="tailor-section-select text-xs border border-gray-300 rounded px-2 py-1 text-gray-700 bg-white" 
                                                        data-scope-limited="<?php echo $tailorScopeLimited ? '1' : '0'; ?>"
                                                        onchange="tailorSection(this, '<?php echo e($variant['id']); ?>', '<?php echo e($variant['job_application_id']); ?>')"
                                                        title="<?php echo $tailorScopeLimited ? 'Tailor one role or project' : 'Tailor one section to the job'; ?>">
                                                    <option value="">Tailor section…</option>
                                                    <option value="professional_summary">Professional summary</option>
                                                    <?php if ($tailorScopeLimited): ?>
                                                        <?php foreach ($tailorWe as $we): ?>
                                                            <?php $weId = $we['id'] ?? $we['original_work_experience_id'] ?? ''; ?>
                                                            <option value="work_experience:<?php echo e($weId); ?>">Role: <?php echo e(($we['position'] ?? '') . ' at ' . ($we['company_name'] ?? '')); ?></option>
                                                        <?php endforeach; ?>
                                                        <?php foreach ($tailorProjects as $proj): ?>
                                                            <?php $projId = $proj['id'] ?? $proj['original_project_id'] ?? ''; ?>
                                                            <option value="project:<?php echo e($projId); ?>">Project: <?php echo e($proj['title'] ?? $proj['name'] ?? ''); ?></option>
                                                        <?php endforeach; ?>
                                                        <option value="skills">Skills</option>
                                                    <?php else: ?>
                                                        <option value="work_experience">Work experience</option>
                                                        <option value="skills">Skills</option>
                                                        <option value="education">Education</option>
                                                        <option value="projects">Projects</option>
                                                    <?php endif; ?>
                                                </select>
                                                <span class="tailor-status text-xs text-gray-500 hidden" aria-live="polite"></span>
                                            </span>
                                        <?php endif; ?>
                                        <?php if (!$variant['is_master']): ?>
                                            <button onclick="editVariantName('<?php echo e($variant['id']); ?>', '<?php echo e(addslashes($variant['variant_name'])); ?>')" 
                                                    class="text-gray-600 hover:text-gray-900" 
                                                    title="Rename">
                                                Rename
                                            </button>
                                            <button onclick="deleteVariant('<?php echo e($variant['id']); ?>', '<?php echo e(addslashes($variant['variant_name'])); ?>')" 
                                                    class="text-red-600 hover:text-red-900" 
                                                    title="Delete">
                                                Delete
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
(function() {
    var csrfTokenName = '<?php echo addslashes(CSRF_TOKEN_NAME); ?>';
    var csrfToken = '<?php echo e($csrf); ?>';

    async function executeBrowserAIForTailor(data, variantId, jobApplicationId, section, setStatus, selectEl) {
        if (typeof BrowserAIService === 'undefined') {
            throw new Error('Browser AI is not available. Use a Chromium-based browser or Safari and refresh the page.');
        }
        var support = BrowserAIService.checkBrowserSupport();
        if (!support.required) {
            throw new Error('Browser does not support WebGPU/WebGL. Use Ollama or a cloud AI in Settings > AI Settings.');
        }
        var modelType = (data.model_type === 'webllm') ? 'webllm' : 'tensorflow';
        await BrowserAIService.initBrowserAI(modelType, data.model || 'llama3.2', function(progress) {
            if (progress && progress.message) setStatus(progress.message, true);
        });
        setStatus('Tailoring…', true);
        var rewrittenText = await BrowserAIService.generateText(data.prompt, { temperature: 0.7, maxTokens: 4000 });
        var text = String(rewrittenText || '').trim();
        text = text.replace(/<\|[^]*?\|>/g, '').replace(/\[INST\][\s\S]*?\[\/INST\]/gi, '').replace(/<s>[\s\S]*?<\/s>/gi, '');
        var codeBlock = text.match(/```(?:json)?\s*([\s\S]*?)```/);
        if (codeBlock) text = codeBlock[1].trim();
        var start = text.indexOf('{');
        if (start === -1) throw new Error('AI did not return valid JSON. Try again or use Ollama/cloud AI.');
        var depth = 0, end = -1;
        for (var i = start; i < text.length; i++) {
            if (text[i] === '{') depth++;
            else if (text[i] === '}') { depth--; if (depth === 0) { end = i; break; } }
        }
        if (end === -1) throw new Error('Could not parse AI response. Try again or use Ollama/cloud AI.');
        var jsonStr = text.substring(start, end + 1);
        var rewrittenData = null;
        try {
            rewrittenData = JSON.parse(jsonStr);
        } catch (e) {
            try {
                jsonStr = jsonStr.replace(/,\s*}/g, '}').replace(/,\s*]/g, ']');
                rewrittenData = JSON.parse(jsonStr);
            } catch (e2) {
                throw new Error('Could not parse AI response. Try again or use Ollama/cloud AI.');
            }
        }
        if (!rewrittenData || typeof rewrittenData !== 'object') throw new Error('Invalid AI response.');
        var formData = new FormData();
        formData.append('update_variant_id', variantId);
        formData.append('job_application_id', jobApplicationId);
        if (section.indexOf('work_experience:') === 0) {
            formData.append('sections_to_rewrite[]', 'work_experience');
            formData.append('single_work_experience_id', section.slice('work_experience:'.length));
        } else if (section.indexOf('project:') === 0) {
            formData.append('sections_to_rewrite[]', 'projects');
            formData.append('single_project_id', section.slice('project:'.length));
        } else {
            formData.append('sections_to_rewrite[]', section);
        }
        formData.append('browser_ai_result', JSON.stringify(rewrittenData));
        formData.append(csrfTokenName, csrfToken);
        var response = await fetch('/api/ai-rewrite-cv.php', { method: 'POST', body: formData, credentials: 'include' });
        var respText = await response.text();
        var result = null;
        try { result = respText ? JSON.parse(respText) : null; } catch (e) { throw new Error('Server returned an invalid response.'); }
        selectEl.disabled = false;
        setStatus('', false);
        if (result && result.success) {
            var msg = result.message || 'Section tailored successfully.';
            if (typeof window.showNotificationModal === 'function') {
                window.showNotificationModal({ type: 'success', title: 'Tailor section', message: msg });
            } else {
                alert(msg);
            }
            if (typeof window.contentEditor !== 'undefined' && typeof window.contentEditor.loadSection === 'function') {
                window.contentEditor.loadSection('cv-variants');
            } else {
                window.location.reload();
            }
        } else {
            var errMsg = (result && result.error) ? result.error : 'Could not tailor section. Please try again.';
            if (typeof window.showNotificationModal === 'function') {
                window.showNotificationModal({ type: 'error', title: 'Tailor section', message: errMsg });
            } else {
                alert(errMsg);
            }
        }
    }
    window.editVariantName = function(variantId, currentName) {
        var newName = prompt('Enter new name for this CV variant:', currentName);
        if (!newName || newName.trim() === '' || newName === currentName) {
            return;
        }
        
        var formData = new FormData();
        formData.append('action', 'rename');
        formData.append('variant_id', variantId);
        formData.append('variant_name', newName.trim());
        formData.append('<?php echo CSRF_TOKEN_NAME; ?>', '<?php echo e($csrf); ?>');
        
        fetch('/api/cv-variants.php', {
            method: 'POST',
            body: formData,
            credentials: 'include'
        })
        .then(function(response) {
            return response.json().then(function(data) {
                if (!response.ok || !data.success) {
                    throw new Error(data.error || 'Could not rename variant.');
                }
                return data;
            });
        })
        .then(function() {
            if (typeof window.contentEditor !== 'undefined' && typeof window.contentEditor.loadSection === 'function') {
                window.contentEditor.loadSection('cv-variants');
            } else {
                window.location.reload();
            }
        })
        .catch(function(err) {
            if (typeof window.showNotificationModal === 'function') {
                window.showNotificationModal({ type: 'error', title: 'Error', message: err.message || 'Could not rename variant. Please try again.' });
            } else {
                alert(err.message || 'Could not rename variant. Please try again.');
            }
        });
    };
    
    window.deleteVariant = function(variantId, variantName) {
        var doDelete = function() {
            var formData = new FormData();
            formData.append('action', 'delete');
            formData.append('variant_id', variantId);
            formData.append('<?php echo CSRF_TOKEN_NAME; ?>', '<?php echo e($csrf); ?>');
            fetch('/api/cv-variants.php', {
                method: 'POST',
                body: formData,
                credentials: 'include'
            })
            .then(function(response) {
                return response.json().then(function(data) {
                    if (!response.ok || !data.success) {
                        throw new Error(data.error || 'Could not delete variant.');
                    }
                    return data;
                });
            })
            .then(function() {
                if (typeof window.contentEditor !== 'undefined' && typeof window.contentEditor.loadSection === 'function') {
                    window.contentEditor.loadSection('cv-variants');
                } else {
                    window.location.reload();
                }
            })
            .catch(function(err) {
                if (typeof window.showNotificationModal === 'function') {
                    window.showNotificationModal({ type: 'error', title: 'Error', message: err.message || 'Could not delete variant. Please try again.' });
                } else {
                    alert(err.message || 'Could not delete variant. Please try again.');
                }
            });
        };
        if (typeof window.showConfirmModal === 'function') {
            window.showConfirmModal({
                type: 'error',
                title: 'Delete variant',
                message: 'Are you sure you want to delete "' + variantName + '"? This action cannot be undone.',
                confirmLabel: 'Delete',
                cancelLabel: 'Cancel'
            }).then(function(ok) {
                if (ok) doDelete();
            });
        } else {
            if (confirm('Are you sure you want to delete "' + variantName + '"? This action cannot be undone.')) {
                doDelete();
            }
        }
    };
    
    window.tailorSection = function(selectEl, variantId, jobApplicationId) {
        var section = selectEl.value;
        if (!section) return;
        selectEl.value = '';
        var runTailor = function() {
            var statusEl = selectEl.closest('span').querySelector('.tailor-status');
            var setStatus = function(text, show) {
                if (statusEl) {
                    statusEl.textContent = text || '';
                    statusEl.classList.toggle('hidden', !show);
                }
            };
            selectEl.disabled = true;
            setStatus('Tailoring…', true);
            var formData = new FormData();
            formData.append('update_variant_id', variantId);
            formData.append('job_application_id', jobApplicationId);
            if (section.indexOf('work_experience:') === 0) {
                formData.append('sections_to_rewrite[]', 'work_experience');
                formData.append('single_work_experience_id', section.slice('work_experience:'.length));
            } else if (section.indexOf('project:') === 0) {
                formData.append('sections_to_rewrite[]', 'projects');
                formData.append('single_project_id', section.slice('project:'.length));
            } else {
                formData.append('sections_to_rewrite[]', section);
            }
            formData.append('<?php echo CSRF_TOKEN_NAME; ?>', '<?php echo e($csrf); ?>');
            fetch('/api/ai-rewrite-cv.php', {
            method: 'POST',
            body: formData,
            credentials: 'include'
            })
            .then(function(response) {
                return response.text().then(function(text) {
                    var data = null;
                    try {
                        data = text ? JSON.parse(text) : null;
                    } catch (e) {
                        throw new Error('Server returned an invalid response. Please try again.');
                    }
                    if (!response.ok) {
                        throw new Error((data && data.error) || 'Request failed (' + response.status + ').');
                    }
                    return data;
                });
            })
            .then(async function(data) {
                if (data.browser_execution) {
                    setStatus('Loading AI model…', true);
                    try {
                        await executeBrowserAIForTailor(data, variantId, jobApplicationId, section, setStatus, selectEl);
                    } catch (e) {
                        selectEl.disabled = false;
                        setStatus('', false);
                        var msg = (e && e.message) ? e.message : 'Browser AI failed. Try again or use Ollama/cloud AI in Settings > AI Settings.';
                        if (typeof window.showNotificationModal === 'function') {
                            window.showNotificationModal({ type: 'error', title: 'Tailor section', message: msg });
                        } else {
                            alert(msg);
                        }
                    }
                    return;
                }
                selectEl.disabled = false;
                setStatus('', false);
                if (data.success) {
                    setStatus('Done', true);
                    var msg = data.message || 'Section tailored successfully.';
                    if (typeof window.showNotificationModal === 'function') {
                        window.showNotificationModal({ type: 'success', title: 'Tailor section', message: msg });
                    }
                    if (typeof window.contentEditor !== 'undefined' && typeof window.contentEditor.loadSection === 'function') {
                        window.contentEditor.loadSection('cv-variants');
                    } else {
                        window.location.reload();
                    }
                } else {
                    var msg = data.error || 'Could not tailor section. Please try again.';
                    if (typeof window.showNotificationModal === 'function') {
                        window.showNotificationModal({ type: 'error', title: 'Tailor section', message: msg });
                    } else {
                        alert(msg);
                    }
                }
            })
            .catch(function(err) {
                selectEl.disabled = false;
                setStatus('', false);
                var msg = err && err.message ? err.message : 'Could not tailor section. Please try again.';
                if (typeof window.showNotificationModal === 'function') {
                    window.showNotificationModal({ type: 'error', title: 'Tailor section', message: msg });
                } else {
                    alert(msg);
                }
            });
        };
        if (typeof window.showConfirmModal === 'function') {
            window.showConfirmModal({
                title: 'Tailor section',
                message: 'Tailor "' + section.replace(/_/g, ' ') + '" to the job for this variant? This may take 30–60 seconds.',
                confirmLabel: 'Tailor',
                cancelLabel: 'Cancel'
            }).then(function(ok) {
                if (ok) runTailor();
            });
        } else {
            if (confirm('Tailor "' + section.replace(/_/g, ' ') + '" to the job for this variant? This may take 30–60 seconds.')) {
                runTailor();
            }
        }
    };
})();
</script>
