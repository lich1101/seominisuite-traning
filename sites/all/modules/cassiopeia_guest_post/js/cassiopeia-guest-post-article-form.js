
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
                $(".btn-final").each(function (e) {
                    let _this = $(this);
                    Drupal.ajax[_this.attr("id")].options.beforeSubmit = function(){
                        $(".cassiopeia-guest-post-article-form").append(str);
                    };
                });
                let real_image = $("input[name='files[image]']");
                // let fake_image = $('.fake-image');
                // fake_image.on('click',function(e){
                //     real_image.click();
                // });
                real_image.change(function (e) {
                    const file = this.files[0];
                    if(file===undefined){
                        $(".cassiopeia-guest-post-article-form .form-managed-file button").removeClass("active");
                    }else{
                        if (file){
                            $(".cassiopeia-guest-post-article-form .form-managed-file button").addClass("active");
                        }
                    }
                });
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
                    $(".program-running").removeClass("active");
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
                $("div.none-duplicate").click(function (e) {
                    $("#duplicate-group .total-result-item").removeClass("active");
                    $(this).addClass("active");
                    $("#duplicate-group .table-result tr").each(function (e) {
                        let _this = $(this);
                        if(_this.find("td.check-result").attr("data-value")!=0){
                            _this.hide();
                        }else{
                            _this.show();
                        }
                    });
                });
                $("div.duplicate").click(function (e) {
                    $("#duplicate-group .total-result-item").removeClass("active");
                    $(this).addClass("active");
                    $("#duplicate-group .table-result tr").each(function (e) {
                        let _this = $(this);
                        if(_this.find("td.check-result").attr("data-value")==0){
                            _this.hide();
                        }else{
                            _this.show();
                        }
                    });
                });
                $("div.all").click(function (e) {
                    $("#duplicate-group .total-result-item").removeClass("active");
                    $(this).addClass("active");
                    $("#duplicate-group .table-result tr").show();
                });
                $.fn.guestPostAddConfirmed = function (data) {
                    $(".cassiopeia-guest-post-article-form .btn-save-post").trigger("mousedown");
                };
                if(!Guest_Post_Duplicate_Content_Check_Complete_Listener){
                    Guest_Post_Duplicate_Content_Check_Complete_Listener = true;
                    document.addEventListener("Guest_Post_Duplicate_Content_Check_Complete", function (e) {
                        $(".program-running").removeClass("active");
                        setModalAlert("Nếu bạn muốn chỉnh sửa bài viết, cần Kiểm tra Đạo văn lại Bài viết!");
                        let noneDuplicate = JSON.parse(e.detail);
                        $("input[name='non_duplicate']").val(noneDuplicate);
                    });
                }

                $(".btn-outline-content-check").click(function (e) {
                    if ($("#footer").hasClass("seoToolExtension") !== true) {
                        installExtension();
                        return false;
                    }
                    let keyword = $("input[name='outline_content[keyword]']").val();
                    if(keyword.trim()===""){
                        setModalAlert("Bạn chưa nhập từ khóa!");
                        return false;
                    }
                    $(".program-running").addClass("active");
                    document.dispatchEvent(new CustomEvent('Guest_Post_Outline_Content_Check', {detail: JSON.stringify(keyword)}));
                    return false;
                });
                // $.fn.guestPostArticleAdd = function (data) {
                //     $modalPreSave.modal("hide");
                //     $('.cassiopeia-guest-post-article-form').append(str);
                //     document.dispatchEvent(new CustomEvent('Guest_Post_Article_Add', {detail: data}))
                // }
                $.fn.guestPostAlert = function (data) {
                    Drupal.CTools.Modal.dismiss();
                    $modalPreSave.modal("hide");
                    let _html = "<ul class='pd-0 mg-0'>";
                    jQuery.each(JSON.parse(data), function( index, value ) {
                       _html+="<li>"+value+"</li>";
                    });
                    _html+="</ul>";
                    setModalAlert(_html,function () {

                    },"<h3 class='margin-0'>Mời bạn chỉnh sửa lại bài viết</h3>");
                    $(".loading-block").remove();
                }
                
                // Lưu trữ dữ liệu kiểm tra đạo văn
                function saveDuplicateCheckData() {
                    console.log('Bắt đầu lưu dữ liệu kiểm tra đạo văn...');
                    let duplicateData = {
                        timestamp: new Date().getTime(),
                        results: {}
                    };
                    
                    // Lưu kết quả từng câu
                    $('.duplicate-content-check .duplicate-content-table tbody tr').each(function() {
                        let $row = $(this);
                        let id = $row.find('td[data-id]').attr('data-id');
                        if (id) {
                            let query = $row.find('td[data-id]').text().trim();
                            let sources = $row.find('.sources').html();
                            let result = $row.find('.check-result').attr('data-duplicate');
                            let resultValue = $row.find('.check-result').attr('data-value');
                            
                            duplicateData.results[id] = {
                                query: query,
                                sources: sources,
                                result: result,
                                resultValue: resultValue
                            };
                        }
                    });
                    
                    // Lưu tổng kết
                    let noneDuplicate = $('.total-result span.none-duplicate').text();
                    let duplicate = $('.total-result span.duplicate').text();
                    duplicateData.summary = {
                        noneDuplicate: noneDuplicate,
                        duplicate: duplicate
                    };
                    
                    // Lưu vào localStorage
                    localStorage.setItem('guest_post_duplicate_check_data', JSON.stringify(duplicateData));
                    console.log('Đã lưu dữ liệu kiểm tra đạo văn:', duplicateData);
                }
                
                // Khôi phục dữ liệu kiểm tra đạo văn
                function restoreDuplicateCheckData() {
                    console.log('Bắt đầu khôi phục dữ liệu kiểm tra đạo văn...');
                    let savedData = localStorage.getItem('guest_post_duplicate_check_data');
                    if (savedData) {
                        try {
                            let duplicateData = JSON.parse(savedData);
                            
                            // Kiểm tra xem dữ liệu có còn hợp lệ không (trong vòng 1 giờ)
                            let now = new Date().getTime();
                            if (now - duplicateData.timestamp > 3600000) { // 1 giờ
                                localStorage.removeItem('guest_post_duplicate_check_data');
                                return false;
                            }
                            
                            // KHÔNG khôi phục nội dung TinyMCE để tránh bị chèn nội dung bài trước đó
                            
                            // Khôi phục kết quả
                            if (duplicateData.results) {
                                console.log('Bắt đầu khôi phục kết quả cho', Object.keys(duplicateData.results).length, 'câu');
                                Object.keys(duplicateData.results).forEach(function(id) {
                                    let result = duplicateData.results[id];
                                    let $row = $('.duplicate-content-check .duplicate-content-table tbody tr').find('td[data-id="' + id + '"]').closest('tr');
                                    if ($row.length) {
                                        $row.find('.sources').html(result.sources);
                                        $row.find('.check-result').attr('data-duplicate', result.result);
                                        $row.find('.check-result').attr('data-value', result.resultValue);
                                        
                                        // Khôi phục icon
                                        if (result.result === 'true') {
                                            $row.find('.check-result').html('<i class="fa fa-times"></i>');
                                        } else {
                                            $row.find('.check-result').html('<i class="fa fa-check"></i>');
                                        }
                                        console.log('Đã khôi phục kết quả cho câu ID:', id);
                                    } else {
                                        console.log('Không tìm thấy row cho câu ID:', id);
                                    }
                                });
                            }
                            
                            // Khôi phục tổng kết
                            if (duplicateData.summary) {
                                $('.total-result span.none-duplicate').text(duplicateData.summary.noneDuplicate);
                                $('.total-result span.duplicate').text(duplicateData.summary.duplicate);
                                console.log('Đã khôi phục tổng kết:', duplicateData.summary);
                            }
                            
                            return true;
                        } catch (e) {
                            console.error('Error restoring duplicate check data:', e);
                            localStorage.removeItem('guest_post_duplicate_check_data');
                        }
                    }
                    return false;
                }
                
                // Xóa dữ liệu kiểm tra đạo văn
                function clearDuplicateCheckData() {
                    localStorage.removeItem('guest_post_duplicate_check_data');
                    console.log('Đã xóa dữ liệu kiểm tra đạo văn');
                }
                
                // Test function để kiểm tra dữ liệu
                function testDuplicateCheckData() {
                    let savedData = localStorage.getItem('guest_post_duplicate_check_data');
                    if (savedData) {
                        console.log('Dữ liệu hiện tại:', JSON.parse(savedData));
                    } else {
                        console.log('Không có dữ liệu');
                    }
                }
                
                // Thêm event listener để lưu dữ liệu khi có thay đổi
                $(document).ready(function() {
                    // Đợi TinyMCE khởi tạo xong rồi mới khôi phục dữ liệu
                    function waitForTinyMCE() {
                        if (typeof tinyMCE !== 'undefined' && tinyMCE.activeEditor) {
                            // Khôi phục dữ liệu khi TinyMCE đã sẵn sàng
                            setTimeout(function() {
                                if (restoreDuplicateCheckData()) {
                                    console.log('Đã khôi phục dữ liệu kiểm tra đạo văn');
                                }
                            }, 500);
                        } else {
                            // Thử lại sau 100ms nếu TinyMCE chưa sẵn sàng
                            setTimeout(waitForTinyMCE, 100);
                        }
                    }
                    
                    // Đợi bảng kết quả được tạo xong
                    function waitForResultTable() {
                        if ($('.duplicate-content-check .duplicate-content-table tbody tr').length > 0) {
                            // Bảng đã có dữ liệu, khôi phục ngay
                            if (restoreDuplicateCheckData()) {
                                console.log('Đã khôi phục dữ liệu kiểm tra đạo văn sau khi bảng sẵn sàng');
                            }
                        } else {
                            // Thử lại sau 200ms nếu bảng chưa sẵn sàng
                            setTimeout(waitForResultTable, 200);
                        }
                    }
                    
                    // Bắt đầu đợi TinyMCE và bảng kết quả
                    waitForTinyMCE();
                    waitForResultTable();
                    
                    // Kiểm tra xem có dữ liệu đã lưu không
                    let savedData = localStorage.getItem('guest_post_duplicate_check_data');
                    if (savedData) {
                        console.log('Phát hiện dữ liệu kiểm tra đạo văn đã lưu:', JSON.parse(savedData));
                    } else {
                        console.log('Không có dữ liệu kiểm tra đạo văn đã lưu');
                    }
                    
                    // Thêm function test vào window để có thể gọi từ console
                    window.testDuplicateCheckData = testDuplicateCheckData;
                    window.saveDuplicateCheckData = saveDuplicateCheckData;
                    window.restoreDuplicateCheckData = restoreDuplicateCheckData;
                    window.clearDuplicateCheckData = clearDuplicateCheckData;
                    window.waitForResultTable = waitForResultTable;
                    console.log('Đã thêm các function test vào window. Gọi testDuplicateCheckData() để kiểm tra dữ liệu.');
                    console.log('Gọi waitForResultTable() để đợi bảng kết quả và khôi phục dữ liệu.');
                    
                    // Lưu dữ liệu khi có thay đổi trong bảng kết quả
                    $(document).on('DOMSubtreeModified', '.duplicate-content-check .duplicate-content-table', function() {
                        // Chỉ lưu khi có dữ liệu thực sự
                        if ($('.duplicate-content-check .duplicate-content-table .check-result[data-duplicate]').length > 0) {
                            setTimeout(function() {
                                saveDuplicateCheckData();
                            }, 100);
                        }
                    });
                    
                    // Lưu dữ liệu khi thay đổi nội dung
                    $(document).on('TinyMCE_Ready', function() {
                        if (typeof tinyMCE !== 'undefined' && tinyMCE.activeEditor) {
                            tinyMCE.activeEditor.on('change', function() {
                                // Chỉ lưu khi có dữ liệu kiểm tra đạo văn
                                if ($('.duplicate-content-check .duplicate-content-table .check-result[data-duplicate]').length > 0) {
                                    saveDuplicateCheckData();
                                }
                            });
                        }
                    });
                    
                    // Xóa dữ liệu khi submit form thành công
                    $(document).on('Guest_Post_Article_Add', function() {
                        clearDuplicateCheckData();
                    });
                    
                    // Lưu dữ liệu khi hoàn thành kiểm tra đạo văn
                    $(document).on('Guest_Post_Duplicate_Content_Check_Complete', function(e) {
                        setTimeout(function() {
                            saveDuplicateCheckData();
                            console.log('Đã lưu dữ liệu kiểm tra đạo văn sau khi hoàn thành');
                        }, 1000);
                    });
                    
                    // Lưu dữ liệu khi có kết quả từng câu
                    $(document).on('click', '.check-result', function() {
                        setTimeout(function() {
                            saveDuplicateCheckData();
                            console.log('Đã lưu dữ liệu kiểm tra đạo văn sau khi có kết quả từng câu');
                        }, 100);
                    });
                    
                    // Lưu dữ liệu khi có thay đổi trong kết quả kiểm tra
                    $(document).on('DOMSubtreeModified', '.check-result', function() {
                        setTimeout(function() {
                            if ($(this).attr('data-duplicate') !== undefined) {
                                saveDuplicateCheckData();
                                console.log('Đã lưu dữ liệu kiểm tra đạo văn sau khi có kết quả mới');
                            }
                        }, 100);
                    });
                    
                    // Lưu dữ liệu khi có thay đổi trong sources
                    $(document).on('DOMSubtreeModified', '.sources', function() {
                        setTimeout(function() {
                            if ($(this).html().trim() !== '') {
                                saveDuplicateCheckData();
                                console.log('Đã lưu dữ liệu kiểm tra đạo văn sau khi có sources mới');
                            }
                        }, 100);
                    });
                    
                    // Lưu dữ liệu khi có kết quả từ extension
                    $(document).on('GuestPostDuplicateContentCheck_Result', function(e) {
                        setTimeout(function() {
                            saveDuplicateCheckData();
                            console.log('Đã lưu dữ liệu kiểm tra đạo văn sau khi có kết quả từ extension');
                        }, 200);
                    });
                });
                
                $.fn.guestPostDuplicateContentCheck = function (data) {
                    if ($("#footer").hasClass("seoToolExtension") !== true) {
                        installExtension();
                        return false;
                    }
                    data = JSON.parse(data);
                    // textContent = data.replace('&nbsp;', " ");
                    textContent = data.replace(/(<([^>]+)>)/gi, " ");
                    let dom_nodes = $($.parseHTML(data));
                    console.log("dom_nodes",data);
                    console.log("textContent",textContent);
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
                        // console.log("temp",temp.trim());
                        let childSplitter = temp.split(" ");
                        let newChildSpillter = new Array();
                        childSplitter.forEach(function (item,index) {
                            if(item.replace(/ /g,'').length>0){
                                newChildSpillter.push(item);
                            }
                        });
                        childSplitter = newChildSpillter;
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
                                  location.href = "/captcha/resolve/booking";
                                }
                              },
                              continue: {
                                text: 'Kiểm tra thủ công',
                                btnClass: 'btn-default',
                                action: function () {
                                  $("select[name='captcha-resolve']").val("manual");
                                  $(".duplicate-content-check .table-result").html(tempHTML);
                                  $(".program-running").addClass("active");
                                  document.dispatchEvent(new CustomEvent('Guest_Post_Duplicate_Content_Check', {detail: data}))
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
                          $(".duplicate-content-check .table-result").html(tempHTML);
                          $(".program-running").addClass("active");
                          document.dispatchEvent(new CustomEvent('Guest_Post_Duplicate_Content_Check', {detail: data}))
                        }
                      }
                    });
                  }else{
                    $(".duplicate-content-check .table-result").html(tempHTML);
                    $(".program-running").addClass("active");
                    document.dispatchEvent(new CustomEvent('Guest_Post_Duplicate_Content_Check', {detail: data}))
                  }
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