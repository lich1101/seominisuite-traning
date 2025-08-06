var search_item_class = "MjjYud";
var start = false;
var recheckTotal = 0;
var recheckCount = 0;
var recheck = [];
var waitingIDS = [];
var now = 0;
var $listOfID = $("#listOfID");
var $checkedItems = $("#checkedItems");
var $totalItems = $("#totalItems");
var loadingHtml = '<div class="loadingio-spinner-reload-nr0v98l51wb"><div class="ldio-nrb44wvqx8m">\n' + "        <div><div></div><div></div><div></div></div>\n" + "    </div></div>";

function isScreenLockSupported() {
    return ('wakeLock' in navigator);
}

// Helper function để xử lý captcha
async function handleCaptchaAuto(request, responseObj, currentObj) {
    try {
        if (typeof solveSimpleChallenge === 'function') {
            console.log("captcha_resolve auto");
            const result = await solveSimpleChallenge(
                responseObj.url || window.location.href,
                responseObj.siteKey,
                responseObj.dataS || ''
            );
            console.log("result", result);
            return result;
        } else {
            console.log("solveSimpleChallenge function not available");
            return false;
        }
    } catch (error) {
        console.error("Error in handleCaptchaAuto:", error);
        return false;
    }
}

// Helper function để tiếp tục xử lý sau khi giải captcha
function continueAfterCaptcha(request, responseObj, currentObj, success = false) {
    if (!success) {
        console.log("Auto captcha failed, switching to manual");
        try {
            $("select[name='captcha-resolve']").val("manual");
        } catch (error) {
            console.error("Error setting captcha mode:", error);
        }
    }
    
    switch (responseObj.checkType) {
        case"CheckBackLinkIndexed":
            setTimeout(function () {
                cassiopeia_recursion_backlink_indexed_check(request.data, currentObj);
                $(".extension-alert").removeClass("active")
            }, 3e3);
            break;
        case"KeyCheck":
            setTimeout(function () {
                cassiopeia_recursion_keyword_check(request.data, currentObj);
                $(".extension-alert").removeClass("active")
            }, 3e3);
            break;
        case"CheckDuplicateContent":
            setTimeout(function () {
                cassiopeia_recursion_duplicate_check(request.data, currentObj);
                $(".extension-alert").removeClass("active")
            }, 3e3);
            break;
        case"CheckBackLink":
            setTimeout(function () {
                cassiopeia_recursion_backlink_check(request.data, currentObj);
                $(".extension-alert").removeClass("active")
            }, 3e3);
            break;
        case"GuestPostDuplicateContentCheck":
            setTimeout(function () {
                cassiopeia_recursion_guest_post_duplicate_content_check(request.data, currentObj);
                $(".extension-alert").removeClass("active")
            }, 3e3);
            break;
        case"GetUrl":
            setTimeout(function () {
                cassiopeia_recursion_get_url_check(request.data, currentObj);
                $(".extension-alert").removeClass("active")
            }, 3e3);
            break;
        case"CheckContent":
            setTimeout(function () {
                cassiopeia_recursion_check_content_step_2(request.data, currentObj);
                $(".extension-alert").removeClass("active")
            }, 3e3);
            break;
        default:
            console.log("Unknown checkType:", responseObj.checkType);
    }
}

// Helper function để xử lý jQuery errors
function safeJQuery(selector, operation) {
    try {
        const element = $(selector);
        if (element.length > 0) {
            return operation(element);
        } else {
            console.log("Element not found:", selector);
            return null;
        }
    } catch (error) {
        console.error("jQuery error:", error);
        return null;
    }
}

// Helper function để validate objects
function validateObject(obj, requiredProps = []) {
    if (!obj || typeof obj !== 'object') {
        return false;
    }
    
    for (const prop of requiredProps) {
        if (!(prop in obj)) {
            console.log("Missing required property:", prop);
            return false;
        }
    }
    
    return true;
}
// function keepServiceWorkerActive(){
//     chrome.runtime.sendMessage({ type: "keepAlive"}, function (response) {
//     });
//     chrome.runtime.lastError = null
// }

