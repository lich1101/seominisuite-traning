var body = document.getElementsByTagName("body")[0];
var editorExtensionId = body.getAttribute("editorExtensionId");
// alert("body",body)
// let _html = "<div style=\"\n" +
//   "    position: fixed;\n" +
//   "    top: 0;\n" +
//   "    left: 0;\n" +
//   "    width: 100%;\n" +
//   "    height: 100%;\n" +
//   "    display: flex;\n" +
//   "    align-items: center;\n" +
//   "    justify-content: center;\n" +
//   "    background: #00000038;\n" +
//   "    color: black;\n" +
//   "    font-size: 35px;\n" +
//   "\">\n" +
//   "        <div style=\"\n" +
//   "    background: white;\n" +
//   "    padding: 20px;\n" +
//   "    border-radius: 5px;\n" +
//   "    box-shadow: 0px 0px 3px white;\n" +
//   "\">Captcha đang được giải tự động, vui lòng đợi!</div>\n" +
//   "    </div>";
// body.innerHTML += _html;
async function wait(time) {
    return new Promise(resolve => {
        setTimeout(resolve, time)
    })
}

async function waitquerySelector(element, time) {
    var _element_ = document.querySelector(element);
    await wait(time);
    return _element_
}

function getRandomElementsInArray(arr, n) {
    var result = new Array(n), len = arr.length, taken = new Array(len);
    if (n > len) throw new RangeError("getRandom: more elements taken than available");
    while (n--) {
        var x = Math.floor(Math.random() * len);
        result[n] = arr[x in taken ? taken[x] : x];
        taken[x] = --len in taken ? taken[len] : len
    }
    return result
}

function getRandomInt(min, max) {
    min = Math.ceil(min);
    max = Math.floor(max);
    return Math.floor(Math.random() * (max - min) + min)
}

