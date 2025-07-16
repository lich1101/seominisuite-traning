var Guest_Post_Duplicate_Content_Check_Complete_Listener = false;
function setModalConfirm(_html, _function, textConfirm = false,text="xóa") {
    jQuery("#modalConfirm").modal("show");
    jQuery("#modalConfirm").css("display", "flex");
    jQuery("#modalConfirm").css("align-items", "center");
    jQuery("#modalConfirm .modal-body").html(_html);
    if (textConfirm !== true) {
        jQuery("#modalConfirm .modal-confirm").hide();
    }else{
        jQuery("#modalConfirm .modal-confirm input").attr("placeholder",text);
    }
    jQuery("#modalConfirm .btn-ok").click(function(e) {
        if (textConfirm === true) {

            // let confirm = jQuery("input[name='confirm']").val();
            // if (confirm.trim().toLowerCase() === text) {
                _function();
                // jQuery("#modalConfirm").modal("hide");
                // jQuery("#modalConfirm .btn-ok").unbind();
            // } else {
            //     jQuery("#modalConfirm .modal-confirm input").addClass("errors");
            //     jQuery("#modalConfirm .modal-confirm input").val("");
            // }
        } else {
            _function();
            jQuery("#modalConfirm").modal("hide");
            jQuery("#modalConfirm .btn-ok").unbind();
        }
    });
}

function removeAccents(str) {
    var AccentsMap = [
        "aàảãáạăằẳẵắặâầẩẫấậ",
        "AÀẢÃÁẠĂẰẲẴẮẶÂẦẨẪẤẬ",
        "dđ", "DĐ",
        "eèẻẽéẹêềểễếệ",
        "EÈẺẼÉẸÊỀỂỄẾỆ",
        "iìỉĩíị",
        "IÌỈĨÍỊ",
        "oòỏõóọôồổỗốộơờởỡớợ",
        "OÒỎÕÓỌÔỒỔỖỐỘƠỜỞỠỚỢ",
        "uùủũúụưừửữứự",
        "UÙỦŨÚỤƯỪỬỮỨỰ",
        "yỳỷỹýỵ",
        "YỲỶỸÝỴ"
    ];
    for (var i = 0; i < AccentsMap.length; i++) {
        var re = new RegExp('[' + AccentsMap[i].substr(1) + ']', 'g');
        var char = AccentsMap[i][0];
        str = str.replace(re, char);
    }
    return str;
}

function setModalAlert(_html, _function = null,_header="") {
    jQuery(".modal").modal("hide");
    jQuery("#modalAlert").modal("show");
    jQuery("#modalAlert").css("display", "flex");
    jQuery("#modalAlert").css("align-items", "center");
    if(_header===""){
        jQuery("#modalAlert .modal-header").addClass("hidden");
    }
    jQuery("#modalAlert .modal-header").html(_header);
    jQuery("#modalAlert .modal-body").html(_html);
    if (_function != null) {
        jQuery("#modalAlert button").click(function(e) {
            _function();
        });
    }
}

function SearchKeyword(e) {
    // var t = document.createEvent("CustomEvent");
    //     // // currentKeywordId = e.Id, domainCheck = (domainCheck = e.GetLinkDomain).replace("//www.", "//");
    //     // var o =   "/search?q=123&safe=off&num=100&start=0&ie=utf-8&oe=utf-8&pws=0&_=" + (new Date).getTime();
    //     // t.initCustomEvent("RequestLink", !0, !1, {
    //     //     url: o,
    //     // }), document.dispatchEvent(t)

    document.dispatchEvent(new CustomEvent('RequestLink', {
        detail: '123'
    }))
}
function formatNumber(n) {
    // format number 1000000 to 1,234,567
    return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}
