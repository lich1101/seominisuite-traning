
var body = document.getElementsByTagName("body")[0];
var editorExtensionId = body.getAttribute("editorExtensionId");
var uid = body.getAttribute("uid");
var data_sitekey_element = document.getElementById("recaptcha");
var data_sitekey = data_sitekey_element.getAttribute("data-sitekey");
var data_s = data_sitekey_element.getAttribute("data-s");
console.log("data_sitekey_element",data_sitekey_element)
console.log("data_sitekey",data_sitekey)
let _html = "<div style=\"\n" +
  "    position: fixed;\n" +
  "    top: 0;\n" +
  "    left: 0;\n" +
  "    width: 100%;\n" +
  "    height: 100%;\n" +
  "    display: flex;\n" +
  "    align-items: center;\n" +
  "    justify-content: center;\n" +
  "    background: #00000038;\n" +
  "    color: black;\n" +
  "    font-size: 35px;\n" +
  "\">\n" +
  "        <div style=\"\n" +
  "    background: white;\n" +
  "    padding: 20px;\n" +
  "    border-radius: 5px;\n" +
  "    box-shadow: 0px 0px 3px white;\n" +
  "\">Captcha đang được giải tự động, vui lòng đợi</div>\n" +
  "    </div>";
body.innerHTML += _html;
var data = new Array();
data['data_sitekey'] = data_sitekey;
async function waitquerySelector(element, time) {
  var _element_ = document.querySelector(element);
  await wait(time);
  return _element_
}
window.addEventListener("load", async function () {
  // alert(123);
    if(editorExtensionId!==undefined && editorExtensionId!==null && editorExtensionId!==""){
        if (window.location.href.indexOf("https://www.google.com/") > -1) {
          console.log("true")
          // setTimeout(function (e) {
            // document.getElementById('captcha-form').submit();
            // setTimeout(function (e) {
            //   console.log("fff")
            // },2000)
          // },2000)
          setTimeout(async function (e) {
            try {
              var response = await window.fetch("https://seominisuite.com/cassiopeia-captcha/resolve", {
                method: "POST",
                headers: {
                  'Content-Type': 'application/json',
                },
                body: JSON.stringify({ data_sitekey: data_sitekey,url:window.location.href,data_s:data_s,uid:uid}),
              });
              
              if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
              }
              
              var ResponseData = await response.json();
              if(ResponseData.success){
                const iframe = await waitquerySelector('iframe[src*="api2/anchor"]', 1e3);
                var _textarea = document.getElementById("g-recaptcha-response");
                _textarea.value = ResponseData.code;
                document.getElementById('captcha-form').submit();
              }else{
                if(ResponseData.limited){
                  chrome.runtime.sendMessage(editorExtensionId, {type: "captcha_resolve_limited"}, function (response) {
                    // console.log(response);
                    chrome.runtime.lastError = null
                  })
                }else{
                  chrome.runtime.sendMessage(editorExtensionId, {type: "captcha_not_hander"}, function (response) {
                    // console.log(response);
                    chrome.runtime.lastError = null
                  })
                }
              }
            } catch (error) {
              console.error("Lỗi khi gọi API giải captcha:", error);
              // Thông báo lỗi cho extension
              chrome.runtime.sendMessage(editorExtensionId, {type: "captcha_error", error: error.message}, function (response) {
                chrome.runtime.lastError = null
              })
            }
          },100)
        }
    }
});