async function cassiopeia_start_check_backlink(response){

    let i = 0;
    let delay = 0;
    // console.log("response",response);
    let _now = response.now;
    now = performance.now();
    let data = response.data;
    let stt = response.stt;
    while (i <= stt) {
        let group = data[i];
        let groupSize = Object.size(group);
        if (start) {
            // console.log(((performance.now() - now)+_now)/1000);
            $.each(group, function (index, _value) {
                chrome.runtime.sendMessage({
                    data: _value,
                    delay: ((performance.now() - now)/1000)+_now,
                    currentObj: _value,
                    type: "CheckBackLink"
                }, function (response) {
                });
                chrome.runtime.lastError = null
            })
        } else {
            break
        }
        await wait(2e3);
        delay+=2000;
        i++;
        diff = ((performance.now() - now)/1000)+_now;
        if(diff>=295){
            delay = 0;
            // console.log("wait 40")
            await wait(40000);
            now = performance.now();
            _now = 0;
        }
    }
}
chrome.runtime.onMessage.addListener(function (request, sender, sendResponse) {

    let responseObj;
    let currentObj;
    
    // Cải thiện logging
    try {
        console.log("request", request);
        if (request.type) {
            console.log("Request type:", request.type);
        }
        
        // Validate request structure
        if (!request || typeof request !== 'object') {
            console.error("Invalid request object:", request);
            return;
        }
        
        if (request.responseObj && typeof request.responseObj === 'object') {
            console.log("ResponseObj structure:", {
                checkType: request.responseObj.checkType,
                currentObj: request.responseObj.currentObj ? 'present' : 'missing',
                status: request.responseObj.status
            });
        }
        
    } catch (error) {
        console.error("Error logging request:", error);
    }
    switch (request.type) {
        case"GuestPostWebsiteGetCategories":
            responseObj = request.responseObj;
            currentObj = request.data;
            cassiopeia_guest_post_website_get_categories(currentObj, responseObj);
            break;
        case"GuestPostArticleAdd":
            console.log("request",request);
            responseObj = request.responseObj;
            currentObj = request.data;
            cassiopeia_guest_post_article_add( currentObj, responseObj);
            break;
        case"Guest_Post_Outline_Content_Check":
            if (start) {
                responseObj = request.responseObj;
                currentObj = responseObj.currentObj;
                if(responseObj.timeout!==undefined && responseObj.timeout==="timeout"){
                    cassiopeia_recursion_guest_post_outline_content_check(currentObj)
                }else{
                    cassiopeia_recursion_guest_post_outline_content_check(currentObj, responseObj)
                }
            }
            break;
        case"Guest_Post_Outline_Content_Check_Step_2":
            if (start) {
                responseObj = request.responseObj;
                currentObj = responseObj.currentObj;
                if (responseObj.status === "ERROR" && responseObj.recheck !== true) {
                    currentObj.recheck = true;
                    recheck.push(currentObj)
                } else {

                }
                cassiopeia_recursion_guest_post_outline_content_check_content_step_2( currentObj, responseObj)
            }
            break;
        case"CheckContentStep2":
            // cassiopeia_recursion_check_content_step_2
            if (start) {
                responseObj = request.responseObj;
                currentObj = responseObj.currentObj;
                if (responseObj.status === "ERROR" && responseObj.recheck !== true) {
                    currentObj.recheck = true;
                    recheck.push(currentObj)
                } else {

                }
                cassiopeia_recursion_check_content_step_2( currentObj, responseObj)
            }
            break;
        case"CheckContent":
            if (start) {
                responseObj = request.responseObj;
                currentObj = responseObj.currentObj;
                if(responseObj.timeout!==undefined && responseObj.timeout==="timeout"){
                    cassiopeia_recursion_check_content(currentObj)
                }else{
                    cassiopeia_recursion_check_content(currentObj, responseObj)
                }
            }
            break;
        case"getNow":
          // console.log("request",request);
            cassiopeia_start_check_backlink(request);
            break;
        case"may_doi_tao_30s":
            console.log("Be right back!")
            responseObj = request.responseObj;
            currentObj = responseObj.currentObj;
            switch (responseObj.checkType) {
                case"CheckBackLinkIndexed":
                    setTimeout(function () {
                        if (start) {
                            cassiopeia_recursion_backlink_indexed_check(request.data, currentObj)
                        }
                    }, 40000);
                    break;
                case"KeyCheck":
                    setTimeout(function () {
                        if (start) {
                            cassiopeia_recursion_keyword_check(request.data, currentObj)
                        }
                    }, 40000);
                    break;
                case"CheckDuplicateContent":
                    setTimeout(function () {
                        if (start) {
                            cassiopeia_recursion_duplicate_check(request.data, currentObj)
                        }
                    }, 40000);
                    break
            }
            break;
        case"CheckBackLinkIndexed":
            if (start) {
                responseObj = request.responseObj;
                currentObj = responseObj.currentObj;
                cassiopeia_recursion_backlink_indexed_check(request.data, currentObj, responseObj)
            }
            break;
        case"GetUrl":
            if (start) {
                responseObj = request.responseObj;
                currentObj = responseObj.currentObj;
                if(responseObj.timeout!==undefined && responseObj.timeout==="timeout"){
                    cassiopeia_recursion_get_url_check(request.data, currentObj)
                }else{
                    cassiopeia_recursion_get_url_check(request.data, currentObj, responseObj)
                }
            }
            break;
        case"KeyCheck":
            if (start) {
                responseObj = request.responseObj;
                currentObj = responseObj.currentObj;
                if(responseObj.timeout!==undefined && responseObj.timeout==="timeout"){
                    cassiopeia_recursion_keyword_check(request.data, currentObj)
                }else{
                    cassiopeia_recursion_keyword_check(request.data, currentObj, responseObj)
                }
            }
            break;
        case"CheckBackLink":
            // console.log("request",request);
            responseObj = request.responseObj;
            currentObj = responseObj.currentObj;
            if (responseObj.status === "ERROR" && responseObj.recheck !== true) {
                currentObj.recheck = true;
                recheck.push(currentObj)
            } else {

            }
            cassiopeia_recursion_backlink_check(request.data, currentObj, responseObj);
            break;
          case"getTels":
            responseObj = request.responseObj;
            currentObj = responseObj.currentObj;
            if (responseObj.status === "ERROR" && responseObj.recheck !== true) {
                currentObj.recheck = true;
                recheck.push(currentObj)
            } else {

            }
            cassiopeia_recursion_getTels(request.data, currentObj, responseObj);
            break;
        case"CheckDuplicateContent":
            if (start) {
                responseObj = request.responseObj;
                currentObj = responseObj.currentObj;
                cassiopeia_recursion_duplicate_check(request.data, currentObj, responseObj)
            }
            break;
        case"GuestPostDuplicateContentCheck":
            if (start) {
                responseObj = request.responseObj;
                currentObj = responseObj.currentObj;
                cassiopeia_recursion_guest_post_duplicate_content_check(request.data, currentObj, responseObj)
            }
            break;
        case"captcha_stop":
            if (start) {
                try {
                    responseObj = request.responseObj;
                    currentObj = responseObj.currentObj;
                    console.log("captcha_stop - responseObj:", responseObj);
                    
                    switch (responseObj.checkType) {
                        case"CheckBackLinkIndexed":
                            setTimeout(function () {
                                cassiopeia_recursion_backlink_indexed_check(request.data, currentObj)
                            }, 3e3);
                            break;
                        case"KeyCheck":
                            setTimeout(function () {
                                cassiopeia_recursion_keyword_check(request.data, currentObj)
                            }, 3e3);
                            break;
                        case"CheckDuplicateContent":
                            setTimeout(function () {
                                cassiopeia_recursion_duplicate_check(request.data, currentObj)
                            }, 3e3);
                            break;
                        case"CheckBackLink":
                            setTimeout(function () {
                                cassiopeia_recursion_backlink_check(request.data, currentObj)
                            }, 3e3);
                            break;
                        case"Guest_Post_Outline_Content_Check":
                            setTimeout(function () {
                                cassiopeia_recursion_guest_post_outline_content_check_content_step_2(request.data, currentObj);
                                $(".extension-alert").removeClass("active")
                            }, 3e3);
                            break;
                        case"GuestPostDuplicateContentCheck":
                            setTimeout(function () {
                                cassiopeia_recursion_guest_post_duplicate_content_check(request.data, currentObj);
                                $(".extension-alert").removeClass("active")
                            }, 3e3);
                            break;
                        case"GetUrl":
                            setTimeout(function () {
                                cassiopeia_recursion_get_url_check(request.data, currentObj);
                                $(".extension-alert").removeClass("active")
                            }, 3e3);
                            break; 
                        case"CheckContent":
                            setTimeout(function () {
                                cassiopeia_recursion_check_content_step_2(request.data, currentObj);
                                $(".extension-alert").removeClass("active")
                            }, 3e3);
                            break;
                        default:
                            console.log("Unknown checkType in captcha_stop:", responseObj.checkType);
                    }
                } catch (error) {
                    console.error("Error in captcha_stop:", error);
                }
            }
            break;
        case"captcha_dos":
            if (start) {
                try {
                    responseObj = request.responseObj;
                    currentObj = responseObj.currentObj;
                    console.log("captcha_dos - responseObj:", responseObj);
                    $(".extension-alert").addClass("active");
                    
                    switch (responseObj.checkType) {
                        case"CheckBackLinkIndexed":
                            setTimeout(function () {
                                cassiopeia_recursion_backlink_indexed_check(request.data, currentObj);
                                $(".extension-alert").removeClass("active")
                            }, 3e5);
                            break;
                        case"KeyCheck":
                            setTimeout(function () {
                                cassiopeia_recursion_keyword_check(request.data, currentObj);
                                $(".extension-alert").removeClass("active")
                            }, 3e5);
                            break;
                        case"CheckDuplicateContent":
                            setTimeout(function () {
                                cassiopeia_recursion_duplicate_check(request.data, currentObj);
                                $(".extension-alert").removeClass("active")
                            }, 3e5);
                            break;
                        case"CheckBackLink":
                            setTimeout(function () {
                                cassiopeia_recursion_backlink_check(request.data, currentObj);
                                $(".extension-alert").removeClass("active")
                            }, 3e5);
                            break;
                        case"GuestPostDuplicateContentCheck":
                            setTimeout(function () {
                                cassiopeia_recursion_guest_post_duplicate_content_check(request.data, currentObj);
                                $(".extension-alert").removeClass("active")
                            }, 3e5);
                            break;
                        case"Guest_Post_Outline_Content_Check":
                            setTimeout(function () {
                                cassiopeia_recursion_guest_post_outline_content_check_content_step_2(request.data, currentObj);
                                $(".extension-alert").removeClass("active")
                            }, 3e5);
                            break;
                        case"GetUrl":
                            setTimeout(function () {
                                cassiopeia_recursion_get_url_check(request.data, currentObj);
                                $(".extension-alert").removeClass("active")
                            }, 3e5);
                            break;
                        case"CheckContent":
                            setTimeout(function () {
                                cassiopeia_recursion_check_content_step_2(request.data, currentObj);
                                $(".extension-alert").removeClass("active")
                            }, 3e5);
                            break;
                        default:
                            console.log("Unknown checkType in captcha_dos:", responseObj.checkType);
                    }
                } catch (error) {
                    console.error("Error in captcha_dos:", error);
                    $(".extension-alert").removeClass("active");
                }
            }
            break;
        case"captcha_resolve_auto":
            if (start) {
                responseObj = request.responseObj;
                currentObj = responseObj.currentObj;
                console.log("captcha_resolve_auto - responseObj:", responseObj);
                
                // Tạo hàm async để xử lý captcha
                (async function() {
                    try {
                        const result = await handleCaptchaAuto(request, responseObj, currentObj);
                        continueAfterCaptcha(request, responseObj, currentObj, result);
                    } catch (error) {
                        console.error("Error in captcha_resolve_auto:", error);
                        // Fallback to manual on error
                        continueAfterCaptcha(request, responseObj, currentObj, false);
                    }
                })();
            }
            break;
        case "captcha_not_hander":
            alert("Captcha không được giải tự động, vui lòng thao tác.");
            break;
        case "captcha_resolve_limited":
            // alert("Bạn đã hết lượt giải captcha tự động, vui lòng thao tác thủ công.");
          let a = $.confirm({
            title: 'Bạn đã hết lượt giải Captcha tự động!',
            content: 'Bạn đã hết lượt giải mã Captcha Tự động. Mời bạn thao tác thủ công tới khi Kết thúc dự án!',
            columnClass: 'captcha_resolve_limited col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3 col-xs-10 col-xs-offset-1',
            buttons: {
              // continue: {
              //   text: 'Tiếp tục',
              //   btnClass: 'btn-success',
              //   action: function () {
              //     jQuery.ajax({
              //       method: "POST",
              //       url: "https://seominisuite.com/cassiopeia-captcha/resolve/get-info",
              //       success: function (result) {
              //         responseObj = request.responseObj;
              //         currentObj = responseObj.currentObj;
              //         // $("select[name='captcha-resolve']").val("manual");
              //         switch (responseObj.checkType) {
              //           case"CheckBackLinkIndexed":
              //             setTimeout(function () {
              //               cassiopeia_recursion_backlink_indexed_check(request.data, currentObj);
              //               $(".extension-alert").removeClass("active")
              //             }, 0);
              //             break;
              //           case"KeyCheck":
              //             setTimeout(function () {
              //               cassiopeia_recursion_keyword_check(request.data, currentObj);
              //               $(".extension-alert").removeClass("active")
              //             }, 0);
              //             break;
              //           case"CheckDuplicateContent":
              //             setTimeout(function () {
              //               cassiopeia_recursion_duplicate_check(request.data, currentObj);
              //               $(".extension-alert").removeClass("active")
              //             }, 0);
              //             break;
              //           case"GuestPostDuplicateContentCheck":
              //             setTimeout(function () {
              //               cassiopeia_recursion_guest_post_duplicate_content_check(request.data, currentObj);
              //               $(".extension-alert").removeClass("active")
              //             }, 0);
              //             break;
              //           case"cassiopeia_recursion_guest_post_outline_content_check_content_step_2":
              //             setTimeout(function () {
              //               cassiopeia_recursion_guest_post_outline_content_check_content_step_2(request.data, currentObj);
              //               $(".extension-alert").removeClass("active")
              //             }, 0);
              //             break;
              //         }
              //       }
              //     });
              //   }
              // },
              // formSubmit: {
              //   text: 'Mua thêm',
              //   btnClass: 'btn-primary',
              //   action: function () {
              //     window.open(
              //       'https://seominisuite.com//captcha/resolve/booking',
              //       '_blank' // <- This is what makes it open in a new window.
              //     );
              //     return false;
              //   }
              // },
              cancel: {
                text: 'Kiểm tra thủ công',
                btnClass: 'btn-success',
                action: function () {
                  responseObj = request.responseObj;
                  currentObj = responseObj.currentObj;
                  $("select[name='captcha-resolve']").val("manual");
                  switch (responseObj.checkType) {
                    case"CheckBackLinkIndexed":
                      setTimeout(function () {
                        cassiopeia_recursion_backlink_indexed_check(request.data, currentObj);
                        $(".extension-alert").removeClass("active")
                      }, 0);
                      break;
                    case"KeyCheck":
                      setTimeout(function () {
                        cassiopeia_recursion_keyword_check(request.data, currentObj);
                        $(".extension-alert").removeClass("active")
                      }, 0);
                      break;
                    case"CheckDuplicateContent":
                      setTimeout(function () {
                        cassiopeia_recursion_duplicate_check(request.data, currentObj);
                        $(".extension-alert").removeClass("active")
                      }, 0);
                      break;
                    case"GuestPostDuplicateContentCheck":
                      setTimeout(function () {
                        cassiopeia_recursion_guest_post_duplicate_content_check(request.data, currentObj);
                        $(".extension-alert").removeClass("active")
                      }, 0);
                      break;
                    case"cassiopeia_recursion_guest_post_outline_content_check_content_step_2":
                      setTimeout(function () {
                        cassiopeia_recursion_guest_post_outline_content_check_content_step_2(request.data, currentObj);
                        $(".extension-alert").removeClass("active")
                      }, 0);
                      break;
                  }

                }
              },
            }
          });
          break;
    }
    
    // Cải thiện error handling cho sendResponse
    try {
        sendResponse({response: ""});
    } catch (error) {
        console.error("Error sending response:", error);
    }
    return true
});
$(document).ready(function (e) {
    // Fix Mixed Content warning
    try {
        // Override favicon to use HTTPS
        const favicon = document.querySelector('link[rel="icon"], link[rel="shortcut icon"]');
        if (favicon && favicon.href && favicon.href.startsWith('http://')) {
            favicon.href = favicon.href.replace('http://', 'https://');
        }
        
        // Fix other mixed content issues
        const images = document.querySelectorAll('img[src^="http://"]');
        images.forEach(img => {
            img.src = img.src.replace('http://', 'https://');
        });
        
        const links = document.querySelectorAll('a[href^="http://"]');
        links.forEach(link => {
            link.href = link.href.replace('http://', 'https://');
        });
        
        // Fix blocked resources
        const blockedScripts = document.querySelectorAll('script[src*="facebook.net"], script[src*="google-analytics.com"]');
        blockedScripts.forEach(script => {
            script.setAttribute('data-blocked', 'true');
            console.log('Blocked script detected:', script.src);
        });
        
    } catch (error) {
        console.log('Error fixing mixed content:', error);
    }
    
    // Override console.error để suppress blocked resource errors
    const originalConsoleError = console.error;
    console.error = function(...args) {
        const message = args.join(' ');
        if (message.includes('ERR_BLOCKED_BY_CLIENT') || 
            message.includes('Failed to load resource') ||
            message.includes('facebook.net') ||
            message.includes('google-analytics.com')) {
            console.log('Suppressed blocked resource error:', message);
            return;
        }
        originalConsoleError.apply(console, args);
    };
    
    $("footer").addClass("seoToolExtension");
    $(".header-note").remove();
    document.addEventListener("Guest_Post_Website_Get_Categories", function (e) {
        let nodes = {};
        console.log("e",e);
        if(e.detail!==null && e.detail!==undefined){
            nodes = JSON.parse(e.detail);
        }
        nodes.forEach(async function (item,_index,arr) {
            cassiopeia_guest_post_website_get_categories(item);
            await wait(2e3);
        });
    });
    document.addEventListener("Guest_Post_Article_Add", function (e) {
        let node = {};
        if(e.detail!==null && e.detail!==undefined){
            node = e.detail;
        }

        cassiopeia_guest_post_article_add(node);
    });
    document.addEventListener("CheckContent", function (e) {
        start = true;
        $(".result").addClass("isLoading");
        let keyword = $("input[name='keyword']").val();

        let data = {};
        let index = 0;
        let temp = {};
        temp["index"] = index;
        temp["key"] = keyword;
        temp["searchEngine"] = "https://www.google.com";
        temp["language"] = "vi";
        $(".page-get-url .progress-block").addClass("active");
        $(".loading-block").addClass("active");
        cassiopeia_recursion_check_content(temp)
    });
    document.addEventListener("Guest_Post_Outline_Content_Check", function (e) {
        start = true;
        $(".result").addClass("isLoading");
        let keyword = e.detail!==null?JSON.parse(e.detail):"";

        let data = {};
        let index = 0;
        let temp = {};
        temp["index"] = index;
        temp["key"] = keyword;
        temp["searchEngine"] = "https://www.google.com";
        temp["language"] = "vi";
        $(".page-get-url .progress-block").addClass("active");
        $(".loading-block").addClass("active");
        console.log("temp",temp);
        cassiopeia_recursion_check_content(temp)
    });
    document.addEventListener("GetUrl", function (e) {
        let totalBacklinks = 0;
        let checkedBacklinks = 0;
        start = true;
        $(".result").addClass("isLoading");
        let keywords = $("textarea[name='keywords']").val();
        let exclude_urls = "";
        // let exclude_urls = $("textarea[name='exclude-urls']").val();
        let max_results = $("input[name='max_results']").val();
        let array_keywords = keywords.split("\n");
        let data = {};
        let index = 0;
        array_keywords.forEach(function (item,_index,arr) {
           if(item.length>=1){
               let temp = {};
               temp["index"] = index;
               temp["key"] = item;
               temp["searchEngine"] = "https://www.google.com";
               temp["language"] = "vi";
               temp["max_results"] = max_results;
               data[index] = temp;
               index++;
               totalBacklinks++;
           }
        });
        $(".page-get-url .progress-block").addClass("active");
        $totalItems.val(totalBacklinks);
        $checkedItems.val(checkedBacklinks);
        $(".progress-bar-block progress").attr("max", totalBacklinks);
        $(".progress-bar-block .totalBacklinks").text(totalBacklinks);
        if (Object.keys(data).length > 0) {
            let currentObj = data[Object.keys(data)[0]];
            cassiopeia_recursion_get_url_check(data, currentObj)
        }
    });
    document.addEventListener("CheckIndexed", function (e) {
        start = true;
        $(".result").addClass("isLoading");
        $(".progress-bar-block").addClass("active");
        let totalBacklinks = 0;
        let checkedBacklinks = 0;
        $(".selectAll").prop("checked", false);
        let domain = $("#project_domain").val();
        let data = {};
        let index = 0;
        $("tbody input").each(function (e) {
            let _this = $(this);
            if (_this.is(":checked")) {
                totalBacklinks++;
                let _parent = $("tr").has(_this);
                _parent.addClass("checking");
                let temp = {};
                temp["index"] = index;
                temp["id"] = _this.val();
                temp["stt"] = _this.attr("data-stt");
                temp["source"] = _this.attr("data-backlink-source");
                temp["domain"] = domain;
                data[index] = temp;
                index++
            }
        });
        $.ajax({
            method: "POST",
            url: "https://seominisuite.com/cassiopeia/ajax",
            data: {
                cmd: "check-cache",
                CheckBacklinkData:JSON.stringify(data)
            },
            success: function (result) {

            }
        });
        $(".loading-block").removeClass("active");
        $totalItems.val(totalBacklinks);
        $checkedItems.val(checkedBacklinks);
        $(".progress-bar-block progress").attr("max", totalBacklinks);
        $(".progress-bar-block .totalBacklinks").text(totalBacklinks);
        if (Object.keys(data).length > 0) {
            let currentObj = data[Object.keys(data)[0]];
            currentObj.check_key_index = 0;
            cassiopeia_recursion_backlink_indexed_check(data, currentObj)
        }
    });
    document.addEventListener("Guest_Post_Duplicate_Content_Check", function (e) {
        start = true;
        $.ajax({
            method: "POST",
            url: "https://seominisuite.com/cassiopeia/ajax",
            data: {cmd: "user-check-duplicate"},
            success: function (result) {
                if (result.response == "EXPIRED") {
                    start = false;
                    running = false;
                    setTimeout(function () {
                        document.dispatchEvent(new CustomEvent("UserExpired", {detail: "123"}))
                    }, 500)
                } else if (result.response == "LIMITED") {
                    setTimeout(function () {
                        document.dispatchEvent(new CustomEvent("UserLimited", {detail: "123"}))
                    }, 500)
                } else {
                    $(".duplicate-content-check .total-result span.none-duplicate").text("");
                    $(".duplicate-content-check .total-result span.duplicate").text("");

                    $(".result").addClass("isLoading");

                    let data = {};
                    let index = 0;
                    $(".duplicate-content-check .part-of-content").each(function (e) {
                        let _this = $(this);
                        let temp = {};
                        temp["index"] = index;
                        temp["value"] = _this.text().trim();
                        temp["id"] = _this.attr("data-id");
                        data[index] = temp;
                        index++
                    });
                    $totalItems.val(parseInt(index));
                    $checkedItems.val(0);
                    $(".duplicate-content-check .progress-bar-block progress").attr("max", parseInt(index));
                    $(".duplicate-content-check .progress-bar-block progress").val(0);
                    $(".duplicate-content-check .progress-bar-block").addClass("active");
                    // console.log("data",data);
                    if (Object.keys(data).length > 0) {
                        let currentObj = data[Object.keys(data)[0]];
                        cassiopeia_recursion_guest_post_duplicate_content_check(data, currentObj)
                    }
                }
            }
        });
    });
    document.addEventListener("CheckDuplicateContent", function (e) {
        start = true;
        $.ajax({
            method: "POST",
            url: "https://seominisuite.com/cassiopeia/ajax",
            data: {cmd: "user-check-duplicate"},
            success: function (result) {
                if (result.response == "EXPIRED") {
                    start = false;
                    running = false;
                    setTimeout(function () {
                        document.dispatchEvent(new CustomEvent("UserExpired", {detail: "123"}))
                    }, 500)
                } else if (result.response == "LIMITED") {
                    setTimeout(function () {
                        document.dispatchEvent(new CustomEvent("UserLimited", {detail: "123"}))
                    }, 500)
                } else {
                    $(".total-result span.none-duplicate").text("");
                    $(".total-result span.duplicate").text("");

                    $(".result").addClass("isLoading");

                    let data = {};
                    let index = 0;
                    $(".part-of-content").each(function (e) {
                        let _this = $(this);
                        let temp = {};
                        temp["index"] = index;
                        temp["value"] = _this.text().trim();
                        temp["id"] = _this.attr("data-id");
                        data[index] = temp;
                        index++
                    });
                    $totalItems.val(parseInt(index));
                    $checkedItems.val(0);
                    $(".progress-bar-block progress").attr("max", parseInt(index));
                    $(".progress-bar-block progress").val(0);
                    $(".progress-bar-block").addClass("active");
                    // console.log("data",data);
                    if (Object.keys(data).length > 0) {
                        let currentObj = data[Object.keys(data)[0]];
                        cassiopeia_recursion_duplicate_check(data, currentObj)
                    }
                }
            }
        })
    });

    document.addEventListener("CheckBacklink", async function (e) {
        start = true;
        $(".result").addClass("isLoading");
        $.ajax({
            method: "POST",
            url: "https://seominisuite.com/cassiopeia/ajax",
            data: {cmd: "user-backlink-check-response-complete", value: 0},
            success: function (result) {
            }
        });
        $(".progress-bar-block").addClass("active");
        let totalBacklinks = 0;
        let checkedBacklinks = 0;
        let domain = $("#project_domain").val();
        domain = getDomainFromHref(domain);
        let data = {};
        let stt = 0;
        let index = 0;
        let slice = {};
        let cache = {};
        $("tbody input").each(function (e) {
            let _this = $(this);
            if (_this.is(":checked")) {
                totalBacklinks++;
                let _parent = $("tr").has(_this);
                _parent.addClass("checking");
                let temp = {};
                temp["recheck"] = false;
                temp["index"] = index;
                temp["id"] = _this.val();
                temp["stt"] = _this.attr("data-stt");
                temp["source"] = _this.attr("data-backlink-source");
                temp["domain"] = domain;
                slice[index] = temp;
                if ((index + 1) % 5 === 0) {
                    data[stt] = slice;
                    slice = {};
                    stt++
                }
                index++
            }
            data[stt] = slice
        });

        $(".loading-block").removeClass("active");
        $totalItems.val(totalBacklinks);
        $checkedItems.val(checkedBacklinks);
        $(".progress-bar-block progress").attr("max", totalBacklinks);
        $(".progress-bar-block .totalBacklinks").text(totalBacklinks);
        $("input").prop("checked", false);
        chrome.runtime.sendMessage({data: data,stt:stt, type: "getNow",checkType:"backlink"}, function (response) {});
        chrome.runtime.lastError = null;
        let timer = 0
    });
    document.addEventListener("getTels", async function (e) {
        start = true;
        let data = e.detail;
      // var data = Object.keys(e.detail).map((key) => [key, e.detail[key]]);

      let i = 0;
      let delay = 0;
      // console.log("response",response);
      let _now = 0;
      now = performance.now();
      let stt = Object.keys(data).length-1;
      console.log("stt",stt);
      // console.log("data",data[0]);
      while (i <= stt) {
        let group = data[i];
        let groupSize = Object.size(group);
        console.log(group);
        if (start) {
          // console.log(((performance.now() - now)+_now)/1000);
          $.each(group, function (index, _value) {
            chrome.runtime.sendMessage({
              data: _value,
              delay: ((performance.now() - now)/1000)+_now,
              currentObj: _value,
              type: "getTels"
            }, function (response) {
            });
            chrome.runtime.lastError = null
          })
        } else {
          break
        }
        await wait(2e3);
        delay+=2000;
        i++;
        diff = ((performance.now() - now)/1000)+_now;
        if(diff>=295){
          delay = 0;
          // console.log("wait 40")
          await wait(40000);
          now = performance.now();
          _now = 0;
        }
      }
    });
    document.addEventListener("CheckKeyword", function (e) {
        start = true;
        $(".result").addClass("isLoading");
        let totalBacklinks = 0;
        let checkedBacklinks = 0;
        $(".progress-bar-block").addClass("active");
        $(".selectAll").prop("checked", false);
        let domain = $("#project_domain").val();
        domain = getDomainFromHref(domain);
        let searchEngine = $("select[name='search-engine']").val();
        let language = $("select[name='language']").val();

        let data = {};
        let index = 0;
        let uid = $("header").attr("data-uid");
        $("tbody input").each(function (e) {
            let _this = $(this);
            if (_this.is(":checked")) {
                totalBacklinks++;
                let _parent = $("tr").has(_this);
                _parent.addClass("checking");
                let temp = {};
                temp["index"] = index;
                temp["uid"] = uid;
                temp["id"] = _this.attr("data-id");
                temp["key"] = _this.attr("data-key");
                temp["domain"] = domain;
                temp["stt"] = _this.attr("data-stt");
                temp["searchEngine"] = searchEngine;
                temp["language"] = language;
                // temp["captcha_resolve"] = captcha_resolve;
                data[index] = temp;
                index++
            }
        });
        $(".loading-block").removeClass("active");
        $totalItems.val(totalBacklinks);
        $checkedItems.val(checkedBacklinks);
        $(".progress-bar-block progress").attr("max", totalBacklinks);
        $(".progress-bar-block .totalBacklinks").text(totalBacklinks);
        if (Object.keys(data).length > 0) {
            let currentObj = data[Object.keys(data)[0]];
            cassiopeia_recursion_keyword_check(data, currentObj)
            // let interval = setInterval(keepServiceWorkerActive, 1000);
        }
    })
});

