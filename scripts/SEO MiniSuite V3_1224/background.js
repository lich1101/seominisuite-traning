var liveTime=0;
var now = 0;
var diff = 0;
var onlytabs = ["https://seominisuite.com/quan-ly-backlink/du-an", "https://seominisuite.com/kiem-tra-dao-van", "https://seominisuite.com/quan-ly-keywords/du-an"];
var check_indexed_keys = {};
check_indexed_keys[0] = "site:";
check_indexed_keys[1] = "";
check_indexed_keys[2] = "inurl:";
function resetAll(tab=null){
    chrome.storage.local.get('key',async function(result){
        let key = result.key;
        key['executeTab'] = null;
        key['request'] = null;
        key['sender'] = null;
        if(tab!==null){
            key['removeTab'] = true;
        }
        key['executeTabTimeout'] = 0;
        // console.log("resetAll");
        chrome.storage.local.set({'key': key},function (e) {

        });
    })
}
// const now = performance.now();
// const keepServiceWorkerActive = () =>
//     dispatchEvent(
//         new CustomEvent('keepactive', {
//             detail: `Active at ${~~(((performance.now() - now) / 1000) / 60)} minutes.`
//         })
//     );
// const handleKeepServiceWorkerActive = (e) => console.log(e.detail);
// addEventListener("keepactive", handleKeepServiceWorkerActive);
// let interval = setInterval(function(e){console.log((performance.now() - now)/1000)}, 1000);
async function cassiopeiaCheckContentPromiseResponse(response,responseText, _request, _sender, waitStatus) {
    if (response !== undefined) {
        chrome.storage.local.get('key',async function(result) {
            let key = result.key;
            let sender = key.sender;
            let request = key.request;
            let executeTabTimeout = key.executeTabTimeout;
            let executeTabDelay = key.executeTabDelay;
            if (responseText === "waiting") {
                let responseObj = {};
                responseObj["status"] = "waiting";
                responseObj["index"] = request.currentObj.index;
                responseObj["id"] = request.currentObj.id;
                responseObj["currentObj"] = request.currentObj;
                responseObj["checkType"] = request.type;
                cassiopeiaSendResponse(request.data, responseObj, sender, "captcha_stop");
                resetAll();
            } else if (responseText === "error") {
                let responseObj = {};
                responseObj["status"] = "error";
                responseObj["index"] = request.currentObj.index;
                responseObj["id"] = request.currentObj.id;
                responseObj["currentObj"] = request.currentObj;
                responseObj["checkType"] = request.type;
                cassiopeiaSendResponse(request.data, responseObj, sender, "captcha_stop");
                resetAll();
            } else {
                if (response.url.includes("sorry/index?continue=https://www.google.com")) { //
                    chrome.tabs.create({url: response.url, active: false},async function (tab) {
                        setVariables(null,null,tab);
                        var editorExtensionId = chrome.runtime.id;
                        const tabId = await tab.id;
                        if (!tab.url) await onTabUrlUpdated(tabId);
                        const results = await chrome.scripting.executeScript(
                            {
                                target: {tabId: tabId},
                                func:setTabId,
                                args: [editorExtensionId,tab],
                            },
                            () => {

                            }
                        );
                        const sss = await chrome.scripting.executeScript(
                            {
                                target: {tabId: tabId, allFrames: true},
                                files: ['js/tab_captcha_2.js'],
                            },
                            (result) => {

                                if (executeTabTimeout !== 0) {
                                    clearTimeout(executeTabTimeout)
                                }
                                executeTabTimeout = setTimeout(function () {
                                    chrome.storage.local.get('key',async function(result){
                                        let executeTab = result.key.executeTab;
                                        let request = result.key.request;
                                        let sender = result.key.sender;
                                        key = result.key;
                                        key['removeTab'] = true;
                                        if (sender && sender.tab.id === sender.tab.id && executeTab && executeTab.id === tabId) {
                                            let responseObj = {};
                                            responseObj["timeout"] = "timeout";
                                            responseObj["index"] = request.currentObj.index;
                                            responseObj["url"] = request.currentObj.url;
                                            responseObj["id"] = request.currentObj.id;
                                            responseObj["stt"] = request.currentObj.stt;
                                            responseObj["currentObj"] = request.currentObj;
                                            responseObj["content"] = new Array(responseText);
                                            cassiopeiaSendResponse(request.data, responseObj, sender, request.type);
                                            resetAll();
                                        }
                                        clearTimeout(executeTabTimeout);
                                        setVariables(null,null,null,0);
                                    });

                                }, executeTabDelay)
                            });
                    })
                } else {
                    chrome.storage.local.get('key',async function(result){
                        let request = result.key.request;
                        let sender = result.key.sender;
                        let responseObj = {};
                        if(request!==null){
                            responseObj["index"] = request.currentObj.index;
                            responseObj["url"] = request.currentObj.url;
                            responseObj["id"] = request.currentObj.id;
                            responseObj["stt"] = request.currentObj.stt;
                            responseObj["currentObj"] = request.currentObj;
                            responseObj["content"] = new Array(responseText);
                            cassiopeiaSendResponse(request.data, responseObj, sender, request.type);
                            resetAll();
                        }
                    })
                }
            }
        })
    }
}
async function cassiopeiaGetUrlPromiseResponse(response,responseText, _request, _sender, waitStatus) {
    if (response !== undefined) {
        chrome.storage.local.get('key',async function(result) {
            let key = result.key;
            let sender = key.sender;
            let request = key.request;
            let executeTabTimeout = key.executeTabTimeout;
            let executeTabDelay = key.executeTabDelay;
            if (responseText === "waiting") {
                let responseObj = {};
                responseObj["status"] = "waiting";
                responseObj["index"] = request.currentObj.index;
                responseObj["id"] = request.currentObj.id;
                responseObj["currentObj"] = request.currentObj;
                responseObj["checkType"] = request.type;
                cassiopeiaSendResponse(request.data, responseObj, sender, "captcha_stop");
                resetAll();
            } else if (responseText === "error") {
                let responseObj = {};
                responseObj["status"] = "error";
                responseObj["index"] = request.currentObj.index;
                responseObj["id"] = request.currentObj.id;
                responseObj["currentObj"] = request.currentObj;
                responseObj["checkType"] = request.type;
                cassiopeiaSendResponse(request.data, responseObj, sender, "captcha_stop");
                resetAll();
            } else {
                if (response.url.includes("sorry/index?continue=https://www.google.com")) {
                    chrome.tabs.create({url: response.url, active: false},async function (tab) {
                        setVariables(null,null,tab);
                        var editorExtensionId = chrome.runtime.id;
                        const tabId = await tab.id;
                        if (!tab.url) await onTabUrlUpdated(tabId);
                        const results = await chrome.scripting.executeScript(
                            {
                                target: {tabId: tabId},
                                func:setTabId,
                                args: [editorExtensionId,tab],
                            },
                            () => {

                            }
                        );
                        const sss = await chrome.scripting.executeScript(
                            {
                                target: {tabId: tabId, allFrames: true},
                                files: ['js/tab_captcha_2.js'],
                            },
                            (result) => {

                                if (executeTabTimeout !== 0) {
                                    clearTimeout(executeTabTimeout)
                                }
                                executeTabTimeout = setTimeout(function () {
                                    chrome.storage.local.get('key',async function(result){
                                        let executeTab = result.key.executeTab;
                                        let request = result.key.request;
                                        let sender = result.key.sender;
                                        key = result.key;
                                        key['removeTab'] = true;
                                        if (sender && sender.tab.id === sender.tab.id && executeTab && executeTab.id === tabId) {
                                            let responseObj = {};
                                            responseObj["timeout"] = "timeout";
                                            responseObj["index"] = request.currentObj.index;
                                            responseObj["url"] = request.currentObj.url;
                                            responseObj["id"] = request.currentObj.id;
                                            responseObj["stt"] = request.currentObj.stt;
                                            responseObj["currentObj"] = request.currentObj;
                                            responseObj["content"] = new Array(responseText);
                                            cassiopeiaSendResponse(request.data, responseObj, sender, "GetUrl");
                                            resetAll();
                                        }
                                        clearTimeout(executeTabTimeout);
                                        setVariables(null,null,null,0);
                                    });

                                }, executeTabDelay)
                            });
                    })
                } else {
                    chrome.storage.local.get('key',async function(result){
                        let request = result.key.request;
                        let sender = result.key.sender;
                        let responseObj = {};
                        if(request!==null){
                            responseObj["index"] = request.currentObj.index;
                            responseObj["url"] = request.currentObj.url;
                            responseObj["id"] = request.currentObj.id;
                            responseObj["stt"] = request.currentObj.stt;
                            responseObj["currentObj"] = request.currentObj;
                            responseObj["content"] = new Array(responseText);
                            cassiopeiaSendResponse(request.data, responseObj, sender, "GetUrl");
                            resetAll();
                        }
                    })
                }
            }
        })
    }
}
async function cassiopeiaKeywordPromiseResponse(response,responseText, _request, _sender, waitStatus) {

    if (response !== undefined) {

        chrome.storage.local.get('key',async function(result) {

            let key = result.key;
            let sender = key.sender;
            let request = key.request;
            let executeTabTimeout = key.executeTabTimeout;
            let executeTabDelay = key.executeTabDelay;
            // let responseText;
            // console.log("response")
            if (responseText === "waiting") {
                // console.log("waiting")
                let responseObj = {};
                responseObj["status"] = "waiting";
                responseObj["index"] = request.currentObj.index;
                responseObj["id"] = request.currentObj.id;
                responseObj["currentObj"] = request.currentObj;
                responseObj["checkType"] = request.type;
                cassiopeiaSendResponse(request.data, responseObj, sender, "captcha_stop");
                resetAll();
            } else if (responseText === "error") {
                let responseObj = {};
                responseObj["status"] = "error";
                responseObj["index"] = request.currentObj.index;
                responseObj["id"] = request.currentObj.id;
                responseObj["currentObj"] = request.currentObj;
                responseObj["checkType"] = request.type;
                cassiopeiaSendResponse(request.data, responseObj, sender, "captcha_stop");
                resetAll();
            } else {

                // let responseText = await response.text();
                if (response.url.includes("sorry/index?continue=https://www.google.com")) {

                    chrome.tabs.create({url: response.url, active: false},async function (tab) {
                        setVariables(null,null,tab);
                        var editorExtensionId = chrome.runtime.id;
                        const tabId = await tab.id;
                        if (!tab.url) await onTabUrlUpdated(tabId);
                        const results = await chrome.scripting.executeScript(
                            {
                                target: {tabId: tabId},
                                func:setTabId,
                                args: [editorExtensionId,tab],
                            },
                            () => {

                            }
                        );
                        const sss = await chrome.scripting.executeScript(
                            {
                                target: {tabId: tabId, allFrames: true},
                                files: ['js/tab_captcha_2.js'],
                            },
                            (result) => {

                                if (executeTabTimeout !== 0) {
                                    clearTimeout(executeTabTimeout)
                                }
                                executeTabTimeout = setTimeout(function () {
                                    chrome.storage.local.get('key',async function(result){
                                        let executeTab = result.key.executeTab;
                                        let request = result.key.request;
                                        let sender = result.key.sender;
                                        key = result.key;
                                        key['removeTab'] = true;
                                        if (sender && sender.tab.id === sender.tab.id && executeTab && executeTab.id === tabId) {
                                            let responseObj = {};
                                            responseObj["timeout"] = "timeout";
                                            responseObj["index"] = request.currentObj.index;
                                            responseObj["url"] = request.currentObj.url;
                                            responseObj["id"] = request.currentObj.id;
                                            responseObj["stt"] = request.currentObj.stt;
                                            responseObj["currentObj"] = request.currentObj;
                                            responseObj["content"] = new Array(responseText);
                                            cassiopeiaSendResponse(request.data, responseObj, sender, "KeyCheck");
                                            resetAll();
                                        }
                                        clearTimeout(executeTabTimeout);
                                        setVariables(null,null,null,0);
                                        // chrome.storage.local.set({'key': key},function (e) {
                                        //     if(tab!==null){
                                        //         wait(3000);
                                        //         deleteTab(tab);
                                        //     }
                                        // });
                                    });

                                }, executeTabDelay)
                            });
                    })
                } else {
                    chrome.storage.local.get('key',async function(result){
                        let request = result.key.request;
                        let sender = result.key.sender;
                        let responseObj = {};
                        if(request!==null){
                            responseObj["index"] = request.currentObj.index;
                            responseObj["url"] = request.currentObj.url;
                            responseObj["id"] = request.currentObj.id;
                            responseObj["stt"] = request.currentObj.stt;
                            responseObj["currentObj"] = request.currentObj;
                            responseObj["content"] = new Array(responseText);
                            cassiopeiaSendResponse(request.data, responseObj, sender, "KeyCheck");
                            resetAll();
                        }
                    })
                }
            }
        })
    }else{
        // console.log("undefined");
    }
}
async function sendKeywordResponse(response){

    chrome.storage.local.get('key',async function(result){
        // console.log("---")
        let key = result.key;
        let executeTab = key.executeTab;
        let executeTabTimeout = key.executeTabTimeout;
        let executeTabDelay = key.executeTabDelay;
        let responseText ;
        const longTask = () => new Promise(resolve => resolve(response.text())).then(value => cassiopeiaKeywordPromiseResponse(response,value, key.request, key.sender, true)).catch(_error => console.log("error",_error));
        const timeout = (cb, interval) => () => new Promise(resolve => setTimeout(() => cb(resolve), interval));
        const onTimeout = timeout(resolve => resolve("waiting"), 120000);
        Promise.race([longTask, onTimeout].map(f => f())).then(value => cassiopeiaKeywordPromiseResponse(response,value, key.request, key.sender, true));
        responseText =  await response.text();
        // return


    });
}
function clearTabs(){
    chrome.storage.local.get('key',async function(result){
        let key = result.key;
        key['tabs'] = [];
        chrome.storage.local.set({'key': key});
    })
}
function setVariables(sender=null,request=null,executeTab=null,executeTabTimeout=null,executeTabDelay=null){
    chrome.storage.local.get('key',async function(result){
        let key = result.key;
        if(request!==null){
            key['request'] = request;
        }
        if(sender!==null){
            key['sender'] = sender;
        }
        if(executeTab!==null){
            key['executeTab'] = executeTab;
        }
        if(executeTab!==null){
            key['tabs'].push(executeTab);
        }
        if(executeTabTimeout!==null){
            key['executeTabTimeout'] = executeTabTimeout;
        }
        if(executeTabDelay!==null){
            key['executeTabDelay'] = executeTabDelay;
        }
        chrome.storage.local.set({'key': key});
    })
}
function setTabId(editorExtensionId,tab,uid="") {
    document.body.setAttribute("editorExtensionId", editorExtensionId);
    document.body.setAttribute("tabID", tab.id);
    document.body.setAttribute("uid", uid);
}
async function wait(time) {
    return new Promise(resolve => {
        setTimeout(resolve, time)
    })
}

