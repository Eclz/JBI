/**
 * JBI University - Form Guard & Submission Idempotency
 *
 * Prevents accidental duplicate form submissions by:
 * 1. Generating and attaching a unique per-render idempotency key to every state-changing form.
 * 2. Synchronously guarding against multiple submit events (e.g. fast double-clicks).
 * 3. Providing visual feedback (spinner / busy text) on submit buttons.
 * 4. Gracefully restoring buttons on bfcache page restore (Back button) or submission timeout.
 */
(function () {
    'use strict';

    const CONFIG = {
        inputName: '_idempotency_key',
        headerName: 'X-Idempotency-Key',
        defaultBusyText: 'Processing...',
        timeoutMs: 15000 // Re-enable if page does not unload within 15 seconds
    };

    /**
     * Generates a cryptographically random or high-entropy key.
     */
    function generateIdempotencyKey() {
        if (typeof crypto !== 'undefined' && typeof crypto.randomUUID === 'function') {
            return crypto.randomUUID();
        }
        return 'idemp_' + Date.now().toString(36) + '_' + Math.random().toString(36).substring(2, 10);
    }

    /**
     * Ensures a form has a hidden idempotency key input.
     */
    function attachKeyToForm(form) {
        if (!form || form.tagName !== 'FORM') return;

        const method = (form.getAttribute('method') || 'GET').toUpperCase();
        // Skip GET forms (search, filter, etc.)
        if (method === 'GET') return;

        let input = form.querySelector('input[name="' + CONFIG.inputName + '"]');
        if (!input) {
            input = document.createElement('input');
            input.type = 'hidden';
            input.name = CONFIG.inputName;
            input.value = generateIdempotencyKey();
            form.appendChild(input);
        } else if (!input.value) {
            input.value = generateIdempotencyKey();
        }
    }

    /**
     * Scans and prepares all forms in a given container (default document).
     */
    function prepareAllForms(root) {
        const container = root || document;
        const forms = container.querySelectorAll('form');
        forms.forEach(attachKeyToForm);
    }

    /**
     * Resets a form from the submitting state back to active.
     */
    function resetFormSubmission(form) {
        if (!form) return;
        form.dataset.submitting = 'false';

        if (form._submitTimeout) {
            clearTimeout(form._submitTimeout);
            form._submitTimeout = null;
        }

        const buttons = form.querySelectorAll('button[type="submit"], input[type="submit"], button:not([type])');
        buttons.forEach(function (btn) {
            if (btn.hasAttribute('data-original-html')) {
                btn.innerHTML = btn.getAttribute('data-original-html');
                btn.removeAttribute('data-original-html');
            }
            if (btn.hasAttribute('data-original-value')) {
                btn.value = btn.getAttribute('data-original-value');
                btn.removeAttribute('data-original-value');
            }
            if (btn.getAttribute('data-guard-disabled') === 'true') {
                btn.disabled = false;
                btn.removeAttribute('data-guard-disabled');
            }
        });
    }

    /**
     * Handles form submit events in the bubbling phase.
     */
    function handleSubmitEvent(event) {
        const form = event.target;
        if (!form || form.tagName !== 'FORM') return;

        // If another handler stopped the submission (e.g. form validation or cancel confirm)
        if (event.defaultPrevented) return;

        const method = (form.getAttribute('method') || 'GET').toUpperCase();
        if (method === 'GET') return;

        // Always ensure the idempotency key is set
        attachKeyToForm(form);

        // Prevent duplicate submit
        if (form.dataset.submitting === 'true') {
            event.preventDefault();
            event.stopImmediatePropagation();
            return false;
        }

        // Mark as submitting immediately to block subsequent submit events
        form.dataset.submitting = 'true';

        // Disable submit buttons on the next tick so the clicked button's name/value
        // are reliably included in the browser's form data
        setTimeout(function () {
            // Check if form is still submitting (wasn't cancelled)
            if (form.dataset.submitting !== 'true') return;

            const buttons = form.querySelectorAll('button[type="submit"], input[type="submit"], button:not([type])');
            buttons.forEach(function (btn) {
                if (!btn.disabled) {
                    btn.setAttribute('data-guard-disabled', 'true');
                    btn.disabled = true;

                    const busyText = btn.getAttribute('data-busy-text') || CONFIG.defaultBusyText;

                    if (btn.tagName === 'INPUT') {
                        btn.setAttribute('data-original-value', btn.value);
                        btn.value = busyText;
                    } else {
                        btn.setAttribute('data-original-html', btn.innerHTML);
                        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> ' + busyText;
                    }
                }
            });
        }, 0);

        // Safety fallback: if navigation takes longer than 15s or stays on page (e.g. file download)
        form._submitTimeout = setTimeout(function () {
            resetFormSubmission(form);
        }, CONFIG.timeoutMs);
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            prepareAllForms(document);
        });
    } else {
        prepareAllForms(document);
    }

    // Observe dynamically inserted forms (modals, Ajax inserts, etc.)
    if (typeof MutationObserver !== 'undefined') {
        const observer = new MutationObserver(function (mutations) {
            mutations.forEach(function (mutation) {
                mutation.addedNodes.forEach(function (node) {
                    if (node.nodeType === 1) {
                        if (node.tagName === 'FORM') {
                            attachKeyToForm(node);
                        } else if (node.querySelectorAll) {
                            prepareAllForms(node);
                        }
                    }
                });
            });
        });

        observer.observe(document.documentElement, {
            childList: true,
            subtree: true
        });
    }

    // Global submit event listener
    document.addEventListener('submit', handleSubmitEvent, false);

    // Reset when restored from browser Back/Forward cache (bfcache)
    window.addEventListener('pageshow', function (event) {
        document.querySelectorAll('form').forEach(resetFormSubmission);
    });

    // Handle AJAX setup if jQuery is loaded
    if (typeof window.jQuery !== 'undefined') {
        window.jQuery(document).ajaxSend(function (event, jqXHR, settings) {
            const method = (settings.type || 'GET').toUpperCase();
            if (method !== 'GET' && method !== 'HEAD' && method !== 'OPTIONS') {
                if (!settings.crossDomain) {
                    // Inject CSRF and Idempotency key if not present
                    const token = document.querySelector('meta[name="csrf-token"]');
                    if (token && !settings.headers?.['X-CSRF-TOKEN']) {
                        jqXHR.setRequestHeader('X-CSRF-TOKEN', token.getAttribute('content'));
                    }
                }
            }
        });
    }

    // Expose utility on window for programmatic reset if needed
    window.JBIFormGuard = {
        reset: resetFormSubmission,
        prepare: prepareAllForms,
        attach: attachKeyToForm
    };
})();