async function cassiopeia_guest_post_article_add(currentObj,responseObject = 1) {
    if (responseObject === 1) {
        chrome.runtime.sendMessage({
            currentObj: currentObj,
            type: "GuestPostArticleAdd"
        }, function (response) {
        });
        chrome.runtime.lastError = null
    }else{
        let data = {};
        data['wp_post'] = JSON.stringify(responseObject);
        data['id'] = JSON.parse(currentObj).id;
        // console.log("123");
        document.dispatchEvent(new CustomEvent('Guest_Post_Article_Add_Complete', {detail: data}))
    }
}

async function cassiopeia_recursion_check_content_step_2(currentObj, responseObject = 1) {
    if (responseObject === 1) {
        chrome.runtime.sendMessage({
            currentObj: currentObj,
            type: "CheckContentStep2"
        }, function (response) {
        });
        chrome.runtime.lastError = null
    } else {
        let listOfID = [];
        let running = true;
        if ($listOfID.attr("data-value") !== "") {
            listOfID = JSON.parse($listOfID.attr("data-value"))
        }
        if (listOfID.length > 0) {
            if (listOfID.includes(currentObj.id) !== true) {
                listOfID.push(currentObj.id);
                $listOfID.attr("data-value", JSON.stringify(listOfID));
            }
        }else{
            listOfID.push(currentObj.id);
            $listOfID.attr("data-value", JSON.stringify(listOfID));
        }
        let checkedItems = $checkedItems.val();
        let totalItems = $totalItems.val();
        if (parseInt(checkedItems) <= parseInt(totalItems) && parseInt(totalItems) !== 0) {
            checkedItems++;
            $checkedItems.val(checkedItems);
            $(".progress-bar-block progress").val(checkedItems);
            $(".progress-bar-block .checkedBacklinks").text(checkedItems)
        }
        let __data = responseObject;
        let result = {};
        if(responseObject.status===200){
            let responseText = responseObject.responseText[0];
            let dom_nodes = $($.parseHTML(responseText));
            let pNodes = dom_nodes.find("p");
            let h1Nodes = dom_nodes.find("h1");
            let isArticle = false;
            let articleContent = "";
            if(pNodes.length>0){
                $.each( pNodes, function( key, value ) {
                    let parent = $(value).parent();
                    let _pNodes = parent.find(">p");
                    if(_pNodes.length>3){
                        isArticle = true;
                        articleContent = parent;
                        return false;
                    }
                });
            }
            if(isArticle===true){
                let h2Nodes = dom_nodes.find("h2");
                let h3Nodes = dom_nodes.find("h3");
                let imgNodes = dom_nodes.find("img");
                if(h2Nodes.length>0){
                    let _h2 = [];
                    $.each( h2Nodes, function( key, value ) {
                        if($(value).text().trim().length){
                            _h2.push($(value).text().trim());
                        }
                    });
                    result['h2'] = _h2;
                }
                // console.log("h3Nodes",h3Nodes);
                if(h3Nodes.length>0){
                    let _h3 = [];
                    $.each( h3Nodes, function( key, value ) {
                        // console.log("$(value).text().length",$(value).text());
                        if($(value).text().trim().length>0){
                            _h3.push($(value).text().trim());
                        }
                    });
                    result['h3'] = _h3;
                }
                if(imgNodes.length>0){
                    let _img = [];
                    $.each( imgNodes, function( key, value ) {
                        _img.push($(value).text());
                    });
                    result['img'] = _img.length;
                }
                let _content = $(articleContent).text().trim();
                let splitter = _content.split(" ");
                let word_count = [];
                $.each( splitter, function( key, value ) {
                    if(value.length>0 && value.trim()!==" "){
                        word_count.push(value);
                    }
                });
                result['word_count'] = word_count.length;
            }
            result['isArticle'] = isArticle;
            if(h1Nodes.length>0){
                let _h1 = [];
                $.each( h1Nodes, function( key, value ) {
                    if($(value).text().trim().length>0){
                        _h1.push($(value).text().trim());
                    }
                });
                result['h1'] = _h1;
            }
            result['href'] = currentObj.href;
            result['anchorText'] = currentObj.anchorText;
            result['index'] = currentObj.index;
            if(result["href"].toLowerCase().includes("search.google")===false){
                $.ajax({
                    method: "POST",
                    url: "https://seominisuite.com/cassiopeia/ajax",
                    data: {cmd: "user-check-content-response", data: JSON.stringify(result)},
                    success: async function (result) {
                        if (result.response === "EXPIRED") {
                            start = false;
                            running = false;
                            setTimeout(function () {
                                document.dispatchEvent(new CustomEvent("UserExpired", {detail: "123"}))
                            }, 500)
                        } else if (result.response === "LIMITED") {
                            start = false;
                            running = false;
                            setTimeout(function () {
                                document.dispatchEvent(new CustomEvent("UserLimited", {detail: "123"}))
                            }, 500)
                        }
                    }
                });
            }
        }
        if (running) {
            if (parseInt(checkedItems) === parseInt(totalItems)) {
                $totalItems.val(0);
                $checkedItems.val(0);
                $(".progress-bar-block").removeClass("active");
                $(".result").removeClass("loading");
                setTimeout(function () {
                    $.ajax({
                        method: "POST",
                        url: "https://seominisuite.com/cassiopeia/ajax",
                        data: {
                            cmd: "user-check-content-complete",
                            value: 1,
                            listOfID: JSON.stringify(listOfID),
                            responseObject: JSON.stringify(responseObject)
                        },
                        success: function (result) {
                            if (result.response === "EXPIRED") {
                                start = false;
                                running = false;
                                setTimeout(function () {
                                    document.dispatchEvent(new CustomEvent("UserExpired", {detail: "123"}))
                                }, 500)
                            } else if (result.response === "LIMITED") {
                                start = false;
                                running = false;
                                setTimeout(function () {
                                    document.dispatchEvent(new CustomEvent("UserLimited", {detail: "123"}))
                                }, 500)
                            } else {
                                $(".loading-block").removeClass("active");
                                document.dispatchEvent(new CustomEvent("CheckContentComplete", {detail: "123"}))
                            }
                        }
                    })
                }, 1e3)
            }
        }
    }
}
async function cassiopeia_recursion_guest_post_outline_content_check_content_step_2(currentObj, responseObject = 1) {
    if (responseObject === 1) {
        chrome.runtime.sendMessage({
            currentObj: currentObj,
            type: "Guest_Post_Outline_Content_Check_Step_2"
        }, function (response) {
        });
        chrome.runtime.lastError = null
    } else {
        let listOfID = [];
        let running = true;
        if ($listOfID.attr("data-value") !== "") {
            listOfID = JSON.parse($listOfID.attr("data-value"))
        }
        if (listOfID.length > 0) {
            if (listOfID.includes(currentObj.id) !== true) {
                listOfID.push(currentObj.id);
                $listOfID.attr("data-value", JSON.stringify(listOfID));
            }
        }else{
            listOfID.push(currentObj.id);
            $listOfID.attr("data-value", JSON.stringify(listOfID));
        }
        let checkedItems = $checkedItems.val();
        let totalItems = $totalItems.val();
        if (parseInt(checkedItems) <= parseInt(totalItems) && parseInt(totalItems) !== 0) {
            checkedItems++;
            $checkedItems.val(checkedItems);
            $(".progress-bar-block progress").val(checkedItems);
            $(".progress-bar-block .checkedBacklinks").text(checkedItems)
        }
        let __data = responseObject;
        let result = {};
        if(responseObject.status===200){
            let responseText = responseObject.responseText[0];
            let dom_nodes = $($.parseHTML(responseText));
            let pNodes = dom_nodes.find("p");
            let h1Nodes = dom_nodes.find("h1");
            let isArticle = false;
            let articleContent = "";
            if(pNodes.length>0){
                $.each( pNodes, function( key, value ) {
                    let parent = $(value).parent();
                    let _pNodes = parent.find(">p");
                    if(_pNodes.length>3){
                        isArticle = true;
                        articleContent = parent;
                        return false;
                    }
                });
            }
            if(isArticle===true){
                let h2Nodes = dom_nodes.find("h2");
                let h3Nodes = dom_nodes.find("h3");
                let imgNodes = dom_nodes.find("img");
                if(h2Nodes.length>0){
                    let _h2 = [];
                    $.each( h2Nodes, function( key, value ) {
                        if($(value).text().trim().length){
                            _h2.push($(value).text().trim());
                        }
                    });
                    result['h2'] = _h2;
                }
                // console.log("h3Nodes",h3Nodes);
                if(h3Nodes.length>0){
                    let _h3 = [];
                    $.each( h3Nodes, function( key, value ) {
                        // console.log("$(value).text().length",$(value).text());
                        if($(value).text().trim().length>0){
                            _h3.push($(value).text().trim());
                        }
                    });
                    result['h3'] = _h3;
                }
                if(imgNodes.length>0){
                    let _img = [];
                    $.each( imgNodes, function( key, value ) {
                        _img.push($(value).text());
                    });
                    result['img'] = _img.length;
                }
                let _content = $(articleContent).text().trim();
                let splitter = _content.split(" ");
                let word_count = [];
                $.each( splitter, function( key, value ) {
                    if(value.length>0 && value.trim()!==" "){
                        word_count.push(value);
                    }
                });
                result['word_count'] = word_count.length;
            }
            result['isArticle'] = isArticle;
            if(h1Nodes.length>0){
                let _h1 = [];
                $.each( h1Nodes, function( key, value ) {
                    if($(value).text().trim().length>0){
                        _h1.push($(value).text().trim());
                    }
                });
                result['h1'] = _h1;
            }
            result['href'] = currentObj.href;
            result['anchorText'] = currentObj.anchorText;
            result['index'] = currentObj.index;
            if(result["href"].toLowerCase().includes("search.google")===false){
                $.ajax({
                    method: "POST",
                    url: "https://seominisuite.com/cassiopeia/ajax",
                    data: {cmd: "user-check-content-response", data: JSON.stringify(result)},
                    success: async function (result) {
                        if (result.response === "EXPIRED") {
                            start = false;
                            running = false;
                            setTimeout(function () {
                                document.dispatchEvent(new CustomEvent("UserExpired", {detail: "123"}))
                            }, 500)
                        } else if (result.response === "LIMITED") {
                            start = false;
                            running = false;
                            setTimeout(function () {
                                document.dispatchEvent(new CustomEvent("UserLimited", {detail: "123"}))
                            }, 500)
                        }
                    }
                });
            }
        }
        if (running) {
            if (parseInt(checkedItems) === parseInt(totalItems)) {
                $totalItems.val(0);
                $checkedItems.val(0);
                $(".progress-bar-block").removeClass("active");
                $(".result").removeClass("loading");
                setTimeout(function () {
                    $.ajax({
                        method: "POST",
                        url: "https://seominisuite.com/cassiopeia/ajax",
                        data: {
                            cmd: "user-check-content-complete",
                            value: 1,
                            listOfID: JSON.stringify(listOfID),
                            responseObject: JSON.stringify(responseObject)
                        },
                        success: function (result) {
                            if (result.response === "EXPIRED") {
                                start = false;
                                running = false;
                                setTimeout(function () {
                                    document.dispatchEvent(new CustomEvent("UserExpired", {detail: "123"}))
                                }, 500)
                            } else if (result.response === "LIMITED") {
                                start = false;
                                running = false;
                                setTimeout(function () {
                                    document.dispatchEvent(new CustomEvent("UserLimited", {detail: "123"}))
                                }, 500)
                            } else {
                                $(".loading-block").removeClass("active");
                                document.dispatchEvent(new CustomEvent("CheckContentComplete", {detail: "123"}))
                            }
                        }
                    })
                }, 1e3)
            }
        }
    }
}
function cassiopeia_recursion_guest_post_outline_content_check(currentObj, responseObject = 1) {
    if (responseObject === 1) {
        chrome.runtime.sendMessage({
            currentObj: currentObj,
            type: "Guest_Post_Outline_Content_Check"
        }, function (response) {
        });
        chrome.runtime.lastError = null
    } else {
        let dom_nodes = $($.parseHTML(responseObject.content[0]));
        let gNodes = dom_nodes.find("div#search div.MjjYud");
        $.ajax({
            method: "POST",
            url: "https://seominisuite.com/cassiopeia/ajax",
            data: {
                cmd: "user-check-content-begin",
                key: currentObj.key,
            },
            success: function() {
                $.each( gNodes, function( key, value ) {
                    let _this = $(value);
                    let a_tag = _this.find("a:first-child");
                    let temp = {};
                    temp["recheck"] = false;
                    temp["index"] = key;
                    temp["href"] = $(a_tag).attr("href");
                    temp["anchorText"] = $(a_tag).find("h3").text();
                    cassiopeia_recursion_guest_post_outline_content_check_content_step_2(temp);
                });
                $totalItems.val(gNodes.length);
                $checkedItems.val(0);
            }
        });
    }
}
function cassiopeia_recursion_check_content(currentObj, responseObject = 1) {
  currentObj.uid = $("header").attr("data-uid");
  currentObj.captcha_resolve = $("select[name='captcha-resolve']").val();
    if (responseObject === 1) {
        chrome.runtime.sendMessage({
            currentObj: currentObj,
            type: "CheckContent"
        }, function (response) {
        });
        chrome.runtime.lastError = null
    } else {
        let dom_nodes = $($.parseHTML(responseObject.content[0]));
        // let gNodes = dom_nodes.find("div[data-header-feature='0'] a");
        let gNodes = dom_nodes.find("div#search div.MjjYud");
        // console.log("gNodes",gNodes);
        $.ajax({
            method: "POST",
            url: "https://seominisuite.com/cassiopeia/ajax",
            data: {
                cmd: "user-check-content-begin",
                key: currentObj.key,
            },
            success: function() {
                $.each( gNodes, function( key, value ) {
                    let _this = $(value);
                    let a_tag = _this.find("a:first-child");
                    let temp = {};
                    temp["recheck"] = false;
                    temp["index"] = key;
                    temp["href"] = $(a_tag).attr("href");
                    temp["anchorText"] = $(a_tag).find("h3").text();
                    cassiopeia_recursion_check_content_step_2(temp);
                });
                $totalItems.val(gNodes.length);
                $checkedItems.val(0);
            }
        });
    }
}