async function tabDragging(callback) {
    chrome.windows.getAll({populate: true}, function (windows) {
        var window = windows.filter(function (x) {
            return x.type === "normal" && x.focused && x.tabs && x.tabs.length
        })[0];
        if (window === undefined) {
            return
        } else {
            var tab = window.tabs[0];
            chrome.tabs.move(tab.id, {index: tab.index}, function () {
                callback(chrome.runtime.lastError && chrome.runtime.lastError.hasOwnProperty("message") && chrome.runtime.lastError.message.indexOf("dragging") !== -1)
            })
        }
    })
}

async function deleteTab(tab) {
    return new Promise((resolve, reject) => {
        try {
            chrome.tabs.remove(tab.id, function (tabIds) {
                resolve(tabIds)
            });
            removeError()
        } catch (e) {
            reject(e)
        }
    })
}

function removeError() {
    chrome.runtime.lastError = null
}

function getAllTabs(options) {
    return new Promise((resolve, reject) => {
        try {
            chrome.tabs.query(options, function (tabs) {
                resolve(tabs)
            })
        } catch (e) {
            reject(e)
        }
    })
}

function cassiopeiaSendResponse(data, responseObj, sender, type) {
    chrome.tabs.sendMessage(sender.tab.id, {type: type, data: data, responseObj: responseObj}, function (response) {
        chrome.power.releaseKeepAwake();
        removeError()
    })
}

