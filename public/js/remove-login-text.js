// Remove "Login" text from all button labels across the admin dashboard
(function () {
    'use strict';

    function removeLoginText() {
        // Find all possible button and link elements
        const selectors = [
            '.fi-btn-label',
            '.fi-link-label',
            '.fi-btn',
            '.fi-ac-btn',
            'button',
            'a.fi-link'
        ];

        selectors.forEach(selector => {
            document.querySelectorAll(selector).forEach(element => {
                // Get all text nodes
                const walker = document.createTreeWalker(
                    element,
                    NodeFilter.SHOW_TEXT,
                    null,
                    false
                );

                let node;
                while (node = walker.nextNode()) {
                    if (node.nodeValue && node.nodeValue.includes('Login')) {
                        node.nodeValue = node.nodeValue.replace(/\s*Login\s*/gi, '').trim();
                    }
                }

                // Also check direct textContent
                if (element.textContent && element.textContent.includes('Login')) {
                    const originalText = element.textContent;
                    const cleanText = originalText.replace(/\s*Login\s*/gi, '').trim();
                    if (originalText !== cleanText && element.childNodes.length === 1 && element.childNodes[0].nodeType === 3) {
                        element.textContent = cleanText;
                    }
                }
            });
        });
    }

    // Run on page load
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', removeLoginText);
    } else {
        removeLoginText();
    }

    // Run after a short delay to catch dynamically loaded content
    setTimeout(removeLoginText, 100);
    setTimeout(removeLoginText, 500);
    setTimeout(removeLoginText, 1000);

    // Watch for DOM changes
    const observer = new MutationObserver(function (mutations) {
        removeLoginText();
    });

    observer.observe(document.body, {
        childList: true,
        subtree: true,
        characterData: true
    });
})();