function cassiopeia_guest_post_duplicate_content_check(data,currentObj,responseObject){
    let $progressbar = $(".duplicate-content-check .progress-bar-block progress");
    let $element = $("td[data-id='" + currentObj.id + "']");
    let $checkResult = $(".duplicate-content-check .check-result");
    let checkedItems = $checkedItems.val();
    let totalItems = $totalItems.val();
    if (parseInt(checkedItems) <= parseInt(totalItems) && parseInt(totalItems) !== 0) {
        checkedItems++;
        $checkedItems.val(checkedItems);
        $progressbar.val(checkedItems);
    }
    if (parseInt(checkedItems) === parseInt(totalItems)) {
        $totalItems.val(0);
        $checkedItems.val(0);
        $(".duplicate-content-check .progress-bar-block").removeClass("active")
    }
    let dom_nodes = $($.parseHTML(responseObject.content[0]));
    let gNodes = dom_nodes.find("div.MjjYud");
    let check = false;
    let sources = [];
    let domainName = $(".duplicate-content-check input[name='duplicate_content[exclude_domains]']").val();
    let domains = [];
    if (domainName.length > 0) {
        let domain_list = JSON.parse(domainName);
        if (domain_list.length > 0) {
            domain_list.forEach(function (item, index) {
                let _domain = getDomainFromHref(item.value);
                domains.push(_domain)
            })
        }
    }
    domainName = getDomainFromHref(domainName);
    $.each(gNodes, function (index, _value) {
        if ($(_value).text().toLowerCase().includes(currentObj.value.toLowerCase())) {
            let _href = $(_value).find("a").attr("href");
            let _domain_href = getDomainFromHref(_href);
            let _url = "<a target='_blank' href='" + _href + "'>" + _domain_href + "</a>";
            if (sources.length <= 5) {
                sources.push(_url)
            }
            check = true
        }
    });
    let _result = '<i class="fa fa-check"></i>';
    if (check === true) {
        _result = '<i class="fa fa-times"></i>'
    }
    let element = $element.parent().find(".check-result");
    let elementSource = $element.parent().find(".sources");
    element.html(_result);
    elementSource.html("" + sources.join(" "));
    element.attr("data-duplicate", check);
    if (check) {
        element.attr("data-value", 1)
    } else {
        element.attr("data-value", 0)
    }
    delete data[currentObj.index];
    if (Object.keys(data).length > 0) {
        currentObj = data[Object.keys(data)[0]];
        cassiopeia_recursion_guest_post_duplicate_content_check(data, currentObj);
        $progressbar.val(checkedItems);
    } else {
        let totalResult = $checkResult.length;
        let duplicateResult = $(".duplicate-content-check .check-result[data-duplicate='true']").length;
        let noneDuplicate = parseInt((1 - duplicateResult / totalResult) * 100);
        $(".duplicate-content-check .total-result span.none-duplicate").text(noneDuplicate + "%");
        $(".duplicate-content-check .total-result span.duplicate").text(100 - parseInt(noneDuplicate) + "%");
        $(".duplicate-content-check .btn-check-duplicate-content").prop("disabled", null);
        $(".result").removeClass("isLoading");
        setTimeout(function () {
            $listOfID.attr("data-value","")
            document.dispatchEvent(new CustomEvent("Guest_Post_Duplicate_Content_Check_Complete", {detail: noneDuplicate}))
        }, 1e3)
    }
}
function cassiopeia_recursion_guest_post_duplicate_content_check(data, currentObj, responseObject = 1) {
  currentObj.uid = $("header").attr("data-uid");
  currentObj.captcha_resolve = $("select[name='captcha-resolve']").val();
    if (responseObject === 1) {
        chrome.runtime.sendMessage({
            data: data,
            currentObj: currentObj,
            type: "GuestPostDuplicateContentCheck"
        }, function (response) {
        });
        chrome.runtime.lastError = null
    } else {
        let listOfID = [];
        let running = true;
        if ($listOfID.attr("data-value") !== "") {
            listOfID = JSON.parse($listOfID.attr("data-value"))
        }
        if (listOfID.length > 0) {
            if (listOfID.includes(currentObj.id) !== true) {
                listOfID.push(currentObj.id);
                $listOfID.attr("data-value", JSON.stringify(listOfID));
                cassiopeia_guest_post_duplicate_content_check(data,currentObj,responseObject);
            }
        } else {
            listOfID.push(currentObj.id);
            $listOfID.attr("data-value", JSON.stringify(listOfID));
            cassiopeia_guest_post_duplicate_content_check(data,currentObj,responseObject);
        }

    }
}
function cassiopeia_recursion_duplicate_check(data, currentObj, responseObject = 1) {
  currentObj.uid = $("header").attr("data-uid");
  currentObj.captcha_resolve = $("select[name='captcha-resolve']").val();
    if (responseObject === 1) {
        chrome.runtime.sendMessage({
            data: data,
            currentObj: currentObj,
            type: "CheckDuplicateContent"
        }, function (response) {
        });
        chrome.runtime.lastError = null
    } else {
        let listOfID = [];
        let running = true;
        if ($listOfID.attr("data-value") !== "") {
            listOfID = JSON.parse($listOfID.attr("data-value"))
        }
        if (listOfID.length > 0) {
            if (listOfID.includes(currentObj.id) !== true) {
                listOfID.push(currentObj.id);
                $listOfID.attr("data-value", JSON.stringify(listOfID));
                let checkedItems = $checkedItems.val();
                let totalItems = $totalItems.val();
                if (parseInt(checkedItems) <= parseInt(totalItems) && parseInt(totalItems) !== 0) {
                    checkedItems++;
                    $checkedItems.val(checkedItems);
                    $(".progress-bar-block progress").val(checkedItems);
                    // $(".progress-bar-block .checkedBacklinks").text(checkedItems)
                }
                if (parseInt(checkedItems) === parseInt(totalItems)) {
                    $totalItems.val(0);
                    $checkedItems.val(0);
                    $(".progress-bar-block").removeClass("active")
                }
                let dom_nodes = $($.parseHTML(responseObject.content[0]));
                let gNodes = dom_nodes.find("div.MjjYud");
                let check = false;
                let sources = [];
                let domainName = $(".domain-name input").val();
                let domains = [];
                if (domainName.length > 0) {
                    let domain_list = JSON.parse(domainName);
                    if (domain_list.length > 0) {
                        domain_list.forEach(function (item, index) {
                            let _domain = getDomainFromHref(item.value);
                            domains.push(_domain)
                        })
                    }
                }
                domainName = getDomainFromHref(domainName);
                $.each(gNodes, function (index, _value) {
                    if ($(_value).text().toLowerCase().includes(currentObj.value.toLowerCase())) {
                        let _href = $(_value).find("a").attr("href");
                        let _domain_href = getDomainFromHref(_href);
                        if (domains.length > 0 && domains.includes(_domain_href)) {
                        } else {
                            let _domain_href = getDomainFromHref(_href);
                            let _url = "<a target='_blank' href='" + _href + "'>" + _domain_href + "</a>";
                            if (sources.length <= 5) {
                                sources.push(_url)
                            }
                            check = true
                        }
                    }
                });
                let _result = '<i class="fa fa-check"></i>';
                if (check === true) {
                    _result = '<i class="fa fa-times"></i>'
                }
                let element = $("td[data-id='" + currentObj.id + "']").parent().find(".check-result");
                let element_top = $("td[data-id=" + currentObj.id + "]").parent().find(".check-result").position().top;
                $(".t-body").scrollTop(element_top - 60);
                let elementSource = $("td[data-id='" + currentObj.id + "']").parent().find(".sources");
                element.html(_result);
                elementSource.html("" + sources.join(" "));
                element.attr("data-duplicate", check);
                if (check) {
                    element.attr("data-value", 1)
                } else {
                    element.attr("data-value", 0)
                }
                delete data[currentObj.index];
                if (Object.keys(data).length > 0) {
                    currentObj = data[Object.keys(data)[0]];
                    cassiopeia_recursion_duplicate_check(data, currentObj);
                    $(".progress-bar-block progress").val(checkedItems);
                } else {
                    let totalResult = $(".check-result").length;
                    let duplicateResult = $(".check-result[data-duplicate='true']").length;
                    let noneDuplicate = parseInt((1 - duplicateResult / totalResult) * 100);
                    $(".total-result span.none-duplicate").text(noneDuplicate + "%");
                    $(".total-result span.duplicate").text(100 - parseInt(noneDuplicate) + "%");
                    $(".btn-check-duplicate-content").prop("disabled", null);
                    $(".result").removeClass("isLoading");
                    setTimeout(function () {
                        $(".t-body").scrollTop(0);
                        $listOfID.attr("data-value","")
                        alert("Đã kiểm tra xong!")
                    }, 1e3)
                }
            }
        } else {
            listOfID.push(currentObj.id);
            $listOfID.attr("data-value", JSON.stringify(listOfID));
            let checkedItems = $checkedItems.val();
            let totalItems = $totalItems.val();
            if (parseInt(checkedItems) <= parseInt(totalItems) && parseInt(totalItems) !== 0) {
                checkedItems++;
                $checkedItems.val(checkedItems);
                $(".progress-bar-block progress").val(checkedItems);
                // $(".progress-bar-block .checkedBacklinks").text(checkedItems)
            }
            if (parseInt(checkedItems) === parseInt(totalItems)) {
                $totalItems.val(0);
                $checkedItems.val(0);
                $(".progress-bar-block").removeClass("active")
            }
            let dom_nodes = $($.parseHTML(responseObject.content[0]));
            let gNodes = dom_nodes.find("div.MjjYud");
            let check = false;
            let sources = [];
            let domainName = $(".domain-name input").val();
            let domains = [];
            if (domainName.length > 0) {
                let domain_list = JSON.parse(domainName);
                if (domain_list.length > 0) {
                    domain_list.forEach(function (item, index) {
                        let _domain = getDomainFromHref(item.value);
                        domains.push(_domain)
                    })
                }
            }
            domainName = getDomainFromHref(domainName);
            $.each(gNodes, function (index, _value) {
                if ($(_value).text().toLowerCase().includes(currentObj.value.toLowerCase())) {
                    let _href = $(_value).find("a").attr("href");
                    let _domain_href = getDomainFromHref(_href);
                    if (domains.length > 0 && domains.includes(_domain_href)) {
                    } else {
                        let _domain_href = getDomainFromHref(_href);
                        let _url = "<a target='_blank' href='" + _href + "'>" + _domain_href + "</a>";
                        if (sources.length <= 5) {
                            sources.push(_url)
                        }
                        check = true
                    }
                }
            });
            let _result = '<i class="fa fa-check"></i>';
            if (check === true) {
                _result = '<i class="fa fa-times"></i>'
            }
            let element = $("td[data-id='" + currentObj.id + "']").parent().find(".check-result");
            let element_top = $("td[data-id=" + currentObj.id + "]").parent().find(".check-result").position().top;
            $(".t-body").scrollTop(element_top - 60);
            let elementSource = $("td[data-id='" + currentObj.id + "']").parent().find(".sources");
            element.html(_result);
            elementSource.html("" + sources.join(" "));
            element.attr("data-duplicate", check);
            if (check) {
                element.attr("data-value", 1)
            } else {
                element.attr("data-value", 0)
            }
            delete data[currentObj.index];
            if (Object.keys(data).length > 0) {
                currentObj = data[Object.keys(data)[0]];
                cassiopeia_recursion_duplicate_check(data, currentObj);
                $(".progress-bar-block progress").val(checkedItems);
            } else {
                let totalResult = $(".check-result").length;
                let duplicateResult = $(".check-result[data-duplicate='true']").length;
                let noneDuplicate = parseInt((1 - duplicateResult / totalResult) * 100);
                $(".total-result span.none-duplicate").text(noneDuplicate + "%");
                $(".total-result span.duplicate").text(100 - parseInt(noneDuplicate) + "%");
                $(".btn-check-duplicate-content").prop("disabled", null);
                $(".result").removeClass("isLoading");
                setTimeout(function () {
                    $(".t-body").scrollTop(0);
                    $listOfID.attr("data-value","")
                    alert("Đã kiểm tra xong!");
                }, 1e3)
            }
        }

    }
}