function cassiopeiaPromiseResponseError(error, _request, _sender, waitStatus) {
    let value = "error";
    return cassiopeiaPromiseResponse(value, _request, _sender, false)
}


async function cassiopeiaPromiseResponse(value, _request, _sender, waitStatus) {
    let data = {};
    if (value !== undefined) {
        data["currentObj"] = _request.currentObj;
        if (value === "waiting") {
            data["index"] = _request.currentObj.index;
            data["id"] = _request.currentObj.id;
            data["stt"] = _request.currentObj.stt;
            data["status"] = "TIME_OUT";
            data["waitStatus"] = "waiting";
            cassiopeiaSendResponse(_request.data, data, _sender, _request.type)
        } else if (value === "error") {
            data["index"] = _request.currentObj.index;
            data["id"] = _request.currentObj.id;
            data["stt"] = _request.currentObj.stt;
            data["recheck"] = _request.currentObj.recheck;
            data["waitStatus"] = true;
            data["status"] = "ERROR";
            cassiopeiaSendResponse(_request.data, data, _sender, _request.type)
        } else {
            let status = value.status;
            let responseText = await value.text();
            data["index"] = _request.currentObj.index;
            data["id"] = _request.currentObj.id;
            data["stt"] = _request.currentObj.stt;
            data["waitStatus"] = waitStatus;
            data["status"] = status;
            data["responseText"] = new Array(responseText);
            cassiopeiaSendResponse(_request.data, data, _sender, _request.type)
        }
    }
}