(function($) {

    $(document).ready(function() {
        $.fn.cassiopeiaAlert = function (data) {
            let _html = "<ul class='pd-0 mg-0'>";
            jQuery.each(JSON.parse(data), function( index, value ) {
                _html+="<li>"+value+"</li>";
            });
            _html+="</ul>";
            setModalAlert(_html,function () {

            },"<h3 class='margin-0'>Yêu cầu gửi đi thành công!</h3>");
        }
        $(".program-running").click(function (e) {
           alert("Chương trình đang chạy, vui lòng chờ trong ít phút!");
        });
        document.addEventListener("Guest_Post_Article_Add_Complete", function (data) {
            console.log("Guest_Post_Article_Add_Complete");
            $.ajax({
                method: "POST",
                url: "https://seominisuite.com/cassiopeia_guest_post/ajax",
                data: {
                    cmd: "GuestPostArticleAddResponse",
                    wp_post: data.detail.wp_post,
                    id: data.detail.id,
                },
                success: function (result) {
                    if(result.response=="FAIL"){
                        alert("Website đã bị lỗi, vui lòng chọn website khác để đăng bài!");
                        $(".loading-block").remove();
                        Drupal.CTools.Modal.dismiss();
                        return false;
                    }else{
                        location.href="/guest-post/article";
                    }
                }
            });
        });
        $("body").on("keyup","input.number-format",function (e) {
           let _this = $(this);
           _this.val(formatNumber(_this.val()));
        });
        $(".node-regulation-detail .right-nav ul li").click(function() {
            let _id = $(this).attr("data-id");
            let element = $(".node-regulation-detail .node-content .items .item[data-id='"+_id+"']");
            $([document.documentElement, document.body]).animate({
                scrollTop: element.offset().top - 73
            }, 500);
        });
        // console.log("word_count",word_count);
        $(".bank-no").click(function (e) {
            navigator.clipboard.writeText($(this).text());
        });
        $("select[name='rank']").change(function (e) {
           let _val = $(this).val();
           console.log("_val",_val);
           if(_val=="other"){
               $(".rank-other").addClass("active");
           }else{
               $(".rank-other").removeClass("active");
           }
        });
        // $("button[type=submit]").click(function (e) {
        //    $(".loading-block").addClass("active");
        // });
        $("#modalConfirm").on("hidden.bs.modal", function () {
            jQuery("#modalConfirm .btn-ok").unbind();
        });
        //
        $("#user-register-form button[type=submit]").click(function (e) {
            $(".loading-block").addClass("active");
        });
        $(".mobile-note .close").click(function(e) {
            $.ajax({
                method: "POST",
                url: "cassiopeia/ajax",
                data: {
                    cmd: "read-note",
                },
                success: function() {
                    $(".mobile-note").remove();
                }
            });
        });
        document.addEventListener("UserExpired", function(e) {
            jQuery('#modalConfirm').modal({
                backdrop: 'static',
                keyboard: false
            })
            jQuery("#modalConfirm .close").hide();
            jQuery("#modalConfirm").modal("show");
            jQuery("#modalConfirm").css("display", "flex");
            jQuery("#modalConfirm").css("align-items", "center");
            jQuery("#modalConfirm .modal-body").html("Tài khoản của bạn đã hết hạn, vui lòng gia hạn để sử dụng chức năng này!");
            jQuery("#modalConfirm .modal-confirm").hide();
            jQuery("#modalConfirm .btn-ok").text("Gia hạn");
            jQuery("#modalConfirm .btn-ok").click(function(e) {
                location.href = "/price-board";
            });

            jQuery("#modalConfirm .btn-default").click(function(e) {
                location.reload();
            });
        });
        document.addEventListener("UserLimited", function(e) {
            console.log("detail", e.detail);
            jQuery('#modalConfirm').modal({
                backdrop: 'static',
                keyboard: false
            })
            jQuery("#modalConfirm").modal("show");
            jQuery("#modalConfirm .close").hide();
            jQuery("#modalConfirm").css("display", "flex");
            jQuery("#modalConfirm").css("align-items", "center");
            jQuery("#modalConfirm .modal-body").html("Bạn đã dùng Vượt quá Tính năng trong gói Trial (Miễn phí). Bạn vui lòng nâng cấp tài khoản để sử dụng tiếp dịch vụ!");
            jQuery("#modalConfirm .modal-confirm").hide();
            jQuery("#modalConfirm .btn-ok").text("Nâng cấp");
            jQuery("#modalConfirm .btn-ok").click(function(e) {
                location.href = "/price-board";
            });
            jQuery("#modalConfirm .btn-default").click(function(e) {
                let url = window.location.href;
                if(url.indexOf('quan-ly-du-an-conten') != -1){

                }else{

                    location.reload();
                }
            });
        });
        setTimeout(function() {
            if ($(".header-note").length) {
                $(".header-note").css("display", "flex");
            }
        }, 1000)
        $('#user-profile-form .form-item-files-picture-upload input').change(function() {
            const file = this.files[0];
            console.log(file);
            if (file) {
                let reader = new FileReader();
                reader.onload = function(event) {
                    console.log(event.target.result);
                    $('.user-picture img').attr('src', event.target.result);
                }
                reader.readAsDataURL(file);
            }
        });
        if ($("footer").hasClass("seoToolExtension")) {

        } else {
            $(".header-main .note").text("Cài Extension để Sử dụng phần mềm");
        }
        $("body").on("click", "#message-model .close", function(e) {
            $("#message-model").remove();
        });
        $(".btn-login-form").click(function(e) {
            $("#modalUserLogin").css("display", "flex");
            $("#modalUserLogin").modal("show");
        });

        // bind the "click" event on the "remove all tags" button
        //         $('.tags-jquery--removeAllBtn').on('click', jqTagify.removeAllTags.bind(jqTagify));
        $(".btn-add-project").click(function() {
            SearchKeyword();
        });
        // document.addEventListener("RequestLink", function(data) {
        //     console.log(data.detail);
        // });
        $('.user-notify').on('click', function() {
            $(this).find('.items').toggleClass('active');
        });

        $('.sidebar-right ul li a').on('click', function(e) {
            var href = $(this).attr('href');
            var _top_ = $(href).offset().top - 74
            $('html, body').animate({
                scrollTop: _top_
            }, 600);
            e.preventDefault();
        });

        $('body').on('click','.sidebar ul li', function() {
            $(this).toggleClass('active').siblings().removeClass('active');
        });
        $('body').on('click','.page-check-content .tab-2 ul li', function() {
            $(this).toggleClass('active').siblings().removeClass('active');
        });
        $('body').on('click','.page-check-content .tab-3 ul li', function() {
            $(this).toggleClass('active').siblings().removeClass('active');
        });


        var dataColumns = [];
        var columnName;

        $(".columns-name .mask-input>input").change(function() {
            if (this.checked) {
                var _this = $(this);
                let data = _this.val();
                dataColumns.push(data);
            }
        });
        // $(".btn-buy-product").click(function(e) {
        //     var nid = $(this).attr("data-nid");
        //     $.ajax({
        //         method: "POST",
        //         url: "cassiopeia/ajax",
        //         data: {
        //             cmd: "buy-product",
        //             nid: nid,
        //         },
        //         success: function() {
        //             $(".loading-block").removeClass("active");
        //         }
        //     });
        // });
        $(".btn-add-to-cart").click(function() {
            $(".loading-block").addClass("active");
            var product_id = $(this).attr("data-nid");
            $.ajax({
                method: "POST",
                url: "cassiopeia/ajax",
                data: {
                    cmd: "add-to-cart",
                    product_id: product_id,
                },
                success: function() {
                    $(".loading-block").removeClass("active");
                }
            });
        });

        $(".toggle-menu").on("click", function() {
            $(this).toggleClass('active');
            $('body').toggleClass('open-sidebar');
        });



        //ADD TOOLTIP TO MODAL

        $(function() {
            $('[data-toggle="tooltip"]').tooltip({
                template: '<div class="tooltip" role="tooltip"><div class="arrow"></div><div class="tooltip-inner"></div></div>'
            })
        });

        var section = document.querySelectorAll('.page-tutorial-main .item');
        var sections = {};
        var i = 0;

        section.forEach(element => {
            sections[element.id] = element.offsetTop;
        });

        $(window).on('scroll', function() {
            var scrollPosition = document.documentElement.scrollTop || document.body.scrollTop;

            for (i in sections) {
                var Position = scrollPosition + $('#' + i).height();
                if (sections[i] <= Position) {
                    document.querySelector('#navbar-spy a.active').setAttribute('class', ' ');
                    document.querySelector('a[href*=' + i + ']').setAttribute('class', 'active');
                }
            }
        });

        new FroalaEditor('div#froala-editor', {
            listAdvancedTypes: true,
            toolbarButtons: ['bold', 'italic', 'underline', 'outdent', 'indent', 'clearFormatting', 'formatOL', 'formatUL', 'insertTable', 'align'],
            placeholderText: 'Nhập nội dung cần check...'
        });

    });
})(jQuery);