function cassiopeia_recursion_backlink_indexed_check(data, currentObj, responseObject = 1) {
  currentObj.uid = $("header").attr("data-uid");
  currentObj.captcha_resolve = $("select[name='captcha-resolve']").val();
    if (responseObject === 1) {
        chrome.runtime.sendMessage({
            data: data,
            currentObj: currentObj,
            type: "CheckBackLinkIndexed"
        }, function (response) {
        });
        chrome.runtime.lastError = null
    } else {
        let indexed = 0;
        let indexedText = "Không";
        let dom_nodes = $($.parseHTML(responseObject.content[0]));
        let gNodes = dom_nodes.find("div.MjjYud");
        let source = currentObj.source;
        let listOfID = [];
        let running = true;
        if ($listOfID.attr("data-value") !== "") {
            listOfID = JSON.parse($listOfID.attr("data-value"))
        }
        if (listOfID.length > 0) {
            if (listOfID.includes(currentObj.id) !== true) {
                listOfID.push(currentObj.id);
                $listOfID.attr("data-value", JSON.stringify(listOfID));
            }
        }else{
            listOfID.push(currentObj.id);
            $listOfID.attr("data-value", JSON.stringify(listOfID));
        }
        if (gNodes.length >= 1) {
            $.each(gNodes, function (index, _value) {
                if ($(_value).find("a").length) {
                    let a_tag = $(_value).find("a");
                    let _href = $(_value).find("a").attr("href");
                    let anchor = $(_value).find("a").text();
                    if (typeof _href !== "undefined" && _href !== false) {
                        // let _domain_href = getDomainFromHref(_href);
                        if (source.toLowerCase()==_href.toLowerCase()) {
                            // console.log("a_tag",a_tag);
                            // console.log("anchor",anchor);
                            // console.log("_href",_href);
                            // console.log("source",source);
                            indexed = 1;
                            indexedText = "Indexed";
                        }
                    }
                }
            });
        }
        let finished = false;
        if(indexed==1){
          finished = true;
        }else{
          if(currentObj.check_key_index<2){
            currentObj.check_key_index++;
            chrome.runtime.sendMessage({
              data: data,
              currentObj: currentObj,
              type: "CheckBackLinkIndexed"
            }, function (response) {
            });
            chrome.runtime.lastError = null
          }else{
            finished = true;
          }
        }
        if(finished){
          $.ajax({
            method: "POST",
            url: "https://seominisuite.com/cassiopeia/ajax",
            data: {cmd: "user-backlink-check-indexed-response", id: responseObject.id, indexed: indexed},
            success: function (result) {
              if (result.response == "EXPIRED") {
                start = false;
                running = false;
                setTimeout(function () {
                  document.dispatchEvent(new CustomEvent("UserExpired", {detail: "123"}))
                }, 500)
              } else if (result.response == "LIMITED") {
                start = false;
                running = false;
                setTimeout(function () {
                  document.dispatchEvent(new CustomEvent("UserLimited", {detail: "123"}))
                }, 500)
              } else {
                let html = "";
                $.each(JSON.parse(result.values), function (index, value) {
                  t = renderBacklinkRow(value, currentObj.stt);
                  html += t
                });
                $(html).replaceAll(".page-user-backlink-project-detail tr[data-nid=" + currentObj.id + "]");
                let element = $("tr[data-nid=" + currentObj.id + "]").position().top;
                $(".t-body").scrollTop(element - 60);
                let checkedItems = $checkedItems.val();
                let totalItems = $totalItems.val();
                if (parseInt(checkedItems) <= parseInt(totalItems) && parseInt(totalItems) !== 0) {
                  checkedItems++;
                  $checkedItems.val(checkedItems);
                  $(".progress-bar-block progress").val(checkedItems);
                  $(".progress-bar-block .checkedBacklinks").text(checkedItems)
                }
                if (parseInt(checkedItems) == parseInt(totalItems)) {
                  $totalItems.val(0);
                  $checkedItems.val(0);
                  $(".progress-bar-block").removeClass("active")
                }
                delete data[currentObj.index];
                if (Object.keys(data).length > 0) {
                  currentObj = data[Object.keys(data)[0]];
                  currentObj.check_key_index = 0;
                  cassiopeia_recursion_backlink_indexed_check(data, currentObj)
                } else {
                  setTimeout(function () {
                    $.ajax({
                      method: "POST",
                      url: "https://seominisuite.com/cassiopeia/ajax",
                      data: {
                        cmd: "user-check-indexed-response-complete",
                        listOfID: JSON.stringify(listOfID),
                        value: 1
                      },
                      success: function (result) {
                        location.reload()
                      }
                    })
                  }, 1e3)
                }
              }
            },
            error: function (textStatus, errorThrown) {
              let checkedItems = $checkedItems.val();
              let totalItems = $totalItems.val();
              if (parseInt(checkedItems) <= parseInt(totalItems) && parseInt(totalItems) !== 0) {
                checkedItems++;
                $checkedItems.val(checkedItems);
                $(".progress-bar-block progress").val(checkedItems);
                $(".progress-bar-block .checkedBacklinks").text(checkedItems)
              }
              if (parseInt(checkedItems) == parseInt(totalItems)) {
                $totalItems.val(0);
                $checkedItems.val(0);
                $(".progress-bar-block").removeClass("active")
              }
              delete data[currentObj.index];
              if (Object.keys(data).length > 0) {
                currentObj = data[Object.keys(data)[0]];
                currentObj.check_key_index = 0;
                cassiopeia_recursion_backlink_indexed_check(data, currentObj)
              } else {
                setTimeout(function () {
                  $.ajax({
                    method: "POST",
                    url: "https://seominisuite.com/cassiopeia/ajax",
                    data: {
                      cmd: "user-check-indexed-response-complete",
                      listOfID: JSON.stringify(listOfID),
                      value: 1
                    },
                    success: function (result) {
                      location.reload()
                    }
                  })
                }, 1e3)
              }
            }
          })
        }
    }
}