chrome.runtime.onMessage.addListener(async function (_request, _sender, sendResponse) {
    chrome.storage.local.get('key',async function(result){
      captchaCount = 0;
      captchaResolved = 0;
        let key = result.key;
        let executeTab = key.executeTab;
        let executeTabTimeout = key.executeTabTimeout;
        let executeTabDelay = key.executeTabDelay;
        let sender = key.sender;
        let request = key.request;
        let _tabs = key.tabs;
        // console.log("key",key);
        // console.log("_request",_request);
        if(_tabs.length){
            _tabs.forEach(function (item,index) {
                if(item.pendingUrl.includes("google.com/search?")||item.pendingUrl.includes("sorry/index?continue")){
                    deleteTab(item);
                }
            });
        }
        key['tabs'] = [];
        key['auto_remove'] = true;
        chrome.storage.local.set({'key': key});
        if (_request.type === "captcha_dos") {
            if (_sender && executeTab && executeTab.id === _sender.tab.id) {
                let responseObj = {};
                responseObj["index"] = request.currentObj.index;
                responseObj["id"] = request.currentObj.id;
                responseObj["currentObj"] = request.currentObj;
                responseObj["checkType"] = request.type;
                cassiopeiaSendResponse(request.data, responseObj, sender, "captcha_dos");
                resetAll();
            }
            // chrome.tabs.remove(_sender.tab.id, function () {
            // });
            sendResponse({response: true})
        }  else if(_request.type === "getNow"){
            let _temp;
            if(now===0){
                _temp = 0;
            }else{
                _temp = (performance.now() - now)/1000;
            }
            chrome.tabs.sendMessage(_sender.tab.id, {type: _request.type, data: _request.data,stt: _request.stt, now: _temp}, function (response) {
                chrome.power.releaseKeepAwake();
                removeError()
            })
        }  else if(_request.type === "captcha_not_hander"){
            // console.log("_request.type",_request.type);
            let responseObj = {};
            if(request!==null){
                responseObj["index"] = request.currentObj.index;
                responseObj["id"] = request.currentObj.id;
                responseObj["currentObj"] = request.currentObj;
                responseObj["checkType"] = request.type;
                cassiopeiaSendResponse(request.data, responseObj, sender, "captcha_stop");
            }
            resetAll();
        }else if(_request.type === "captcha_resolve_limited"){ // hết lượt giải captcha
            // console.log("_request.type",_request.type);
            let responseObj = {};
            if(request!==null){
                responseObj["index"] = request.currentObj.index;
                responseObj["id"] = request.currentObj.id;
                responseObj["currentObj"] = request.currentObj;
                responseObj["checkType"] = request.type;
                cassiopeiaSendResponse(request.data, responseObj, sender, "captcha_resolve_limited");
            }
            resetAll();
        }else {
            setVariables(_sender,_request);
            sendResponse({response: ""});
            if (_request.type === "KeyCheck") {

                let linkUrl = _request.currentObj.searchEngine + "/search?q=" + _request.currentObj.key + "&safe=off&num=100&start=0&ie=utf-8&oe=utf-8&pws=0&hl=vi";
                var myRequest = new Request(linkUrl);
                let response = await fetch(myRequest);
                if (response.ok) {
                    let responseText = await response.text(); // Get JSON value from the response body
                    cassiopeiaKeywordPromiseResponse(response,responseText, key.request, key.sender, true)
                } else {
                  console.log("_request.currentObj",_request.currentObj)
                    if (response.url.includes("sorry/index?continue=https://www.google.com") ) { // gặp captcha
                        chrome.tabs.create({url: response.url, active: false},async function (tab) {
                            key['request'] = _request;
                            key['sender'] = _sender;
                            key['auto_remove'] = false;
                            chrome.storage.local.set({'key': key});
                            setVariables(null,null,tab);
                            var editorExtensionId = chrome.runtime.id;
                            if(tab==null){ // google chặn captcha
                                let responseObj = {};
                                responseObj["index"] = request.currentObj.index;
                                responseObj["id"] = request.currentObj.id;
                                responseObj["currentObj"] = request.currentObj;
                                responseObj["checkType"] = request.type;
                                cassiopeiaSendResponse(request.data, responseObj, sender, "captcha_stop");
                            }else{
                                const tabId = await tab.id;
                                if (!tab.url) await onTabUrlUpdated(tabId);
                                const results = await chrome.scripting.executeScript(
                                    {
                                        target: {tabId: tabId},
                                        func:setTabId,
                                        args: [editorExtensionId,tab,_request.currentObj.uid],
                                    },
                                    () => {

                                    }
                                );
                              if(_request.currentObj.captcha_resolve==="auto"){
                                const sss = await chrome.scripting.executeScript(
                                  {
                                    target: {tabId: tabId, allFrames: true},
                                    files: ['js/tab_captcha_2.js'],
                                  },
                                  (result) => {

                                    if (executeTabTimeout !== 0) {
                                      clearTimeout(executeTabTimeout)
                                    }
                                    executeTabTimeout = setTimeout(async function () {
                                      let responseText = await response.text();
                                      chrome.storage.local.get('key',async function(result){
                                        let executeTab = result.key.executeTab;
                                        let request = result.key.request;
                                        let sender = result.key.sender;
                                        if (sender && sender.tab.id === sender.tab.id && executeTab && executeTab.id === tabId) {
                                          let responseObj = {};
                                          responseObj["timeout"] = "timeout";
                                          responseObj["index"] = request.currentObj.index;
                                          responseObj["url"] = request.currentObj.url;
                                          responseObj["id"] = request.currentObj.id;
                                          responseObj["stt"] = request.currentObj.stt;
                                          responseObj["currentObj"] = request.currentObj;
                                          responseObj["content"] = new Array(responseText);
                                          cassiopeiaSendResponse(request.data, responseObj, sender, "KeyCheck");
                                          resetAll();
                                          // deleteTab(tab);
                                        }
                                        clearTimeout(executeTabTimeout);
                                        setVariables(null,null,null,0);
                                      });
                                      // deleteTab(tab);
                                    }, executeTabDelay)
                                  });
                              }
                            }
                        })
                    }else{
                        let responseObj = {};
                        responseObj["index"] = _request.currentObj.index;
                        responseObj["id"] = _request.currentObj.id;
                        responseObj["currentObj"] = _request.currentObj;
                        responseObj["checkType"] = _request.type;
                        cassiopeiaSendResponse(_request.data, responseObj, _sender, _request.type);
                        resetAll();
                    }
                }
            } else if (_request.type === "CheckContentStep2") {
                const longTask = () => new Promise(resolve => resolve(fetch(_request.currentObj.href))).then(value => cassiopeiaPromiseResponse(value, _request, _sender, true)).catch(_error => cassiopeiaPromiseResponseError(_error, _request, _sender, false));
                const timeout = (cb, interval) => () => new Promise(resolve => setTimeout(() => cb(resolve), interval));
                const onTimeout = timeout(resolve => resolve("waiting"), 60000);
                Promise.race([longTask, onTimeout].map(f => f())).then(value => cassiopeiaPromiseResponse(value, _request, _sender, true));
            } else if (_request.type === "GuestPostArticleAdd") {
                cassiopeiaWPAddPost(_request,_sender);
            }
            else if (_request.type === "GuestPostWebsiteGetCategories") {
                cassiopeiaWPCheck(_request,_sender);
            } else if (_request.type === "CheckContent") {
                let linkUrl = _request.currentObj.searchEngine + "/search?q=" + _request.currentObj.key + "&num=10&hl=" + _request.currentObj.language + "&start=0&ie=utf-8&oe=utf-8";
                let myRequest = new Request(linkUrl);
                let response = await fetch(myRequest);
                if (response.ok) {
                    let responseText = await response.text(); // Get JSON value from the response body
                    cassiopeiaCheckContentPromiseResponse(response,responseText, key.request, key.sender, true)
                } else {
                    // console.log("Is not finished")
                    if (response.url.includes("sorry/index?continue=https://www.google.com")) {

                        chrome.tabs.create({url: response.url, active: false},async function (tab) {
                            key['request'] = _request;
                            key['sender'] = _sender;
                            key['auto_remove'] = false;
                            chrome.storage.local.set({'key': key});
                            setVariables(null,null,tab);
                            let editorExtensionId = chrome.runtime.id;
                            if(tab==null){
                                let responseObj = {};
                                responseObj["index"] = request.currentObj.index;
                                responseObj["id"] = request.currentObj.id;
                                responseObj["currentObj"] = request.currentObj;
                                responseObj["checkType"] = request.type;
                                cassiopeiaSendResponse(request.data, responseObj, sender, "captcha_stop");
                            }else{
                                const tabId = await tab.id;
                                if (!tab.url) await onTabUrlUpdated(tabId);
                              const results = await chrome.scripting.executeScript(
                                {
                                  target: {tabId: tabId},
                                  func:setTabId,
                                  args: [editorExtensionId,tab,_request.currentObj.uid],
                                },
                                () => {

                                }
                              );
                              if(_request.currentObj.captcha_resolve==="auto"){
                                const sss = await chrome.scripting.executeScript(
                                  {
                                    target: {tabId: tabId, allFrames: true},
                                    files: ['js/tab_captcha_2.js'],
                                  },
                                  (result) => {

                                    if (executeTabTimeout !== 0) {
                                      clearTimeout(executeTabTimeout)
                                    }
                                    executeTabTimeout = setTimeout(async function () {
                                      let responseText = await response.text();
                                      chrome.storage.local.get('key',async function(result){
                                        let executeTab = result.key.executeTab;
                                        let request = result.key.request;
                                        let sender = result.key.sender;
                                        if (sender && sender.tab.id === sender.tab.id && executeTab && executeTab.id === tabId) {
                                          let responseObj = {};
                                          responseObj["timeout"] = "timeout";
                                          responseObj["index"] = request.currentObj.index;
                                          responseObj["url"] = request.currentObj.url;
                                          responseObj["id"] = request.currentObj.id;
                                          responseObj["stt"] = request.currentObj.stt;
                                          responseObj["currentObj"] = request.currentObj;
                                          responseObj["content"] = new Array(responseText);
                                          cassiopeiaSendResponse(request.data, responseObj, sender, "CheckContent");
                                          resetAll();
                                          // deleteTab(tab);
                                        }
                                        clearTimeout(executeTabTimeout);
                                        setVariables(null,null,null,0);
                                      });
                                      // deleteTab(tab);
                                    }, executeTabDelay)
                                  });
                              }
                            }
                        })
                    }else{
                        let responseObj = {};
                        responseObj["index"] = _request.currentObj.index;
                        responseObj["id"] = _request.currentObj.id;
                        responseObj["currentObj"] = _request.currentObj;
                        responseObj["checkType"] = _request.type;
                        cassiopeiaSendResponse(_request.data, responseObj, _sender, _request.type);
                        resetAll();
                    }
                }
            }
            else if (_request.type === "Guest_Post_Outline_Content_Check") {
                let linkUrl = _request.currentObj.searchEngine + "/search?q=" + _request.currentObj.key + "&num=10&hl=" + _request.currentObj.language + "&start=0&ie=utf-8&oe=utf-8";
                let myRequest = new Request(linkUrl);
                let response = await fetch(myRequest);
                if (response.ok) {
                    let responseText = await response.text(); // Get JSON value from the response body
                    cassiopeiaCheckContentPromiseResponse(response,responseText, key.request, key.sender, true)
                } else {
                    if (response.url.includes("sorry/index?continue=https://www.google.com")) {

                        chrome.tabs.create({url: response.url, active: false},async function (tab) {
                            key['request'] = _request;
                            key['sender'] = _sender;
                            key['auto_remove'] = false;
                            chrome.storage.local.set({'key': key});
                            setVariables(null,null,tab);
                            let editorExtensionId = chrome.runtime.id;
                            if(tab==null){
                                let responseObj = {};
                                responseObj["index"] = request.currentObj.index;
                                responseObj["id"] = request.currentObj.id;
                                responseObj["currentObj"] = request.currentObj;
                                responseObj["checkType"] = request.type;
                                cassiopeiaSendResponse(request.data, responseObj, sender, "captcha_stop");
                            }else{
                                const tabId = await tab.id;
                                if (!tab.url) await onTabUrlUpdated(tabId);
                                const results = await chrome.scripting.executeScript(
                                    {
                                        target: {tabId: tabId},
                                        func:setTabId,
                                        args: [editorExtensionId,tab],
                                    },
                                    () => {

                                    }
                                );
                                const sss = await chrome.scripting.executeScript(
                                    {
                                        target: {tabId: tabId, allFrames: true},
                                        files: ['js/tab_captcha_2.js'],
                                    },
                                    (result) => {

                                        if (executeTabTimeout !== 0) {
                                            clearTimeout(executeTabTimeout)
                                        }
                                        executeTabTimeout = setTimeout(async function () {
                                            let responseText = await response.text();
                                            chrome.storage.local.get('key',async function(result){
                                                let executeTab = result.key.executeTab;
                                                let request = result.key.request;
                                                let sender = result.key.sender;
                                                if (sender && sender.tab.id === sender.tab.id && executeTab && executeTab.id === tabId) {
                                                    let responseObj = {};
                                                    responseObj["timeout"] = "timeout";
                                                    responseObj["index"] = request.currentObj.index;
                                                    responseObj["url"] = request.currentObj.url;
                                                    responseObj["id"] = request.currentObj.id;
                                                    responseObj["stt"] = request.currentObj.stt;
                                                    responseObj["currentObj"] = request.currentObj;
                                                    responseObj["content"] = new Array(responseText);
                                                    cassiopeiaSendResponse(request.data, responseObj, sender, "Guest_Post_Outline_Content_Check");
                                                    resetAll();
                                                    // deleteTab(tab);
                                                }
                                                clearTimeout(executeTabTimeout);
                                                setVariables(null,null,null,0);
                                            });
                                            // deleteTab(tab);
                                        }, executeTabDelay)
                                    });
                            }
                        })
                    }else{
                        let responseObj = {};
                        responseObj["index"] = _request.currentObj.index;
                        responseObj["id"] = _request.currentObj.id;
                        responseObj["currentObj"] = _request.currentObj;
                        responseObj["checkType"] = _request.type;
                        cassiopeiaSendResponse(_request.data, responseObj, _sender, _request.type);
                        resetAll();
                    }
                }
            } else if (_request.type === "GetUrl") {
                // console.log("currentObj",_request.currentObj);
                let linkUrl = _request.currentObj.searchEngine + "/search?q=" + _request.currentObj.key + "&num="+_request.currentObj.max_results+"&hl=" + _request.currentObj.language + "&start=0&ie=utf-8&oe=utf-8";
                // console.log("linkUrl",linkUrl);
                // return ;
                let myRequest = new Request(linkUrl);
                let response = await fetch(myRequest);
                if (response.ok) {
                    let responseText = await response.text(); // Get JSON value from the response body
                    cassiopeiaGetUrlPromiseResponse(response,responseText, key.request, key.sender, true)
                } else {
                    if (response.url.includes("sorry/index?continue=https://www.google.com")) {
                        chrome.tabs.create({url: response.url, active: false},async function (tab) {
                            key['request'] = _request;
                            key['sender'] = _sender;
                            key['auto_remove'] = false;
                            chrome.storage.local.set({'key': key});
                            setVariables(null,null,tab);
                            var editorExtensionId = chrome.runtime.id;
                            if(tab==null){
                                let responseObj = {};
                                responseObj["index"] = request.currentObj.index;
                                responseObj["id"] = request.currentObj.id;
                                responseObj["currentObj"] = request.currentObj;
                                responseObj["checkType"] = request.type;
                                cassiopeiaSendResponse(request.data, responseObj, sender, "captcha_stop");
                            }else{
                                const tabId = await tab.id;
                                if (!tab.url) await onTabUrlUpdated(tabId);
                              const results = await chrome.scripting.executeScript(
                                {
                                  target: {tabId: tabId},
                                  func:setTabId,
                                  args: [editorExtensionId,tab,_request.currentObj.uid],
                                },
                                () => {

                                }
                              );
                              if(_request.currentObj.captcha_resolve==="auto"){
                                const sss = await chrome.scripting.executeScript(
                                  {
                                    target: {tabId: tabId, allFrames: true},
                                    files: ['js/tab_captcha_2.js'],
                                  },
                                  (result) => {

                                    if (executeTabTimeout !== 0) {
                                      clearTimeout(executeTabTimeout)
                                    }
                                    executeTabTimeout = setTimeout(async function () {
                                      let responseText = await response.text();
                                      chrome.storage.local.get('key',async function(result){
                                        let executeTab = result.key.executeTab;
                                        let request = result.key.request;
                                        let sender = result.key.sender;
                                        if (sender && sender.tab.id === sender.tab.id && executeTab && executeTab.id === tabId) {
                                          let responseObj = {};
                                          responseObj["timeout"] = "timeout";
                                          responseObj["index"] = request.currentObj.index;
                                          responseObj["url"] = request.currentObj.url;
                                          responseObj["id"] = request.currentObj.id;
                                          responseObj["stt"] = request.currentObj.stt;
                                          responseObj["currentObj"] = request.currentObj;
                                          responseObj["content"] = new Array(responseText);
                                          cassiopeiaSendResponse(request.data, responseObj, sender, "GetUrl");
                                          resetAll();
                                        }
                                        clearTimeout(executeTabTimeout);
                                        setVariables(null,null,null,0);
                                      });
                                    }, executeTabDelay)
                                  }
                                );
                              }
                            }
                        })
                    }else{
                        let responseObj = {};
                        responseObj["index"] = _request.currentObj.index;
                        responseObj["id"] = _request.currentObj.id;
                        responseObj["currentObj"] = _request.currentObj;
                        responseObj["checkType"] = _request.type;
                        cassiopeiaSendResponse(_request.data, responseObj, _sender, _request.type);
                        resetAll();
                    }
                }
            } else if (_request.type === "CheckBackLinkIndexed") {
              // console.log("_request",_request);
                // if(now===0){
                //     now = performance.now();
                // }else{
                    // diff = (performance.now() - now)/1000;
                    // console.log("diff",diff);
                    // if(diff>295){
                    //     let responseObj = {};
                    //     responseObj["index"] = _request.currentObj.index;
                    //     responseObj["id"] = _request.currentObj.id;
                    //     responseObj["currentObj"] = _request.currentObj;
                    //     responseObj["checkType"] = _request.type;
                    //     cassiopeiaSendResponse(_request.data, responseObj, _sender, "may_doi_tao_30s");
                    //     resetAll();
                    //     return;
                    // }
                    // console.log("now now",(performance.now() - now)/1000)
                // }
              let check_key_index = _request.currentObj.check_key_index;
                let check_key = check_indexed_keys[check_key_index];
              // console.log("check_key_index",check_key_index);
              // console.log("check_key",check_key);
                let linkUrl = "https://www.google.com/search?q=" + check_key+_request.currentObj.source + "&hl=vi";
                // let response = await fetch(linkUrl);
                let response = await fetch(linkUrl).catch(error => {
                    // _error = true
                    // console.log("error",error);
                });
                let responseText = await response.text();
                if (response.url.includes("sorry/index?continue=https://www.google.com")) {
                    chrome.tabs.create({url: response.url, active: false},async function (tab) {
                        key['request'] = _request;
                        key['sender'] = _sender;
                        key['auto_remove'] = false;
                        chrome.storage.local.set({'key': key});
                        let _delay = Math.floor((295-diff)*1000);
                        // if(executeTabDelay>_delay){
                        //     executeTabDelay = _delay;
                        // }
                        setVariables(null,null,tab);
                        var editorExtensionId = chrome.runtime.id;
                        const tabId = await tab.id;
                        if (!tab.url) await onTabUrlUpdated(tabId);
                        const results = await chrome.scripting.executeScript(
                            {
                                target: {tabId: tabId},
                                func:setTabId,
                                args: [editorExtensionId,tab,_request.currentObj.uid],
                            },
                            () => {

                            }
                        );
                      if(_request.currentObj.captcha_resolve==="auto"){
                        chrome.scripting.executeScript(
                          {
                            target: {tabId: tabId, allFrames: true},
                            files: ['js/tab_captcha_2.js'],
                          },
                          (result) => {
                            // console.log("result",result);
                            executeTab = tab;
                            if (executeTabTimeout != 0) {
                              clearTimeout(executeTabTimeout)
                            }
                            executeTabTimeout = setTimeout(function () {
                              chrome.storage.local.get('key',async function(result){
                                let executeTab = result.key.executeTab;
                                let request = result.key.request;
                                let sender = result.key.sender;
                                if (sender && sender.tab.id === _sender.tab.id && executeTab && executeTab.id === tab.id) {
                                  let responseObj = {};
                                  responseObj["index"] = _request.currentObj.index;
                                  responseObj["id"] = _request.currentObj.id;
                                  responseObj["backlink"] = _request.currentObj.backlink;
                                  responseObj["currentObj"] = _request.currentObj;
                                  responseObj["content"] = new Array(responseText);
                                  cassiopeiaSendResponse(_request.data, responseObj, _sender, "CheckBackLinkIndexed");
                                  resetAll();
                                }
                                clearTimeout(executeTabTimeout);
                                setVariables(null,null,null,0);
                              });
                            }, executeTabDelay)
                          });
                      }
                    })
                } else {
                    let responseObj = {};

                    responseObj["index"] = _request.currentObj.index;
                    responseObj["id"] = _request.currentObj.id;
                    responseObj["backlink"] = _request.currentObj.backlink;
                    responseObj["currentObj"] = _request.currentObj;
                    responseObj["content"] = new Array(responseText);
                    cassiopeiaSendResponse(_request.data, responseObj, _sender, "CheckBackLinkIndexed");
                    resetAll();
                }
            } else if (_request.type === "CheckBackLink") {
                let _error = false;
                const longTask = () => new Promise(resolve => resolve(fetch(_request.currentObj.source))).then(value => cassiopeiaPromiseResponse(value, _request, _sender, true)).catch(_error => cassiopeiaPromiseResponseError(_error, _request, _sender, false));
                const timeout = (cb, interval) => () => new Promise(resolve => setTimeout(() => cb(resolve), interval));
                const onTimeout = timeout(resolve => resolve("waiting"), 60000);
                Promise.race([longTask, onTimeout].map(f => f())).then(value => cassiopeiaPromiseResponse(value, _request, _sender, true));
                return true
            }else if (_request.type === "getTels") {
                let _error = false;
                const longTask = () => new Promise(resolve => resolve(fetch(_request.currentObj.source))).then(value => cassiopeiaPromiseResponse(value, _request, _sender, true)).catch(_error => cassiopeiaPromiseResponseError(_error, _request, _sender, false));
                const timeout = (cb, interval) => () => new Promise(resolve => setTimeout(() => cb(resolve), interval));
                const onTimeout = timeout(resolve => resolve("waiting"), 60000);
                Promise.race([longTask, onTimeout].map(f => f())).then(value => cassiopeiaPromiseResponse(value, _request, _sender, true));
                return true
            } else if (_request.type === "CheckDuplicateContent") {
                let linkUrl = 'https://www.google.com/search?q="' + _request.currentObj.value + '"&num=100&hl=vi&start=0&ie=utf-8&oe=utf-8';
                let _error = false;
                let response = await fetch(linkUrl).catch(error => {
                    _error = true
                });
                if (_error !== true) {
                    let responseText = await response.text();
                    if (response.url.includes("sorry/index?continue=https://www.google.com")) {
                        chrome.tabs.create({url: response.url, active: false},async function (tab) {
                            key['request'] = _request;
                            key['sender'] = _sender;
                            key['auto_remove'] = false;
                            chrome.storage.local.set({'key': key});
                            let _delay = Math.floor((295-diff)*1000);
                            // if(executeTabDelay>_delay){
                            //     executeTabDelay = _delay;
                            // }
                            setVariables(null,null,tab);
                            var editorExtensionId = chrome.runtime.id;
                            const tabId = await tab.id;
                            if (!tab.url) await onTabUrlUpdated(tabId);
                          const results = await chrome.scripting.executeScript(
                            {
                              target: {tabId: tabId},
                              func:setTabId,
                              args: [editorExtensionId,tab,_request.currentObj.uid],
                            },
                            () => {

                            }
                          );
                          if(_request.currentObj.captcha_resolve==="auto"){
                            const sss = await chrome.scripting.executeScript(
                              {
                                target: {tabId: tabId, allFrames: true},
                                files: ['js/tab_captcha_2.js'],
                              },
                              (result) => {

                                if (executeTabTimeout !== 0) {
                                  clearTimeout(executeTabTimeout)
                                }

                              });
                          }

                            // wait((executeTabDelay));
                        })
                    } else {
                        chrome.storage.local.get('key',async function(result) {

                            let key = result.key;
                            let sender = key.sender;
                            let request = key.request;
                            let executeTabTimeout = key.executeTabTimeout;
                            let executeTabDelay = key.executeTabDelay;
                            let responseObj = {};
                            responseObj["index"] = _request.currentObj.index;
                            responseObj["id"] = _request.currentObj.id;
                            responseObj["currentObj"] = _request.currentObj;
                            responseObj["content"] = new Array(responseText);
                            cassiopeiaSendResponse(request.data, responseObj, sender, "CheckDuplicateContent");
                            // cassiopeiaSendResponse(_request.data, responseObj, _sender, "CheckDuplicateContent");
                            resetAll();
                        })

                    }
                } else {

                }
            }else if (_request.type === "GuestPostDuplicateContentCheck") {
                let linkUrl = 'https://www.google.com/search?q="' + _request.currentObj.value + '"&num=100&hl=vi&start=0&ie=utf-8&oe=utf-8';
                let _error = false;
                let response = await fetch(linkUrl).catch(error => {
                    _error = true
                });
                if (_error !== true) {
                    let responseText = await response.text();
                    if (response.url.includes("sorry/index?continue=https://www.google.com")) {
                        chrome.tabs.create({url: response.url, active: false},async function (tab) {
                            key['request'] = _request;
                            key['sender'] = _sender;
                            key['auto_remove'] = false;
                            chrome.storage.local.set({'key': key});
                            let _delay = Math.floor((295-diff)*1000);
                            // if(executeTabDelay>_delay){
                            //     executeTabDelay = _delay;
                            // }
                            setVariables(null,null,tab);
                            var editorExtensionId = chrome.runtime.id;
                            const tabId = await tab.id;
                            if (!tab.url) await onTabUrlUpdated(tabId);
                          const results = await chrome.scripting.executeScript(
                            {
                              target: {tabId: tabId},
                              func:setTabId,
                              args: [editorExtensionId,tab,_request.currentObj.uid],
                            },
                            () => {

                            }
                          );
                          if(_request.currentObj.captcha_resolve==="auto"){
                            const sss = await chrome.scripting.executeScript(
                              {
                                target: {tabId: tabId, allFrames: true},
                                files: ['js/tab_captcha_2.js'],
                              },
                              (result) => {

                                if (executeTabTimeout !== 0) {
                                  clearTimeout(executeTabTimeout)
                                }

                              });
                          }
                        })
                    } else {
                        chrome.storage.local.get('key',async function(result) {

                            let key = result.key;
                            let sender = key.sender;
                            let request = key.request;
                            let executeTabTimeout = key.executeTabTimeout;
                            let executeTabDelay = key.executeTabDelay;
                            let responseObj = {};
                            responseObj["index"] = _request.currentObj.index;
                            responseObj["id"] = _request.currentObj.id;
                            responseObj["currentObj"] = _request.currentObj;
                            responseObj["content"] = new Array(responseText);
                            cassiopeiaSendResponse(request.data, responseObj, sender, "GuestPostDuplicateContentCheck");
                            resetAll();
                        })

                    }
                } else {

                }
            }
        }
    });
    return true
});
function loadTabJS(){

}
chrome.tabs.onUpdated.addListener(async function (tabId, changeInfo, tab) {
  console.log("updated")

    $checkInonlytabs = false;
    for (var i = 0; i < onlytabs.length; i++) {
        if (tab.url.includes(onlytabs[i]) === true) {
            $checkInonlytabs = true
        }
    }
    if ($checkInonlytabs) {
        var tabs = await getAllTabs({});
        $counterTab = 0;
        if (tabs && tabs.hasOwnProperty("length")) {
            for (var i = 0; i < tabs.length; i++) {
                for (var j = 0; j < onlytabs.length; j++) {
                    if (tabs[i].url.includes(onlytabs[j]) === true) {
                        $counterTab = $counterTab + 1
                    }
                }
            }
        }
        if ($counterTab >= 2) {
            wait(500);
            await deleteTab(tab);
            chrome.runtime.lastError = null;
            alert("Vui lòng chỉ mở 1 tab chức năng!")
        } else {
          // if(changeInfo.status=="completed"){
            afterUpdateTab(tabId, changeInfo, tab)
          // }

        }
    } else {
      // if(changeInfo.status=="completed"){
        afterUpdateTab(tabId, changeInfo, tab)
      // }
    }
});
function onTabUrlUpdated(tabId) {
    return new Promise((resolve, reject) => {
        const onUpdated = (id, info) => id === tabId && info.url && done(true);
        const onRemoved = id => id === tabId && done(false);
        chrome.tabs.onUpdated.addListener(onUpdated);
        chrome.tabs.onRemoved.addListener(onRemoved);
        function done(ok) {
            chrome.tabs.onUpdated.removeListener(onUpdated);
            chrome.tabs.onRemoved.removeListener(onRemoved);
            (ok ? resolve : reject)();
        }
    });
}
chrome.tabs.onCreated.addListener(async function (tab) {
    $checkInonlytabs = false;
    for (var i = 0; i < onlytabs.length; i++) {
        if (tab.url.includes(onlytabs[i]) === true) {
            $checkInonlytabs = true
        }
    }
    if ($checkInonlytabs) {
        var tabs = await getAllTabs({});
        $counterTab = 0;
        for (var i = 0; i < tabs.length; i++) {
            for (var j = 0; j < onlytabs.length; j++) {
                if (tab.url.includes(onlytabs[i]) === true) {
                    $counterTab = $counterTab + 1
                }
            }
        }
        if ($counterTab >= 1) {
            wait(500);
            deleteTab(tab);
            alert("Vui lòng chỉ mở 1 tab chức năng!")
        } else {
        }
    } else {
    }
});
chrome.tabs.onRemoved.addListener(function (tabId, removeInfo) {
    chrome.storage.local.get('key', function (result) {
        let key = result.key;
        // console.log("key",key);
        let sender = key.sender;
        let request = key.request;
        let executeTab = key.executeTab;
        let auto_remove = key.auto_remove;
        if (sender) {
            if (executeTab) {
                if (tabId === executeTab.id && auto_remove==false) {
                    let responseObj = {};
                    responseObj["index"] = request.currentObj.index;
                    responseObj["id"] = request.currentObj.id;
                    responseObj["currentObj"] = request.currentObj;
                    responseObj["checkType"] = request.type;
                    cassiopeiaSendResponse(request.data, responseObj, sender, "captcha_stop");
                    resetAll();
                }
            }
        }
    })
});
var captchaCount = 0;
var captchaResolved = 0;
function afterUpdateTab(tabId, changeInfo, tab) {
  console.log("changeInfo",changeInfo)

    chrome.storage.local.get('key', function (result) {
        let key = result.key;
        let executeTab = key.executeTab;
        let sender = key.sender;
      let request = key.request;
        let executeTabTimeout = key.executeTabTimeout;
        let executeTabDelay = key.executeTabDelay;
      if(changeInfo.status==="complete"){
        if(executeTab !== null && tabId === executeTab.id){
          captchaCount++;
        }
      }
        if (changeInfo.hasOwnProperty("url")) {
            if (changeInfo.url.includes("search?q")) {
                console.log("_executeTab 2",executeTab);
                if (executeTab !== null && tabId === executeTab.id) {
                    // console.log("exe")
                    // wait(100);
                    chrome.scripting.executeScript(
                        {
                            target: {tabId: tabId},
                            files: ['js/tab_script_check_keyword.min.js'],
                        },
                        (result) => {
                            // console.log("result",result);
                            let responseObj = {};
                            if (request.type === "KeyCheck") {
                                responseObj["captcha"] = "yes";
                                responseObj["index"] = request.currentObj.index;
                                responseObj["url"] = request.currentObj.url;
                                responseObj["id"] = request.currentObj.id;
                                responseObj["stt"] = request.currentObj.stt
                            } else if (request.type === "CheckDuplicateContent") {
                                responseObj["index"] = request.currentObj.index;
                                responseObj["id"] = request.currentObj.id
                            } else if (request.type === "CheckBackLinkIndexed") {
                                responseObj["index"] = request.currentObj.index;
                                responseObj["id"] = request.currentObj.id;
                                responseObj["backlink"] = request.currentObj.backlink;
                                responseObj["currentObj"] = request.currentObj
                            }
                            let temp = [];
                            result.forEach(function (item, index, arr) {
                                temp.push(item.result);
                            });
                            if(result[0]!==null && result[0]!==""){
                                responseObj["content"] = temp;
                            }
                            responseObj["currentObj"] = request.currentObj;
                            cassiopeiaSendResponse(request.data, responseObj, sender, request.type);
                            clearTimeout(executeTabTimeout);
                            resetAll(tab);
                            captchaResolved = 1;
                            // console.log("exe finished");
                            // let flag = true;
                            // chrome.storage.local.set({'key': key});
                            // tabDragging(function (dragging) {
                            //     if (!dragging) {
                            //         wait(3000);
                            //         deleteTab(tab);
                            //         flag = false;
                            //     }
                            // });
                            // if(flag){
                            //     wait(3000);
                            //     deleteTab(tab);
                            // }
                        });
                }
            }else if (changeInfo.url.includes("sorry/index")) {
              console.log("asdlfkjlaskf",changeInfo)
              // chrome.storage.local.get(['_request_'], function(result) {
              // console.log('Value currently is ' + tabId+" executeTab "+executeTab);
              // });
            }
        }else{
            // console.log("not url",changeInfo)
            // console.log("tabId",tabId)
            // console.log("executeTab",executeTab)
        }
      console.log("captchaCount",captchaCount)
        if(captchaCount>=2 && captchaResolved===0){ // 1 more captcha
          chrome.tabs.get(tabId,async function(_tab) {
            var editorExtensionId = chrome.runtime.id;
            if (chrome.runtime.lastError) {
              console.error(chrome.runtime.lastError);
              return;
            }
            if (_tab.url.includes("sorry/index") ) { // gặp captcha
              const results = await chrome.scripting.executeScript(
                {
                  target: {tabId: tabId},
                  func:setTabId,
                  args: [editorExtensionId,_tab],
                },
                () => {

                }
              );
              const sss = await chrome.scripting.executeScript(
                {
                  target: {tabId: tabId, allFrames: true},
                  files: ['js/tab_captcha.js'],
                },
                (result) => {

                  if (executeTabTimeout !== 0) {
                    clearTimeout(executeTabTimeout)
                  }
                  executeTabTimeout = setTimeout(async function () {
                    let responseText = "";
                    chrome.storage.local.get('key',async function(result){
                      let executeTab = result.key.executeTab;
                      let request = result.key.request;
                      let sender = result.key.sender;
                      if (sender && sender.tab.id === sender.tab.id && executeTab && executeTab.id === tabId) {
                        let responseObj = {};
                        responseObj["timeout"] = "timeout";
                        responseObj["index"] = request.currentObj.index;
                        responseObj["url"] = request.currentObj.url;
                        responseObj["id"] = request.currentObj.id;
                        responseObj["stt"] = request.currentObj.stt;
                        responseObj["currentObj"] = request.currentObj;
                        responseObj["content"] = new Array(responseText);
                        cassiopeiaSendResponse(request.data, responseObj, sender, "KeyCheck");
                        resetAll();
                        deleteTab(_tab);
                      }
                      clearTimeout(executeTabTimeout);
                      setVariables(null,null,null,0);
                    });
                    // deleteTab(tab);
                  }, executeTabDelay)
                });
            }
          });
        }
    });

    // if (executeTab !== null && tabId === executeTab.id) {

    // }else{
    //     if(executeTab!=null){
    //         console.log("executeTab",executeTab.id);
    //         console.log("tabid",tabId);
    //     }else{
    //         console.log("executeTab null");
    //     }
    // }
}

