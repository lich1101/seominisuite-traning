// disable_client_scripts.js
// Script để disable client-side scripts tạm thời

console.log('🚫 Disabling Client-Side Scripts');
console.log('================================');

// 1. Override solveSimpleChallenge để ngăn client-side execution
if (typeof window.solveSimpleChallenge !== 'undefined') {
    console.log('❌ Found solveSimpleChallenge - disabling...');
    const originalSolveSimpleChallenge = window.solveSimpleChallenge;
    
    window.solveSimpleChallenge = function(sitekey, data_s, url) {
        console.log('🚫 Client-side solveSimpleChallenge blocked');
        console.log('   Sitekey:', sitekey);
        console.log('   Data-s:', data_s);
        console.log('   URL:', url);
        console.log('   Use server-side Puppeteer instead');
        return null;
    };
    
    console.log('✅ solveSimpleChallenge disabled');
} else {
    console.log('✅ No solveSimpleChallenge found');
}

// 2. Override các function captcha khác
const captchaFunctions = [
    'solveCaptcha',
    'solveRecaptcha', 
    'autoSolve',
    'captchaSolver',
    'recaptchaSolver'
];

captchaFunctions.forEach(funcName => {
    if (typeof window[funcName] === 'function') {
        console.log(`❌ Found ${funcName} - disabling...`);
        window[funcName] = function(...args) {
            console.log(`🚫 Client-side ${funcName} blocked`);
            return null;
        };
    }
});

// 3. Block các script tags mới
const originalCreateElement = document.createElement;
document.createElement = function(tagName) {
    const element = originalCreateElement.call(document, tagName);
    
    if (tagName.toLowerCase() === 'script') {
        console.log('🚫 New script tag creation blocked');
        // Override src setter
        Object.defineProperty(element, 'src', {
            set: function(value) {
                console.log('🚫 Script src blocked:', value);
                if (value.includes('captcha') || value.includes('recaptcha')) {
                    console.log('⚠️  Captcha script blocked:', value);
                }
            },
            get: function() {
                return '';
            }
        });
        
        // Override textContent setter
        Object.defineProperty(element, 'textContent', {
            set: function(value) {
                if (value.includes('captcha') || value.includes('solveSimpleChallenge')) {
                    console.log('🚫 Captcha script content blocked');
                    return;
                }
                originalCreateElement.call(document, 'script').textContent = value;
            }
        });
    }
    
    return element;
};

// 4. Block các iframe captcha
const originalAppendChild = Node.prototype.appendChild;
Node.prototype.appendChild = function(child) {
    if (child.tagName === 'IFRAME' && child.src && child.src.includes('captcha')) {
        console.log('🚫 Captcha iframe blocked:', child.src);
        return child; // Return without appending
    }
    return originalAppendChild.call(this, child);
};

// 5. Override fetch để block captcha API calls
if (window.fetch) {
    const originalFetch = window.fetch;
    window.fetch = function(url, options) {
        if (typeof url === 'string' && (url.includes('captcha') || url.includes('2captcha'))) {
            console.log('🚫 Captcha API call blocked:', url);
            return Promise.resolve(new Response(JSON.stringify({
                success: false,
                message: 'Client-side captcha solving disabled'
            }), {
                status: 403,
                headers: { 'Content-Type': 'application/json' }
            }));
        }
        return originalFetch.call(this, url, options);
    };
}

// 6. Override XMLHttpRequest
const originalXHROpen = XMLHttpRequest.prototype.open;
XMLHttpRequest.prototype.open = function(method, url, ...args) {
    if (typeof url === 'string' && (url.includes('captcha') || url.includes('2captcha'))) {
        console.log('🚫 Captcha XHR call blocked:', url);
        this.abort();
        return;
    }
    return originalXHROpen.call(this, method, url, ...args);
};

// 7. Monitor và block các event listeners
const originalAddEventListener = EventTarget.prototype.addEventListener;
EventTarget.prototype.addEventListener = function(type, listener, options) {
    if (typeof listener === 'function') {
        const listenerStr = listener.toString();
        if (listenerStr.includes('captcha') || listenerStr.includes('solveSimpleChallenge')) {
            console.log('🚫 Captcha event listener blocked:', type);
            return;
        }
    }
    return originalAddEventListener.call(this, type, listener, options);
};

// 8. Block các mutation observers
const originalMutationObserver = window.MutationObserver;
window.MutationObserver = function(callback) {
    const wrappedCallback = function(mutations) {
        // Filter out captcha-related mutations
        const filteredMutations = mutations.filter(mutation => {
            if (mutation.type === 'childList') {
                return !Array.from(mutation.addedNodes).some(node => {
                    if (node.tagName === 'SCRIPT' && node.src && node.src.includes('captcha')) {
                        console.log('🚫 Captcha script mutation blocked');
                        return true;
                    }
                    if (node.tagName === 'IFRAME' && node.src && node.src.includes('captcha')) {
                        console.log('🚫 Captcha iframe mutation blocked');
                        return true;
                    }
                    return false;
                });
            }
            return true;
        });
        
        if (filteredMutations.length > 0) {
            callback(filteredMutations);
        }
    };
    
    return new originalMutationObserver(wrappedCallback);
};

// 9. Override console.error để catch CSP violations
const originalConsoleError = console.error;
console.error = function(...args) {
    if (args[0] && typeof args[0] === 'string') {
        if (args[0].includes('Content Security Policy')) {
            console.log('🚫 CSP Violation detected - likely from client-side script');
        }
        if (args[0].includes('solveSimpleChallenge')) {
            console.log('🚫 solveSimpleChallenge error - client-side script blocked');
        }
    }
    originalConsoleError.apply(console, args);
};

// 10. Monitor DOM changes
const observer = new MutationObserver(function(mutations) {
    mutations.forEach(function(mutation) {
        if (mutation.type === 'childList') {
            mutation.addedNodes.forEach(function(node) {
                if (node.tagName === 'SCRIPT') {
                    console.log('🚫 New script detected:', node.src || 'inline');
                    if (node.src && node.src.includes('captcha')) {
                        console.log('⚠️  Captcha script blocked');
                        node.remove();
                    }
                }
            });
        }
    });
});

observer.observe(document.head, { childList: true, subtree: true });
observer.observe(document.body, { childList: true, subtree: true });

console.log('✅ Client-side script blocking enabled');
console.log('📝 All captcha-related client-side scripts will be blocked');
console.log('🔧 Server-side Puppeteer will handle captcha solving'); 