function cassiopeia_recursion_backlink_check(data, currentObj, responseObject = 1) {
    if (responseObject === 1) { // send message to SW
        chrome.runtime.sendMessage({data: data, currentObj: currentObj, type: "CheckBackLink"}, function (response) {});
        chrome.runtime.lastError = null
    } else {
        // console.log("responseObject",responseObject);
        let listOfID = [];
        let running = true;
        if ($listOfID.attr("data-value") !== "") {
            listOfID = JSON.parse($listOfID.attr("data-value"))
        }
        if (listOfID.length > 0) {
            if (listOfID.includes(currentObj.id) !== true) {
                listOfID.push(currentObj.id);
                $listOfID.attr("data-value", JSON.stringify(listOfID));
                checkBacklinkResponse(currentObj,responseObject,running);
            } else {
                let __data = responseObject;
                if(responseObject.status===200){
                    let responseText = responseObject.responseText[0];
                    let dom_nodes = $($.parseHTML(responseText));
                    let gNodes = dom_nodes.find("a[href*='" + currentObj.domain + "']");
                    let _indexed = 0;
                    if (gNodes.length !== 0) {
                        let backlink = [];
                        for (let i = 0; i < gNodes.length; i++) {
                            let temp = {};
                            let isInContent = 0;
                            let gNode = gNodes[i];
                            let gNodeParent = $(gNode).parents("p");
                            let gNodeGrandParent = $(gNodeParent).parent();
                            if (gNodeGrandParent.length > 0 && gNodeGrandParent.find("p").length > 1) {
                                isInContent = 1
                            }
                            let _href = $(gNode).attr("href");
                            let _rel = $(gNode).attr("rel");
                            let _anchorText = $(gNode).text();
                            temp["isInContent"] = isInContent;
                            temp["_href"] = _href;
                            temp["_rel"] = _rel;
                            temp["_anchorText"] = _anchorText;
                            backlink.push(temp)
                        }
                        __data["backlink"] = backlink
                    }
                }
                $.ajax({
                    method: "POST",
                    url: "https://seominisuite.com/cassiopeia/ajax",
                    data: {cmd: "user-backlink-check-response", responseObject: JSON.stringify(__data)},
                    success: function (result) {
                        if (result.response == "EXPIRED") {
                            start = false;
                            running = false;
                            setTimeout(function () {
                                document.dispatchEvent(new CustomEvent("UserExpired", {detail: "123"}))
                            }, 500)
                        } else if (result.response == "LIMITED") {
                            start = false;
                            running = false;
                            setTimeout(function () {
                                document.dispatchEvent(new CustomEvent("UserLimited", {detail: "123"}))
                            }, 500)
                        } else {
                            if (result.response != "FAIL") {
                                let html = "";
                                $.each(JSON.parse(result.values), function (index, value) {
                                    t = renderBacklinkRow(value, currentObj.stt);
                                    html += t
                                });
                                $(html).replaceAll(".page-user-backlink-project-detail tr[data-nid=" + currentObj.id + "]")
                            }
                        }
                    }
                });
                if (running) {
                    if (currentObj.recheck == true) {
                        let checkedItems = $checkedItems.val();
                        let totalItems = $totalItems.val();
                        if (parseInt(checkedItems) <= parseInt(totalItems) && parseInt(totalItems) !== 0) {
                            checkedItems++;
                            $checkedItems.val(checkedItems);
                            $(".progress-bar-block progress").val(checkedItems);
                            $(".progress-bar-block .checkedBacklinks").text(checkedItems)
                        }
                        if (parseInt(checkedItems) == parseInt(totalItems)) {
                            $totalItems.val(0);
                            $checkedItems.val(0);
                            $(".progress-bar-block").removeClass("active");
                            $(".result").removeClass("loading");
                            setTimeout(function () {
                                $.ajax({
                                    method: "POST",
                                    url: "https://seominisuite.com/cassiopeia/ajax",
                                    data: {
                                        cmd: "user-backlink-check-response-complete",
                                        value: 1,
                                        listOfID: JSON.stringify(listOfID),
                                        responseObject: JSON.stringify(responseObject)
                                    },
                                    success: function (result) {
                                        if (result.response == "EXPIRED") {
                                            start = false;
                                            running = false;
                                            setTimeout(function () {
                                                document.dispatchEvent(new CustomEvent("UserExpired", {detail: "123"}))
                                            }, 500)
                                        } else {
                                            location.reload()
                                        }
                                    }
                                })
                            }, 1e3)
                        }
                    }
                }
            }
        } else {
            listOfID.push(currentObj.id);
            $listOfID.attr("data-value", JSON.stringify(listOfID));
            checkBacklinkResponse(currentObj,responseObject,running);
        }
    }
}
function cassiopeia_recursion_getTels(data, currentObj, responseObject = 1) {
    if (responseObject === 1) { // send message to SW
        chrome.runtime.sendMessage({data: data, currentObj: currentObj, type: "getTels"}, function (response) {});
        chrome.runtime.lastError = null
    } else {
      setTimeout(function() {
        let data = {};
        data.data = data;
        data.currentObj = currentObj;
        data.responseObject = responseObject;
        document.dispatchEvent(new CustomEvent('getTelsResponse', {
          detail: data
        }));
      }, 500);
    }
}
async function checkBacklinkResponse(currentObj,responseObject,running){
    let checkedItems = $checkedItems.val();
    let totalItems = $totalItems.val();
    if (parseInt(checkedItems) <= parseInt(totalItems) && parseInt(totalItems) !== 0) {
        checkedItems++;
        $checkedItems.val(checkedItems);
        $(".progress-bar-block progress").val(checkedItems);
        $(".progress-bar-block .checkedBacklinks").text(checkedItems)
    }
    let __data = responseObject;
    if(responseObject.status===200){
        let responseText = responseObject.responseText[0];
        let dom_nodes = $($.parseHTML(responseText));
        let gNodes = dom_nodes.find("a[href*='" + currentObj.domain + "']");
        // console.log("currentObj.domain",currentObj.domain);
        // console.log("gNodes",gNodes);
        // console.log("dom_nodes",dom_nodes);
        let _indexed = 0;
        if (gNodes.length !== 0) {
            let backlink = [];
            for (let i = 0; i < gNodes.length; i++) {
                let temp = {};
                let isInContent = 0;
                let gNode = gNodes[i];
                let gNodeParent = $(gNode).parents("p");
                let gNodeGrandParent = $(gNodeParent).parent();
                if (gNodeGrandParent.length > 0 && gNodeGrandParent.find("p").length > 1) {
                    isInContent = 1
                }
                let _href = $(gNode).attr("href");
                let _rel = $(gNode).attr("rel");
                let _anchorText = $(gNode).text();
                temp["isInContent"] = isInContent;
                temp["_href"] = _href;
                temp["_rel"] = _rel;
                temp["_anchorText"] = _anchorText;
                backlink.push(temp)
            }
            __data["backlink"] = backlink
        }
    }

    // __data["currentObj"] = currentObj;
    // console.log("backlink",backlink);
    // return false;
    $.ajax({
        method: "POST",
        url: "https://seominisuite.com/cassiopeia/ajax",
        data: {cmd: "user-backlink-check-response", responseObject: JSON.stringify(__data)},
        success: async function (result) {
            if (result.response == "EXPIRED") {
                start = false;
                running = false;
                setTimeout(function () {
                    document.dispatchEvent(new CustomEvent("UserExpired", {detail: "123"}))
                }, 500)
            } else if (result.response == "LIMITED") {
                start = false;
                running = false;
                setTimeout(function () {
                    document.dispatchEvent(new CustomEvent("UserLimited", {detail: "123"}))
                }, 500)
            } else {
                if (result.response != "FAIL") {
                    let html = "";
                    $.each(JSON.parse(result.values), function (index, value) {
                        t = renderBacklinkRow(value, currentObj.stt);
                        html += t
                    });
                    $(html).replaceAll(".page-user-backlink-project-detail tr[data-nid=" + currentObj.id + "]");
                    let element = $("tr[data-nid=" + currentObj.id + "]");
                    if (element.length > 0) {
                        $(".t-body").scrollTop(element.position().top)
                    }
                }
            }
        }
    });
    let listOfID = JSON.parse($listOfID.attr("data-value"))
    if (running) {
        if (parseInt(checkedItems) == parseInt(totalItems)) {
            if (recheck.length > 0) {
                recheckTotal = recheck.length;
                $totalItems.val(recheckTotal);
                $checkedItems.val(0);
                $(".progress-bar-block progress").attr("max", recheckTotal);
                $(".progress-bar-block .totalBacklinks").text(recheckTotal);
                let newdata = {};
                let stt = 0;
                let slice = {};
                $.each(recheck, function (index, _value) {
                    slice[index] = _value;
                    if ((index + 1) % 5 === 0) {
                        newdata[stt] = slice;
                        slice = {};
                        stt++
                    }
                });
                newdata[stt] = slice;
                let i = 0;
                while (i <= stt) {
                    let group = newdata[i];
                    let groupSize = Object.size(group);
                    // console.log("recheck",(performance.now() - now)/1000);
                    $.each(group, function (index, _value) {
                        chrome.runtime.sendMessage({
                            data: _value,
                            currentObj: _value,
                            delay: (performance.now() - now)/1000,
                            type: "CheckBackLink"
                        }, function (response) {
                        });
                        chrome.runtime.lastError = null
                    });
                    wait(2e3);
                    i++;
                    diff = (performance.now() - now)/1000;
                    if(diff>=295){
                        delay = 0;
                        // console.log("wait 40");
                        await wait(40000);
                        now = performance.now();
                    }
                }
            } else {
                $totalItems.val(0);
                $checkedItems.val(0);
                $(".progress-bar-block").removeClass("active");
                $(".result").removeClass("loading");
                setTimeout(function () {
                    $.ajax({
                        method: "POST",
                        url: "https://seominisuite.com/cassiopeia/ajax",
                        data: {
                            cmd: "user-backlink-check-response-complete",
                            value: 1,
                            listOfID: JSON.stringify(listOfID),
                            responseObject: JSON.stringify(responseObject)
                        },
                        success: function (result) {
                            if (result.response == "EXPIRED") {
                                start = false;
                                running = false;
                                setTimeout(function () {
                                    document.dispatchEvent(new CustomEvent("UserExpired", {detail: "123"}))
                                }, 500)
                            } else if (result.response == "LIMITED") {
                                start = false;
                                running = false;
                                setTimeout(function () {
                                    document.dispatchEvent(new CustomEvent("UserLimited", {detail: "123"}))
                                }, 500)
                            } else {
                                location.reload()
                            }
                        }
                    })
                }, 1e3)
            }
        }
    }
}
function keywordResponse(data,currentObj,responseObject,running){
    let checkedItems = $checkedItems.val();
    let totalItems = $totalItems.val();
    if (parseInt(checkedItems) <= parseInt(totalItems) && parseInt(totalItems) !== 0) {
        checkedItems++;
        $checkedItems.val(checkedItems);
        $(".progress-bar-block progress").val(checkedItems);
        $(".progress-bar-block .checkedBacklinks").text(checkedItems)
    }
    if (parseInt(checkedItems) == parseInt(totalItems)) {
        $totalItems.val(0);
        $checkedItems.val(0);
        $(".progress-bar-block").removeClass("active")
    }
    let domain = $("#project_domain").val();
    domain = getDomainFromHref(domain);
    let check = false;
    let dom_nodes = $($.parseHTML(responseObject.content[0]));
    // document.dispatchEvent(new CustomEvent("keywordCheckResponse", {detail: responseObject.content[0]}));
    // return;
    // let gNodes = dom_nodes.find("div#search>div>div>div");
    let _index;
    let result = {};
    let stt = 0;
    let minIndex;
    let minHref;
    let lastKey = 0;
    let gNodes = dom_nodes.find("div#search .MjjYud>div");
    let _gNodes = dom_nodes.find("block-component .MjjYud>div");
    let sNodes = [];
    let _sNodes = [];
    // console.log("_gNodes",_gNodes);
    // return;
    if(_gNodes.length){
        $.each(_gNodes, function (index, _value) {
            let flag = true;
            let divNodes = $(_value).find("div");
            let gImgNodes = $(_value).find("g-img");
            let aNodes = $(_value).find("a");
            if(gImgNodes.length){
                // flag = false;
            }
            if(aNodes.length<1){
                flag = false;
            }
            // if(flag){
                $.each(divNodes, function (_index, __value) {
                    var attr = $(this).attr('data-vid');
                    let href = $(this).attr("href");
                    if (typeof attr !== typeof undefined && attr !== false) {
                        flag = false;
                        return;
                    }
                });
            // }
            if(flag){
                _sNodes.push(_value);
            }
        });
    }

    let _fNodes = {};
    $.each(_sNodes, function (index, _value) {
        if ($(_value).find("a").length) {
            let a_value = $(_value).find("a:first-child");
            let _href = $(a_value).attr("href");
            if (typeof _href !== "undefined" && _href !== false && _href.toLowerCase().includes('google')!==true) {
                _fNodes[_href] = a_value;
            }
        }
    });
    // console.log("_fNodes",_fNodes);
    // return;
    if(gNodes.length){
        $.each(gNodes, function (index, _value) {
            let flag = true;
            let divNodes = $(_value).find("div");
            let gImgNodes = $(_value).find("g-img");
            let aNodes = $(_value).find("a");
            if(gImgNodes.length){
                // flag = false;
            }
            if(aNodes.length<1){
                flag = false;
            }
            // if(flag){
                $.each(divNodes, function (_index, __value) {
                    var attr = $(this).attr('data-vid');
                    let href = $(this).attr("href");
                    if (typeof attr !== typeof undefined && attr !== false) {
                        flag = false;
                        return;
                    }
                });
            // }
            if(flag){
                sNodes.push(_value);
            }
        });
    }
    let fNodes = {};
    $.each(sNodes, function (index, _value) {
        if ($(_value).find("a").length) {
            let a_value = $(_value).find("a:first-child");
            let _href = $(a_value).attr("href");
            if (typeof _href !== "undefined" && _href !== false && _href.toLowerCase().includes('google')!==true) {
               fNodes[_href] = a_value;
            }
        }
    });
    let __index=0;
    // console.log("_fNodes",_fNodes);
    // console.log("fNodes",fNodes);
    let finalNodes = Object.assign(_fNodes,fNodes);
    // console.log("finalNodes",finalNodes);
    // return;
    $.each(finalNodes, function (index, a_value) {
        let flag = true;
        let _href = $(a_value).attr("href");
        // console.log("_href",_href);
        if (typeof _href !== "undefined" && _href !== false) {
            if(_href.indexOf("/search")===0){
               flag=false;
            }else{
                let _domain_href = getDomainFromHref(_href);
                // console.log("domain",domain);
                // console.log("_domain_href",_domain_href);
                // console.log("_domain_href",_href);
                if (_domain_href.toLowerCase().includes(domain.toLowerCase()) && domain.toLowerCase().includes(_domain_href.toLowerCase())) {
                    if (stt === 0) {
                        minIndex = __index;
                        minHref = _href;
                    } else {
                        if (parseInt(__index) < parseInt(minIndex)) {
                            minIndex = __index;
                            minHref = _href;
                        }
                    }
                    check = true;
                    stt++;
                  console.log("minIndex",minIndex)
                  console.log("a_value",a_value)
                }
            }
        }
        if(flag){
            __index++;
        }
    });
  // console.log("minIndex",minIndex);
  // return;
    // return;
    if (check == true) {
        result["index"] = minIndex;
        result["url"] = minHref
    }
    if (Object.keys(data).length == 1) {
        lastKey = 1
    }
    let listOfID = JSON.parse($listOfID.attr("data-value"))
    if (check !== true) {
        $.ajax({
            method: "POST",
            url: "https://seominisuite.com/cassiopeia/ajax",
            data: {cmd: "user-key-check", id: currentObj.id, index: -1, lastKey: lastKey, stt: currentObj.stt},
            success: function (result) {
                if (result.response.status == "EXPIRED") {
                    setTimeout(function () {
                        document.dispatchEvent(new CustomEvent("UserExpired", {detail: "123"}))
                    }, 500)
                } else if (result.response.status == "LIMITED") {
                    setTimeout(function () {
                        document.dispatchEvent(new CustomEvent("UserLimited", {detail: "123"}))
                    }, 500)
                } else {
                    $(result.html).replaceAll(".page-keyword-project-detail tr[data-nid=" + currentObj.id + "]");
                    let element = $("tr[data-nid=" + currentObj.id + "]").position().top;
                    $(".t-body").scrollTop(element - 60);
                    delete data[currentObj.index];
                    if (Object.keys(data).length > 0) {
                        currentObj = data[Object.keys(data)[0]];
                        cassiopeia_recursion_keyword_check(data, currentObj)
                    } else {
                        setTimeout(function () {
                            $.ajax({
                                method: "POST",
                                url: "https://seominisuite.com/cassiopeia/ajax",
                                data: {
                                    cmd: "check-complete",
                                    listOfID: JSON.stringify(listOfID),
                                    value: 1
                                },
                                success: function (result) {
                                    location.reload()
                                }
                            })
                        }, 1e3)
                    }
                }
            },
            error: function (textStatus, errorThrown) {
                $(result.html).replaceAll(".page-keyword-project-detail tr[data-nid=" + currentObj.id + "]");
                let element = $("tr[data-nid=" + currentObj.id + "]").position().top;
                $(".t-body").scrollTop(element - 60);
                delete data[currentObj.index];
                if (Object.keys(data).length > 0) {
                    currentObj = data[Object.keys(data)[0]];
                    cassiopeia_recursion_keyword_check(data, currentObj)
                } else {
                    setTimeout(function () {
                        $.ajax({
                            method: "POST",
                            url: "https://seominisuite.com/cassiopeia/ajax",
                            data: {cmd: "check-complete", value: 1,
                                listOfID: JSON.stringify(listOfID),},
                            success: function (result) {
                                location.reload()
                            }
                        })
                    }, 1e3)
                }
            }
        })
    } else {
        $.ajax({
            method: "POST",
            url: "https://seominisuite.com/cassiopeia/ajax",
            data: {
                cmd: "user-key-check",
                id: currentObj.id,
                stt: currentObj.stt,
                index: JSON.stringify(result),
                lastKey: lastKey
            },
            success: function (result) {
                if (result.response.status == "EXPIRED") {
                    setTimeout(function () {
                        document.dispatchEvent(new CustomEvent("UserExpired", {detail: "123"}))
                    }, 500)
                } else if (result.response.status == "LIMITED") {
                    setTimeout(function () {
                        document.dispatchEvent(new CustomEvent("UserLimited", {detail: "123"}))
                    }, 500)
                } else {
                    $(result.html).replaceAll(".page-keyword-project-detail tr[data-nid=" + currentObj.id + "]");
                    let element = $("tr[data-nid=" + currentObj.id + "]").position().top;
                    $(".t-body").scrollTop(element - 60);
                    delete data[currentObj.index];
                    if (Object.keys(data).length > 0) {
                        currentObj = data[Object.keys(data)[0]];
                        cassiopeia_recursion_keyword_check(data, currentObj)
                    } else {
                        setTimeout(function () {
                            $.ajax({
                                method: "POST",
                                url: "https://seominisuite.com/cassiopeia/ajax",
                                data: {
                                    cmd: "check-complete",
                                    listOfID: JSON.stringify(listOfID),
                                    value: 1
                                },
                                success: function (result) {
                                    location.reload()
                                }
                            })
                        }, 1e3)
                    }
                }
            },
            error: function (textStatus, errorThrown) {
                $(result.html).replaceAll(".page-keyword-project-detail tr[data-nid=" + currentObj.id + "]");
                let element = $("tr[data-nid=" + currentObj.id + "]").position().top;
                $(".t-body").scrollTop(element - 60);
                delete data[currentObj.index];
                if (Object.keys(data).length > 0) {
                    currentObj = data[Object.keys(data)[0]];
                    cassiopeia_recursion_keyword_check(data, currentObj)
                } else {
                    setTimeout(function () {
                        $.ajax({
                            method: "POST",
                            url: "https://seominisuite.com/cassiopeia/ajax",
                            data: {
                                cmd: "check-complete",
                                listOfID: JSON.stringify(listOfID),
                                value: 1
                            },
                            success: function (result) {
                                location.reload()
                            }
                        })
                    }, 1e3)
                }
            }
        })
    }
}
function cassiopeia_recursion_get_url_check(data, currentObj, responseObject = null) {
  currentObj.uid = $("header").attr("data-uid");
  currentObj.captcha_resolve = $("select[name='captcha-resolve']").val();
    if (responseObject === null) {
        chrome.runtime.sendMessage({data: data, currentObj: currentObj, type: "GetUrl"}, function (response) {
        });
        chrome.runtime.lastError = null
    } else {
        // let temp = $("textarea[name='exclude-urls']").val();
        let include_urls = $("textarea[name='include-urls']").val().trim().split("\n");
        let exclude_urls = $("textarea[name='exclude-urls']").val().trim().split("\n");
        let checkedItems = $checkedItems.val();
        let totalItems = $totalItems.val();
        if (parseInt(checkedItems) <= parseInt(totalItems) && parseInt(totalItems) !== 0) {
            checkedItems++;
            $checkedItems.val(checkedItems);
            $(".progress-bar-block progress").val(checkedItems);
            $(".progress-bar-block .checkedBacklinks").text(checkedItems)
        }
        if (parseInt(checkedItems) == parseInt(totalItems)) {
            $totalItems.val(0);
            $checkedItems.val(0);
            // $(".progress-bar-block").removeClass("active")
        }
        // console.log("exclude_urls",exclude_urls);
        let dom_nodes = $($.parseHTML(responseObject.content[0]));
        let gNodes = dom_nodes.find("div#search div.MjjYud");
        let response_data = {};
      console.log("gNodes",gNodes);
        $.each(gNodes, function (index, _value) {
            // if ($(_value).find("div.g").length) {
                if ($(_value).find("a").length) {
                    let flag = true;
                    let _href = $(_value).find("a").attr("href");
                    let _anchor = $(_value).find("a").text();
                    if(!_href) return; // Thêm kiểm tra để tránh lỗi undefined
                    if(exclude_urls.length>0){
                        $.each(exclude_urls,function (__index,__value) {
                            let _domain = getDomainFromHref(__value);
                            if(_domain!==""&&_domain!==undefined&&_href.toLowerCase().includes(_domain.toLowerCase())===true){
                                flag = false;
                                // return false;
                            }
                            // console.log("flag",flag);
                        });
                    }

                    if(flag){
                        if($("input[name='include']").is(":checked")){
                            $.each(include_urls,function (__index,__value) {
                                let _domain = getDomainFromHref(__value);
                                if(_domain!==""&&__value!==undefined&&_href.toLowerCase().includes(_domain.toLowerCase())===true){
                                    response_data[_href] = _anchor;
                                }
                            });
                        }else{
                            response_data[_href] = _anchor;
                        }
                        // response_data[_href] = _anchor;
                    }
                }
            // }
        });
        $.ajax({
            method: "POST",
            url: "https://seominisuite.com/cassiopeia/ajax",
            data: {
                cmd: "get-url-response",
                data:response_data,
                index:currentObj.index,
                key:currentObj.key,
            },
            success: function (result) {
                delete data[currentObj.index];
                if (Object.keys(data).length > 0) {
                    currentObj = data[Object.keys(data)[0]];
                    cassiopeia_recursion_get_url_check(data, currentObj)
                } else {
                    setTimeout(function () {
                        $.ajax({
                            method: "POST",
                            url: "https://seominisuite.com/cassiopeia/ajax",
                            data: {
                                cmd: "check-complete",
                                // listOfID: JSON.stringify(listOfID),
                                value: 1
                            },
                            success: function (result) {
                                location.href = "https://seominisuite.com/get-url/result";
                            }
                        })
                    }, 1e3)
                }
            },
            error: function (textStatus, errorThrown) {
                delete data[currentObj.index];
                if (Object.keys(data).length > 0) {
                    currentObj = data[Object.keys(data)[0]];
                    cassiopeia_recursion_get_url_check(data, currentObj)
                } else {
                    setTimeout(function () {
                        $.ajax({
                            method: "POST",
                            url: "https://seominisuite.com/cassiopeia/ajax",
                            data: {
                                cmd: "check-complete",
                                // listOfID: JSON.stringify(listOfID),
                                value: 1
                            },
                            success: function (result) {
                                // location.reload()
                            }
                        })
                    }, 1e3)
                }
            }
        });
    }
}
function check_user_captcha_resolve_info(){
  $.ajax({
    method: "POST",
    url: "https://seominisuite.com/cassiopeia-captcha/resolve/get-info",
    success: function (result) {
      return JSON.parse(result)
    }
  });
}
function cassiopeia_recursion_keyword_check(data, currentObj, responseObject = null) {
    currentObj.uid = $("header").attr("data-uid");
    currentObj.captcha_resolve = $("select[name='captcha-resolve']").val();
    if (responseObject === null) {
        chrome.runtime.sendMessage({data: data, currentObj: currentObj, type: "KeyCheck"}, function (response) {
        });
        chrome.runtime.lastError = null
    } else {
        // console.log("respoQnseObject",responseObject);
        // console.log("data",data);
        let listOfID = [];
        let running = true;
        if ($listOfID.attr("data-value") !== "") {
            listOfID = JSON.parse($listOfID.attr("data-value"))
        }
        let _object = {};
        _object.data = data;
        _object.currentObj = currentObj;
        _object.responseObject = responseObject;
        // document.dispatchEvent(new CustomEvent('keywordCheckResponse', {detail: JSON.stringify(_object)}));
        if (listOfID.length > 0) {
            // if (listOfID.includes(currentObj.id) !== true) {
                listOfID.push(currentObj.id);
                $listOfID.attr("data-value", JSON.stringify(listOfID));
                keywordResponse(data,currentObj,responseObject,running);
            // }else{
            //     console.log("duplicate");
            // }
        } else {
            listOfID.push(currentObj.id);
            $listOfID.attr("data-value", JSON.stringify(listOfID));
            keywordResponse(data,currentObj,responseObject,running);
        }
    }
}
function cassiopeia_guest_post_website_get_categories(currentObj,responseObject = null){
    if (responseObject === null) {
        chrome.runtime.sendMessage({ currentObj: currentObj, type: "GuestPostWebsiteGetCategories"}, function (response) {
        });
        chrome.runtime.lastError = null
    } else {
        document.dispatchEvent(new CustomEvent('Guest_Post_Website_Get_Categories_Complete', {detail: JSON.stringify(responseObject)}));
    }
}
function removeElement(array, elem) {
    var index = array.indexOf(elem);
    if (index > -1) {
        array.splice(index, 1)
    }
}