chrome.runtime.onInstalled.addListener(async function (details) {
    var key = {};
    key['tabs'] = [];
    key['executeTab'] = null;
    key['request'] = null;
    key['sender'] = null;
    key['spinUrlRequest'] = "";
    key['executeTabDelay'] = 120000;
    key['executeTabTimeout'] = 0;
    chrome.storage.local.set({'key': key});
    // console.log("Extension Install")
    var tabs = await getAllTabs({});
    if (tabs && tabs.hasOwnProperty("length")) {
        for (var i = 0; i < tabs.length; i++) {
            if (tabs[i].url.search("https://seominisuite.com") == 0) {
                chrome.tabs.reload(tabs[i].id, {}, function () {
                })
            }
        }
    }
});
chrome.runtime.onMessageExternal.addListener(function (request, sender, sendResponse) {
    sendResponse({response: ""})
});
async function _cassiopeiaWPCheck(node,protocol="http"){
    let wp_response = {};
    const formData = new FormData();
    formData.append("action", "seominisuite_check_status");
    const data  = new URLSearchParams(formData);
    let response = await fetch(node.domain+"/wp-admin/admin-ajax.php", {
        method: 'POST',
        body:data,
        headers: {
            'Content-type': 'application/x-www-form-urlencoded',
        }
    }).then(async function (response) {
        if (response.ok) {
            wp_response = await response.json(); // Get JSON value from the response body
            // console.log("wp_response",wp_response)
            node.wp_response = wp_response;
            cassiopeiaSendResponse(_request.currentObj, node, _sender, _request.type);
        } else {
            if(protocol==="http"){
                node.wp_response =  "FAIL";// Get JSON value from the response body
                cassiopeiaSendResponse(_request.currentObj, node, _sender, _request.type);
            }else{
               let check = await cassiopeiaWPCheck(node,"https");
               // console.log("check",check);
            }

        }
    }).catch(
        function (_error) {
            node.wp_response =  "FAIL";// Get JSON value from the response body
            cassiopeiaSendResponse(_request.currentObj, node, _sender, _request.type);
        }
    );
}
async function cassiopeiaWPCheck(_request,_sender,protocol="https"){
    let node = _request.currentObj;
    const formData = new FormData();
    formData.append("action", "seominisuite_check_status");
    const data  = new URLSearchParams(formData);
    let response = await fetch(node.domain+"/wp-admin/admin-ajax.php", {
        method: 'POST',
        body:data,
        headers: {
            'Content-type': 'application/x-www-form-urlencoded',
        }
    }).then(async function (response) {
        if (response.ok) {
            cassiopeiaGuestPostGetCategories(_request,_sender,protocol);
        } else {
            if(protocol==="http"){
                node.wp_response =  "FAIL";// Get JSON value from the response body
                cassiopeiaSendResponse(_request.currentObj, node, _sender, _request.type);
            }else{
                cassiopeiaWPCheck(_request,_sender,"http");
            }

        }
    }).catch(
        function (_error) {
            node.wp_response =  "FAIL";// Get JSON value from the response body
            cassiopeiaSendResponse(_request.currentObj, node, _sender, _request.type);
        }
    );
}
async function cassiopeiaWPAddPost(_request,_sender,protocol="https"){

    let node = JSON.parse(_request.currentObj);
    // console.log("node",node);
    const formData = new FormData();
    formData.append("action", "seominisuite_check_status");
    const data  = new URLSearchParams(formData);
    let response = await fetch(node.website_domain+"/wp-admin/admin-ajax.php", {
        method: 'POST',
        body:data,
        headers: {
            'Content-type': 'application/x-www-form-urlencoded',
        }
    }).then(async function (response) {
        if (response.ok) {
            let node = JSON.parse(_request.currentObj);
            let content = node.content;
            node.content = "";

            // console.log("node",node);
            let wp_response = {};
            const formData = new FormData();
            formData.append("action", "seominisuite_add_article");
            formData.append("node", JSON.stringify(node));
            formData.append("content", content);
            // formData.append("content", node.content);
            // formData.append("image", node.image.url);
            // formData.append("node", JSON.stringify(node));
            const data  = new URLSearchParams(formData);
            let response = await fetch(node.website_domain+"/wp-admin/admin-ajax.php", {
                method: 'POST',
                body:data,
                headers: {
                    'Content-type': 'application/x-www-form-urlencoded',
                }
            });
            if (response.ok) {
                let wp_response = await response.json(); // Get JSON value from the response body
                cassiopeiaSendResponse(_request.currentObj, wp_response, _sender, _request.type);
            } else {
                let wp_response =  null;// Get JSON value from the response body
                cassiopeiaSendResponse(_request.currentObj, wp_response, _sender, _request.type);
            }
        } else {
            if(protocol==="http"){
                let wp_response =  null;
                cassiopeiaSendResponse(_request.currentObj, wp_response, _sender, _request.type);
            }else{
                cassiopeiaWPAddPost(_request,_sender,"http");
            }

        }
    }).catch(
        function (_error) {
            let wp_response =  null;
            cassiopeiaSendResponse(_request.currentObj, wp_response, _sender, _request.type);
        }
    );
}
async function cassiopeiaGuestPostGetCategories(_request,_sender,protocol){
    let node = _request.currentObj;
    let wp_response = {};
    const formData = new FormData();
    formData.append("action", "seominisuite_get_categories");
    const data  = new URLSearchParams(formData);
    let response = await fetch(node.domain+"/wp-admin/admin-ajax.php", {
        method: 'POST',
        body:data,
        headers: {
            'Content-type': 'application/x-www-form-urlencoded',
        }
    }).then(async function (response) {
        if (response.ok) {
            wp_response = await response.json(); // Get JSON value from the response body
            // console.log("wp_response",wp_response);
            node.wp_response = wp_response;
            cassiopeiaSendResponse(_request.currentObj, node, _sender, _request.type);
        } else {
            node.wp_response =  "FAIL";// Get JSON value from the response body
            cassiopeiaSendResponse(_request.currentObj, node, _sender, _request.type);
        }
    }).catch(
        function (_error) {
            node.wp_response =  "FAIL";// Get JSON value from the response body
            cassiopeiaSendResponse(_request.currentObj, node, _sender, _request.type);
        }
    );
}