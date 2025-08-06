// debug_client_side.js
// Script để debug client-side scripts và tìm script gây lỗi

console.log('🔍 Debug Client-Side Scripts');
console.log('============================');

// Kiểm tra các script đã load
console.log('📋 Loaded Scripts:');
const scripts = document.querySelectorAll('script');
scripts.forEach((script, index) => {
  console.log(`${index + 1}. ${script.src || 'inline script'}`);
  if (script.src) {
    console.log(`   Source: ${script.src}`);
  }
  if (script.textContent) {
    console.log(`   Content: ${script.textContent.substring(0, 100)}...`);
  }
});

// Kiểm tra các extension hoặc script tự động
console.log('\n🔧 Checking for auto-injected scripts:');
if (typeof window.solveSimpleChallenge !== 'undefined') {
  console.log('❌ Found solveSimpleChallenge function - this is client-side!');
  console.log('   Function source:', window.solveSimpleChallenge.toString());
} else {
  console.log('✅ No solveSimpleChallenge function found');
}

// Kiểm tra các global functions
console.log('\n🌐 Global Functions:');
const globalFunctions = Object.getOwnPropertyNames(window).filter(name => 
  typeof window[name] === 'function' && 
  (name.includes('captcha') || name.includes('solve') || name.includes('recaptcha'))
);
globalFunctions.forEach(func => {
  console.log(`   ${func}: ${typeof window[func]}`);
});

// Kiểm tra các event listeners
console.log('\n🎧 Event Listeners:');
if (window.addEventListener) {
  console.log('   addEventListener available');
}

// Kiểm tra các mutation observers
console.log('\n👀 Mutation Observers:');
if (window.MutationObserver) {
  console.log('   MutationObserver available');
}

// Kiểm tra các iframe
console.log('\n🖼️ Iframes:');
const iframes = document.querySelectorAll('iframe');
iframes.forEach((iframe, index) => {
  console.log(`${index + 1}. ${iframe.src || 'no src'}`);
});

// Kiểm tra CSP violations
console.log('\n🚫 CSP Violations:');
if (window.console && console.error) {
  const originalError = console.error;
  console.error = function(...args) {
    if (args[0] && typeof args[0] === 'string' && args[0].includes('Content Security Policy')) {
      console.log('🚫 CSP Violation detected:', args);
    }
    originalError.apply(console, args);
  };
}

// Kiểm tra các script tags với src
console.log('\n📜 Script tags with src:');
const scriptTags = document.querySelectorAll('script[src]');
scriptTags.forEach((script, index) => {
  console.log(`${index + 1}. ${script.src}`);
  if (script.src.includes('captcha') || script.src.includes('recaptcha')) {
    console.log(`   ⚠️  Potential captcha script: ${script.src}`);
  }
});

// Kiểm tra các inline scripts
console.log('\n📝 Inline scripts:');
const inlineScripts = document.querySelectorAll('script:not([src])');
inlineScripts.forEach((script, index) => {
  const content = script.textContent || script.innerHTML;
  if (content.includes('captcha') || content.includes('recaptcha') || content.includes('solve')) {
    console.log(`${index + 1}. Found captcha-related inline script:`);
    console.log(`   Content: ${content.substring(0, 200)}...`);
  }
});

console.log('\n✅ Debug completed!'); 