function getDomainFromHref(href) {
    let c = "";
    if(href!==undefined){
        let a = href.split("//");
        let d;
        if (a.length < 2) {
            d = a[0]
        } else {
            d = a[1]
        }
        if (d !== null && d !== "" && d !== undefined) {
            let b = d.split("/");
            c = b[0].toLowerCase();
            c = c.replaceAll("www.", "")
        }
    }
    return c
}

async function wait(time) {
    return new Promise(resolve => {
        setTimeout(resolve, time)
    })
}

Object.size = function (obj) {
    let size = 0, key;
    for (key in obj) {
        if (obj.hasOwnProperty(key)) size++
    }
    return size
};

function renderBacklinkRow(value, stt) {
    let is_in_content = "-";
    if (value.is_in_content == 1) {
        is_in_content = "Yes"
    } else if (value.is_in_content == 0) {
        is_in_content = "No"
    }
    let indexed = "-";
    if (value.indexed == 1) {
        indexed = "Yes"
    } else {
        indexed = "No"
    }
    let tags = "";
    if (value.tags !== null) {
        tags = value.tags
    }
    let status = "";
    let errorCode = "";
    let errorMessage = "";
    if (value.status !== null) {
        if (parseInt(value.status) !== 200) {
            switch (parseInt(value.status)) {
                case"404":
                    errorCode = 404;
                    errorMessage = "Lỗi " + errorCode + ": Nguồn đặt Backlink không tồn tại!";
                    break;
                case"999":
                    errorCode = "error";
                    errorMessage = "Lỗi " + errorCode + ": Nguồn đặt Backlink không hợp lệ!";
                    break;
                case"500":
                    errorCode = 500;
                    errorMessage = "Lỗi " + errorCode + ": Nguồn đặt Backlink không hoạt động!";
                    break;
                default:
                    errorCode = value.status;
                    errorMessage = "Lỗi " + errorCode
            }
            errorCode = 'FAIL <i class="fa fa-info-circle" title=\'' + errorMessage + "'></i>"
        } else {
            errorCode = "SUCCESS"
        }
    }
    if (value.id == null) {
        errorCode = "FAIL <i title='Không tìm thấy backlink!' class=\"fa fa-info-circle\" ></i>"
    }
    let t = "<tr data-nid='" + value.bid + "'>\n" + '<td class="col-expand"></td>' + "    <td class='col-checkbox'>" + '<label class="mask-chekbox">\n' + '    <input data-stt="" type="checkbox" name="select" class="" data-domain="" value=\'' + value.bid + "' data-backlink-source='" + value.source + "' >\n" + '    <span class="mask-checked"></span>\n' + "</label>" + "</td>\n" + "    <td class='w-3' style='white-space: nowrap'>" + stt + "</td>\n" + "    <td class='w-18 text-blue'>" + value.source + "</td>\n" + "    <td><span class='loading-item'></span><span class='td-content'>" + value.rel + "</span></td>\n" + "    <td><span class='loading-item'></span><span class='td-content'>" + value.anchor_text + "</span></td>\n" + "    <td class='text-blue'><span class='loading-item'></span><span class='td-content'>" + value.url + "</span></td>\n" + "    <td><span class='loading-item'></span><span class='td-content " + indexed + "'>" + indexed + "</span></td>\n" + "    <td style='    word-break: unset;'>" + tags + "</td>\n" + "    <td>" + value.date_created + "</td>\n" + "    <td><span class='loading-item'></span><span class='td-content'><span title='" + errorMessage + "'>" + errorCode + "</span></span></td>\n" + "</tr>";
    return t
}