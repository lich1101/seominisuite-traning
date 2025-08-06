// extension_detector.js
// Script để detect extensions và client-side scripts gây xung đột

console.log('🔍 Extension Detector Started');
console.log('=============================');

// 1. Kiểm tra các extension phổ biến
const commonExtensions = [
  'chrome-extension://',
  'moz-extension://',
  'safari-extension://'
];

console.log('🔧 Checking for browser extensions...');
commonExtensions.forEach(ext => {
  if (window.location.href.includes(ext)) {
    console.log(`⚠️  Found extension URL: ${ext}`);
  }
});

// 2. Kiểm tra các global objects của extension
const extensionObjects = [
  'chrome',
  'browser',
  'safari',
  'extension',
  'webextension'
];

extensionObjects.forEach(obj => {
  if (typeof window[obj] !== 'undefined') {
    console.log(`⚠️  Found extension object: ${obj}`);
  }
});

// 3. Kiểm tra các script đã inject
console.log('\n📜 Checking injected scripts...');
const allScripts = document.querySelectorAll('script');
allScripts.forEach((script, index) => {
  if (script.src) {
    console.log(`Script ${index + 1}: ${script.src}`);
    if (script.src.includes('extension') || script.src.includes('captcha')) {
      console.log(`⚠️  Suspicious script: ${script.src}`);
    }
  }
});

// 4. Kiểm tra các function đã được inject
console.log('\n🔧 Checking injected functions...');
const suspiciousFunctions = [
  'solveSimpleChallenge',
  'solveCaptcha', 
  'solveRecaptcha',
  'autoSolve',
  'captchaSolver'
];

suspiciousFunctions.forEach(funcName => {
  if (typeof window[funcName] === 'function') {
    console.log(`❌ Found suspicious function: ${funcName}`);
    try {
      const funcSource = window[funcName].toString();
      console.log(`   Source: ${funcSource.substring(0, 200)}...`);
    } catch (e) {
      console.log(`   Cannot read function source`);
    }
  }
});

// 5. Kiểm tra các event listeners
console.log('\n🎧 Checking event listeners...');
if (window.addEventListener) {
  // Override addEventListener để detect new listeners
  const originalAddEventListener = window.addEventListener;
  window.addEventListener = function(type, listener, options) {
    if (type === 'load' || type === 'DOMContentLoaded') {
      console.log(`⚠️  New event listener added: ${type}`);
    }
    return originalAddEventListener.call(this, type, listener, options);
  };
}

// 6. Kiểm tra các mutation observers
console.log('\n👀 Checking mutation observers...');
if (window.MutationObserver) {
  // Override MutationObserver để detect new observers
  const originalMutationObserver = window.MutationObserver;
  window.MutationObserver = function(callback) {
    console.log('⚠️  New MutationObserver created');
    return new originalMutationObserver(callback);
  };
}

// 7. Kiểm tra các iframe
console.log('\n🖼️ Checking iframes...');
const iframes = document.querySelectorAll('iframe');
iframes.forEach((iframe, index) => {
  console.log(`Iframe ${index + 1}: ${iframe.src || 'no src'}`);
  if (iframe.src && iframe.src.includes('captcha')) {
    console.log(`⚠️  Captcha iframe detected: ${iframe.src}`);
  }
});

// 8. Kiểm tra các object đã được modify
console.log('\n🔍 Checking modified objects...');
const originalObjects = {
  'document.querySelector': document.querySelector,
  'document.querySelectorAll': document.querySelectorAll,
  'window.fetch': window.fetch,
  'XMLHttpRequest': window.XMLHttpRequest
};

// Override để detect modifications
Object.keys(originalObjects).forEach(key => {
  const parts = key.split('.');
  const obj = parts[0] === 'window' ? window : document;
  const prop = parts[1];
  
  if (obj[prop] !== originalObjects[key]) {
    console.log(`⚠️  Object modified: ${key}`);
  }
});

// 9. Kiểm tra các CSP violations
console.log('\n🚫 Monitoring CSP violations...');
const originalError = console.error;
console.error = function(...args) {
  if (args[0] && typeof args[0] === 'string') {
    if (args[0].includes('Content Security Policy')) {
      console.log('🚫 CSP Violation:', args.join(' '));
    }
    if (args[0].includes('solveSimpleChallenge')) {
      console.log('🚫 solveSimpleChallenge error:', args.join(' '));
    }
  }
  originalError.apply(console, args);
};

// 10. Kiểm tra các script tags được thêm động
console.log('\n📝 Monitoring dynamic script additions...');
const observer = new MutationObserver(function(mutations) {
  mutations.forEach(function(mutation) {
    if (mutation.type === 'childList') {
      mutation.addedNodes.forEach(function(node) {
        if (node.tagName === 'SCRIPT') {
          console.log('🆕 New script added dynamically:', node.src || 'inline');
          if (node.src && node.src.includes('captcha')) {
            console.log('⚠️  Captcha script added dynamically:', node.src);
          }
        }
      });
    }
  });
});

observer.observe(document.head, { childList: true, subtree: true });
observer.observe(document.body, { childList: true, subtree: true });

// 11. Kiểm tra các global variables
console.log('\n🌐 Checking global variables...');
const globalVars = Object.getOwnPropertyNames(window).filter(name => 
  name.includes('captcha') || 
  name.includes('solve') || 
  name.includes('recaptcha') ||
  name.includes('extension')
);

globalVars.forEach(varName => {
  console.log(`Global variable: ${varName} = ${typeof window[varName]}`);
});

console.log('\n✅ Extension detection completed!');
console.log('📝 Monitor console for any suspicious activity...'); 