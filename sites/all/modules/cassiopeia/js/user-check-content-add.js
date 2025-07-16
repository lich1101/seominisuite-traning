function load_check_content(activeTab=1){
    let content = tinyMCE.activeEditor.getContent();
    jQuery(".cache-block").html(content);
    let dom_nodes = jQuery(".cache-block");
    let pNodes = dom_nodes.find("p");
    let h1Nodes = dom_nodes.find("h1");
    let isArticle = false;
    let result = {};
    result['h1'] = result['h2'] = result['h3'] = result['img'] = 0
    let articleContent = "";
    let h2Nodes = dom_nodes.find("h2");
    let h3Nodes = dom_nodes.find("h3");
    let imgNodes = dom_nodes.find("img");
    if(h2Nodes.length>0){
        let _h2 = [];
        jQuery.each( h2Nodes, function( key, value ) {
            _h2.push(jQuery(value).text());
        });
        result['h2'] = _h2.length;
    }
    if(h3Nodes.length>0){
        let _h3 = [];
        jQuery.each( h3Nodes, function( key, value ) {
            _h3.push(jQuery(value).text());
        });
        result['h3'] = _h3.length;
    }
    if(imgNodes.length>0){
        let _img = [];
        jQuery.each( imgNodes, function( key, value ) {
            _img.push(jQuery(value).text());
        });
        result['img'] = _img.length;
    }
    let _content = jQuery(".cache-block").text().trim();
    let splitter = _content.split(" ");
    let word_count = [];
    jQuery.each( splitter, function( key, value ) {
        if(value.length>0 && value.trim()!==" "){
            word_count.push(value);
        }
    });
    let nid = jQuery("#nid").val();
    let current_point = jQuery("input[name='field_content_point[und][0][value]']").val();
    let keyword = jQuery("input[name='keyword']").val();
    result['activeTab'] = activeTab;
    result['nid'] = nid;
    result['current_point'] = current_point;
    result['word_count'] = word_count.length;
    result['h1'] = h1Nodes.length;
    jQuery("input[name='field_content_word_count[und][0][value]']").val(result['word_count']);
    jQuery("input[name='field_content_h2_count[und][0][value]']").val(result['h2']);
    jQuery("input[name='field_content_h3_count[und][0][value]']").val(result['h3']);
    jQuery("input[name='field_content_img_count[und][0][value]']").val(result['img']);
    // let activeTab = jQuery(".page-check-content .information ul.nav li.active").attr("data-index");
    jQuery.ajax({
        method: "POST",
        url: "/cassiopeia/ajax",
        data: {
            cmd: "load-check-content",
            data: result,
        },
        success: function(result) {
            jQuery(".result").html(result.html);
            let point = jQuery(".gauge").attr("data-point");
            jQuery("input[name='field_content_point[und][0][value]']").val(point);
            jQuery("input[name='field_content_keyword[und][0][value]']").val(keyword);
        }
    });
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
            console.log('JQEURY EVENT: ', 'added', tagName)
        })
        .on("invalid", function(e, tagName) {
            console.log('JQEURY EVENT: ',"invalid", e, ' ', tagName);
        });
    var jqTagify = $input.data('tagify');
}

(function ($) {

    $(document).ready(function () {
        var unload = 0;
        $(window).on("load",function (e) {
            load_check_content();
            cassiopeiaTagify();
        });
        setTimeout(function (e) {

            var typingTimer;
            var ed = tinyMCE.get('edit-body-und-0-value');

            ed.on("keyup",function (e) {
                let content = ed.getContent();
                typingTimer = setTimeout(function() {
                    load_check_content();
                }, 350);
                unload = 1;
            });

            ed.on("keydown",function (e) {
                clearTimeout(typingTimer);
            });
            ed.on("click",function (e) {
                // var range = document.createRange();
                // range.setStart(p_parent, nodeIndex+1);
                // range.setEnd(p_parent, nodeIndex+1);
                // console.log("range",range);
                // var rng = tinymce.DOM.createRng();
                // console.log(rng.startContainer);
                // window.getSelection().addRange(range);
                // ed.selection.setRng(range,true);
            });
        },1000);
        $(window).on('beforeunload', function(){
            if(unload==1){
                if(unload==0){
                    return true;
                }
                if(confirm()){
                    return true;
                }
                else
                    return false;
            }
            console.log("unload",unload);
        });
        $("body").on("click",".btn-add-heading",function (e) {
            e.stopPropagation();
            let text = $(this).attr("data-text");
            let heading = $(this).attr("data-heading");
            if(heading=="h1"){
                $("input[name='title']").val(text);
            }else{
                var ed = tinyMCE.get('edit-body-und-0-value');                // get editor instance
                // var range = ed.selection.getRng();                  // get range
                // console.log("range",range);
                var newNode = ed.getDoc().createElement (heading );  // create img node

                // newNode.insert;
                newNode.innerHTML = text;

                var current_node = ed.selection.getNode();
                var dom_current_node = $(current_node);

                if(dom_current_node.is("p")){
                    dom_current_node.replaceWith(newNode);
                }else if(dom_current_node.is("h1")||dom_current_node.is("h2")||dom_current_node.is("h3")){
                    dom_current_node.after(newNode);
                }else{
                    dom_current_node.append(newNode);
                }
                var range = ed.selection.getRng();
                console.log("range",range);
                // ed.focus();
            }
        })
        $("body").on("click",".page-check-content .information ul.nav.active li.tab-3 a",function (e) {
           load_check_content(3);
        });
        $(".btn-save").click(function (e) {
            load_check_content();
            let title = $("input[name='title']").val().trim();
            if(title.length<1){
                setModalAlert("Bạn chưa nhập tiêu đề bài viết!");
                $("input[name='title']").focus();
                return false;
            }
           $("form.node-form").submit();
            unload =0;
        });
        $(".btn-check-content").click(function (e) {
            load_check_content();
            if ($("#footer").hasClass("seoToolExtension") !== true) {
                installExtension();
            }else{
                let keyword = $("input[name='keyword']").val();
                if(keyword.trim()===""){
                    setModalAlert("Bạn chưa nhập từ khóa!");
                    return false;
                }
              let captcha_resolve = $("select[name='captcha-resolve']").val();
              if(captcha_resolve==="auto"){ // giải captcha tự động
                jQuery.ajax({
                  method: "POST",
                  url: "/cassiopeia-captcha/resolve/get-info",
                  success: function (result) {
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
                              // location.href = "/captcha/resolve/booking";
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
                              document.dispatchEvent(new CustomEvent('CheckContent', {detail: '123'}))
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
                      document.dispatchEvent(new CustomEvent('CheckContent', {detail: '123'}))
                    }
                  }
                });
              }else{
                document.dispatchEvent(new CustomEvent('CheckContent', {detail: '123'}))
              }
            }
        });
        document.addEventListener("CheckContentComplete", function(e) {
            load_check_content();
        });
        $("body").on("click",".page-check-content .tab-detail li ul li",function (e) {
           e.stopPropagation();
        });
        $("body").on("click",".btn-goto",function (e) {
           e.stopPropagation();
        });
        $("body").click(function (e) {
           $(".tutorial-background").remove();
           $(".tutorial-text").remove();
        });


    })
})(jQuery);