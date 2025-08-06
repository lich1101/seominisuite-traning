// extension_fix.js
// Script để inject vào extension, thay thế hàm solveSimpleChallenge

// Thay thế hàm solveSimpleChallenge cũ
if (typeof window !== 'undefined') {
  window.solveSimpleChallenge = async function(sitekey, data_s, url) {
    console.log('Extension calling server for captcha resolution...');
    
    try {
      const response = await fetch('https://seominisuite.com/cassiopeia-captcha/resolve', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          url: url || window.location.href,
          data_sitekey: sitekey,
          data_s: data_s || '',
          uid: 'extension_user' // hoặc user ID thực tế
        })
      });
      
      const result = await response.json();
      console.log('Server response:', result);
      
      if (result.success) {
        // Điền token vào textarea
        const textarea = document.getElementById('g-recaptcha-response');
        if (textarea) {
          textarea.value = result.code;
          textarea.dispatchEvent(new Event('input', { bubbles: true }));
          console.log('Token filled successfully');
        }
        
        // Submit form nếu có
        const form = document.querySelector('form');
        if (form) {
          form.submit();
          console.log('Form submitted');
        }
        
        return result.code;
      } else {
        console.error('Captcha resolution failed:', result.message);
        if (result.limited) {
          console.error('User has no remaining captcha solves');
        }
        return null;
      }
    } catch (error) {
      console.error('Error calling server:', error);
      return null;
    }
  };
  
  console.log('Extension fix applied - solveSimpleChallenge replaced with server-side version');
} 