async function audioPasseCaptcha() {
    // console.log("editorExtensionId");
    var captchaFrame = await waitquerySelector('iframe[src*="api2/bframe"]', 1e3);
    var imageFrame = captchaFrame.contentWindow.document.getElementById("rc-imageselect");
    var keypress_delay = 200;
    if (imageFrame) {
        var audioButton = captchaFrame.contentWindow.document.getElementById("recaptcha-audio-button");
        if (audioButton) {
            audioButton.click();
            var counter = 0;
            var _error_ = false;
            while (counter < 5) {
                var audioFrame = await waitquerySelector('iframe[src*="api2/bframe"]', 1e3);
                var doscaptcha = audioFrame.contentWindow.document.getElementsByClassName("rc-doscaptcha-body");
                if (doscaptcha.length > 0) {
                    // console.log("editorExtensionId",editorExtensionId);
                    chrome.runtime.sendMessage(editorExtensionId, {type: "captcha_dos"}, function (response) {
                        console.log(response);
                        chrome.runtime.lastError = null
                    });
                    _error_ = true;
                    break
                } else {
                    if (audioFrame) {
                        var audioSource = audioFrame.contentWindow.document.getElementById("audio-source");
                        if (audioSource) {
                            var audioSourceLink = audioSource.src;
                            var audioResponse = await window.fetch(audioSourceLink);
                            var buffer = await audioResponse.arrayBuffer();
                            var audioBytes = Array.from(new Uint8Array(buffer));
                            var speechResponse = await window.fetch("https://seominisuite.com/speech", {
                                method: "POST",
                                body: new Uint8Array(audioBytes).buffer
                            });
                            var speechResponseData = await speechResponse.json();
                            console.log(speechResponseData);
                            if (!speechResponseData || !speechResponseData.hasOwnProperty("message") || !speechResponseData.message || !speechResponseData.message.hasOwnProperty("text") || speechResponseData.message.text == "") {
                                console.log("1:" + counter);
                                counter = counter + 1;
                                audioFrame = await waitquerySelector('iframe[src*="api2/bframe"]', 1e3);
                                var reloadButton = audioFrame.contentWindow.document.getElementById("recaptcha-reload-button");
                                await reloadButton.click();
                                continue
                            }
                            var audioTranscript = "";
                            var input = audioFrame.contentWindow.document.getElementById("audio-response");
                            if (input !== null) {
                                input.click()
                            }
                            await wait(getRandomInt(1, 10) * 100);
                            for (var i = 0; i < speechResponseData.message.text.length; i++) {
                                audioTranscript = audioTranscript + speechResponseData.message.text[i];
                                input.value = audioTranscript;
                                var e = new KeyboardEvent("keypress", {
                                    bubbles: true,
                                    cancelable: true,
                                    key: speechResponseData.message.text[i].charCodeAt(0),
                                    charCode: speechResponseData.message.text[i].charCodeAt(0),
                                    keyCode: speechResponseData.message.text[i].charCodeAt(0),
                                    shiftKey: false
                                });
                                input.dispatchEvent(e);
                                await wait(getRandomInt(1, 10) * keypress_delay)
                            }
                            var verifyButton = audioFrame.contentWindow.document.getElementById("recaptcha-verify-button");
                            verifyButton.click();
                            const iframe = await waitquerySelector('iframe[src*="api2/anchor"]', 1e3);
                            if (!iframe) {
                                break
                            } else {
                                if (!!iframe.contentWindow.document.querySelector('#recaptcha-anchor[aria-checked="true"]')) {
                                    break
                                } else {
                                    counter = counter + 1;
                                    audioFrame = await waitquerySelector('iframe[src*="api2/bframe"]', 1e3);
                                    var reloadButton = audioFrame.contentWindow.document.getElementById("recaptcha-reload-button");
                                    await reloadButton.click();
                                    continue
                                }
                            }
                        }
                    }
                }
                counter = counter + 1
            }
            if (counter >= 5 && _error_ == false) {
                var imageButton = captchaFrame.contentWindow.document.getElementById("recaptcha-image-button");
                imageButton.click();
                console.log(imageButton);
                chrome.runtime.sendMessage(editorExtensionId, {type: "captcha_not_hander"}, function (response) {
                    console.log(response);
                    chrome.runtime.lastError = null
                })
            }
        }
    } else {
        var counter = 0;
        var _error_ = false;
        while (counter < 5) {
            var audioFrame = await waitquerySelector('iframe[src*="api2/bframe"]', 1e3);
            var doscaptcha = audioFrame.contentWindow.document.getElementsByClassName("rc-doscaptcha-body");
            if (doscaptcha.length > 0) {
                chrome.runtime.sendMessage(editorExtensionId, {type: "captcha_dos"}, function (response) {
                    console.log(response);
                    chrome.runtime.lastError = null
                });
                _error_ = true;
                break
            } else {
                if (audioFrame) {
                    var audioSource = audioFrame.contentWindow.document.getElementById("audio-source");
                    if (audioSource) {
                        var audioSourceLink = audioSource.src;
                        var audioResponse = await window.fetch(audioSourceLink);
                        var buffer = await audioResponse.arrayBuffer();
                        var audioBytes = Array.from(new Uint8Array(buffer));
                        var speechResponse = await window.fetch("https://seominisuite.com/speech", {
                            method: "POST",
                            body: new Uint8Array(audioBytes).buffer
                        });
                        var speechResponseData = await speechResponse.json();
                        console.log(speechResponseData);
                        if (!speechResponseData || !speechResponseData.hasOwnProperty("message") || !speechResponseData.message || !speechResponseData.message.hasOwnProperty("text") || speechResponseData.message.text == "") {
                            counter = counter + 1;
                            audioFrame = await waitquerySelector('iframe[src*="api2/bframe"]', 1e3);
                            var reloadButton = audioFrame.contentWindow.document.getElementById("recaptcha-reload-button");
                            await reloadButton.click();
                            continue
                        }
                        var audioTranscript = "";
                        var input = audioFrame.contentWindow.document.getElementById("audio-response");

                        input.click();
                        await wait(getRandomInt(1, 10) * 100);
                        for (var i = 0; i < speechResponseData.message.text.length; i++) {
                            audioTranscript = audioTranscript + speechResponseData.message.text[i];
                            input.value = audioTranscript;
                            var e = new KeyboardEvent("keypress", {
                                bubbles: true,
                                cancelable: true,
                                key: speechResponseData.message.text[i].charCodeAt(0),
                                charCode: speechResponseData.message.text[i].charCodeAt(0),
                                keyCode: speechResponseData.message.text[i].charCodeAt(0),
                                shiftKey: false
                            });
                            input.dispatchEvent(e);
                            await wait(getRandomInt(1, 10) * keypress_delay)
                        }
                        var verifyButton = audioFrame.contentWindow.document.getElementById("recaptcha-verify-button");
                        verifyButton.click();
                        const iframe = await waitquerySelector('iframe[src*="api2/anchor"]', 1e3);
                        if (!iframe) {
                            break
                        } else {
                            if (!!iframe.contentWindow.document.querySelector('#recaptcha-anchor[aria-checked="true"]')) {
                                break
                            } else {
                                counter = counter + 1;
                                audioFrame = await waitquerySelector('iframe[src*="api2/bframe"]', 1e3);
                                var reloadButton = audioFrame.contentWindow.document.getElementById("recaptcha-reload-button");
                                await wait(getRandomInt(1, 10) * 100);
                                await reloadButton.click();
                                continue
                            }
                        }
                    }
                }
            }
            counter = counter + 1
        }
        if (counter >= 5 && _error_ == false) {
            var imageButton = captchaFrame.contentWindow.document.getElementById("recaptcha-image-button");
            console.log(imageButton);
            imageButton.click();
            chrome.runtime.sendMessage(editorExtensionId, {type: "captcha_not_hander"}, function (response) {
                console.log(response);
                chrome.runtime.lastError = null
            })
        }
    }
}

