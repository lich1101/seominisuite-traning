
// DISABLED: Client-side reCAPTCHA script injection
// This file has been disabled to prevent CSP violations
// Server-side Puppeteer integration is used instead
console.log('🚫 Client-side reCAPTCHA script injection disabled');
console.log('✅ Server-side Puppeteer integration is active');

var footer = jQuery("footer");
(function($) {
  $("document").ready(function(e) {
    // Log that client-side captcha is disabled
    console.log('🚫 Client-side captcha form injection disabled');
    console.log('✅ Use server-side captcha solving instead');
    
    // Don't inject any reCAPTCHA scripts or forms
    // This prevents CSP violations and conflicts with server-side solving
  });
})(jQuery);