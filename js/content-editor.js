/**
 * Content Editor JavaScript Module
 * Handles dynamic form loading, AJAX submissions, and section navigation
 */

(function() {
    'use strict';

    const data = window.contentEditorData || {};
    let currentSectionId = data.currentSectionId || 'professional-summary';
    let currentGuidance = null;
    let isLoadingSection = false;
    let formHandlersInitialized = false;

    // Initialize on DOM ready
    document.addEventListener('DOMContentLoaded', function() {
        initializeEditor();
    });

    function initializeEditor() {
        // Check for hash on initial load
        const hash = window.location.hash.substring(1);
        if (hash) {
            const hashParts = hash.split('&');
            const sectionFromHash = hashParts[0];
            // Allow CV sections, jobs, ai-tools, cv-variants, profile/appearance/visibility, and custom sections
            const validSections = ['jobs', 'ai-tools', 'cv-variants', 'profile', 'appearance', 'visibility'];
            const isValidCvSection = data.sections && data.sections.some(s => s.id === sectionFromHash);
            const isCustomSection = sectionFromHash && sectionFromHash.startsWith('custom-');
            if (sectionFromHash && (isValidCvSection || validSections.includes(sectionFromHash) || isCustomSection)) {
                currentSectionId = sectionFromHash;
            }
        }
        
        // Update sidebar to reflect current section
        updateSidebarActiveState(currentSectionId);
        const initialHash = window.location.hash.substring(1);
        const initialVariantId = getHashParam(initialHash, 'variant_id');
        if (initialVariantId) {
            updateSidebarLinkHrefs(initialVariantId);
        }
        updateViewCvBar();
        
        // Handle initial section load BEFORE manipulating hash
        // This ensures view/edit/add parameters are preserved
        loadSection(currentSectionId);
        loadGuidance(currentSectionId);
        
        // Prevent default hash scrolling on page load (after section is loaded)
        // This prevents browser from scrolling to hash element
        if (window.location.hash) {
            const hash = window.location.hash;
            // Only manipulate hash if it's not a content-editor hash (doesn't start with #jobs, #ai-tools, #cv-variants, or section IDs)
            const hashValue = hash.substring(1);
            const isContentEditorHash = data.sections?.some(s => s.id === hashValue.split('&')[0]) ||
                                       ['jobs', 'ai-tools', 'cv-variants', 'profile', 'appearance', 'visibility'].includes(hashValue.split('&')[0]) ||
                                       hashValue.split('&')[0].startsWith('custom-');
            if (!isContentEditorHash) {
                window.history.replaceState(null, null, ' ');
                setTimeout(() => {
                    window.history.replaceState(null, null, hash);
                }, 0);
            }
        }

        // Handle hash changes (browser back/forward, or in-place view/edit change e.g. #jobs&view=id)
        window.addEventListener('hashchange', handleHashChange);
        
        // Handle sidebar clicks
        document.querySelectorAll('.section-nav-item').forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                const sectionId = this.dataset.sectionId;
                if (sectionId) {
                    navigateToSection(sectionId);
                }
            });
        });

        // Auto-hide flash messages after 5 seconds
        setTimeout(() => {
            document.querySelectorAll('.fixed.top-20').forEach(el => {
                el.style.transition = 'opacity 0.3s';
                el.style.opacity = '0';
                setTimeout(() => el.remove(), 300);
            });
        }, 5000);
    }

    function handleHashChange(e) {
        const hash = window.location.hash.substring(1);
        const oldHash = (e && e.oldURL && e.oldURL.indexOf('#') >= 0) ? (e.oldURL.split('#')[1] || '') : '';
        
        if (!hash) return;
        
        const hashParts = hash.split('&');
        const sectionId = hashParts[0];
        
        if (sectionId && sectionId !== currentSectionId) {
            currentSectionId = sectionId;
            updateSidebarActiveState(sectionId);
            const variantId = getHashParam(hash, 'variant_id');
            updateSidebarLinkHrefs(variantId);
            loadSection(sectionId);
            loadGuidance(sectionId);
        } else if (sectionId === currentSectionId) {
            // Same section - check if edit, view, add, or variant_id parameter changed
            const getParam = (h, name) => (h && h.includes('&' + name + '=')) ? ((h.split('&').find(p => p.startsWith(name + '=')) || '').replace(name + '=', '') || null) : null;
            const currEdit = getParam(hash, 'edit');
            const currView = getParam(hash, 'view');
            const currAdd = getParam(hash, 'add');
            const currVariant = getParam(hash, 'variant_id');
            const prevEdit = getParam(oldHash, 'edit');
            const prevView = getParam(oldHash, 'view');
            const prevAdd = getParam(oldHash, 'add');
            const prevVariant = getParam(oldHash, 'variant_id');
            const currCreate = getParam(hash, 'create');
            const prevCreate = getParam(oldHash, 'create');
            if (currEdit !== prevEdit || (sectionId === 'jobs' && (currView !== prevView || currAdd !== prevAdd)) || ((sectionId === 'ai-tools' || sectionId === 'cv-variants') && currVariant !== prevVariant) || (sectionId === 'cv-variants' && currCreate !== prevCreate)) {
                loadSection(sectionId);
            }
        }
        updateViewCvBar();
    }

    function getHashParam(hash, name) {
        if (!hash || !hash.includes('&' + name + '=')) return null;
        const part = hash.split('&').find(p => p.startsWith(name + '='));
        return part ? part.replace(name + '=', '') : null;
    }

    function navigateToSection(sectionId) {
        if (!sectionId) return;
        
        const mainElement = document.getElementById('main-content') || document.querySelector('main');
        const currentHash = window.location.hash.substring(1);
        const variantId = getHashParam(currentHash, 'variant_id');
        // Preserve variant_id when switching sections so "Edit variant" keeps context
        let newHash = sectionId;
        if (variantId) {
            newHash = sectionId + '&variant_id=' + encodeURIComponent(variantId);
        }
        
        currentSectionId = sectionId;
        
        // Set hash to newHash BEFORE loadSection so the fetch URL includes variant_id (and edit/view/add)
        window.history.replaceState(null, '', '#' + newHash);
        
        // Update sidebar active state and link hrefs (so sidebar shows correct targets)
        updateSidebarActiveState(sectionId);
        updateSidebarLinkHrefs(variantId);
        updateViewCvBar();
        
        // Load section content; loadSection reads window.location.hash so variant_id is passed
        loadSection(sectionId);
        
        setTimeout(() => {
            if (mainElement) mainElement.scrollTop = 0;
            window.scrollTo(0, 0);
            if (document.documentElement) document.documentElement.scrollTop = 0;
            if (document.body) document.body.scrollTop = 0;
        }, 10);
        
        // Load guidance
        loadGuidance(sectionId);
    }

    // Accent colour per section type - matches the colours hardcoded server-side in
    // section-sidebar.php/_section-nav-item.php (bg-{c}-50, text-{c}-700, border-l-{c}-500,
    // text-{c}-600 for the icon). Keep this map in sync with those PHP files.
    const SIDEBAR_ACCENT_COLORS = {
        jobs: 'green',
        'ai-tools': 'purple',
        'cv-variants': 'indigo'
    };
    const SIDEBAR_ALL_COLORS = ['blue', 'green', 'purple', 'indigo'];

    function updateSidebarActiveState(sectionId) {
        const bgClasses = SIDEBAR_ALL_COLORS.map(c => `bg-${c}-100`);
        const textClasses = SIDEBAR_ALL_COLORS.map(c => `text-${c}-700`);
        const borderLClasses = SIDEBAR_ALL_COLORS.map(c => `border-l-${c}-500`);
        const iconClasses = SIDEBAR_ALL_COLORS.map(c => `text-${c}-600`);

        document.querySelectorAll('.section-nav-item').forEach(item => {
            const itemSectionId = item.dataset.sectionId;
            const svg = item.querySelector('svg');
            const isActive = itemSectionId === sectionId;
            const color = SIDEBAR_ACCENT_COLORS[sectionId] || 'blue';

            item.classList.remove(...bgClasses, ...textClasses, ...borderLClasses, 'bg-white', 'border-l-gray-300');
            if (svg) svg.classList.remove('text-gray-400', ...iconClasses);

            if (isActive) {
                item.classList.remove('text-gray-700', 'hover:border-gray-400', 'hover:bg-gray-50', 'hover:shadow');
                item.classList.add(`bg-${color}-100`, `text-${color}-700`, `border-l-${color}-500`);
                if (svg) svg.classList.add(`text-${color}-600`);
            } else {
                item.classList.add('text-gray-700', 'bg-white', 'border-l-gray-300', 'hover:border-gray-400', 'hover:bg-gray-50', 'hover:shadow');
                if (svg) svg.classList.add('text-gray-400');
            }
        });
    }

    function updateSidebarLinkHrefs(variantId) {
        document.querySelectorAll('.section-nav-item').forEach(item => {
            const sectionId = item.dataset.sectionId;
            if (sectionId) {
                item.setAttribute('href', variantId ? '#' + sectionId + '&variant_id=' + encodeURIComponent(variantId) : '#' + sectionId);
            }
        });
    }

    function updateViewCvBar() {
        const hash = window.location.hash.substring(1);
        const variantId = getHashParam(hash, 'variant_id');
        const bar = document.getElementById('view-cv-bar');
        const viewLink = document.getElementById('view-cv-link');
        const pdfLink = document.getElementById('preview-pdf-link');
        const nameEl = document.getElementById('view-cv-bar-variant-name');
        if (!bar || !viewLink || !pdfLink) return;
        if (variantId) {
            bar.classList.add('hidden');
            viewLink.href = '/cv.php?variant_id=' + encodeURIComponent(variantId);
            pdfLink.href = '/preview-cv.php?variant_id=' + encodeURIComponent(variantId);
            var relatedJobId = '';
            if (window.contentEditorData && window.contentEditorData.cvVariants) {
                var v = window.contentEditorData.cvVariants.find(function (x) { return x.id === variantId; });
                relatedJobId = (v && v.job_application_id) ? v.job_application_id : '';
            }
            document.dispatchEvent(new CustomEvent('contenteditor:varianteditshown', { detail: { variantId: variantId, relatedJobId: relatedJobId } }));
        } else {
            bar.classList.add('hidden');
            if (nameEl) nameEl.textContent = '';
            document.dispatchEvent(new CustomEvent('contenteditor:variantedithidden'));
        }
    }

    function loadSection(sectionId) {
        
        // Prevent concurrent loads
        if (isLoadingSection) {
            return;
        }
        
        const contentArea = document.getElementById('section-content');
        if (!contentArea) return;

        isLoadingSection = true;

        // Show loading state
        contentArea.innerHTML = `
            <div class="max-w-3xl mx-auto">
                <div class="text-center py-12">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
                    <p class="mt-4 text-gray-500">Loading section...</p>
                </div>
            </div>
        `;

        // Check for edit/view/add/variant_id parameters in hash
        const hash = window.location.hash.substring(1);
        const editParam = hash.includes('&edit=') ? '&' + hash.split('&').find(p => p.startsWith('edit=')) : '';
        const viewParam = (sectionId === 'jobs' && hash.includes('&view=')) ? '&' + hash.split('&').find(p => p.startsWith('view=')) : '';
        const addParam = (sectionId === 'jobs' && hash.includes('&add=')) ? '&' + hash.split('&').find(p => p.startsWith('add=')) : '';
        const createParam = (sectionId === 'cv-variants' && hash.includes('&create=')) ? '&' + hash.split('&').find(p => p.startsWith('create=')) : '';
        const jobParam = (sectionId === 'cv-variants' && hash.includes('&job=')) ? '&' + hash.split('&').find(p => p.startsWith('job=')) : '';
        const variantParam = hash.includes('&variant_id=') ? '&' + hash.split('&').find(p => p.startsWith('variant_id=')) : '';
        
        
        // Fetch section form via AJAX
        fetch(`/api/content-editor/get-section-form.php?section_id=${encodeURIComponent(sectionId)}${editParam}${viewParam}${addParam}${createParam}${jobParam}${variantParam}`, {
            credentials: 'include',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.text();
        })
        .then(html => {
            const mainElement = document.getElementById('main-content') || contentArea.closest('main');
            const savedScrollTop = mainElement && editParam ? mainElement.scrollTop : null;
            
            contentArea.innerHTML = html;
            const scrollTopBefore = mainElement ? mainElement.scrollTop : null;

            // Jobs view: use gray background on main and remove section padding
            var hasJobsView = contentArea.querySelector('[data-jobs-view-container]');
            if (mainElement) mainElement.classList.toggle('jobs-view-active', !!hasJobsView);
            contentArea.classList.toggle('jobs-view-active', !!hasJobsView);

            // Notify nav bar when job view is shown/hidden (for View CV / Edit CV buttons)
            if (hasJobsView) {
                var container = contentArea.querySelector('[data-jobs-view-container]');
                var linkedVariantId = container ? (container.getAttribute('data-linked-variant-id') || '') : '';
                document.dispatchEvent(new CustomEvent('contenteditor:jobviewshown', { detail: { linkedVariantId: linkedVariantId } }));
            } else {
                document.dispatchEvent(new CustomEvent('contenteditor:jobviewhidden'));
            }

            // Jobs view: move Quick Nav into grid slot (between left sidebar and main) so it moves with layout when sidebar collapses
            var quickNavSlot = document.getElementById('jobs-quick-nav-slot');
            if (quickNavSlot) {
                if (hasJobsView) {
                    var quickNav = contentArea.querySelector('[data-jobs-quick-nav]');
                    if (quickNav) {
                        quickNavSlot.appendChild(quickNav);
                        quickNavSlot.classList.remove('hidden');
                        quickNavSlot.setAttribute('aria-hidden', 'false');
                    }
                } else {
                    quickNavSlot.innerHTML = '';
                    quickNavSlot.classList.add('hidden');
                    quickNavSlot.setAttribute('aria-hidden', 'true');
                }
            }

            // When NOT editing, scroll middle pane and window to top so user sees section from the start
            if (!editParam && !addParam) {
                if (mainElement) mainElement.scrollTop = 0;
                window.scrollTo(0, 0);
                if (document.documentElement) document.documentElement.scrollTop = 0;
                if (document.body) document.body.scrollTop = 0;
            }
            // When loading jobs edit or add form, scroll main content to top so user isn't left at the foot
            if (sectionId === 'jobs' && (editParam || addParam) && mainElement) {
                mainElement.scrollTop = 0;
            }
            
            // Initialize markdown editors for dynamically loaded forms
            setTimeout(() => {
                if (typeof window.MarkdownEditor !== 'undefined' && window.MarkdownEditor.initAll) {
                    window.MarkdownEditor.initAll();
                } else {
                    // Retry if not loaded yet
                    setTimeout(() => {
                        if (typeof window.MarkdownEditor !== 'undefined' && window.MarkdownEditor.initAll) {
                            window.MarkdownEditor.initAll();
                        }
                    }, 200);
                }
            }, 100);
            
            // Handle delete custom section button (emitted by custom-section-form.php)
            var deleteCustomSectionBtn = contentArea.querySelector('[data-action="delete-custom-section"]');
            if (deleteCustomSectionBtn) {
                deleteCustomSectionBtn.addEventListener('click', function() {
                    var sId = this.dataset.sectionId;
                    if (!confirm('Delete this custom section and all its items?')) return;
                    var csrfToken = (window.contentEditorData && window.contentEditorData.csrfToken) || '';
                    var csrfTokenName = (window.contentEditorData && window.contentEditorData.csrfTokenName) || '_csrf_token';
                    var body = new URLSearchParams();
                    body.append('action', 'delete');
                    body.append('id', sId);
                    body.append(csrfTokenName, csrfToken);
                    fetch('/api/content-editor/save-custom-section.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: body.toString()
                    }).then(function(r) { return r.json(); }).then(function(resp) {
                        if (resp.success) {
                            // Remove from sidebar nav
                            var navLink = document.querySelector('a[data-section-id="custom-' + sId + '"]');
                            if (navLink) navLink.remove();
                            navigateToSection('professional-summary');
                        }
                    }).catch(function() {});
                });
            }

            // Execute inline scripts from custom section form (and other partials that use them)
            if (sectionId.startsWith('custom-')) {
                setTimeout(function() {
                    var scripts = contentArea.querySelectorAll('script');
                    scripts.forEach(function(oldScript) {
                        if (oldScript.src) return;
                        var newScript = document.createElement('script');
                        Array.from(oldScript.attributes).forEach(function(attr) {
                            newScript.setAttribute(attr.name, attr.value);
                        });
                        newScript.textContent = oldScript.textContent;
                        oldScript.parentNode.replaceChild(newScript, oldScript);
                    });
                }, 50);
            }

            // Initialize form handlers (only for CV sections, not jobs/ai-tools/cv-variants)
            if (sectionId !== 'jobs' && sectionId !== 'ai-tools' && sectionId !== 'cv-variants') {
                initializeFormHandlers(sectionId);
                
                // Initialize responsibilities editor if present (for work experience)
                setTimeout(() => {
                    initializeResponsibilitiesEditor();
                }, 100);
                
                // Initialize project image upload handlers if present
                if (sectionId === 'projects') {
                    setTimeout(() => {
                        initializeProjectImageHandlers();
                    }, 100);
                }
                // Initialize work experience reorder (drag-and-drop) when list is shown
                if (sectionId === 'work-experience') {
                    setTimeout(() => {
                        initializeWorkExperienceReorder(contentArea);
                        initWorkExperienceAutosave(contentArea);
                        initWorkExperienceDisplayToggles(contentArea);
                    }, 150);
                }
                // Initialize profile photo upload/delete handlers
                if (sectionId === 'profile') {
                    setTimeout(() => {
                        initProfilePhotoSection(contentArea);
                    }, 100);
                }
                // Initialize visibility toggles and copy-URL button
                if (sectionId === 'visibility') {
                    setTimeout(() => {
                        initVisibilityToggles(contentArea);
                        initVisibilitySkillSelection(contentArea);
                        initInPageScrollLinks(contentArea);
                    }, 100);
                }
                // Initialize appearance: template select, header colour scheme, accent colour
                if (sectionId === 'appearance') {
                    setTimeout(() => {
                        initAppearanceTemplateSelect(contentArea);
                        initAppearanceColourScheme(contentArea);
                        initAppearanceAccentColour(contentArea);
                    }, 100);
                }
                // Initialize certifications reorder
                if (sectionId === 'certifications') {
                    setTimeout(() => {
                        initializeCertificationsReorder(contentArea);
                    }, 150);
                }
            } else if (sectionId === 'cv-variants') {
                // Extract and execute inline scripts for CV variants panel (list or create form)
                // Inline scripts don't execute when inserted via innerHTML, so we need to extract and run them
                setTimeout(() => {
                    const scripts = contentArea.querySelectorAll('script');
                    scripts.forEach(oldScript => {
                        if (oldScript.src) return;
                        const newScript = document.createElement('script');
                        Array.from(oldScript.attributes).forEach(attr => {
                            newScript.setAttribute(attr.name, attr.value);
                        });
                        newScript.textContent = oldScript.textContent;
                        oldScript.parentNode.replaceChild(newScript, oldScript);
                    });
                }, 150);
            } else if (sectionId === 'jobs') {
                // Load jobs panel script; then run init for list (initJobsPanelContentEditor), edit form (initJobsEditForm), add form (initJobsAddForm), or view (initJobsView)
                const runJobsInit = function() {
                    // Extract and execute inline scripts first (for keyword extraction, etc.)
                    const scripts = contentArea.querySelectorAll('script');
                    scripts.forEach(oldScript => {
                        // Skip external scripts (those with src attribute)
                        if (oldScript.src) return;
                        const newScript = document.createElement('script');
                        // Copy attributes
                        Array.from(oldScript.attributes).forEach(attr => {
                            newScript.setAttribute(attr.name, attr.value);
                        });
                        // Copy script content
                        newScript.textContent = oldScript.textContent;
                        // Replace old script with new one (this will execute it)
                        oldScript.parentNode.replaceChild(newScript, oldScript);
                    });
                    
                    // Then run the appropriate init function
                    const editForm = contentArea.querySelector('[data-jobs-edit-form]');
                    const addForm = contentArea.querySelector('[data-jobs-add-form]');
                    const viewContainer = contentArea.querySelector('[data-jobs-view-container]');
                    if (editForm && typeof window.initJobsEditForm === 'function') {
                        window.initJobsEditForm(editForm);
                    } else if (addForm && typeof window.initJobsAddForm === 'function') {
                        window.initJobsAddForm(addForm);
                    } else if (viewContainer && typeof window.initJobsView === 'function') {
                        window.initJobsView(viewContainer);
                    } else if (typeof window.initJobsPanelContentEditor === 'function') {
                        window.initJobsPanelContentEditor();
                    }
                };
                setTimeout(function() {
                    const existing = document.querySelector('script[data-jobs-panel-loaded]');
                    if (existing) {
                        runJobsInit();
                        return;
                    }
                    const script = document.createElement('script');
                    script.src = '/js/jobs-panel-content-editor.js?v=' + (Date.now ? Date.now() : '1');
                    script.setAttribute('data-jobs-panel-loaded', '1');
                    script.onload = runJobsInit;
                    script.onerror = function() {
                        const container = document.getElementById('jobs-applications-container');
                        if (container) {
                            container.innerHTML = '<div class="p-6 text-center text-red-500">Failed to load jobs panel. Please refresh the page.</div>';
                        }
                    };
                    document.head.appendChild(script);
                }, 100);
            } else if (sectionId === 'ai-tools') {
                // Initialize AI tools panel - extract and execute inline scripts
                // Inline scripts don't execute when inserted via innerHTML, so we need to extract and run them
                setTimeout(() => {
                    const aiToolsPanel = contentArea.querySelector('[data-ai-tools-panel]');
                    if (aiToolsPanel) {
                        // Find and execute any script tags in the loaded HTML
                        const scripts = contentArea.querySelectorAll('script');
                        scripts.forEach(oldScript => {
                            const newScript = document.createElement('script');
                            // Copy attributes
                            Array.from(oldScript.attributes).forEach(attr => {
                                newScript.setAttribute(attr.name, attr.value);
                            });
                            // Copy script content
                            newScript.textContent = oldScript.textContent;
                            // Replace old script with new one (this will execute it)
                            oldScript.parentNode.replaceChild(newScript, oldScript);
                        });
                        
                        // Also dispatch event to trigger initialization if needed
                        aiToolsPanel.dispatchEvent(new Event('ai-tools-loaded'));
                    }
                }, 150);
            }
            
            // Scroll behavior: only scroll when editing, preserve scroll position when switching sections
            // mainElement is already declared above
            
            
            if (editParam) {
                // In edit mode - scroll to show the section heading (h1 "Work Experience" or similar)
                setTimeout(() => {
                    // Find the main section heading (h1) - this is the "Work Experience" title
                    let scrollTarget = contentArea.querySelector('h1');
                    
                    // Fallback: find the form header (h2 "Edit Work Experience")
                    if (!scrollTarget) {
                        const formContainer = contentArea.querySelector('form[data-section-form]')?.closest('.bg-white.shadow.rounded-lg');
                        if (formContainer) {
                            scrollTarget = formContainer.querySelector('h2');
                        }
                    }
                    
                    // Final fallback: the form itself
                    if (!scrollTarget) {
                        scrollTarget = contentArea.querySelector('form[data-section-form]');
                    }
                    
                    
                    if (scrollTarget && mainElement) {
                        // First, scroll the element into view
                        scrollTarget.scrollIntoView({ 
                            behavior: 'smooth',
                            block: 'start',
                            inline: 'nearest'
                        });
                        
                        // Then adjust the scroll position upward by 70px after a short delay
                        // This ensures the h1 heading is positioned higher with some padding
                        setTimeout(() => {
                            const currentScroll = mainElement.scrollTop;
                            const adjustedScroll = Math.max(0, currentScroll - 70);
                            mainElement.scrollTo({ 
                                top: adjustedScroll, 
                                behavior: 'smooth' 
                            });
                            
                            setTimeout(() => {
                            }, 100);
                        }, 300); // Wait for scrollIntoView to complete
                    }
                }, 200); // Wait for responsibilities editor and DOM to be ready
            } else {
                // When NOT editing (sidebar or hash section switch) - show middle pane and window from top for better UX
                var scrollToTop = function() {
                    if (mainElement) mainElement.scrollTop = 0;
                    window.scrollTo(0, 0);
                    if (document.documentElement) document.documentElement.scrollTop = 0;
                    if (document.body) document.body.scrollTop = 0;
                };
                scrollToTop();
                requestAnimationFrame(scrollToTop);
                setTimeout(scrollToTop, 100);
                setTimeout(scrollToTop, 250);
            }
            
            // Don't call loadSectionData here - the form partial already contains the current data
            // Only reload data after saves/deletes
            isLoadingSection = false;
        })
        .catch(error => {
            console.error('Error loading section:', error);
            contentArea.innerHTML = `
                <div class="max-w-3xl mx-auto">
                    <div class="bg-red-50 border border-red-200 rounded-md p-4">
                        <p class="text-sm font-medium text-red-800">Error loading section. Please refresh the page.</p>
                    </div>
                </div>
            `;
            isLoadingSection = false;
        });
    }

    function loadGuidance(sectionId) {
        fetch(`/api/content-editor/get-guidance.php?section_id=${encodeURIComponent(sectionId)}`, {
            credentials: 'include',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.guidance) {
                updateGuidancePanel(data.guidance);
            }
        })
        .catch(error => {
            console.error('Error loading guidance:', error);
        });
    }

    function updateGuidancePanel(guidance) {
        const guidancePanel = document.querySelector('.content-editor-sidebar:last-child');
        if (!guidancePanel || !guidance) return;

        // Build the guidance HTML dynamically
        let html = `
            <div class="bg-white border-l border-gray-200 h-full overflow-y-auto">
                <div class="p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Suggestions</h2>
                    
                    <div class="mb-6">
                        <h3 class="text-base font-semibold text-gray-800 mb-2">${escapeHtml(guidance.title || '')}</h3>
                        <p class="text-sm text-gray-600 leading-relaxed">${escapeHtml(guidance.description || '')}</p>
                    </div>
        `;

        // Add tips
        if (guidance.tips && guidance.tips.length > 0) {
            html += `
                <div class="mb-6">
                    <h4 class="text-sm font-semibold text-gray-700 mb-3">Tips</h4>
                    <ul class="space-y-2">
            `;
            guidance.tips.forEach(tip => {
                html += `
                    <li class="text-sm text-gray-600 flex items-start">
                        <svg class="w-4 h-4 text-blue-500 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <span>${escapeHtml(tip).replace(/\n/g, '<br>')}</span>
                    </li>
                `;
            });
            html += `
                    </ul>
                </div>
            `;
        }

        // Add examples
        if (guidance.examples && guidance.examples.length > 0) {
            html += `
                <div class="mb-6">
                    <h4 class="text-sm font-semibold text-gray-700 mb-3">Examples</h4>
                    <div class="space-y-3">
            `;
            guidance.examples.forEach(example => {
                html += `
                    <div class="bg-gray-50 p-3 rounded-md text-sm text-gray-700">
                        ${escapeHtml(example).replace(/\n/g, '<br>')}
                    </div>
                `;
            });
            html += `
                    </div>
                </div>
            `;
        }

        // Add common mistakes
        if (guidance.common_mistakes && guidance.common_mistakes.length > 0) {
            html += `
                <div class="mb-6">
                    <h4 class="text-sm font-semibold text-gray-700 mb-3">Common Mistakes to Avoid</h4>
                    <ul class="space-y-2">
            `;
            guidance.common_mistakes.forEach(mistake => {
                html += `
                    <li class="text-sm text-gray-600 flex items-start">
                        <svg class="w-4 h-4 text-red-500 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                        <span>${escapeHtml(mistake)}</span>
                    </li>
                `;
            });
            html += `
                    </ul>
                </div>
            `;
        }

        // CV Preview (always show so user can refresh preview)
        html += `
            <div class="mt-6 pt-6 border-t border-gray-200">
                <h4 class="text-sm font-semibold text-gray-700 mb-3">CV Preview</h4>
                <p class="text-sm text-gray-600 mb-3">See your changes after saving sections.</p>
                <button type="button" id="content-editor-update-preview" class="w-full px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-md transition-colors">
                    Update Preview
                </button>
                <div id="content-editor-cv-preview" class="mt-4 overflow-auto border border-gray-200 rounded-md bg-white min-h-[200px]" style="max-height: 400px;"></div>
            </div>
        `;

        html += `
                </div>
            </div>
        `;

        // Update the guidance panel content
        guidancePanel.innerHTML = html;

        // Re-attach Update Preview button listener (element was replaced when guidance updated)
        const previewBtn = document.getElementById('content-editor-update-preview');
        if (previewBtn && typeof window.contentEditorRefreshPreview === 'function') {
            previewBtn.addEventListener('click', window.contentEditorRefreshPreview);
        }
    }

    function initializeFormHandlers(sectionId) {
        // Handle form submissions - use event delegation to avoid duplicate listeners
        const contentArea = document.getElementById('section-content');
        if (!contentArea) return;
        
        // Only attach listeners once to prevent multiple handlers firing
        if (formHandlersInitialized) {
            return;
        }
        
        formHandlersInitialized = true;
        
        
        // Handle form submissions - derive sectionId from form context
        contentArea.addEventListener('submit', function(e) {
            const form = e.target.closest('form[data-section-form]');
            if (form) {
                e.preventDefault();
                // Derive sectionId from current hash or form context
                const hash = window.location.hash.substring(1);
                const sectionFromHash = hash ? hash.split('&')[0] : currentSectionId;
                handleFormSubmit(form, sectionFromHash);
            }
        });
        
        // Handle button clicks with delegation - derive sectionId from button context
        contentArea.addEventListener('click', function(e) {
            const target = e.target.closest('[data-action]');
            if (!target) return;
            
            const action = target.dataset.action;
            const entryId = target.dataset.entryId;
            
            // Derive sectionId from button's parent list ID (e.g., "work-experience-entries-list" -> "work-experience")
            const entriesList = target.closest('[id$="-entries-list"]');
            let actualSectionId = currentSectionId; // fallback to current section
            
            if (entriesList && entriesList.id) {
                // Extract section ID from entries list ID (e.g., "work-experience-entries-list" -> "work-experience")
                const listId = entriesList.id;
                const match = listId.match(/^(.+)-entries-list$/);
                if (match) {
                    actualSectionId = match[1];
                }
            } else {
                // Fallback: derive from current hash
                const hash = window.location.hash.substring(1);
                if (hash) {
                    actualSectionId = hash.split('&')[0];
                }
            }
            
            const entryType = target.dataset.entryType || actualSectionId;
            
            
            if (action === 'delete') {
                e.preventDefault();
                if (confirm('Are you sure you want to delete this item?')) {
                    deleteEntry(entryId, entryType, actualSectionId);
                }
            } else if (action === 'edit') {
                e.preventDefault();
                editEntry(entryId, actualSectionId);
            } else if (action === 'add') {
                e.preventDefault();
                showAddForm(actualSectionId);
            } else if (action === 'cancel') {
                e.preventDefault();
                cancelEdit(actualSectionId);
            }
        });

    }

    function handleFormSubmit(form, sectionId) {
        const formData = new FormData(form);
        const variantId = getHashParam(window.location.hash.substring(1), 'variant_id');
        if (variantId && !formData.has('variant_id')) {
            formData.append('variant_id', variantId);
        }
        const submitButton = form.querySelector('button[type="submit"]');
        const originalText = submitButton ? submitButton.textContent : 'Save';

        if (submitButton) {
            submitButton.disabled = true;
            submitButton.textContent = 'Saving...';
        }

        // Use the form's own action attribute when set (e.g. custom item forms post to save-custom-item.php)
        const submitUrl = (form.getAttribute('action') && !form.getAttribute('action').endsWith('/content-editor.php'))
            ? form.getAttribute('action')
            : '/api/content-editor/save-section.php';

        fetch(submitUrl, {
            method: 'POST',
            body: formData,
            credentials: 'include',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('success', data.message || 'Saved successfully');
                if (typeof window.contentEditorRefreshPreview === 'function') {
                    window.contentEditorRefreshPreview();
                }
                // Only reload if it's a create action (to show new entry in list)
                // For updates, reload to refresh the form and responsibilities
                if (form.dataset.formType === 'create' || form.dataset.formType === 'add' || form.dataset.formType === 'add_strength') {
                    // Work experience: drop straight into edit mode for the new entry so the
                    // "add responsibilities" step is visible immediately instead of returning to the list.
                    if (sectionId === 'work-experience' && data.id) {
                        const hash = window.location.hash.substring(1);
                        const variantParam = hash.split('&').find(p => p.startsWith('variant_id='));
                        let newHash = sectionId + '&edit=' + data.id;
                        if (variantParam) newHash += '&' + variantParam;
                        window.history.replaceState(null, '', '#' + newHash);
                    }
                    setTimeout(() => {
                        loadSection(sectionId);
                    }, 500);
                } else {
                    // For updates, clear edit parameter but preserve variant_id, then reload
                    const hash = window.location.hash.substring(1);
                    const sectionFromHash = hash.split('&')[0];
                    let newHash = sectionFromHash;
                    const variantParam = hash.split('&').find(p => p.startsWith('variant_id='));
                    if (variantParam) newHash += '&' + variantParam;
                    window.history.replaceState(null, '', '#' + newHash);
                    setTimeout(() => {
                        loadSection(sectionId);
                    }, 500);
                }
            } else {
                showNotification('error', data.error || 'Failed to save');
            }
        })
        .catch(error => {
            console.error('Error saving:', error);
            showNotification('error', 'An error occurred while saving');
        })
        .finally(() => {
            if (submitButton) {
                submitButton.disabled = false;
                submitButton.textContent = originalText;
            }
        });
    }

    // Autosave the top-level work experience fields (company/position/dates/description) on blur
    // while editing. Responsibilities already save per-action; without this, users who edit these
    // fields and then go straight to adding responsibilities (which never touches this form) could
    // navigate away and silently lose those edits, since only an explicit "Update" click saved them.
    function initWorkExperienceAutosave(contentArea) {
        const form = contentArea.querySelector('form[data-section-form][data-form-type="update"]');
        if (!form) return;

        const statusEl = contentArea.querySelector('#work-experience-autosave-status');
        const fields = form.querySelectorAll(
            '[name="company_name"], [name="position"], [name="start_date"], [name="end_date"], [name="description"], [name="hide_date"]'
        );
        if (!fields.length) return;

        let inFlight = false;
        let pendingResave = false;
        let statusClearTimer = null;

        function setStatus(text, isError) {
            if (!statusEl) return;
            if (statusClearTimer) {
                clearTimeout(statusClearTimer);
                statusClearTimer = null;
            }
            statusEl.textContent = text;
            statusEl.classList.toggle('text-red-600', !!isError);
            statusEl.classList.toggle('text-gray-500', !isError);
            if (!isError && text) {
                statusClearTimer = setTimeout(() => { statusEl.textContent = ''; }, 2500);
            }
        }

        function save() {
            const companyName = form.querySelector('[name="company_name"]');
            const position = form.querySelector('[name="position"]');
            const startDate = form.querySelector('[name="start_date"]');
            if (!companyName || !position || !startDate || !companyName.value.trim() || !position.value.trim() || !startDate.value) {
                // Required field currently empty (mid-edit) - skip silently, don't spam errors.
                return;
            }

            if (inFlight) {
                pendingResave = true;
                return;
            }

            inFlight = true;
            setStatus('Saving…', false);

            const formData = new FormData(form);
            const variantId = getHashParam(window.location.hash.substring(1), 'variant_id');
            if (variantId && !formData.has('variant_id')) {
                formData.append('variant_id', variantId);
            }

            fetch('/api/content-editor/save-section.php', {
                method: 'POST',
                body: formData,
                credentials: 'include',
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    setStatus('Saved', false);
                    if (typeof window.contentEditorRefreshPreview === 'function') {
                        window.contentEditorRefreshPreview();
                    }
                } else {
                    setStatus(data.error || 'Failed to save changes', true);
                }
            })
            .catch(() => {
                setStatus('Failed to save changes', true);
            })
            .finally(() => {
                inFlight = false;
                if (pendingResave) {
                    pendingResave = false;
                    save();
                }
            });
        }

        fields.forEach(field => {
            field.addEventListener('blur', save);
            if (field.type === 'checkbox') {
                field.addEventListener('change', save);
            }
        });
    }

    // "Show Key Responsibilities bullets" toggles at the top of the work-experience section —
    // lets a user control online/PDF visibility without leaving the editor for preview-cv.php.
    function initWorkExperienceDisplayToggles(contentArea) {
        const wrap = contentArea.querySelector('#we-responsibilities-toggles');
        if (!wrap) return;

        const variantId = wrap.dataset.variantId || null;
        const onlineCheckbox = wrap.querySelector('#we-show-responsibilities-online');
        const pdfCheckbox = wrap.querySelector('#we-show-responsibilities-pdf');
        const statusEl = wrap.querySelector('#we-responsibilities-toggle-status');
        let statusClearTimer = null;

        function setStatus(text, isError) {
            if (!statusEl) return;
            if (statusClearTimer) {
                clearTimeout(statusClearTimer);
                statusClearTimer = null;
            }
            statusEl.textContent = text;
            statusEl.classList.toggle('text-red-600', !!isError);
            statusEl.classList.toggle('text-gray-500', !isError);
            if (!isError && text) {
                statusClearTimer = setTimeout(() => { statusEl.textContent = ''; }, 2000);
            }
        }

        function post(url, body) {
            setStatus('Saving…', false);
            fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                credentials: 'include',
                body: JSON.stringify(body)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success === true) {
                    setStatus('Saved', false);
                } else {
                    setStatus(data.error || 'Failed to save', true);
                }
            })
            .catch(() => setStatus('Failed to save', true));
        }

        if (onlineCheckbox) {
            onlineCheckbox.addEventListener('change', () => {
                const value = onlineCheckbox.checked;
                if (variantId) {
                    post('/api/variant-pdf-preferences.php', {
                        variant_id: variantId,
                        show_responsibilities_online: value,
                        csrf_token: window.contentEditorData.csrfToken
                    });
                } else {
                    post('/api/save-profile-sections-online.php', {
                        show_responsibilities_online: value,
                        csrf_token: window.contentEditorData.csrfToken
                    });
                }
            });
        }

        // PDF toggle only exists (and only persists) in variant context - see work-experience-form.php.
        if (pdfCheckbox && variantId) {
            pdfCheckbox.addEventListener('change', () => {
                post('/api/variant-pdf-preferences.php', {
                    variant_id: variantId,
                    show_responsibilities_in_pdf: pdfCheckbox.checked,
                    csrf_token: window.contentEditorData.csrfToken
                });
            });
        }
    }

    // Photo upload/delete for the Profile section - reuses the existing
    // api/update-profile-photo.php endpoint unchanged (same one profile.php used).
    function initProfilePhotoSection(contentArea) {
        const section = contentArea.querySelector('#profile-photo-section');
        if (!section) return;

        const statusEl = contentArea.querySelector('#profile-photo-status');
        const uploadInput = contentArea.querySelector('#profile-photo-upload');
        const captureInput = contentArea.querySelector('#profile-photo-capture');
        const deleteBtn = contentArea.querySelector('#profile-photo-delete');

        function showStatus(message, type) {
            if (!statusEl) return;
            const colors = {
                success: 'text-green-600 bg-green-50',
                error: 'text-red-600 bg-red-50',
                info: 'text-blue-600 bg-blue-50'
            };
            statusEl.innerHTML = `<p class="text-sm p-2 rounded ${colors[type] || colors.info}">${message}</p>`;
        }

        function handleUpload(event) {
            const file = event.target.files[0];
            if (!file) return;

            if (file.size > 5 * 1024 * 1024) {
                showStatus('File too large. Maximum size is 5MB.', 'error');
                return;
            }
            if (!file.type.match('image.*')) {
                showStatus('Please select an image file.', 'error');
                return;
            }

            const formData = new FormData();
            formData.append('photo', file);
            formData.append(window.contentEditorData.csrfTokenName, window.contentEditorData.csrfToken);

            showStatus('Uploading…', 'info');

            fetch('/api/update-profile-photo.php', {
                method: 'POST',
                body: formData,
                credentials: 'include'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Reload the section so the preview, delete button, and the
                    // "Show photo on online CV" checkbox all reflect the new state.
                    loadSection('profile');
                } else {
                    showStatus('Error: ' + (data.error || 'Upload failed'), 'error');
                }
            })
            .catch(error => {
                showStatus('Error uploading photo: ' + error.message, 'error');
            });
        }

        function handleDelete() {
            if (!confirm('Are you sure you want to delete your profile photo?')) return;

            const formData = new FormData();
            formData.append('action', 'delete');
            formData.append(window.contentEditorData.csrfTokenName, window.contentEditorData.csrfToken);

            fetch('/api/update-profile-photo.php', {
                method: 'POST',
                body: formData,
                credentials: 'include'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    loadSection('profile');
                } else {
                    showStatus('Error: ' + (data.error || 'Delete failed'), 'error');
                }
            })
            .catch(error => {
                showStatus('Error deleting photo: ' + error.message, 'error');
            });
        }

        if (uploadInput) uploadInput.addEventListener('change', handleUpload);
        if (captureInput) captureInput.addEventListener('change', handleUpload);
        if (deleteBtn) deleteBtn.addEventListener('click', handleDelete);
    }

    // In-page "jump to X" links (e.g. "Choose which skills appear in your PDF") must not
    // change the URL hash - the SPA's hashchange handler treats any hash as a section ID
    // and would try (and fail) to load a section called e.g. "visibility-skill-selection".
    // Intercept the click and scroll manually instead.
    function initInPageScrollLinks(contentArea) {
        contentArea.querySelectorAll('[data-scroll-to]').forEach(link => {
            link.addEventListener('click', (e) => {
                const target = contentArea.querySelector('#' + link.dataset.scrollTo);
                if (!target) return;
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
        });
    }

    // "What's Included" toggle table for the Visibility section - each checkbox saves
    // itself immediately via the existing merge-on-save preference endpoints.
    function initVisibilityToggles(contentArea) {
        const wrap = contentArea.querySelector('#visibility-toggles');
        if (!wrap) return;

        const variantId = wrap.dataset.variantId || null;
        const statusEl = contentArea.querySelector('#visibility-toggle-status');
        let statusClearTimer = null;

        function setStatus(text, isError) {
            if (!statusEl) return;
            if (statusClearTimer) {
                clearTimeout(statusClearTimer);
                statusClearTimer = null;
            }
            statusEl.textContent = text;
            statusEl.classList.toggle('text-red-600', !!isError);
            statusEl.classList.toggle('text-gray-500', !isError);
            if (!isError && text) {
                statusClearTimer = setTimeout(() => { statusEl.textContent = ''; }, 2000);
            }
        }

        function post(url, body) {
            setStatus('Saving…', false);
            fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                credentials: 'include',
                body: JSON.stringify(body)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success === true) {
                    setStatus('Saved', false);
                } else {
                    setStatus(data.error || 'Failed to save', true);
                }
            })
            .catch(() => setStatus('Failed to save', true));
        }

        wrap.querySelectorAll('[data-section-toggle]').forEach(checkbox => {
            checkbox.addEventListener('change', () => {
                const key = checkbox.dataset.key;
                const kind = checkbox.dataset.kind;
                const value = checkbox.checked;
                const csrf = window.contentEditorData.csrfToken;

                if (kind === 'online') {
                    if (variantId) {
                        post('/api/variant-pdf-preferences.php', { variant_id: variantId, sections_online: { [key]: value }, csrf_token: csrf });
                    } else {
                        post('/api/save-profile-sections-online.php', { sections_online: { [key]: value }, csrf_token: csrf });
                    }
                } else if (kind === 'pdf') {
                    if (variantId) {
                        post('/api/variant-pdf-preferences.php', { variant_id: variantId, sections: { [key]: value }, csrf_token: csrf });
                    } else {
                        post('/api/profile-pdf-preferences.php', { sections: { [key]: value }, csrf_token: csrf });
                    }
                } else if (kind === 'responsibilities-online') {
                    if (variantId) {
                        post('/api/variant-pdf-preferences.php', { variant_id: variantId, show_responsibilities_online: value, csrf_token: csrf });
                    } else {
                        post('/api/save-profile-sections-online.php', { show_responsibilities_online: value, csrf_token: csrf });
                    }
                } else if (kind === 'responsibilities-pdf') {
                    if (variantId) {
                        post('/api/variant-pdf-preferences.php', { variant_id: variantId, show_responsibilities_in_pdf: value, csrf_token: csrf });
                    } else {
                        post('/api/profile-pdf-preferences.php', { show_responsibilities_in_pdf: value, csrf_token: csrf });
                    }
                } else if (kind === 'include-photo-pdf') {
                    if (variantId) {
                        post('/api/variant-pdf-preferences.php', { variant_id: variantId, include_photo: value, csrf_token: csrf });
                    } else {
                        post('/api/profile-pdf-preferences.php', { include_photo: value, csrf_token: csrf });
                    }
                } else if (kind === 'include-qr-pdf') {
                    if (variantId) {
                        post('/api/variant-pdf-preferences.php', { variant_id: variantId, include_qr: value, csrf_token: csrf });
                    } else {
                        post('/api/profile-pdf-preferences.php', { include_qr: value, csrf_token: csrf });
                    }
                } else if (kind === 'photo-online') {
                    // Master-profile-only setting - no per-variant override exists for this.
                    post('/api/save-profile-sections-online.php', { show_photo_online: value, csrf_token: csrf });
                }
            });
        });
    }

    // "Select Skills for PDF" checkbox list - which skills export for templates with a
    // skill-count cap. Checkbox state is server-rendered; this just wires autosave.
    function initVisibilitySkillSelection(contentArea) {
        const wrap = contentArea.querySelector('#visibility-skill-selection');
        if (!wrap) return;

        const templateId = wrap.dataset.templateId;
        if (!templateId) return;

        const statusEl = contentArea.querySelector('#visibility-skill-status');
        let statusClearTimer = null;
        function setStatus(text, isError) {
            if (!statusEl) return;
            if (statusClearTimer) {
                clearTimeout(statusClearTimer);
                statusClearTimer = null;
            }
            statusEl.textContent = text;
            statusEl.classList.toggle('text-red-600', !!isError);
            statusEl.classList.toggle('text-gray-500', !isError);
            if (!isError && text) {
                statusClearTimer = setTimeout(() => { statusEl.textContent = ''; }, 2000);
            }
        }

        let saveTimeout = null;
        function save() {
            if (saveTimeout) clearTimeout(saveTimeout);
            setStatus('Saving…', false);
            saveTimeout = setTimeout(() => {
                saveTimeout = null;
                const selectedIds = Array.from(wrap.querySelectorAll('.visibility-skill-checkbox:checked'))
                    .map(cb => cb.dataset.skillId);
                const formData = new FormData();
                formData.append('template_id', templateId);
                formData.append('selected_skill_ids', JSON.stringify(selectedIds));
                formData.append(window.contentEditorData.csrfTokenName, window.contentEditorData.csrfToken);
                fetch('/api/save-template-skill-selection.php', { method: 'POST', body: formData })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            setStatus('Saved', false);
                        } else {
                            console.error('Failed to save skill selection:', data.error);
                            setStatus('Failed to save', true);
                        }
                    })
                    .catch(err => {
                        console.error('Error saving skill selection:', err);
                        setStatus('Failed to save', true);
                    });
            }, 300);
        }

        function updateCategoryToggleLabel(categoryEl) {
            const toggle = categoryEl.querySelector('.visibility-skill-category-toggle');
            if (!toggle) return;
            const checkboxes = categoryEl.querySelectorAll('.visibility-skill-checkbox');
            const allChecked = checkboxes.length > 0 && Array.from(checkboxes).every(cb => cb.checked);
            toggle.textContent = allChecked ? 'Clear all' : 'Select all';
        }

        wrap.querySelectorAll('.visibility-skill-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', () => {
                const categoryEl = checkbox.closest('[data-skill-category]');
                if (categoryEl) updateCategoryToggleLabel(categoryEl);
                save();
            });
        });

        // "Select all" / "Clear all" per category, so a user with dozens of skills doesn't
        // have to click every checkbox individually to include/exclude a whole group.
        wrap.querySelectorAll('.visibility-skill-category-toggle').forEach(toggle => {
            const categoryEl = toggle.closest('[data-skill-category]');
            if (!categoryEl) return;
            updateCategoryToggleLabel(categoryEl);
            toggle.addEventListener('click', () => {
                const checkboxes = categoryEl.querySelectorAll('.visibility-skill-checkbox');
                const allChecked = Array.from(checkboxes).every(cb => cb.checked);
                checkboxes.forEach(cb => { cb.checked = !allChecked; });
                updateCategoryToggleLabel(categoryEl);
                save();
            });
        });
    }

    // Populates the Appearance section's template <select> using the module-scope
    // helper exposed by content-editor.php's own <script type="module"> block.
    function initAppearanceTemplateSelect(contentArea) {
        const select = contentArea.querySelector('#appearance-template-select');
        if (!select) return;

        const currentTemplateId = select.dataset.currentTemplate || '';
        let allowedTemplateIds = [];
        try {
            allowedTemplateIds = JSON.parse(select.dataset.allowedTemplateIds || '[]');
        } catch (e) { /* ignore, treat as no restriction */ }

        function populate() {
            if (typeof window.populateAppearanceTemplateSelect === 'function') {
                window.populateAppearanceTemplateSelect(select, currentTemplateId, allowedTemplateIds);
            } else {
                setTimeout(populate, 50);
            }
        }
        populate();
    }

    // Header colour scheme swatches + custom pickers + live gradient preview,
    // for the Appearance section's "Template & Header Colours" form (master CV only).
    function initAppearanceColourScheme(contentArea) {
        const fromColor = contentArea.querySelector('#cv_header_from_color');
        const fromText = contentArea.querySelector('#cv_header_from_color_text');
        const toColor = contentArea.querySelector('#cv_header_to_color');
        const toText = contentArea.querySelector('#cv_header_to_color_text');
        const preview = contentArea.querySelector('#appearance-color-preview');
        if (!fromColor || !toColor) return;

        function updatePreview() {
            if (preview) {
                preview.style.background = `linear-gradient(to right, ${fromColor.value}, ${toColor.value})`;
            }
            if (fromText) fromText.value = fromColor.value;
            if (toText) toText.value = toColor.value;
        }

        fromColor.addEventListener('change', updatePreview);
        toColor.addEventListener('change', updatePreview);
        if (fromText) {
            fromText.addEventListener('change', () => {
                const value = fromText.value.trim();
                if (/^#[0-9A-Fa-f]{6}$/.test(value)) {
                    fromColor.value = value;
                    updatePreview();
                }
            });
        }
        if (toText) {
            toText.addEventListener('change', () => {
                const value = toText.value.trim();
                if (/^#[0-9A-Fa-f]{6}$/.test(value)) {
                    toColor.value = value;
                    updatePreview();
                }
            });
        }
        contentArea.querySelectorAll('[data-colour-scheme]').forEach(btn => {
            btn.addEventListener('click', () => {
                fromColor.value = btn.dataset.from;
                toColor.value = btn.dataset.to;
                updatePreview();
            });
        });
    }

    // Accent colour preset/custom picker - instant-save via the profile/variant
    // pdf-preferences endpoints (api/profile-pdf-preferences.php or variant-pdf-preferences.php).
    function initAppearanceAccentColour(contentArea) {
        const section = contentArea.querySelector('#appearance-accent-section');
        if (!section) return;

        const variantId = section.dataset.variantId || null;
        const statusEl = contentArea.querySelector('#appearance-accent-status');
        const presetRadios = section.querySelectorAll('input[name="colour-preset"]');
        const customRow = contentArea.querySelector('#appearance-custom-accent-row');
        const customColor = contentArea.querySelector('#appearance-custom-accent-color');
        const customHex = contentArea.querySelector('#appearance-custom-accent-hex');
        let statusClearTimer = null;

        function setStatus(text, isError) {
            if (!statusEl) return;
            if (statusClearTimer) {
                clearTimeout(statusClearTimer);
                statusClearTimer = null;
            }
            statusEl.textContent = text;
            statusEl.classList.toggle('text-red-600', !!isError);
            statusEl.classList.toggle('text-gray-500', !isError);
            if (!isError && text) {
                statusClearTimer = setTimeout(() => { statusEl.textContent = ''; }, 2000);
            }
        }

        function save(body) {
            setStatus('Saving…', false);
            const csrf = window.contentEditorData.csrfToken;
            const url = variantId ? '/api/variant-pdf-preferences.php' : '/api/profile-pdf-preferences.php';
            const payload = variantId ? { variant_id: variantId, ...body, csrf_token: csrf } : { ...body, csrf_token: csrf };
            fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                credentials: 'include',
                body: JSON.stringify(payload)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success === true) {
                    setStatus('Saved', false);
                } else {
                    setStatus(data.error || 'Failed to save', true);
                }
            })
            .catch(() => setStatus('Failed to save', true));
        }

        presetRadios.forEach(radio => {
            radio.addEventListener('change', () => {
                if (customRow) customRow.classList.toggle('hidden', radio.value !== 'custom');
                save({ colour_preset: radio.value });
            });
        });
        if (customColor) {
            customColor.addEventListener('input', () => {
                if (customHex) customHex.value = customColor.value;
                save({ custom_accent_hex: customColor.value });
            });
        }
        if (customHex) {
            customHex.addEventListener('change', () => {
                const hex = customHex.value.trim();
                if (/^#[0-9A-Fa-f]{6}$/.test(hex)) {
                    if (customColor) customColor.value = hex;
                    save({ custom_accent_hex: hex });
                }
            });
        }
    }

    function loadSectionData(sectionId) {
        // This function is kept for potential future use but not called automatically
        // to prevent infinite reload loops. The form partial already contains current data.
        return Promise.resolve();
    }

    function updateEntryList(sectionId, entries) {
        // Don't reload here - this causes infinite loops
        // The form partial already contains the entry list
        // Only reload after explicit user actions (save/delete)
    }

    function deleteEntry(entryId, entryType, sectionId) {
        const formData = new FormData();
        formData.append('action', 'delete');
        formData.append('entry_id', entryId);
        formData.append('section_id', entryType);
        formData.append(data.csrfTokenName, data.csrfToken);
        const variantId = getHashParam(window.location.hash.substring(1), 'variant_id');
        if (variantId && (entryType === 'work-experience' || entryType === 'professional-summary')) {
            formData.append('variant_id', variantId);
        }

        fetch('/api/content-editor/save-section.php', {
            method: 'POST',
            body: formData,
            credentials: 'include',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('success', 'Deleted successfully');
                if (typeof window.contentEditorRefreshPreview === 'function') {
                    window.contentEditorRefreshPreview();
                }
                // Reload section to refresh entry list
                setTimeout(() => {
                    loadSection(sectionId);
                }, 500);
            } else {
                showNotification('error', data.error || 'Failed to delete');
            }
        })
        .catch(error => {
            console.error('Error deleting:', error);
            showNotification('error', 'An error occurred while deleting');
        });
    }

    function editEntry(entryId, sectionId) {
        
        const currentHash = window.location.hash.substring(1);
        const variantId = getHashParam(currentHash, 'variant_id');
        let newHash = sectionId + '&edit=' + entryId;
        if (variantId) {
            newHash += '&variant_id=' + encodeURIComponent(variantId);
        }
        currentSectionId = sectionId;
        
        
        // Use replaceState to prevent scrolling
        window.history.replaceState(null, '', '#' + newHash);
        loadSection(sectionId);
    }

    function showAddForm(sectionId) {
        // Show add form (usually already visible, but ensure it's shown)
        const addForm = document.getElementById(`${sectionId}-add-form`);
        if (addForm) {
            addForm.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            addForm.querySelector('input, textarea, select')?.focus();
        }
    }

    function cancelEdit(sectionId) {
        // Remove edit parameter from hash and reload
        window.history.replaceState(null, '', `#${sectionId}`);
        loadSection(sectionId);
    }

    function showNotification(type, message) {
        const notification = document.createElement('div');
        notification.className = `fixed top-20 left-1/2 transform -translate-x-1/2 z-50 max-w-md w-full`;
        notification.innerHTML = `
            <div class="bg-${type === 'success' ? 'green' : 'red'}-50 border border-${type === 'success' ? 'green' : 'red'}-200 rounded-md p-4 shadow-lg">
                <p class="text-sm font-medium text-${type === 'success' ? 'green' : 'red'}-800">${escapeHtml(message)}</p>
            </div>
        `;
        document.body.appendChild(notification);

        setTimeout(() => {
            notification.style.transition = 'opacity 0.3s';
            notification.style.opacity = '0';
            setTimeout(() => notification.remove(), 300);
        }, 5000);
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    /**
     * Normalize suggested_replacement format based on section type.
     * Handles cases where AI returns nested objects instead of expected format.
     */
    function normalizeSuggestedReplacement(replacement, sectionId) {
        if (!replacement) return null;
        
        // If it's already a string, return as-is
        if (typeof replacement === 'string') {
            return replacement;
        }
        
        // Handle nested object formats (AI sometimes returns these)
        if (typeof replacement === 'object' && replacement !== null) {
            // For professional-summary, extract text from nested structure
            if (sectionId === 'professional-summary') {
                // If it's an object with professional_summary key, extract and join text
                if (replacement.professional_summary) {
                    const summary = replacement.professional_summary;
                    if (typeof summary === 'string') {
                        return summary;
                    }
                    // If it's an object with text fields, join them
                    if (typeof summary === 'object') {
                        const parts = [];
                        // Common field names AI might use
                        ['text', 'summary', 'content', 'past_15_years', 'specialist_skills', 'recent_focus', 'achievements', 'target_roles'].forEach(field => {
                            if (summary[field] && typeof summary[field] === 'string') {
                                parts.push(summary[field]);
                            }
                        });
                        if (parts.length > 0) {
                            return parts.join(' ');
                        }
                    }
                }
                // If it's an array, join the strings
                if (Array.isArray(replacement)) {
                    return replacement.map(item => typeof item === 'string' ? item : JSON.stringify(item)).join(' ');
                }
                // Last resort: stringify the object
                return JSON.stringify(replacement, null, 2);
            }
            
            // For work-experience, single-entry suggested replacement (title, company, description, responsibility_categories)
            if (sectionId === 'work-experience') {
                if (replacement && typeof replacement === 'object' && !Array.isArray(replacement)) {
                    const title = replacement.title || replacement.position || '';
                    const company = replacement.company || replacement.company_name || '';
                    const desc = replacement.description || '';
                    const cats = replacement.responsibility_categories || [];
                    let out = title && company ? `${title} at ${company}` : (title || company || '');
                    if (desc) {
                        out += (out ? '\n\n' : '') + desc;
                    }
                    if (Array.isArray(cats) && cats.length > 0) {
                        out += (out ? '\n\n' : '') + 'Key responsibilities:';
                        cats.forEach(function (cat) {
                            const name = cat.name || cat.category || 'Responsibilities';
                            const items = cat.items || [];
                            out += '\n\n' + name + ':';
                            items.forEach(function (item) {
                                const c = (item && typeof item === 'object' && item.content) ? item.content : (typeof item === 'string' ? item : '');
                                if (c) out += '\n  • ' + c;
                            });
                        });
                    }
                    return out.trim() || JSON.stringify(replacement, null, 2);
                }
                return JSON.stringify(replacement, null, 2);
            }

            // For qualification-equivalence, single-entry suggested replacement (object with level, description)
            if (sectionId === 'qualification-equivalence') {
                if (replacement && typeof replacement === 'object' && !Array.isArray(replacement)) {
                    const level = replacement.level || '';
                    const desc = replacement.description || '';
                    let out = level ? `Level: ${level}` : '';
                    if (desc) {
                        out += (out ? '\n\n' : '') + desc;
                    }
                    return out.trim() || JSON.stringify(replacement, null, 2);
                }
                return JSON.stringify(replacement, null, 2);
            }

            // For interests, single-entry suggested replacement (object with name, description)
            if (sectionId === 'interests') {
                if (replacement && typeof replacement === 'object' && !Array.isArray(replacement)) {
                    const name = replacement.name || '';
                    const desc = replacement.description || '';
                    let out = name ? `Name: ${name}` : '';
                    if (desc) {
                        out += (out ? '\n\n' : '') + desc;
                    }
                    return out.trim() || JSON.stringify(replacement, null, 2);
                }
                return JSON.stringify(replacement, null, 2);
            }

            // For projects, single-entry suggested replacement (object with title, description, url, dates)
            if (sectionId === 'projects') {
                if (replacement && typeof replacement === 'object' && !Array.isArray(replacement)) {
                    const title = replacement.title || '';
                    const desc = replacement.description || '';
                    const url = replacement.url || '';
                    const start = replacement.start_date || '';
                    const end = replacement.end_date || '';
                    let out = title ? `Title: ${title}` : '';
                    if (start || end) {
                        out += (out ? '\n\n' : '') + 'Dates: ' + (start || '?') + ' - ' + (end || 'Present');
                    }
                    if (desc) {
                        out += (out ? '\n\n' : '') + desc;
                    }
                    if (url) {
                        out += (out ? '\n\n' : '') + 'URL: ' + url;
                    }
                    return out.trim() || JSON.stringify(replacement, null, 2);
                }
                return JSON.stringify(replacement, null, 2);
            }

            // For skills, format grouped by category
            if (sectionId === 'skills') {
                if (Array.isArray(replacement)) {
                    // Group skills by category
                    const grouped = {};
                    replacement.forEach(skill => {
                        // Handle both 'skill'/'proficiency' and 'name'/'level' field names
                        const name = skill.name || skill.skill || 'Unknown';
                        const level = skill.level || skill.proficiency || '';
                        const category = skill.category || 'Other';
                        
                        if (!grouped[category]) {
                            grouped[category] = [];
                        }
                        grouped[category].push({ name, level });
                    });
                    
                    // Format as readable text grouped by category
                    let formatted = '';
                    Object.keys(grouped).sort().forEach(category => {
                        formatted += `\n${category}:\n`;
                        grouped[category].forEach(skill => {
                            formatted += `  • ${skill.name}`;
                            if (skill.level) {
                                formatted += ` (${skill.level})`;
                            }
                            formatted += '\n';
                        });
                    });
                    return formatted.trim();
                }
                // If not an array, stringify
                return JSON.stringify(replacement, null, 2);
            }
            
            // For other sections, return as JSON string for now
            return JSON.stringify(replacement, null, 2);
        }
        
        return String(replacement);
    }

    /**
     * Parse JSON from AI output: strip markdown fences, extract one {...} object,
     * repair common JSON issues, then parse. Throws on failure after logging details.
     */
    function parseAssessmentJsonFromAI(raw) {
        let text = String(raw || '').trim();
        
        // Log raw input for debugging
        console.log('parseAssessmentJsonFromAI: Raw input length:', text.length);
        
        // Strip model special tokens (e.g. <|start_header_id|>assistant<|end_header_id|>) that break JSON
        text = text.replace(/<\|[^]*?\|>/g, '');
        
        // Strip markdown code fences (```json ... ``` or ``` ... ```)
        const codeBlock = text.match(/```(?:json)?\s*([\s\S]*?)```/);
        if (codeBlock) {
            text = codeBlock[1].trim();
            console.log('parseAssessmentJsonFromAI: Stripped markdown fences, length:', text.length);
        }
        
        // Extract first balanced {...} block (avoids grabbing extra text after })
        let jsonStr = text;
        const start = text.indexOf('{');
        if (start >= 0) {
            let depth = 0, end = -1;
            for (let i = start; i < text.length; i++) {
                const ch = text[i];
                if (ch === '{') depth++;
                else if (ch === '}') { depth--; if (depth === 0) { end = i; break; } }
            }
            if (end >= 0) {
                jsonStr = text.slice(start, end + 1);
                console.log('parseAssessmentJsonFromAI: Extracted JSON object, length:', jsonStr.length);
            }
        }
        
        // Strip any remaining model tokens inside the JSON (can appear in string values)
        jsonStr = jsonStr.replace(/<\|[^]*?\|>/g, '');
        
        // Repair common LLM JSON issues
        // 1. Trailing commas before ] or }
        jsonStr = jsonStr.replace(/,(\s*[}\]])/g, '$1');
        // 2. Fix missing colons: "key" "value" -> "key": "value"
        jsonStr = jsonStr.replace(/"\s*"\s*"/g, '": "');
        // 3. Fix missing colons: "key" [ -> "key": [
        jsonStr = jsonStr.replace(/"\s*(\[)/g, '": $1');
        // 4. Fix missing colons: "key" { -> "key": {
        jsonStr = jsonStr.replace(/"\s*(\{)/g, '": $1');
        // 5. Replace unescaped newlines in string values (control chars) with space to avoid parse errors
        jsonStr = jsonStr.replace(/"([^"\\]*(?:\\.[^"\\]*)*)"/g, function (m) {
            return m.replace(/\r?\n/g, ' ');
        });
        
        try {
            const parsed = JSON.parse(jsonStr);
            console.log('parseAssessmentJsonFromAI: Successfully parsed JSON');
            return parsed;
        } catch (e) {
            // Extract error position if available
            const errorMatch = e.message.match(/position (\d+)/);
            const errorPos = errorMatch ? parseInt(errorMatch[1]) : -1;
            
            // Log detailed error info
            console.error('parseAssessmentJsonFromAI: Parse error at position', errorPos);
            console.error('parseAssessmentJsonFromAI: Error message:', e.message);
            
            if (errorPos >= 0 && errorPos < jsonStr.length) {
                // Show context around error (100 chars before and after)
                const contextStart = Math.max(0, errorPos - 100);
                const contextEnd = Math.min(jsonStr.length, errorPos + 100);
                const context = jsonStr.slice(contextStart, contextEnd);
                const markerPos = errorPos - contextStart;
                const markedContext = context.slice(0, markerPos) + '>>>ERROR HERE<<<' + context.slice(markerPos);
                console.error('parseAssessmentJsonFromAI: Context around error:\n', markedContext);
            } else {
                // Show first 500 and last 500 chars if we can't find error position
                console.error('parseAssessmentJsonFromAI: First 500 chars:', jsonStr.slice(0, 500));
                console.error('parseAssessmentJsonFromAI: Last 500 chars:', jsonStr.slice(-500));
            }
            
            // Try one more repair: remove any lines that look like they're outside JSON structure
            // This handles cases where the model adds explanatory text after the JSON
            let repaired = jsonStr;
            try {
                // Try to find and extract just the JSON part more aggressively
                const jsonMatch = repaired.match(/\{[\s\S]*\}/);
                if (jsonMatch && jsonMatch[0] !== repaired) {
                    repaired = jsonMatch[0];
                    console.log('parseAssessmentJsonFromAI: Trying repaired version (extracted from match)');
                    return JSON.parse(repaired);
                }
            } catch (e2) {
                console.error('parseAssessmentJsonFromAI: Repair attempt also failed');
            }
            
            throw new Error('AI returned invalid JSON. Please try again.');
        }
    }
    
    // Assess section function - available globally
    window.assessSection = async function(sectionId) {
        try {
            // Show loading in guidance panel
            const guidancePanel = document.querySelector('.content-editor-sidebar:last-child');
            if (guidancePanel) {
                guidancePanel.innerHTML = `
                    <div class="p-6">
                        <div class="text-center py-12">
                            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-purple-600 mx-auto"></div>
                            <p class="mt-4 text-gray-600">Assessing section...</p>
                        </div>
                    </div>
                `;
            }
            
            // Call API to assess section
            const formData = new FormData();
            formData.append('section_id', sectionId);
            formData.append(data.csrfTokenName, data.csrfToken);
            // When assessing work-experience while editing a single entry, pass its id so AI focuses on that entry only
            if (sectionId === 'work-experience' && window.location.hash && window.location.hash.includes('&edit=')) {
                const hash = window.location.hash.replace(/^#/, '');
                const editPart = hash.split('&').find(p => p.startsWith('edit='));
                if (editPart) {
                    const workExperienceId = editPart.slice(5);
                    if (workExperienceId) {
                        formData.append('work_experience_id', workExperienceId);
                    }
                }
            }
            // When assessing qualification-equivalence while editing a single entry, pass its id so AI focuses on that entry only
            if (sectionId === 'qualification-equivalence' && window.location.hash && window.location.hash.includes('&edit=')) {
                const hash = window.location.hash.replace(/^#/, '');
                const editPart = hash.split('&').find(p => p.startsWith('edit='));
                if (editPart) {
                    const qualificationEquivalenceId = editPart.slice(5);
                    if (qualificationEquivalenceId) {
                        formData.append('qualification_equivalence_id', qualificationEquivalenceId);
                    }
                }
            }
            // When on a single interest edit page (#interests&edit=<id>), pass interest_id so AI assesses that one entry only.
            // When on the list/add view (#interests, no edit), do not pass it — assess all interests overall.
            if (sectionId === 'interests' && window.location.hash && window.location.hash.includes('&edit=')) {
                const hash = window.location.hash.replace(/^#/, '');
                const editPart = hash.split('&').find(p => p.startsWith('edit='));
                if (editPart) {
                    const interestId = editPart.slice(5);
                    if (interestId) {
                        formData.append('interest_id', interestId);
                    }
                }
            }
            // When assessing projects while editing a single entry, pass its id so AI focuses on that entry only.
            if (sectionId === 'projects' && window.location.hash && window.location.hash.includes('&edit=')) {
                const hash = window.location.hash.replace(/^#/, '');
                const editPart = hash.split('&').find(p => p.startsWith('edit='));
                if (editPart) {
                    const projectId = editPart.slice(5);
                    if (projectId) {
                        formData.append('project_id', projectId);
                    }
                }
            }
            
            
            const response = await fetch('/api/content-editor/assess-section.php', {
                method: 'POST',
                body: formData,
                credentials: 'include',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            
            // Check if response is actually JSON
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                const text = await response.text();
                throw new Error('Server returned non-JSON response: ' + text.substring(0, 200));
            }
            
            const result = await response.json();
            
            // Handle browser execution required case
            if (result.browser_execution) {
                // Browser AI execution - execute client-side
                await executeBrowserAISectionAssessment(result, sectionId, guidancePanel);
                return;
            }
            
            if (result.success && result.assessment) {
                // Display recommendations in guidance panel
                if (guidancePanel) {
                    const assessment = result.assessment;
                    let html = `
                        <div class="p-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">AI Recommendations: ${escapeHtml(sectionId.replace('-', ' '))}</h3>
                            <div class="space-y-4">
                    `;
                    
                    if (assessment.strengths && assessment.strengths.length > 0) {
                        html += `
                            <div>
                                <h4 class="text-sm font-semibold text-green-700 mb-2">Strengths</h4>
                                <ul class="list-disc list-inside space-y-1 text-sm text-gray-700">
                        `;
                        assessment.strengths.forEach(strength => {
                            html += `<li>${escapeHtml(strength)}</li>`;
                        });
                        html += `</ul></div>`;
                    }
                    
                    if (assessment.weaknesses && assessment.weaknesses.length > 0) {
                        html += `
                            <div>
                                <h4 class="text-sm font-semibold text-red-700 mb-2">Areas for Improvement</h4>
                                <ul class="list-disc list-inside space-y-1 text-sm text-gray-700">
                        `;
                        assessment.weaknesses.forEach(weakness => {
                            html += `<li>${escapeHtml(weakness)}</li>`;
                        });
                        html += `</ul></div>`;
                    }
                    
                    if (assessment.recommendations && assessment.recommendations.length > 0) {
                        html += `
                            <div>
                                <h4 class="text-sm font-semibold text-blue-700 mb-2">Recommendations</h4>
                                <ul class="list-disc list-inside space-y-1 text-sm text-gray-700">
                        `;
                        assessment.recommendations.forEach(rec => {
                            html += `<li>${escapeHtml(rec)}</li>`;
                        });
                        html += `</ul></div>`;
                    }
                    
                    if (assessment.suggested_replacement) {
                        const normalizedReplacement = normalizeSuggestedReplacement(assessment.suggested_replacement, sectionId);
                        html += `
                            <div class="mt-4 pt-4 border-t">
                                <h4 class="text-sm font-semibold text-purple-700 mb-2">Suggested Replacement</h4>
                                <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                                    <pre class="whitespace-pre-wrap text-sm text-gray-800 font-mono">${escapeHtml(normalizedReplacement)}</pre>
                                </div>
                                <p class="mt-2 text-xs text-gray-500">You can copy this improved version and use it to update your section.</p>
                            </div>
                        `;
                    }
                    
                    html += `
                            </div>
                            <div class="mt-6 pt-4 border-t">
                                <a href="/cv-quality.php" class="text-sm text-purple-600 hover:text-purple-700">View Full CV Assessment →</a>
                            </div>
                        </div>
                    `;
                    
                    guidancePanel.innerHTML = html;
                }
                
                showNotification('success', 'Section assessment completed');
            } else {
                throw new Error(result.error || 'Assessment failed');
            }
        } catch (error) {
            console.error('Error assessing section:', error);
            showNotification('error', 'Failed to assess section: ' + error.message);
            
            // Restore guidance panel
            loadGuidance(sectionId);
        }
    };
    
    /**
     * Execute browser AI for section-specific assessment
     */
    async function executeBrowserAISectionAssessment(result, sectionId, guidancePanel) {
        try {
            // Check browser support
            if (typeof BrowserAIService === 'undefined') {
                throw new Error('Browser AI service not loaded');
            }
            
            const support = BrowserAIService.checkBrowserSupport();
            if (!support.required) {
                throw new Error('Browser does not support WebGPU or WebGL. Browser AI requires a modern browser with GPU support.');
            }
            
            // Show loading in guidance panel
            if (guidancePanel) {
                guidancePanel.innerHTML = `
                    <div class="p-6">
                        <div class="text-center py-12">
                            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-purple-600 mx-auto"></div>
                            <p class="mt-4 text-gray-600">Loading AI model. This may take a few minutes on first use...</p>
                        </div>
                    </div>
                `;
            }
            
            // Initialize browser AI
            const modelType = result.model_type === 'webllm' ? 'webllm' : 'tensorflow';
            await BrowserAIService.initBrowserAI(modelType, result.model, (progress) => {
                if (guidancePanel && progress.message) {
                    guidancePanel.querySelector('p').textContent = progress.message;
                }
            });
            
            // Use prompt from backend (it's already section-specific)
            const prompt = result.prompt || '';
            if (!prompt) {
                throw new Error('No prompt provided for browser AI execution');
            }
            
            // Update loading overlay
            if (guidancePanel) {
                guidancePanel.querySelector('p').textContent = 'Assessing section... This may take 30-60 seconds.';
            }
            
            // Generate assessment using browser AI
            const assessmentText = await BrowserAIService.generateText(prompt, {
                temperature: 0.3,
                maxTokens: 2000
            });
            
            // Parse assessment JSON with defensive extraction and repair
            let assessment = parseAssessmentJsonFromAI(assessmentText);
            if (typeof BrowserAIService !== 'undefined' && BrowserAIService.humanizeObjectStrings) {
                assessment = BrowserAIService.humanizeObjectStrings(assessment);
            }
            
            // Cleanup
            await BrowserAIService.cleanup();
            
            // Display assessment in guidance panel
            if (guidancePanel) {
                let html = `
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">AI Recommendations: ${escapeHtml(sectionId.replace('-', ' '))}</h3>
                        <div class="space-y-4">
                `;
                
                if (assessment.strengths && assessment.strengths.length > 0) {
                    html += `
                        <div>
                            <h4 class="text-sm font-semibold text-green-700 mb-2">Strengths</h4>
                            <ul class="list-disc list-inside space-y-1 text-sm text-gray-700">
                    `;
                    assessment.strengths.forEach(strength => {
                        html += `<li>${escapeHtml(strength)}</li>`;
                    });
                    html += `</ul></div>`;
                }
                
                if (assessment.weaknesses && assessment.weaknesses.length > 0) {
                    html += `
                        <div>
                            <h4 class="text-sm font-semibold text-red-700 mb-2">Areas for Improvement</h4>
                            <ul class="list-disc list-inside space-y-1 text-sm text-gray-700">
                    `;
                    assessment.weaknesses.forEach(weakness => {
                        html += `<li>${escapeHtml(weakness)}</li>`;
                    });
                    html += `</ul></div>`;
                }
                
                if (assessment.recommendations && assessment.recommendations.length > 0) {
                    html += `
                        <div>
                            <h4 class="text-sm font-semibold text-blue-700 mb-2">Recommendations</h4>
                            <ul class="list-disc list-inside space-y-1 text-sm text-gray-700">
                    `;
                    assessment.recommendations.forEach(rec => {
                        html += `<li>${escapeHtml(rec)}</li>`;
                    });
                    html += `</ul></div>`;
                }
                
                if (assessment.suggested_replacement) {
                    const normalizedReplacement = normalizeSuggestedReplacement(assessment.suggested_replacement, sectionId);
                    html += `
                        <div class="mt-4 pt-4 border-t">
                            <h4 class="text-sm font-semibold text-purple-700 mb-2">Suggested Replacement</h4>
                            <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                                <pre class="whitespace-pre-wrap text-sm text-gray-800 font-mono">${escapeHtml(normalizedReplacement)}</pre>
                            </div>
                            <p class="mt-2 text-xs text-gray-500">You can copy this improved version and use it to update your section.</p>
                        </div>
                    `;
                }
                
                html += `
                        </div>
                        <div class="mt-6 pt-4 border-t">
                            <a href="/cv-quality.php" class="text-sm text-purple-600 hover:text-purple-700">View Full CV Assessment →</a>
                        </div>
                    </div>
                `;
                
                guidancePanel.innerHTML = html;
            }
            
            showNotification('success', 'Section assessment completed');
        } catch (error) {
            console.error('Browser AI execution error:', error);
            const helpInfo = (typeof BrowserAIService !== 'undefined' && BrowserAIService.getWebLLMErrorHelp)
                ? BrowserAIService.getWebLLMErrorHelp(error)
                : { message: 'Browser AI Error: ' + error.message };
            showNotification('error', helpInfo.message);

            // Restore guidance panel
            loadGuidance(sectionId);
        }
    }

    function initializeResponsibilitiesEditor() {
        // Check if responsibilities editor container exists
        const editorContainer = document.querySelector('[id^="responsibilities-editor-"]');
        if (!editorContainer) return;
        
        const workExperienceId = editorContainer.dataset.workExperienceId;
        if (!workExperienceId) {
            console.error('Work experience ID not found in editor container');
            return;
        }
        
        // Check if script is already loaded and function is available
        if (typeof window.initResponsibilitiesEditor !== 'undefined') {
            // Function is available, load data immediately
            loadResponsibilitiesData(workExperienceId, editorContainer);
            return;
        }
        
        // Check if script tag already exists
        let script = document.querySelector('script[src*="work-experience-responsibilities.js"]');
        if (script) {
            // Script tag exists, wait for it to load
            const checkFunction = setInterval(() => {
                if (typeof window.initResponsibilitiesEditor !== 'undefined') {
                    clearInterval(checkFunction);
                    loadResponsibilitiesData(workExperienceId, editorContainer);
                }
            }, 50);
            
            // Timeout after 5 seconds
            setTimeout(() => {
                clearInterval(checkFunction);
                if (typeof window.initResponsibilitiesEditor === 'undefined') {
                    console.error('Responsibilities editor script failed to load');
                    editorContainer.innerHTML = '<p class="text-red-500 p-4">Error loading responsibilities editor. Please refresh the page.</p>';
                }
            }, 5000);
        } else {
            // Load the script dynamically
            script = document.createElement('script');
            script.src = '/js/work-experience-responsibilities.js?v=' + Date.now();
            script.onload = function() {
                setTimeout(() => {
                    if (typeof window.initResponsibilitiesEditor !== 'undefined') {
                        loadResponsibilitiesData(workExperienceId, editorContainer);
                    } else {
                        console.error('Script loaded but initResponsibilitiesEditor function not found');
                        editorContainer.innerHTML = '<p class="text-red-500 p-4">Error initializing responsibilities editor. Please refresh the page.</p>';
                    }
                }, 100);
            };
            script.onerror = function() {
                console.error('Failed to load responsibilities editor script');
                editorContainer.innerHTML = '<p class="text-red-500 p-4">Error loading responsibilities editor. Please refresh the page.</p>';
            };
            document.head.appendChild(script);
        }
    }
    
    function loadResponsibilitiesData(workExperienceId, editorContainer) {
        // Show loading state
        editorContainer.innerHTML = `
            <div class="text-center py-8">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div>
                <p class="mt-2 text-sm text-gray-500">Loading responsibilities...</p>
            </div>
        `;
        
        const hash = (window.location.hash || '').replace(/^#/, '');
        const variantPart = hash.split('&').find(p => p.startsWith('variant_id='));
        const variantId = variantPart ? variantPart.slice(11) : null;
        let url = `/api/responsibilities.php?work_experience_id=${encodeURIComponent(workExperienceId)}&action=get`;
        if (variantId) {
            url += '&variant_id=' + encodeURIComponent(variantId);
        }
        
        fetch(url, {
            credentials: 'include'
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (typeof window.initResponsibilitiesEditor === 'undefined') {
                console.error('initResponsibilitiesEditor function not available after script load');
                editorContainer.innerHTML = '<p class="text-red-500 p-4">Error initializing responsibilities editor. Please refresh the page.</p>';
                return;
            }
            
            if (data.success) {
                window.initResponsibilitiesEditor(workExperienceId, data.categories || [], editorContainer, variantId);
            } else {
                window.initResponsibilitiesEditor(workExperienceId, [], editorContainer, variantId);
            }
        })
        .catch(error => {
            console.error('Error loading responsibilities:', error);
            editorContainer.innerHTML = '<p class="text-red-500 p-4">Error loading responsibilities. Please refresh the page.</p>';
        });
    }

    function initializeProjectImageHandlers() {
        const projectImagePreview = document.getElementById('project-image-preview');
        const projectImageInput = document.getElementById('project_image');
        const projectImageStatus = document.getElementById('project-image-status');
        const projectImageClear = document.getElementById('project-image-clear');
        const projectImageUrlInput = document.getElementById('image_url');
        const projectImagePathInput = document.getElementById('image_path');
        const projectImageResponsiveInput = document.getElementById('image_responsive');
        
        if (!projectImageInput || !projectImagePreview) return;
        
        function showProjectImageStatus(message, type) {
            if (!projectImageStatus) return;
            const classes = {
                success: 'border-green-200 text-green-700 bg-green-50',
                error: 'border-red-200 text-red-700 bg-red-50',
                info: 'border-blue-200 text-blue-700 bg-blue-50'
            };
            projectImageStatus.className = 'mt-2 rounded-md border px-3 py-2 text-sm ' + (classes[type] || classes.info);
            projectImageStatus.textContent = message;
            projectImageStatus.classList.remove('hidden');
        }
        
        function setProjectImagePreview(src) {
            if (src) {
                projectImagePreview.innerHTML = '<img src="' + src + '" alt="Project Image" class="w-32 h-32 object-cover rounded-md border border-gray-200">';
                projectImagePreview.className = 'w-32 h-32 rounded-md border border-gray-200';
                if (projectImageClear) projectImageClear.classList.remove('hidden');
            } else {
                projectImagePreview.innerHTML = 'No image';
                projectImagePreview.className = 'w-32 h-32 rounded-md border border-dashed border-gray-300 bg-gray-50 flex items-center justify-center text-gray-400 text-sm';
                if (projectImageClear) projectImageClear.classList.add('hidden');
            }
        }
        
        function resetProjectImagePreview() {
            setProjectImagePreview('');
            if (projectImageUrlInput) projectImageUrlInput.value = '';
            if (projectImagePathInput) projectImagePathInput.value = '';
            if (projectImageResponsiveInput) projectImageResponsiveInput.value = '';
        }
        
        let isUploading = false;
        
        function handleProjectImageUpload(event) {
            const file = event.target.files[0];
            if (!file) return;
            
            if (isUploading) {
                showProjectImageStatus('Upload already in progress. Please wait...', 'info');
                return;
            }
            
            if (file.size > 5 * 1024 * 1024) {
                showProjectImageStatus('File too large. Maximum size is 5MB.', 'error');
                projectImageInput.value = '';
                return;
            }
            
            if (!file.type.match('image.*')) {
                showProjectImageStatus('Please choose an image file.', 'error');
                projectImageInput.value = '';
                return;
            }
            
            const reader = new FileReader();
            reader.onload = function(e) {
                setProjectImagePreview(e.target.result);
            };
            reader.readAsDataURL(file);
            
            const formData = new FormData();
            formData.append('project_image', file);
            formData.append(data.csrfTokenName, data.csrfToken);
            
            isUploading = true;
            showProjectImageStatus('Uploading image...', 'info');
            
            fetch('/api/upload-project-image.php', {
                method: 'POST',
                body: formData,
                credentials: 'include'
            })
            .then(response => response.json())
            .then(result => {
                isUploading = false;
                if (result.success) {
                    showProjectImageStatus('Image uploaded successfully', 'success');
                    if (projectImageUrlInput) projectImageUrlInput.value = result.url || '';
                    if (projectImagePathInput) projectImagePathInput.value = result.path || '';
                    if (projectImageResponsiveInput && result.responsive) {
                        projectImageResponsiveInput.value = JSON.stringify(result.responsive);
                    }
                    if (result.url) {
                        setProjectImagePreview(result.url);
                    }
                    setTimeout(() => {
                        if (projectImageStatus) projectImageStatus.classList.add('hidden');
                    }, 3000);
                } else {
                    showProjectImageStatus(result.error || 'Upload failed', 'error');
                    resetProjectImagePreview();
                }
            })
            .catch(error => {
                isUploading = false;
                console.error('Upload error:', error);
                showProjectImageStatus('Upload failed. Please try again.', 'error');
                resetProjectImagePreview();
            });
        }
        
        function clearProjectImage() {
            if (confirm('Remove this image?')) {
                resetProjectImagePreview();
                if (projectImageInput) projectImageInput.value = '';
                if (projectImageStatus) projectImageStatus.classList.add('hidden');
            }
        }
        
        if (projectImageInput) {
            projectImageInput.addEventListener('change', handleProjectImageUpload);
        }
        if (projectImageClear) {
            projectImageClear.addEventListener('click', clearProjectImage);
        }

        // Reuse an image already uploaded on another of this user's projects - no upload,
        // just point this project's hidden fields at the same stored file/responsive set.
        document.querySelectorAll('.project-image-reuse-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                if (projectImageUrlInput) projectImageUrlInput.value = btn.dataset.imageUrl || '';
                if (projectImagePathInput) projectImagePathInput.value = btn.dataset.imagePath || '';
                if (projectImageResponsiveInput) projectImageResponsiveInput.value = btn.dataset.imageResponsive || '';
                if (projectImageInput) projectImageInput.value = '';
                setProjectImagePreview(btn.dataset.thumb || '');
                if (projectImageStatus) projectImageStatus.classList.add('hidden');
            });
        });
    }

    function initializeWorkExperienceReorder(container) {
        const list = container.querySelector('#work-experiences-list');
        const toggleBtn = container.querySelector('#toggle-reorder-btn');
        const resetBtn = container.querySelector('#reset-reorder-btn');
        const reorderInfo = container.querySelector('#reorder-info');
        if (!list || !toggleBtn) return;

        let isReordering = false;
        let draggedElement = null;

        function getCsrfToken() {
            const input = container.querySelector('input[name="csrf_token"]');
            return input ? input.value : '';
        }

        function saveOrder() {
            const items = list.querySelectorAll('.work-experience-item');
            const orderedIds = Array.from(items).map(function(item) { return item.getAttribute('data-id'); });

            const formData = new FormData();
            formData.append('csrf_token', getCsrfToken());
            formData.append('action', 'reorder');
            formData.append('ordered_ids', JSON.stringify(orderedIds));
            const variantId = getHashParam(window.location.hash.substring(1), 'variant_id');
            if (variantId) formData.append('variant_id', variantId);

            fetch('/api/reorder-work-experience.php', {
                method: 'POST',
                body: formData,
                credentials: 'include'
            })
            .then(function(response) { return response.json(); })
            .then(function(data) {
                if (data.success) {
                    let msg = container.querySelector('.work-experience-reorder-success');
                    if (!msg) {
                        msg = document.createElement('div');
                        msg.className = 'work-experience-reorder-success mb-4 rounded-md bg-green-50 p-4 text-green-700 text-sm';
                        const parent = container.querySelector('#work-experience-entries-list');
                        if (parent && parent.firstChild) parent.insertBefore(msg, parent.firstChild);
                        else if (parent) parent.appendChild(msg);
                    }
                    msg.textContent = 'Order updated successfully.';
                    msg.classList.remove('hidden');
                    setTimeout(function() { msg.classList.add('hidden'); }, 3000);
                } else {
                    alert('Failed to save order. Please try again.');
                }
            })
            .catch(function() {
                alert('Failed to save order. Please try again.');
            });
        }

        function resetToDateOrder() {
            if (!confirm('Reset order to date-based sorting (newest first)?')) return;
            const formData = new FormData();
            formData.append('csrf_token', getCsrfToken());
            formData.append('action', 'reset');
            const variantId = getHashParam(window.location.hash.substring(1), 'variant_id');
            if (variantId) formData.append('variant_id', variantId);
            fetch('/api/reorder-work-experience.php', {
                method: 'POST',
                body: formData,
                credentials: 'include'
            })
            .then(function(response) { return response.json(); })
            .then(function(data) {
                if (data.success) {
                    loadSection('work-experience');
                } else {
                    alert('Failed to reset order. Please try again.');
                }
            })
            .catch(function() {
                alert('Failed to reset order. Please try again.');
            });
        }

        function handleDragStart(e) {
            draggedElement = this;
            this.classList.add('opacity-50');
            e.dataTransfer.effectAllowed = 'move';
            e.dataTransfer.setData('text/plain', this.getAttribute('data-id'));
        }

        function handleDragOver(e) {
            if (e.preventDefault) e.preventDefault();
            e.dataTransfer.dropEffect = 'move';
            if (this !== draggedElement && this.classList.contains('work-experience-item')) {
                this.classList.add('border-blue-500', 'bg-blue-50');
            }
            return false;
        }

        function handleDragLeave(e) {
            if (this !== draggedElement && this.classList.contains('work-experience-item')) {
                this.classList.remove('border-blue-500', 'bg-blue-50');
            }
        }

        function handleDrop(e) {
            if (e.stopPropagation) e.stopPropagation();
            if (draggedElement !== this && this.classList.contains('work-experience-item')) {
                const items = Array.from(list.querySelectorAll('.work-experience-item'));
                const draggedIndex = items.indexOf(draggedElement);
                const targetIndex = items.indexOf(this);
                if (draggedIndex < targetIndex) {
                    list.insertBefore(draggedElement, this.nextSibling);
                } else {
                    list.insertBefore(draggedElement, this);
                }
                saveOrder();
            }
            this.classList.remove('border-blue-500', 'bg-blue-50');
            return false;
        }

        function handleDragEnd(e) {
            this.classList.remove('opacity-50');
            draggedElement = null;
            list.querySelectorAll('.work-experience-item').forEach(function(el) {
                el.classList.remove('border-blue-500', 'bg-blue-50');
            });
        }

        function toggleReorderMode() {
            isReordering = !isReordering;
            const items = list.querySelectorAll('.work-experience-item');
            const dragHandles = list.querySelectorAll('.drag-handle');

            if (isReordering) {
                toggleBtn.textContent = 'Done reordering';
                toggleBtn.classList.remove('bg-gray-50', 'text-gray-700', 'border-gray-300', 'hover:bg-gray-100', 'hover:border-gray-400', 'hover:text-gray-900', 'hover:shadow-sm');
                toggleBtn.classList.add('bg-blue-600', 'text-white', 'border-blue-600', 'hover:bg-blue-700');
                if (reorderInfo) reorderInfo.classList.remove('hidden');
                items.forEach(function(item) {
                    item.setAttribute('draggable', 'true');
                    item.classList.add('cursor-move', 'border-2', 'border-blue-300');
                    var h = item.querySelector('.drag-handle');
                    if (h) h.classList.remove('hidden');
                    item.addEventListener('dragstart', handleDragStart);
                    item.addEventListener('dragover', handleDragOver);
                    item.addEventListener('dragleave', handleDragLeave);
                    item.addEventListener('drop', handleDrop);
                    item.addEventListener('dragend', handleDragEnd);
                });
            } else {
                toggleBtn.textContent = 'Reorder';
                toggleBtn.classList.remove('bg-blue-600', 'text-white', 'border-blue-600', 'hover:bg-blue-700');
                toggleBtn.classList.add('bg-gray-50', 'text-gray-700', 'border-gray-300', 'hover:bg-gray-100', 'hover:border-gray-400', 'hover:text-gray-900', 'hover:shadow-sm');
                if (reorderInfo) reorderInfo.classList.add('hidden');
                items.forEach(function(item) {
                    item.setAttribute('draggable', 'false');
                    item.classList.remove('cursor-move', 'border-2', 'border-blue-300', 'border-blue-500', 'bg-blue-50');
                    var h = item.querySelector('.drag-handle');
                    if (h) h.classList.add('hidden');
                    item.removeEventListener('dragstart', handleDragStart);
                    item.removeEventListener('dragover', handleDragOver);
                    item.removeEventListener('dragleave', handleDragLeave);
                    item.removeEventListener('drop', handleDrop);
                    item.removeEventListener('dragend', handleDragEnd);
                });
            }
        }

        toggleBtn.addEventListener('click', toggleReorderMode);
        if (resetBtn) resetBtn.addEventListener('click', resetToDateOrder);
    }

    function initializeCertificationsReorder(container) {
        const list = container.querySelector('#certifications-list');
        const toggleBtn = container.querySelector('#toggle-cert-reorder-btn');
        const resetBtn = container.querySelector('#reset-cert-reorder-btn');
        const reorderInfo = container.querySelector('#cert-reorder-info');
        if (!list || !toggleBtn) return;

        let isReordering = false;
        let draggedElement = null;

        function getCsrfToken() {
            const input = container.querySelector('input[name="csrf_token"]');
            return input ? input.value : '';
        }

        function saveOrder() {
            const items = list.querySelectorAll('.certification-item');
            const orderedIds = Array.from(items).map(function(item) { return item.getAttribute('data-id'); });

            const formData = new FormData();
            formData.append('csrf_token', getCsrfToken());
            formData.append('action', 'reorder');
            formData.append('ordered_ids', JSON.stringify(orderedIds));

            fetch('/api/reorder-certifications.php', {
                method: 'POST',
                body: formData,
                credentials: 'include'
            })
            .then(function(response) { return response.json(); })
            .then(function(data) {
                if (data.success) {
                    let msg = container.querySelector('.cert-reorder-success');
                    if (!msg) {
                        msg = document.createElement('div');
                        msg.className = 'cert-reorder-success mb-4 rounded-md bg-green-50 p-4 text-green-700 text-sm';
                        const parent = container.querySelector('#certifications-entries-list');
                        if (parent && parent.firstChild) parent.insertBefore(msg, parent.firstChild);
                        else if (parent) parent.appendChild(msg);
                    }
                    msg.textContent = 'Order updated successfully.';
                    msg.classList.remove('hidden');
                    setTimeout(function() { msg.classList.add('hidden'); }, 3000);
                } else {
                    alert('Failed to save order. Please try again.');
                }
            })
            .catch(function() {
                alert('Failed to save order. Please try again.');
            });
        }

        function resetToDateOrder() {
            if (!confirm('Reset order to date-based sorting (newest first)?')) return;
            const formData = new FormData();
            formData.append('csrf_token', getCsrfToken());
            formData.append('action', 'reset');
            fetch('/api/reorder-certifications.php', {
                method: 'POST',
                body: formData,
                credentials: 'include'
            })
            .then(function(response) { return response.json(); })
            .then(function(data) {
                if (data.success) {
                    loadSection('certifications');
                } else {
                    alert('Failed to reset order. Please try again.');
                }
            })
            .catch(function() {
                alert('Failed to reset order. Please try again.');
            });
        }

        function handleDragStart(e) {
            draggedElement = this;
            this.classList.add('opacity-50');
            e.dataTransfer.effectAllowed = 'move';
            e.dataTransfer.setData('text/plain', this.getAttribute('data-id'));
        }

        function handleDragOver(e) {
            if (e.preventDefault) e.preventDefault();
            e.dataTransfer.dropEffect = 'move';
            if (this !== draggedElement && this.classList.contains('certification-item')) {
                this.classList.add('border-blue-500', 'bg-blue-50');
            }
            return false;
        }

        function handleDragLeave(e) {
            if (this !== draggedElement && this.classList.contains('certification-item')) {
                this.classList.remove('border-blue-500', 'bg-blue-50');
            }
        }

        function handleDrop(e) {
            if (e.stopPropagation) e.stopPropagation();
            if (draggedElement !== this && this.classList.contains('certification-item')) {
                const items = Array.from(list.querySelectorAll('.certification-item'));
                const draggedIndex = items.indexOf(draggedElement);
                const targetIndex = items.indexOf(this);
                if (draggedIndex < targetIndex) {
                    list.insertBefore(draggedElement, this.nextSibling);
                } else {
                    list.insertBefore(draggedElement, this);
                }
                saveOrder();
            }
            this.classList.remove('border-blue-500', 'bg-blue-50');
            return false;
        }

        function handleDragEnd(e) {
            this.classList.remove('opacity-50');
            draggedElement = null;
            list.querySelectorAll('.certification-item').forEach(function(el) {
                el.classList.remove('border-blue-500', 'bg-blue-50');
            });
        }

        function toggleReorderMode() {
            isReordering = !isReordering;
            const items = list.querySelectorAll('.certification-item');

            if (isReordering) {
                toggleBtn.textContent = 'Done reordering';
                toggleBtn.classList.remove('bg-blue-600', 'hover:bg-blue-700');
                toggleBtn.classList.add('bg-green-600', 'hover:bg-green-700');
                if (reorderInfo) reorderInfo.classList.remove('hidden');
                items.forEach(function(item) {
                    item.setAttribute('draggable', 'true');
                    item.classList.add('cursor-move', 'border-2', 'border-blue-300');
                    var h = item.querySelector('.drag-handle');
                    if (h) h.classList.remove('hidden');
                    item.addEventListener('dragstart', handleDragStart);
                    item.addEventListener('dragover', handleDragOver);
                    item.addEventListener('dragleave', handleDragLeave);
                    item.addEventListener('drop', handleDrop);
                    item.addEventListener('dragend', handleDragEnd);
                });
            } else {
                toggleBtn.textContent = 'Reorder certifications';
                toggleBtn.classList.remove('bg-green-600', 'hover:bg-green-700');
                toggleBtn.classList.add('bg-blue-600', 'hover:bg-blue-700');
                if (reorderInfo) reorderInfo.classList.add('hidden');
                items.forEach(function(item) {
                    item.setAttribute('draggable', 'false');
                    item.classList.remove('cursor-move', 'border-2', 'border-blue-300', 'border-blue-500', 'bg-blue-50');
                    var h = item.querySelector('.drag-handle');
                    if (h) h.classList.add('hidden');
                    item.removeEventListener('dragstart', handleDragStart);
                    item.removeEventListener('dragover', handleDragOver);
                    item.removeEventListener('dragleave', handleDragLeave);
                    item.removeEventListener('drop', handleDrop);
                    item.removeEventListener('dragend', handleDragEnd);
                });
            }
        }

        toggleBtn.addEventListener('click', toggleReorderMode);
        if (resetBtn) resetBtn.addEventListener('click', resetToDateOrder);
    }

    // =============================================
    // Section Sidebar Drag-and-Drop Reorder
    // Two independent lists: main body and sidebar column.
    // Sections can only be reordered within their own group.
    // =============================================

    function initializeSectionSidebarReorder() {
        const toggleBtn   = document.getElementById('toggle-section-reorder-btn');
        const reorderInfo = document.getElementById('section-reorder-info');
        const saveBtn     = document.getElementById('save-section-order-btn');
        const mainList    = document.getElementById('main-sections-list');
        const sidebarList = document.getElementById('sidebar-sections-list');

        if (!toggleBtn || (!mainList && !sidebarList)) return;

        let isReordering = false;

        // Make one drag list independently sortable
        function makeDraggable(list) {
            if (!list) return;
            var dragSrc = null;

            function onDragStart(e) {
                dragSrc = this;
                e.dataTransfer.effectAllowed = 'move';
                e.dataTransfer.setData('text/plain', this.dataset.sectionId);
                this.classList.add('opacity-50');
            }
            function onDragOver(e) {
                e.preventDefault();
                e.dataTransfer.dropEffect = 'move';
                this.classList.add('ring-2', 'ring-blue-400', 'ring-inset');
            }
            function onDragLeave() {
                this.classList.remove('ring-2', 'ring-blue-400', 'ring-inset');
            }
            function onDrop(e) {
                e.stopPropagation();
                e.preventDefault();
                this.classList.remove('ring-2', 'ring-blue-400', 'ring-inset');
                if (dragSrc && dragSrc !== this) {
                    list.insertBefore(dragSrc, this);
                }
            }
            function onDragEnd() {
                this.classList.remove('opacity-50');
                list.querySelectorAll('.section-nav-wrapper').forEach(function(w) {
                    w.classList.remove('ring-2', 'ring-blue-400', 'ring-inset');
                });
            }

            list._enableReorder = function() {
                list.querySelectorAll('.section-nav-wrapper').forEach(function(wrapper) {
                    wrapper.setAttribute('draggable', 'true');
                    wrapper.classList.add('cursor-move', 'rounded-md');
                    var handle = wrapper.querySelector('.drag-handle-sidebar');
                    if (handle) handle.classList.remove('hidden');
                    var link = wrapper.querySelector('a');
                    if (link) link.style.pointerEvents = 'none';
                    wrapper.addEventListener('dragstart', onDragStart);
                    wrapper.addEventListener('dragover',  onDragOver);
                    wrapper.addEventListener('dragleave', onDragLeave);
                    wrapper.addEventListener('drop',      onDrop);
                    wrapper.addEventListener('dragend',   onDragEnd);
                });
            };

            list._disableReorder = function() {
                list.querySelectorAll('.section-nav-wrapper').forEach(function(wrapper) {
                    wrapper.setAttribute('draggable', 'false');
                    wrapper.classList.remove('cursor-move', 'rounded-md', 'ring-2', 'ring-blue-400', 'ring-inset', 'opacity-50');
                    var handle = wrapper.querySelector('.drag-handle-sidebar');
                    if (handle) handle.classList.add('hidden');
                    var link = wrapper.querySelector('a');
                    if (link) link.style.pointerEvents = '';
                    wrapper.removeEventListener('dragstart', onDragStart);
                    wrapper.removeEventListener('dragover',  onDragOver);
                    wrapper.removeEventListener('dragleave', onDragLeave);
                    wrapper.removeEventListener('drop',      onDrop);
                    wrapper.removeEventListener('dragend',   onDragEnd);
                });
            };
        }

        makeDraggable(mainList);
        makeDraggable(sidebarList);

        function getOrderedIds(list) {
            if (!list) return [];
            return Array.from(list.querySelectorAll('.section-nav-wrapper'))
                        .map(function(w) { return w.dataset.sectionId; });
        }

        function toggleReorderMode() {
            isReordering = !isReordering;
            if (isReordering) {
                toggleBtn.textContent = 'Done';
                toggleBtn.classList.add('text-green-700', 'bg-green-50', 'border-green-200', 'hover:bg-green-100', 'focus:ring-green-500');
                toggleBtn.classList.remove('text-blue-700', 'bg-blue-50', 'border-blue-200', 'hover:bg-blue-100', 'focus:ring-blue-500');
                if (reorderInfo) reorderInfo.classList.remove('hidden');
                if (mainList)    mainList._enableReorder();
                if (sidebarList) sidebarList._enableReorder();
            } else {
                toggleBtn.textContent = 'Reorder';
                toggleBtn.classList.remove('text-green-700', 'bg-green-50', 'border-green-200', 'hover:bg-green-100', 'focus:ring-green-500');
                toggleBtn.classList.add('text-blue-700', 'bg-blue-50', 'border-blue-200', 'hover:bg-blue-100', 'focus:ring-blue-500');
                if (reorderInfo) reorderInfo.classList.add('hidden');
                if (mainList)    mainList._disableReorder();
                if (sidebarList) sidebarList._disableReorder();
            }
        }

        function saveSectionOrder() {
            // Collect both lists in order: main first, then sidebar column
            var order = getOrderedIds(mainList).concat(getOrderedIds(sidebarList));
            var csrfToken    = (window.contentEditorData && window.contentEditorData.csrfToken) || '';
            var csrfTokenName = (window.contentEditorData && window.contentEditorData.csrfTokenName) || '_csrf_token';
            var body = new URLSearchParams();
            body.append('section_order', JSON.stringify(order));
            body.append(csrfTokenName, csrfToken);

            saveBtn.disabled = true;
            saveBtn.textContent = 'Saving…';

            fetch('/api/save-section-order.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: body.toString()
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.success) {
                    saveBtn.textContent = 'Saved!';
                    setTimeout(function() {
                        saveBtn.textContent = 'Save order';
                        saveBtn.disabled = false;
                        toggleReorderMode();
                    }, 800);
                } else {
                    saveBtn.textContent = 'Error – try again';
                    saveBtn.disabled = false;
                }
            })
            .catch(function() {
                saveBtn.textContent = 'Error – try again';
                saveBtn.disabled = false;
            });
        }

        toggleBtn.addEventListener('click', toggleReorderMode);
        if (saveBtn) saveBtn.addEventListener('click', saveSectionOrder);
    }

    function initializeCustomSections() {
        var addBtn    = document.getElementById('add-custom-section-btn');
        var addForm   = document.getElementById('add-custom-section-form');
        var titleInput = document.getElementById('new-custom-section-title');
        var createBtn  = document.getElementById('create-custom-section-btn');
        var navList    = document.getElementById('custom-sections-nav');
        var noMsg      = document.getElementById('no-custom-sections-msg');

        if (!addBtn) return;

        addBtn.addEventListener('click', function() {
            addForm.classList.toggle('hidden');
            if (!addForm.classList.contains('hidden') && titleInput) titleInput.focus();
        });

        if (titleInput) {
            titleInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') { e.preventDefault(); if (createBtn) createBtn.click(); }
            });
        }

        if (createBtn) {
            createBtn.addEventListener('click', function() {
                var title = (titleInput ? titleInput.value : '').trim();
                if (!title) { if (titleInput) titleInput.focus(); return; }

                var csrfToken     = (window.contentEditorData && window.contentEditorData.csrfToken) || '';
                var csrfTokenName = (window.contentEditorData && window.contentEditorData.csrfTokenName) || '_csrf_token';
                var body = new URLSearchParams();
                body.append('action', 'create');
                body.append('title', title);
                body.append(csrfTokenName, csrfToken);

                createBtn.disabled = true;

                fetch('/api/content-editor/save-custom-section.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: body.toString()
                })
                .then(function(r) { return r.json(); })
                .then(function(resp) {
                    if (resp.success && resp.id) {
                        var sectionId = 'custom-' + resp.id;
                        var escapedTitle = resp.title
                            .replace(/&/g, '&amp;')
                            .replace(/</g, '&lt;')
                            .replace(/>/g, '&gt;');

                        // Add as a draggable wrapper into the Main list (so it's reorderable)
                        var mainList = document.getElementById('main-sections-list');
                        var wrapper = document.createElement('div');
                        wrapper.className = 'section-nav-wrapper relative';
                        wrapper.dataset.sectionId = sectionId;
                        wrapper.setAttribute('draggable', 'false');
                        wrapper.innerHTML =
                            '<div class="drag-handle-sidebar hidden absolute left-0 top-0 bottom-0 flex items-center pl-1 cursor-move text-gray-400" style="z-index:1">' +
                            '<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M7 2a2 2 0 1 0 0 4 2 2 0 0 0 0-4zM7 8a2 2 0 1 0 0 4 2 2 0 0 0 0-4zM7 14a2 2 0 1 0 0 4 2 2 0 0 0 0-4zM13 2a2 2 0 1 0 0 4 2 2 0 0 0 0-4zM13 8a2 2 0 1 0 0 4 2 2 0 0 0 0-4zM13 14a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"/></svg>' +
                            '</div>' +
                            '<a href="#' + sectionId + '" class="section-nav-item flex items-center justify-between gap-2 px-3 py-2.5 border border-gray-300 border-l-4 border-l-gray-300 bg-white shadow-sm text-sm font-medium text-gray-700 transition-all hover:border-gray-400 hover:bg-gray-50 hover:shadow" data-section-id="' + sectionId + '">' +
                            '<div class="flex items-center min-w-0">' +
                            '<svg class="w-5 h-5 mr-2 flex-shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>' +
                            '<span class="truncate">' + escapedTitle + '</span>' +
                            '</div></a>';
                        if (mainList) mainList.appendChild(wrapper);

                        // Wire up the inner link click
                        var innerLink = wrapper.querySelector('a');
                        if (innerLink) {
                            innerLink.addEventListener('click', function(e) {
                                e.preventDefault();
                                navigateToSection(sectionId);
                            });
                        }

                        if (titleInput) titleInput.value = '';
                        if (addForm) addForm.classList.add('hidden');
                        createBtn.disabled = false;
                        navigateToSection(sectionId);
                    } else {
                        createBtn.disabled = false;
                    }
                })
                .catch(function() { createBtn.disabled = false; });
            });
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        initializeCustomSections();
    });

    // Initialise sidebar reorder once the DOM is ready (runs after initializeEditor)
    document.addEventListener('DOMContentLoaded', function() {
        initializeSectionSidebarReorder();
    });

    // Export for global access if needed
    window.contentEditor = {
        navigateToSection,
        loadSection,
        loadSectionData
    };
})();
