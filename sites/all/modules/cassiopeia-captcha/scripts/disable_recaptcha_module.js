// disable_recaptcha_module.js
// Script để vô hiệu hóa module recaptcha và ngăn CSP violations

console.log('🚫 Disabling reCAPTCHA module script loading...');

// 1. Override drupal_add_js để block recaptcha scripts
if (typeof Drupal !== 'undefined' && Drupal.behaviors) {
    const originalAddJs = Drupal.behaviors.recaptchaReload;
    if (originalAddJs) {
        console.log('🚫 Found reCAPTCHA module behavior - disabling...');
        Drupal.behaviors.recaptchaReload = function(context, settings) {
            console.log('🚫 reCAPTCHA module behavior blocked');
            console.log('✅ Server-side Puppeteer integration is active instead');
        };
    }
}

// 2. Block reCAPTCHA script loading
const originalCreateElement = document.createElement;
document.createElement = function(tagName) {
    const element = originalCreateElement.call(document, tagName);
    
    if (tagName.toLowerCase() === 'script') {
        // Override src setter
        Object.defineProperty(element, 'src', {
            set: function(value) {
                if (value && value.includes('recaptcha/api.js')) {
                    console.log('🚫 reCAPTCHA script blocked:', value);
                    console.log('✅ Server-side Puppeteer integration is active');
                    return; // Don't set src
                }
                originalCreateElement.call(document, 'script').src = value;
            },
            get: function() {
                return '';
            }
        });
    }
    
    return element;
};

// 3. Block existing reCAPTCHA scripts
document.addEventListener('DOMContentLoaded', function() {
    const recaptchaScripts = document.querySelectorAll('script[src*="recaptcha"]');
    recaptchaScripts.forEach(script => {
        console.log('🚫 Removing existing reCAPTCHA script:', script.src);
        script.remove();
    });
    
    const recaptchaDivs = document.querySelectorAll('.g-recaptcha');
    recaptchaDivs.forEach(div => {
        console.log('🚫 Removing reCAPTCHA widget:', div);
        div.remove();
    });
});

// 4. Override grecaptcha if it exists
if (typeof grecaptcha !== 'undefined') {
    console.log('🚫 Found grecaptcha - disabling...');
    window.grecaptcha = {
        render: function() {
            console.log('🚫 grecaptcha.render() blocked');
            return 'disabled';
        },
        execute: function() {
            console.log('🚫 grecaptcha.execute() blocked');
            return Promise.resolve('disabled');
        },
        ready: function() {
            console.log('🚫 grecaptcha.ready() blocked');
        }
    };
}

// 5. Monitor for new script additions
const observer = new MutationObserver(function(mutations) {
    mutations.forEach(function(mutation) {
        if (mutation.type === 'childList') {
            mutation.addedNodes.forEach(function(node) {
                if (node.tagName === 'SCRIPT') {
                    if (node.src && node.src.includes('recaptcha')) {
                        console.log('🚫 New reCAPTCHA script detected and blocked:', node.src);
                        node.remove();
                    }
                }
            });
        }
    });
});

observer.observe(document.head, { childList: true, subtree: true });

console.log('✅ reCAPTCHA module script blocking enabled');
console.log('✅ Server-side Puppeteer integration is active'); 