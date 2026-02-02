/**
 * Resizable Panes for Content Editor
 * Allows users to drag resize handles to adjust column widths,
 * and to fully collapse either sidebar (double-click handle; when collapsed, click handle to expand).
 */

(function() {
    'use strict';

    var leftCollapsed = false;
    var rightCollapsed = false;
    var DEFAULT_LEFT = 280;
    var DEFAULT_RIGHT = 320;

    function getSavedWidths() {
        try {
            var saved = localStorage.getItem('content-editor-column-widths');
            if (saved) {
                return JSON.parse(saved);
            }
        } catch (e) {
            console.error('Error loading saved widths:', e);
        }
        return null;
    }

    function saveWidths(leftWidth, rightWidth) {
        try {
            localStorage.setItem('content-editor-column-widths', JSON.stringify({
                left: leftWidth,
                right: rightWidth,
                timestamp: Date.now()
            }));
        } catch (e) {
            console.error('Error saving widths:', e);
        }
    }

    function getSavedCollapsed() {
        try {
            var saved = localStorage.getItem('content-editor-sidebars-collapsed');
            if (saved) {
                var obj = JSON.parse(saved);
                return { left: !!obj.left, right: !!obj.right };
            }
        } catch (e) {
            console.error('Error loading saved collapse state:', e);
        }
        return { left: false, right: false };
    }

    function saveCollapsed(left, right) {
        try {
            localStorage.setItem('content-editor-sidebars-collapsed', JSON.stringify({
                left: left,
                right: right
            }));
        } catch (e) {
            console.error('Error saving collapse state:', e);
        }
    }

    var chevronRightSvg = '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>';
    var chevronLeftSvg = '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>';

    function applyCollapsedState(leftSidebar, rightSidebar, resizeHandle1, resizeHandle2, leftToggleBtn, rightToggleBtn) {
        if (leftCollapsed) {
            leftSidebar.classList.add('collapsed');
            resizeHandle1.classList.add('collapsed-tab');
            resizeHandle1.title = 'Drag to resize';
            if (leftToggleBtn) {
                leftToggleBtn.innerHTML = chevronRightSvg;
                leftToggleBtn.title = 'Show section nav';
                leftToggleBtn.setAttribute('aria-label', 'Show section nav');
            }
        } else {
            leftSidebar.classList.remove('collapsed');
            resizeHandle1.classList.remove('collapsed-tab');
            resizeHandle1.title = 'Drag to resize';
            if (leftToggleBtn) {
                leftToggleBtn.innerHTML = chevronLeftSvg;
                leftToggleBtn.title = 'Hide section nav';
                leftToggleBtn.setAttribute('aria-label', 'Hide section nav');
            }
        }
        if (rightCollapsed) {
            rightSidebar.classList.add('collapsed');
            resizeHandle2.classList.add('collapsed-tab');
            resizeHandle2.title = 'Drag to resize';
            if (rightToggleBtn) {
                rightToggleBtn.innerHTML = chevronLeftSvg;
                rightToggleBtn.title = 'Show suggestions panel';
                rightToggleBtn.setAttribute('aria-label', 'Show suggestions panel');
            }
        } else {
            rightSidebar.classList.remove('collapsed');
            resizeHandle2.classList.remove('collapsed-tab');
            resizeHandle2.title = 'Drag to resize';
            if (rightToggleBtn) {
                rightToggleBtn.innerHTML = chevronRightSvg;
                rightToggleBtn.title = 'Hide suggestions panel';
                rightToggleBtn.setAttribute('aria-label', 'Hide suggestions panel');
            }
        }
    }

    function initializeResizablePanes() {
        var leftSidebar = document.getElementById('left-sidebar');
        var mainContent = document.getElementById('main-content');
        var rightSidebar = document.getElementById('right-sidebar');
        var resizeHandle1 = document.getElementById('resize-handle-1');
        var resizeHandle2 = document.getElementById('resize-handle-2');

        if (!leftSidebar || !mainContent || !rightSidebar || !resizeHandle1 || !resizeHandle2) {
            return;
        }

        var savedCollapsed = getSavedCollapsed();
        leftCollapsed = savedCollapsed.left;
        rightCollapsed = savedCollapsed.right;

        var savedWidths = getSavedWidths();
        if (!leftCollapsed) {
            leftSidebar.style.width = (savedWidths && savedWidths.left) ? (savedWidths.left + 'px') : (DEFAULT_LEFT + 'px');
        }
        if (!rightCollapsed) {
            rightSidebar.style.width = (savedWidths && savedWidths.right) ? (savedWidths.right + 'px') : (DEFAULT_RIGHT + 'px');
        }

        /* Toggle buttons on the resize handles – one per side, always visible, click to open/close */
        var leftToggleWrap = document.createElement('div');
        leftToggleWrap.className = 'handle-toggle-wrap';
        var leftToggleBtn = document.createElement('button');
        leftToggleBtn.type = 'button';
        leftToggleBtn.className = 'sidebar-toggle-btn';
        leftToggleBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            if (leftCollapsed) { expandLeft(); } else { collapseLeft(); }
            this.blur();
        });
        leftToggleWrap.appendChild(leftToggleBtn);
        resizeHandle1.appendChild(leftToggleWrap);

        var rightToggleWrap = document.createElement('div');
        rightToggleWrap.className = 'handle-toggle-wrap';
        var rightToggleBtn = document.createElement('button');
        rightToggleBtn.type = 'button';
        rightToggleBtn.className = 'sidebar-toggle-btn';
        rightToggleBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            if (rightCollapsed) { expandRight(); } else { collapseRight(); }
            this.blur();
        });
        rightToggleWrap.appendChild(rightToggleBtn);
        resizeHandle2.appendChild(rightToggleWrap);

        applyCollapsedState(leftSidebar, rightSidebar, resizeHandle1, resizeHandle2, leftToggleBtn, rightToggleBtn);

        var isResizing1 = false;
        var isResizing2 = false;
        var startX1 = 0;
        var startX2 = 0;
        var startLeftWidth = 0;
        var startRightWidth = 0;
        var startMainWidth = 0;

        function collapseLeft() {
            if (leftCollapsed) return;
            saveWidths(leftSidebar.offsetWidth, rightSidebar.offsetWidth);
            leftCollapsed = true;
            saveCollapsed(leftCollapsed, rightCollapsed);
            applyCollapsedState(leftSidebar, rightSidebar, resizeHandle1, resizeHandle2, leftToggleBtn, rightToggleBtn);
        }
        function expandLeft() {
            if (!leftCollapsed) return;
            leftCollapsed = false;
            saveCollapsed(leftCollapsed, rightCollapsed);
            var latest = getSavedWidths();
            var w = (latest && latest.left) ? latest.left : DEFAULT_LEFT;
            leftSidebar.style.width = w + 'px';
            applyCollapsedState(leftSidebar, rightSidebar, resizeHandle1, resizeHandle2, leftToggleBtn, rightToggleBtn);
        }
        function collapseRight() {
            if (rightCollapsed) return;
            saveWidths(leftSidebar.offsetWidth, rightSidebar.offsetWidth);
            rightCollapsed = true;
            saveCollapsed(leftCollapsed, rightCollapsed);
            applyCollapsedState(leftSidebar, rightSidebar, resizeHandle1, resizeHandle2, leftToggleBtn, rightToggleBtn);
        }
        function expandRight() {
            if (!rightCollapsed) return;
            rightCollapsed = false;
            saveCollapsed(leftCollapsed, rightCollapsed);
            var latest = getSavedWidths();
            var w = (latest && latest.right) ? latest.right : DEFAULT_RIGHT;
            rightSidebar.style.width = w + 'px';
            applyCollapsedState(leftSidebar, rightSidebar, resizeHandle1, resizeHandle2, leftToggleBtn, rightToggleBtn);
        }

        resizeHandle1.addEventListener('dblclick', function(e) {
            e.preventDefault();
            if (leftCollapsed) {
                expandLeft();
            } else {
                collapseLeft();
            }
        });
        resizeHandle2.addEventListener('dblclick', function(e) {
            e.preventDefault();
            if (rightCollapsed) {
                expandRight();
            } else {
                collapseRight();
            }
        });

        resizeHandle1.addEventListener('click', function(e) {
            if (leftCollapsed) {
                e.preventDefault();
                expandLeft();
            }
        });
        resizeHandle2.addEventListener('click', function(e) {
            if (rightCollapsed) {
                e.preventDefault();
                expandRight();
            }
        });

        resizeHandle1.addEventListener('mousedown', function(e) {
            if (leftCollapsed) return;
            if (e.target.closest && e.target.closest('.sidebar-toggle-btn')) return;
            isResizing1 = true;
            startX1 = e.clientX;
            startLeftWidth = leftSidebar.offsetWidth;
            startMainWidth = mainContent.offsetWidth;
            resizeHandle1.classList.add('active');
            document.body.style.cursor = 'col-resize';
            document.body.style.userSelect = 'none';
            e.preventDefault();
        });

        resizeHandle2.addEventListener('mousedown', function(e) {
            if (rightCollapsed) return;
            if (e.target.closest && e.target.closest('.sidebar-toggle-btn')) return;
            isResizing2 = true;
            startX2 = e.clientX;
            startRightWidth = rightSidebar.offsetWidth;
            startMainWidth = mainContent.offsetWidth;
            resizeHandle2.classList.add('active');
            document.body.style.cursor = 'col-resize';
            document.body.style.userSelect = 'none';
            e.preventDefault();
        });

        document.addEventListener('mousemove', function(e) {
            if (!isResizing1 && !isResizing2) return;

            if (isResizing1) {
                var diff = e.clientX - startX1;
                var newLeftWidth = Math.max(200, Math.min(500, startLeftWidth + diff));
                var newMainWidth = startMainWidth - diff;
                if (newMainWidth >= 300) {
                    leftSidebar.style.width = newLeftWidth + 'px';
                }
            }

            if (isResizing2) {
                var diff = startX2 - e.clientX;
                var newRightWidth = Math.max(250, Math.min(600, startRightWidth + diff));
                var newMainWidth = startMainWidth - diff;
                if (newMainWidth >= 300) {
                    rightSidebar.style.width = newRightWidth + 'px';
                }
            }
        });

        document.addEventListener('mouseup', function() {
            if (isResizing1 || isResizing2) {
                isResizing1 = false;
                isResizing2 = false;
                resizeHandle1.classList.remove('active');
                resizeHandle2.classList.remove('active');
                document.body.style.cursor = '';
                document.body.style.userSelect = '';
                if (!leftCollapsed && !rightCollapsed) {
                    saveWidths(leftSidebar.offsetWidth, rightSidebar.offsetWidth);
                } else {
                    saveWidths(leftCollapsed ? 0 : leftSidebar.offsetWidth, rightCollapsed ? 0 : rightSidebar.offsetWidth);
                }
            }
        });

        /** Layout presets: all | left-middle | right-middle | middle */
        function setLayout(layout) {
            var leftOpen = (layout === 'all' || layout === 'left-middle');
            var rightOpen = (layout === 'all' || layout === 'right-middle');
            if (!leftOpen && !leftCollapsed) collapseLeft();
            if (leftOpen && leftCollapsed) expandLeft();
            if (!rightOpen && !rightCollapsed) collapseRight();
            if (rightOpen && rightCollapsed) expandRight();
            if (typeof window.dispatchEvent === 'function') {
                try { window.dispatchEvent(new CustomEvent('contenteditorlayoutchange', { detail: { layout: getLayout() } })); } catch (e) {}
            }
        }
        function getLayout() {
            if (!leftCollapsed && !rightCollapsed) return 'all';
            if (!leftCollapsed && rightCollapsed) return 'left-middle';
            if (leftCollapsed && !rightCollapsed) return 'right-middle';
            return 'middle';
        }
        window.contentEditorLayout = { setLayout: setLayout, getLayout: getLayout };
        try {
            window.dispatchEvent(new CustomEvent('contenteditorlayoutready'));
        } catch (e) {}
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeResizablePanes);
    } else {
        initializeResizablePanes();
    }
})();
