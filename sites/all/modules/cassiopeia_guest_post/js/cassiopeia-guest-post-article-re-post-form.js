
if (typeof (window.cassiopeia_tagifies) == 'undefined') {
    window.cassiopeia_tagifies = {};
}
(function ($) {
    Drupal.behaviors.cassiopeia_guest_post_article_form = {
        attach: function (context, settings) {
            $('.cassiopeia-guest-post-article-form', context).once('cassiopeia-guest-post-article-form',function () {
                console.log("attach");
                var typingTimer;
                var str = "<div class=\"loading-block active\">\n" +
                    "    <div class=\"loading-block-container\">\n" +
                    "        <div class=\"lds-css ng-scope\">\n" +
                    "            <div class=\"lds-spin\" style=\"width:100%;height:100%\"><div><div></div></div><div><div></div></div><div><div></div></div><div><div></div></div><div><div></div></div><div><div></div></div><div><div></div></div><div><div></div></div></div>\n" +
                    "        </div>\n" +
                    "    </div>\n" +
                    "</div>";
                var $modalPreSave = $("#modal-pre-save");


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
                $("document").ready(function (e) {
                    cassiopeiaTagify();
                    $("#modalContent").addClass("casiopeia-modal-article-form");
                    setTimeout(function (e) {
                        var typingTimer;
                        let element = $("textarea[name='content[value]']");
                        if(element.length){
                            let element_id = element.attr("id");
                            console.log("element_id",element_id);
                            var ed = tinyMCE.get(element_id);
                            console.log("ed",ed);
                            ed.on("keyup",function (e) {
                                let content = ed.getContent();
                                typingTimer = setTimeout(function() {
                                    $("input[name='non_duplicate']").val(0);
                                }, 350);
                                unload = 1;
                            });

                            ed.on("keydown",function (e) {
                                clearTimeout(typingTimer);
                            });
                        }
                    },1000);

                    let real_image = $("input[name='files[image]']");
                    let fake_image = $('.fake-image');
                    fake_image.on('click',function(e){
                        real_image.click();
                    });
                    real_image.change(function (e) {
                        const file = this.files[0];
                        if(file===undefined){
                            $('#imgPreview').attr('src', "/sites/all/themes/cassiopeia_admin_theme_white/images/icon-choose-file.png");
                            $(".img-name").html(" " +
                                "<div>Chọn ảnh từ máy tính</div>\n" +
                                "                    <div>hoặc kéo và thả</div>");
                        }else{
                            if (file){
                                let reader = new FileReader();
                                reader.onload = function(event){
                                    $('#imgPreview').attr('src', event.target.result);
                                    $(".img-name").text(file.name);
                                }
                                reader.readAsDataURL(file);
                            }
                        }
                    });
                    fake_image.on('drop dragdrop',function(e){
                        e.preventDefault();
                        e.stopPropagation();
                        droppedFiles = e.originalEvent.dataTransfer.files;
                        real_image.prop("files",droppedFiles);
                        real_image.trigger("change");
                    });
                    fake_image.on('dragenter',function(event){
                        event.preventDefault();
                        // $(this).html('drop now').css('background','blue');
                    });
                    fake_image.on('dragleave',function(){
                        // $(this).html('drop here').css('background','red');
                    });
                    fake_image.on('dragover',function(event){
                        event.preventDefault();
                    })
                    $(".cassiopeia-guest-post-article-form .targets span.text").click(function (e) {
                        $(".list-of-target").toggleClass("active");
                    });
                    // guestPostAddConfirm
                    // $("#modal-pre-save .btn-save").click(function (e) {
                    //     $(".cassiopeia-guest-post-article-form .btn-save-post").trigger("mousedown");
                    // });
                });
                document.addEventListener("CheckContentComplete", function (e) {
                    $(".btn-form-build").trigger("mousedown");
                });
                $(".btn-close-modal").click(function (e) {
                    Drupal.CTools.Modal.dismiss();
                    return false;
                });
                $.fn.guestPostAddConfirm = function (data) {
                    let access = JSON.parse(data);
                    console.log("access,",access);
                    if(access){
                        $(".btn-pre-save-form").trigger("click");
                    }else{
                        setModalConfirm("Bạn cần nâng cấp gói để được đăng bài!", function(e) {
                            window.open(
                                '/price-board',
                                '_blank' // <- This is what makes it open in a new window.
                            );
                        })
                    }
                };
                $.fn.guestPostAddConfirmed = function (data) {
                    $(".cassiopeia-guest-post-article-form .btn-save-post").trigger("mousedown");
                };
                document.addEventListener("Guest_Post_Duplicate_Content_Check_Complete", function (e) {
                    let noneDuplicate = JSON.parse(e.detail);
                    $("input[name='non_duplicate']").val(noneDuplicate);
                });

                $(".btn-outline-content-check").click(function (e) {
                    let keyword = $("input[name='outline_content[keyword]']").val();
                    document.dispatchEvent(new CustomEvent('Guest_Post_Outline_Content_Check', {detail: JSON.stringify(keyword)}));
                    return false;
                });
                $.fn.guestPostArticleAdd = function (data) {
                    $modalPreSave.modal("hide");
                    $('.cassiopeia-guest-post-article-form').append(str);
                    document.dispatchEvent(new CustomEvent('Guest_Post_Article_Add', {detail: data}))
                }
                $.fn.guestPostAlert = function (data) {
                    // Drupal.CTools.Modal.dismiss();
                    // $modalPreSave.modal("hide");
                    let _html = "<ul class='pd-0 mg-0'>";
                    jQuery.each(JSON.parse(data), function( index, value ) {
                        _html+="<li>"+value+"</li>";
                    });
                    _html+="</ul>";
                    setModalAlert(_html);
                }
                $.fn.guestPostDuplicateContentCheck = function (data) {
                    data = JSON.parse(data);
                    textContent = data.replace(/(<([^>]+)>)/gi, ".");
                    let splitter = textContent.split(".");

                    let textArray = [];
                    let temp = "";
                    let id = 1;
                    let tempHTML = "";
                    let _count = 0;
                    let stt=1;
                    for (let i = 0; i < splitter.length; i++) {
                        temp = splitter[i];
                        if (temp === "") {
                            continue;
                        }
                        // temp  = textContent.replace(/(<([^>]+)>)/gi, "/");
                        console.log("temp",temp);
                        let childSplitter = temp.split(" ");
                        console.log("childSplitter",childSplitter);
                        if (childSplitter.length > 15) {
                            let childTempTextArray = [];
                            let temTextArray = [];
                            for (let j = 0; j < childSplitter.length; j++) {
                                childTempTextArray.push(childSplitter[j]);
                                if (j % 15 === 0 && j > 0) {
                                    temTextArray.push(childTempTextArray);
                                    childTempTextArray = [];
                                }
                            }
                            temTextArray.push(childTempTextArray);
                            if (temTextArray[temTextArray.length - 1].length < 15) {
                                if (temTextArray[temTextArray.length - 2].length) {
                                    temTextArray[temTextArray.length - 2] = temTextArray[temTextArray.length - 2].concat(temTextArray[temTextArray.length - 1]);
                                    temTextArray.splice(-1, 1);
                                }
                            }
                            for (let k = 0; k < temTextArray.length; k++) {
                                let tempString = temTextArray[k].join(" ").trim();
                                tempString = tempString.trim();
                                tempHTML += "<tr><td data-sort='string' data-id='" + id + "' class='part-of-content text-left' title='"+tempString+"'>" + tempString + "</td><td data-key='source' class='sources'></td><td data-key='result' class='check-result'></td></tr>";
                                id++;
                                stt++;
                            }
                            _count++;
                        } else {
                            if (childSplitter.length > 3) {
                                _count++;
                                temp = temp.trim();
                                textArray.push(temp);
                                tempHTML += "<tr><td data-sort='string' data-id='" + id + "' class='part-of-content text-left' title='"+temp+"'>" + temp + "</td><td data-key='source' class='sources'></td><td data-key='result' class='check-result'></td></tr>";

                                temp = "";
                                id++;
                                stt++;
                            }else{
                                // alert("Đoạn văn quá ngắn!");
                                // return false;
                            }
                        }
                    }
                    if(_count===0){
                        setModalAlert("Đoạn văn quá ngắn!");
                        return false;
                    }

                    $(".duplicate-content-check .table-result").html(tempHTML);
                    document.dispatchEvent(new CustomEvent('Guest_Post_Duplicate_Content_Check', {detail: data}))
                }
                $(".btn-add-heading").click(function (e) {
                    e.stopPropagation();
                    let element = $("textarea[name='content[value]']");
                    let element_id = element.attr("id");
                    let text = $(this).attr("data-text");
                    let heading = $(this).attr("data-heading");
                    if(heading=="h1"){
                        $("input[name='title']").val(text);
                    }else{
                        var ed = tinyMCE.get(element_id);                // get editor instance
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
                    }
                    return false;
                });
            });
        },
        detach: function(context, settings, trigger) {
            $('.cassiopeia-guest-post-article-form', context).removeOnce('cassiopeia-guest-post-article-form', function() {});
        }
    };
})(jQuery);