// window.addEventListener("load", async function () {
//   return false;
  // await wait(3000);

    setTimeout(async function (e) {
      console.log("doing")
      if(editorExtensionId!==undefined && editorExtensionId!==null && editorExtensionId!==""){
        if (window.location.href.indexOf("https://www.google.com/") > -1) {
          var recaptchaFrame = await waitquerySelector('iframe[src*="api2/anchor"]', 1000);
          if (recaptchaFrame) {
            console.log("recaptcha 2");
            var checkbox = recaptchaFrame.contentWindow.document.getElementById("recaptcha-anchor");
            if (checkbox) {
              await wait(getRandomInt(5, 10) * 100);
              checkbox.click();
              var captchaFrame = await waitquerySelector('iframe[src*="api2/bframe"]', 1000);
              if(captchaFrame){
                var typeDo = getRandomInt(0, 9);
                if ([0, 1, 2, 3, 4].includes(typeDo)) {
                  var imageFrame =  captchaFrame.contentWindow.document.getElementById("rc-imageselect");
                  if (imageFrame) {
                    var captchaFrame = await waitquerySelector('iframe[src*="api2/bframe"]', 1e3);
                    var listImages = captchaFrame.contentWindow.document.getElementsByClassName("rc-imageselect-tile");
                    if (listImages.length > 0) {
                      var listImageSeleteds = getRandomElementsInArray(listImages, getRandomInt(3, 5));
                      for (var i = 0; i < listImageSeleteds.length; i++) {
                        listImageSeleteds[i].click();
                        await wait(getRandomInt(1, 3) * 500)
                      }
                      var verifyButton = captchaFrame.contentWindow.document.getElementById("recaptcha-verify-button");
                      verifyButton.click();
                      const iframe = await waitquerySelector('iframe[src*="api2/anchor"]', 1e3);
                      if (iframe) {
                        await wait(getRandomInt(1, 10) * 100);
                        audioPasseCaptcha()
                      }
                    }
                  } else {
                    var doscaptcha = captchaFrame.contentWindow.document.getElementsByClassName("rc-doscaptcha-body");
                    if (doscaptcha.length > 0) {
                      chrome.runtime.sendMessage(editorExtensionId, {type: "captcha_dos"}, function (response) {
                        console.log(response);
                        chrome.runtime.lastError = null
                      })
                    } else {
                      var imageButton = captchaFrame.contentWindow.document.getElementById("recaptcha-image-button");
                      if(imageButton!==null){
                        await wait(getRandomInt(1, 10) * 100);
                        imageButton.click();
                        var captchaFrame = await waitquerySelector('iframe[src*="api2/bframe"]', 1e3);
                        var listImages = captchaFrame.contentWindow.document.getElementsByClassName("rc-imageselect-tile");
                        if (listImages.length > 0) {
                          var listImageSeleteds = getRandomElementsInArray(listImages, getRandomInt(3, 5));
                          for (var i = 0; i < listImageSeleteds.length; i++) {
                            listImageSeleteds[i].click();
                            await wait(getRandomInt(1, 3) * 500)
                          }
                          var verifyButton = captchaFrame.contentWindow.document.getElementById("recaptcha-verify-button");
                          verifyButton.click();
                          const iframe = await waitquerySelector('iframe[src*="api2/anchor"]', 1e3);
                          if (iframe) {
                            await wait(getRandomInt(1, 10) * 100);
                            audioPasseCaptcha()
                          }
                        }
                      }else{
                        const iframe = await waitquerySelector('iframe[src*="api2/anchor"]', 1e3);
                        if (iframe) {
                          await wait(getRandomInt(1, 10) * 100);
                          audioPasseCaptcha()
                        }else{
                          chrome.runtime.sendMessage(editorExtensionId, {type: "captcha_not_hander"}, function (response) {
                            console.log(response);
                            chrome.runtime.lastError = null
                          })
                        }

                      }
                    }
                  }
                } else {
                  var doscaptcha = captchaFrame.contentWindow.document.getElementsByClassName("rc-doscaptcha-body");
                  if (doscaptcha.length > 0) {
                    chrome.runtime.sendMessage(editorExtensionId, {type: "captcha_dos"}, function (response) {
                      console.log(response);
                      chrome.runtime.lastError = null
                    })
                  } else {
                    await wait(getRandomInt(1, 10) * 100);
                    audioPasseCaptcha()
                  }
                }
              }
            }
          } else {
            // console.log("captcha 1");
            chrome.runtime.sendMessage(editorExtensionId, {type: "captcha_not_hander"}, function (response) {
              console.log(response);
              chrome.runtime.lastError = null
            })
          }
        }
      }
    },1000);
// });