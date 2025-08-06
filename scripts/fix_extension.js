// fix_extension.js - Script để sửa extension
// Thay vì giải captcha client-side, gọi về server

// Thay thế hàm solveSimpleChallenge cũ
window.solveSimpleChallenge = async function(sitekey, data_s, url) {
  try {
    // Gọi về server của bạn thay vì giải trực tiếp
    const response = await fetch('https://seominisuite.com/cassiopeia-captcha/resolve', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        url: url,
        data_sitekey: sitekey,
        data_s: data_s || '',
        uid: 'extension_user' // hoặc user ID thực tế
      })
    });
    
    const result = await response.json();
    
    if (result.success) {
      // Điền token vào textarea
      const textarea = document.getElementById('g-recaptcha-response');
      if (textarea) {
        textarea.value = result.code;
        textarea.dispatchEvent(new Event('input', { bubbles: true }));
      }
      
      // Submit form nếu có
      const form = document.querySelector('form');
      if (form) {
        form.submit();
      }
      
      return result.code;
    } else {
      console.error('Captcha resolution failed:', result.message);
      return null;
    }
  } catch (error) {
    console.error('Error calling server:', error);
    return null;
  }
};

// Thay thế các hàm khác nếu cần
console.log('Extension fixed to use server-side captcha resolution'); 