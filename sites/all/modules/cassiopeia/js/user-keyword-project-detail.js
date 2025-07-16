function cassiopeiaGetCheckedInput(){
    var data = [];
    jQuery("tbody input").each(function (e) {
        var _this = $(this);
        if(_this.is(":checked")){
            data.push(_this.val());
        }
    });
    return data;
}
function cassiopeiaTagify(){
    var _tags = JSON.parse(jQuery("#tags").val());
    var _whitelist = "";
    var obj = [];
    jQuery.each(_tags, function( index, value ) {
        var temp = {};
        temp['id'] = index;
        temp['value'] = value;
        _whitelist+=","+value;
        obj.push(value);
    });
    var $input = jQuery('input.tagify-input').tagify({
        whitelist : obj
    })
        .on('add', function(e, tagName){
            // console.log('JQEURY EVENT: ', 'added', tagName)
        })
        .on("invalid", function(e, tagName) {
            // console.log('JQEURY EVENT: ',"invalid", e, ' ', tagName);
        });
    var jqTagify = $input.data('tagify');
}
(function ($) {
    const modalTagOption =  $("#modalTagOption");
    const modalTag = $("#modalTags");
    const modalTagTitle = $("#modalTags .modal-title");
    $(document).ready(function () {
        cassiopeiaTagify();
        // keywordCheckResponse
        document.addEventListener("keywordCheckResponse", function (e) {
            let _object = JSON.parse(e.detail);
            let data = _object.data;
            let currentObj = _object.currentObj;
            let responseObject = _object.responseObject;
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
            let gNodes = dom_nodes.find("div#search .g>div");
            let sNodes = [];
            // console.log("gNodes",gNodes);
            if(gNodes.length){
                $.each(gNodes, function (index, _value) {
                    let flag = true;
                    let divNodes = $(_value).find("div");
                    let gImgNodes = $(_value).find("g-img");
                    let aNodes = $(_value).find("a");
                    if(gImgNodes.length){
                        flag = false;
                    }
                    if(aNodes.length<1){
                        flag = false;
                    }
                    // if(flag){
                    $.each(divNodes, function (_index, __value) {
                        var attr = $(this).attr('data-vid');
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
            $.each(sNodes, function (index, _value) {
                if ($(_value).find("a").length) {
                    let a_value = $(_value).find("a:first-child");
                    let _href = $(a_value).attr("href");
                    if (typeof _href !== "undefined" && _href !== false) {
                        let _domain_href = getDomainFromHref(_href);
                        console.log("_domain_href",_domain_href);
                        if (_domain_href.toLowerCase().includes(domain.toLowerCase()) && domain.toLowerCase().includes(_domain_href.toLowerCase())) {
                            if (stt === 0) {
                                minIndex = index;
                                minHref = _href;
                            } else {
                                if (parseInt(index) < parseInt(minIndex)) {
                                    minIndex = index;
                                    minHref = _href;
                                }
                            }
                            check = true;
                            stt++
                        }
                    }
                }
            });
            if (check == true) {
                result["index"] = minIndex;
                result["url"] = minHref
            }else{
                result = -1;
            }
            if (Object.keys(data).length == 1) {
                lastKey = 1
            }
            // let listOfID = JSON.parse($listOfID.attr("data-value"))
            $.ajax({
                method: "POST",
                url: "/cassiopeia/ajax",
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
                            let new_object = {};
                            new_object.data = data;
                            new_object.currentObj = currentObj;
                            new_object.responseObject = responseObject;
                            document.dispatchEvent(new CustomEvent('cassiopeiaKeyCheck', {detail: JSON.stringify(new_object)}));
                            // cassiopeia_recursion_keyword_check(data, currentObj)
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
                                        // location.reload()
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
                        let new_object = {};
                        new_object.data = data;
                        new_object.currentObj = currentObj;
                        new_object.responseObject = responseObject;
                        document.dispatchEvent(new CustomEvent('cassiopeiaKeyCheck', {detail: JSON.stringify(new_object)}));
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
                                    // location.reload()
                                }
                            })
                        }, 1e3)
                    }
                }
            });
            return;
        });
        $(".btn-export").click(function(e){
            let _this = $(this);
            let project_id = $("#project_id").val();
            let data = [];
            if(_this.hasClass("excel")){
                $(".result tbody input").each(function(e) {
                    var _this = $(this);
                    if (_this.is(":checked")) {
                        data.push(_this.val());
                    }
                });
                $.ajax({
                    method: "POST",
                    url: "/cassiopeia/ajax",
                    data: {
                        cmd: "setExportData",
                        project_id:project_id,
                        data: JSON.stringify(data),
                    },
                    success: function(result) {
                        location.href = "/quan-ly-keywords/du-an/"+project_id+"/export?data="+result.request_time;
                    }
                });
                return false;
            }else{
                setModalConfirm("Bạn cần nâng cấp gói để sử dụng chức năng này!",function (e) {
                    location.href = "/price-board";
                })
                return false;
            }
        });
        $(".modal-tag-manager .close").click(function (e) {
            $(".modal-tag-manager").removeClass("active");
        });
        $(".btn-tag-manager").click(function (e) {
            $(".modal-tag-manager").addClass("active");
        });
        var typingTimer;
        $(".page-search input").keyup(function(e) {
            var _this = $(this);
            clearTimeout(typingTimer);
            typingTimer = setTimeout(function() {
                var key = null;
                if (e.which != null) {
                    key = e.which;
                } else {
                    key = e.keyCode;
                }
                console.log("key",key);
                if (key != 40 && key != 39 && key != 38 && key != 37) {
                    var _key = _this.val().trim().replaceAll(" ","").toLowerCase();
                    _key = removeAccents(_key);
                    $(".result tbody tr").each(function (e) {
                        let _this = $(this);
                        let _text = _this.text();
                        _text = _text.replaceAll(" ","").toLowerCase();
                        _text = removeAccents(_text);
                        if(_text.indexOf(_key) != -1){
                            _this.removeClass("inactive");
                        }else{
                            _this.addClass("inactive");
                        }
                    });
                }
            }, 300);
            e.stopPropagation();
        });
        $(".page-search input").keydown(function(e) {
            clearTimeout(typingTimer);
        });
        $('.btn-clear-text').on('click', function(e) {
            e.stopPropagation();
            $('.page-search .input-group-search>input[type="text"]').val('');
            $(".result tbody tr").each(function(e) {
                let _this = $(this);
                _this.removeClass("inactive");
            });
            return false;
        });
        // var result = $(".result tbody tr").sort(function(a, b) {
        //     var contentA = parseInt($(a).find("td[data-key='sort_7']").attr("data-value"));
        //     var contentB = parseInt($(b).find("td[data-key='sort_7']").attr("data-value"));
        //     return (contentA > contentB) ? -1 : (contentA < contentB) ? 1 : 0;
        // });
        // $(".result table tbody").html(result);
        let stt=1;
        $(".result tbody tr").each(function (e) {
            let _this = $(this);
            _this.find("td.stt").text(stt);
            _this.find("input").attr("data-stt",stt);
            stt++;
        });
        $(".sort").click(async function(e) {
            await $(".loading-block").addClass("active");
            let _this = $(this);
            $(".sort").removeClass("current");
            _this.addClass("current");
            let direction = _this.attr("data-direction");
            let sort = _this.attr("data-sort");
            if (direction == "DESC") {
                _this.attr("data-direction", "ASC");
            } else {
                _this.attr("data-direction", "DESC");
            }
            var result = $(".result tbody tr").sort(function(a, b) {
                switch (sort) {
                    case "sort_1":
                    case "sort_6":
                    case "sort_8":
                        var contentA = $(a).find("td[data-key='" + sort + "']").attr("data-value");
                        var contentB = $(b).find("td[data-key='" + sort + "']").attr("data-value");
                        if (direction == "DESC") {
                            if(contentA=="-"){
                                contentA = 'a';
                            }
                            if(contentB=='-'){
                                contentB = 'a';
                            }
                            console.log("contentA",contentA);
                            console.log("contentB",contentB);
                            return (contentA > contentB) ? -1 : (contentA < contentB) ? 1 : 0;
                        } else {
                            if(contentA=="-"){
                                contentA = 'z';
                            }
                            if(contentB=='-'){
                                contentB = 'z';
                            }
                            console.log("contentA",contentA);
                            console.log("contentB",contentB);
                            return (contentA < contentB) ? -1 : (contentA > contentB) ? 1 : 0;
                        }
                        break;
                    case "sort_2":
                    case "sort_3":
                    case "sort_4":
                    case "sort_5":
                    case "sort_7":
                        var contentA = ($(a).find("td[data-key='" + sort + "']").attr("data-value"));
                        var contentB = ($(b).find("td[data-key='" + sort + "']").attr("data-value"));
                        if(contentA=="-"){

                        }else{
                            contentA = parseInt(contentA);
                        }
                        if(contentB=='-'){

                        }else{
                            contentB = parseInt(contentB);
                        }
                        if (direction == "DESC") {
                            if(contentA=="-"){
                                contentA = -1;
                            }
                            if(contentB=='-'){
                                contentB = -1;
                            }
                            return (contentA > contentB) ? -1 : (contentA < contentB) ? 1 : 0;
                        } else {
                            if(contentA=="-"){
                                contentA = 99999;
                            }
                            if(contentB=='-'){
                                contentB = 99999;
                            }
                            return (contentA < contentB) ? -1 : (contentA > contentB) ? 1 : 0;
                        }
                        break;
                }
                if (direction == "DESC") {
                    return (contentA > contentB) ? -1 : (contentA < contentB) ? 1 : 0;
                } else {
                    return (contentA < contentB) ? -1 : (contentA > contentB) ? 1 : 0;
                }
            });
            await $(".result table tbody").html(result);
            let stt=1;
            $(".result tbody tr").each(function (e) {
                let _this = $(this);
                _this.find("td.stt").text(stt);
                stt++;
            });
            setTimeout(function () {

                $(".loading-block").removeClass("active");
            },500);
        });
        $(".btn-delete").click(function (e) {
            let data = [];
            $("tbody input").each(function(e) {
                let _this = $(this);
                if (_this.is(":checked")) {
                    data.push(_this.val());
                }
            });
            if(data.length===0){
                setModalAlert("Bạn chưa chọn từ khóa!");
            }else{
                setModalConfirm("Bạn có chắc chắn muốn xóa những từ khóa đã chọn?",function (e) {
                    var project_id = $("#project_id").val();
                    var _data = [];
                    $(".page-keyword-project-detail tbody input").each(function (e) {
                        var _this = $(this);
                        if(_this.is(":checked")){
                            _data.push(_this.attr("data-id"));
                        }
                    });
                    $.ajax({
                        method   :"POST",
                        url      :"/cassiopeia/ajax",
                        data     : {
                            cmd : "user-delete-key-search",
                            _data : JSON.stringify(_data),
                            project_id:project_id,
                        },
                        success : function(result){
                            location.reload();
                        }
                    });
                });
            }
        });
        $("#modalTags button").click(function (e) {
            $(".loading-block").addClass("active");
            let nid = $("#project_id").val();
            var tags = $("#modalTags input.tagify-input").val();
            var option = modalTagOption.val();
            var data = [];
            $(" tbody input").each(function (e) {
                var _this = $(this);
                if(_this.is(":checked")){
                    data.push(_this.attr("data-id"));
                }
            });
            if(data.length>0){
                $.ajax({
                    method   :"POST",
                    url      :"/cassiopeia/ajax",
                    data     : {
                        cmd : "keyword-change-tags",
                        data : JSON.stringify(data),
                        tags : tags,
                        option : option,
                        nid : nid,
                    },
                    success : function(result){
                        $(".loading-block").removeClass("active");
                        location.reload();
                    }
                });
            }
        });
        $(".btn-key-check").click(async function (e) {
            if ($("#footer").hasClass("seoToolExtension") !== true) {
                installExtension();
            }else{
                var data = [];
                $("tbody input").each(function (e) {
                    var _this = $(this);
                    if(_this.is(":checked")){
                        data.push(_this.val());
                    }
                });

                if(data.length===0){
                    setModalAlert("Bạn chưa chọn từ khóa!");
                }else{
                  let captcha_resolve = $("select[name='captcha-resolve']").val();
                  if(captcha_resolve==="auto"){ // giải captcha tự động
                    jQuery.ajax({
                      method: "POST",
                      url: "/cassiopeia-captcha/resolve/get-info",
                      success: function (result) {
                        // let response = JSON.parse(result);
                        // console.log("result",result);
                        if(result.remaining<1 ){
                          let a = $.confirm({
                            title: 'Hết lượt giải captcha tự động!',
                            content: 'Bạn đã hết lượt giải captcha tự động!',
                            columnClass: 'captcha_resolve_limited col-md-6 col-md-offset-3 col-sm-6 col-sm-offset-3 col-xs-10 col-xs-offset-1',
                            buttons: {
                              formSubmit: {
                                text: '<span class="icon glyphicon glyphicon-plus" aria-hidden="true"></span> Mua thêm',
                                btnClass: 'btn-success',
                                action: function () {
                                  // document.dispatchEvent(new CustomEvent('CheckKeyword', {detail: '123'}))
                                  window.open(
                                    'https://seominisuite.com//captcha/resolve/booking',
                                    '_blank' // <- This is what makes it open in a new window.
                                  );
                                }
                              },
                              continue: {
                                text: 'Kiểm tra thủ công',
                                btnClass: 'btn-default',
                                action: function () {
                                  $("select[name='captcha-resolve']").val("manual");
                                  document.dispatchEvent(new CustomEvent('CheckKeyword', {detail: '123'}))
                                }
                              },
                              cancel: {
                                text: '×',
                                btnClass: 'btn-default btn-close',
                                action: function () {

                                }
                              },
                            }
                          });
                        }else{
                          document.dispatchEvent(new CustomEvent('CheckKeyword', {detail: '123'}))
                        }
                      }
                    });
                  }else{
                    document.dispatchEvent(new CustomEvent('CheckKeyword', {detail: '123'}))
                  }
                }
            }
        });
        $(".form-bulk button").click(function (e) {
            var option = $(".form-bulk select").val();
            var data = [];
            $("tbody input").each(function (e) {
                var _this = $(this);
                if(_this.is(":checked")){
                    data.push(_this.val());
                }
            });
            switch (option){
                case "keywordCheck" :
                    if(data.length===0){
                        setModalAlert("Bạn chưa chọn từ khóa!");
                    }else{

                        $(".loading-block").addClass("active");
                        setTimeout(function(){ document.dispatchEvent(new CustomEvent('CheckKeyword', {detail: '123'})); }, 500);
                    }
                    break;
                case "deleteKeyword" :
                    if(data.length===0){
                        setModalAlert("Bạn chưa chọn từ khóa!");
                    }else{
                        setModalConfirm("Bạn có chắc chắn muốn xóa những từ khóa đã chọn?",function (e) {
                            var project_id = $("#project_id").val();
                            var _data = [];
                            $(".page-keyword-project-detail tbody input").each(function (e) {
                                var _this = $(this);
                                if(_this.is(":checked")){
                                    _data.push(_this.attr("data-id"));
                                }
                            });
                            $.ajax({
                                method   :"POST",
                                url      :"/cassiopeia/ajax",
                                data     : {
                                    cmd : "user-delete-key-search",
                                    _data : JSON.stringify(_data),
                                    project_id:project_id,
                                },
                                success : function(result){
                                    location.reload();
                                }
                            });
                        });
                    }
                    break;
                case "addToTags" :
                    if (data.length === 0) {
                        setModalAlert("Bạn chưa chọn từ khóa!");
                    } else {
                        modalTagOption.val("ADD");
                        modalTagTitle.text("Thêm vào tags");
                        modalTag.modal("show");
                        modalTag.css("display","flex");
                        modalTag.css("align-items","center");
                    }

                    break;
                case "removeFormTags" :
                    if (data.length === 0) {
                        setModalAlert("Bạn chưa chọn từ khóa!");
                    } else {
                        modalTagOption.val("DELETE");
                        modalTagTitle.text("Xóa khỏi tags");
                        modalTag.modal("show");
                        modalTag.css("display","flex");
                        modalTag.css("align-items","center");
                    }

                    break;
            }

        });
        $(".selectAll").change(function (e) {
            if($(this).is(":checked")){
                $("tbody input").prop("checked",true);
                // $("tbody tr").addClass("tr-checked");
            } else{
                $("tbody input").prop("checked",false);
                // $("tbody tr").removeClass("tr-checked");
            }
        });
        $(".btn-add-key").click(function (e) {
            $("#modalAddKeyword input[name='keyword_nid']").val("");
            $("#modalAddKeyword").css({"display":"flex","align-items":"center"});
            $("#modalAddKeyword").modal("show");
            $("#modalAddKeyword .modal-title").text("Thêm mới từ khóa");
        });

    })
})(jQuery);
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