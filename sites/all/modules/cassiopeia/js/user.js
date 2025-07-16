function cassiopeia_remove_class(_class) {
    jQuery("input").removeClass(_class);
}

function cassiopeia_required_check(_element) {
    var _form = jQuery("form").has(_element);
    var inputs = (_form.find("input.required"));
    var flag = true;
    inputs.each(function(e) {
        var temp = jQuery(this).val();
        if (temp.trim() == "") {
            jQuery(this).addClass("_required");
            flag = false;
        } else {
            jQuery(this).removeClass("_required");
        }
    });
    if (flag == false) {
        setModalAlert("Vui lòng nhập đầy đủ thông tin !");
    }
    return flag;
}
function installExtension(){
    // jQuery('#modalConfirm').modal({backdrop: 'static', keyboard: false})
    // jQuery("#modalConfirm .close").hide();
    jQuery("#modalConfirm").modal("show");
    jQuery("#modalConfirm").css("display", "flex");
    jQuery("#modalConfirm").css("align-items", "center");
    jQuery("#modalConfirm .modal-body").html("Bạn cần cài đặt Extension để sử dụng chức năng này!");
    jQuery("#modalConfirm .modal-confirm").hide();
    jQuery("#modalConfirm .btn-ok").text("Cài đặt");
    jQuery("#modalConfirm .btn-ok").click(function(e) {
        // location.href = "https://chrome.google.com/webstore/detail/jkofekpejnnboenpjppalhmlimgggeni";
        window.open('https://chrome.google.com/webstore/detail/jkofekpejnnboenpjppalhmlimgggeni', '_blank');

    });
    jQuery("#modalConfirm .btn-default").text("Hướng dẫn");
    jQuery("#modalConfirm .btn-default").click(function(e) {
        location.href = "/cai-dat-chrome-extensions";
    });
}
(function($) {
    function changeTagName(tag_input) {
        let val = tag_input.val();
        let tid = tag_input.attr("data-tid")
        $.ajax({
            method: "POST",
            url: "/cassiopeia/ajax",
            data: {
                cmd: "changeTagName",
                tid: tid,
                val: val,
            },
            success: function(result) {

            }
        });
    }
    $("document").ready(function(e) {

        var typingTimer;
        var changeTagNameInterval = 300;
        let tx_prduct_input = $(".modal-tag-manager input");
        tx_prduct_input.change(function (e) {
            var _this = $(this);
            var key = _this.val().trim();
            clearTimeout(typingTimer);
            typingTimer = setTimeout(changeTagName(_this), changeTagNameInterval);
            e.stopPropagation();
        });
        tx_prduct_input.keyup(function(e){
            var _this = $(this);
            var key = _this.val().trim();
            clearTimeout(typingTimer);
            typingTimer = setTimeout(changeTagName(_this), changeTagNameInterval);
            e.stopPropagation();
        });
        tx_prduct_input.on('keydown', function () {
            clearTimeout(typingTimer);
        });
        // $("body").on("change","input[name='select']",function () {
        //     let _this = $(this);
        //     let _parent = $("tr").has(_this);
        //     if(_this.is(":checked")){
        //         _parent.addClass("tr-checked");
        //     }else{
        //         _parent.removeClass("tr-checked");
        //     }
        // })

        $(".modal-tag-manager .btn-delete-tag").click(function (e) {
            let tid = $(this).attr("data-tid");
            setModalConfirm("Bạn có muốn xóa Tag này?",function (e) {
                $.ajax({
                    method: "POST",
                    url: "/cassiopeia/ajax",
                    data: {
                        cmd: "delete-tag",
                        tid: tid,
                    },
                    success: function(result) {
                        if(result.response=="OK"){
                            setModalAlert("Xóa thành công!");
                            $(".modal-tag-manager tr[data-tid="+tid+"]").remove();
                            location.reload();
                        }else{
                            setModalAlert("Bạn không có quyền xóa Tag này!");
                        }
                    }
                });
            });
        });

        $(".extension-alert .block-container .block-button button").click(function (e) {
           $(".extension-alert").removeClass("active");
        });
        $("#modalBacklinkProject").on("hidden.bs.modal", function () {
            $("input").removeClass("errors");
            $("p").html("");
        });
        $("#modalKeywordProject").on("hidden.bs.modal", function () {
            $("input").removeClass("errors");
            $("p").html("");
        });
        $("#cassiopeia-backlink-project-form button[type=submit]").click(function(e) {
            e.stopPropagation();
            let __this = $(this);
            let flag = true;
            $("#cassiopeia-backlink-project-form input.required").each(function(e) {
                let _this = $(this);
                if (_this.val().trim() === "") {
                    flag = false;
                    _this.addClass('errors');
                    $(".input-wrap").has(_this).find('p').html('Trường dữ liệu này là bắt buộc!');
                }else{
                    $(".input-wrap").has(_this).find('p').html('');
                    _this.removeClass("errors");
                }
            });
            if (flag !== true) {
                return false;
            }else{
                $(".loading-block").addClass("active");
            }
            let pattern = /^([a-zA-Z0-9][a-zA-Z0-9-_]*\.)*[a-zA-Z0-9]*[a-zA-Z0-9-_]*[[a-zA-Z0-9]+$/;
            let domain = $("#cassiopeia-backlink-project-form input[name='project_domain']").val().trim();
            domain = domain.replaceAll("https","");
            domain = domain.replaceAll("http","");
            domain = domain.replaceAll(":","");
            domain = domain.replaceAll("/","");
            domain = domain.replaceAll("www.","");
            console.log("domain",domain);
            let check = pattern.test(domain);
            if(check!==true){
                $("input[name='project_domain']").addClass('errors');
                $(".input-wrap").has("input[name='project_domain']").find('p').html('Domain không hợp lệ!');
                return false;
            }

        });
        $("#cassiopeia-content-project-form button[type=submit]").click(function(e) {
            e.stopPropagation();
            let __this = $(this);
            let flag = true;
            $("#cassiopeia-content-project-form input.required").each(function(e) {
                let _this = $(this);
                if (_this.val().trim() === "") {
                    flag = false;
                    _this.addClass('errors');
                    $(".input-wrap").has(_this).find('p').html('Trường dữ liệu này là bắt buộc!');
                }else{
                    $(".input-wrap").has(_this).find('p').html('');
                    _this.removeClass("errors");
                }
            });
            if (flag !== true) {
                return false;
            }else{
                $(".loading-block").addClass("active");
            }
            let pattern = /^([a-zA-Z0-9][a-zA-Z0-9-_]*\.)*[a-zA-Z0-9]*[a-zA-Z0-9-_]*[[a-zA-Z0-9]+$/;
            let domain = $("#cassiopeia-content-project-form input[name='project_domain']").val().trim();
            domain = domain.replaceAll("https","");
            domain = domain.replaceAll("http","");
            domain = domain.replaceAll(":","");
            domain = domain.replaceAll("/","");
            domain = domain.replaceAll("www.","");
            console.log("domain",domain);
            let check = pattern.test(domain);
            if(check!==true){
                $("input[name='project_domain']").addClass('errors');
                $(".input-wrap").has("input[name='project_domain']").find('p').html('Domain không hợp lệ!');
                return false;
            }else{
                $(".loading-block").addClass("active");
            }

        });
        $("#cassiopeia-user-project-form button[type=submit]").click(function(e) {
            let flag = true;
            $("#cassiopeia-user-project-form input.required").each(function(e) {
                let _this = $(this);
                if (_this.val().trim() === "") {
                    flag = false;
                    _this.addClass('errors');
                    $(".input-wrap").has(_this).find('p').html('Trường dữ liệu này là bắt buộc!');
                }else{
                    $(".input-wrap").has(_this).find('p').html('');
                    _this.removeClass("errors");
                }
            });
            if (flag !== true) {
                return false;
            }
            let pattern = /^(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)+[a-z0-9][a-z0-9-]{0,61}[a-z0-9]$/;
            let domain = $("#cassiopeia-user-project-form input[name='project_domain']").val().trim();
            domain = domain.replaceAll("https","");
            domain = domain.replaceAll("http","");
            domain = domain.replaceAll(":","");
            domain = domain.replaceAll("/","");
            domain = domain.replaceAll("www.","");
            let check = pattern.test(domain);
            console.log("domain",domain);
            console.log("check",check);
            if(check!==true){
                $("input[name='project_domain']").addClass('errors');
                $(".input-wrap").has("input[name='project_domain']").find('p').html('Domain không hợp lệ!');
                return false;
            }
            $(".loading-block").addClass("active");
        });
        function cassiopeia_recursion_keyword_create(data,tids){
            let currentProgress = $(".create-backlink-progress-bar progress").val();
            let part = data.slice(0,10);
            data.splice(0,10);
            let pid = $("#project_id").val();
            $.ajax({
                method: "POST",
                url: "/cassiopeia/ajax",
                data: {
                    cmd: "create-keyword",
                    part: JSON.stringify(part),
                    tids:tids,
                    pid:pid,
                },
                success: function(result) {
                    $(".create-backlink-progress-bar progress").val(parseInt(currentProgress)+part.length);
                    if(result.response=="OK"){
                        if(data.length>0 && result.response=="OK"){
                            cassiopeia_recursion_keyword_create(data,tids);
                        }else{
                            $.ajax({
                                method: "POST",
                                url: "/cassiopeia/ajax",
                                data: {
                                    cmd: "create-keyword-complete",
                                },
                                success: function(result) {
                                    location.reload();
                                }
                            });
                        }
                    }else{
                        setModalAlert("Đã xảy ra lỗi!",function (e) {
                            location.reload();
                        });
                    }
                }
            });
        }
        function cassiopeia_recursion_backlink_create(data,tids){
            let currentProgress = $(".create-backlink-progress-bar progress").val();
            let part = data.slice(0,10);
            data.splice(0,10);
            let pid = $("#project_id").val();
            $.ajax({
                method: "POST",
                url: "/cassiopeia/ajax",
                data: {
                    cmd: "create-backlink",
                    part: JSON.stringify(part),
                    tids:tids,
                    pid:pid,
                },
                success: function(result) {
                    $(".create-backlink-progress-bar progress").val(parseInt(currentProgress)+part.length);
                    if(result.response=="OK"){
                        if(data.length>0 && result.response=="OK"){
                            cassiopeia_recursion_backlink_create(data,tids);
                        }else{
                            $.ajax({
                                method: "POST",
                                url: "/cassiopeia/ajax",
                                data: {
                                    cmd: "create-backlink-complete",
                                },
                                success: function(result) {
                                    location.reload();
                                }
                            });
                        }
                    }else{
                        setModalAlert("Đã xảy ra lỗi!",function (e) {
                            location.reload();
                        });
                    }
                }
            });
        }
        $("#cassiopeia-add-backlink-form button[type=submit]").click(function(e) {
            let flag = true;
            $("#cassiopeia-add-backlink-form .required").each(function(e) {
                let _this = $(this);
                if (_this[0].value == "") {
                    flag = false;
                    $('#cassiopeia-add-backlink-form').siblings().addClass('errors');
                    _this.addClass('errors');
                    _this.siblings('.tagify').addClass('errors');
                    _this.parents(".input-wrap").find('p').html('Trường dữ liệu này là bắt buộc!');
                }
            });
            if (flag !== true) {
                return false;
            }else{
                $(".loading-block").addClass("active");
            }
            let _content = $("#cassiopeia-add-backlink-form textarea").val();
            let splitter = _content.split("\n");
            let tags = $("input[name='tags']").val();
            let project_id = $("#project_id").val();
            if(splitter.length>100000){
                $(".loading-block").removeClass("active")
                setModalAlert("Số backlinks đã vượt quá giới hạn cho phép!");
            }else{
                $.ajax({
                    method: "POST",
                    url: "/cassiopeia/ajax",
                    data: {
                        cmd: "create-backlink-init",
                        pid: project_id,
                        tags:tags,
                    },
                    success: function(result) {
                        if(result.response=="OK"){
                            $(".create-backlink-progress-bar progress").attr("max",splitter.length);
                            $("#modalAddBacklink").modal("hide");
                            $(".loading-block").removeClass("active");
                            $(".modal-create-backlink-progress").addClass("active");
                            cassiopeia_recursion_backlink_create(splitter,result.tids);
                        }else{
                            setModalAlert("Đã xảy ra lỗi!");
                        }
                    }
                });
            }
            // cassiopeia_recursion_backlink_create(splitter);
            return false;
        });
        $("#cassiopeia-add-keyword-form button[type=submit]").click(function(e) {
            let flag = true;
            $("#cassiopeia-add-keyword-form .required").each(function(e) {
                let _this = $(this);
                if (_this[0].value == "") {
                    flag = false;
                    $('#cassiopeia-add-keyword-form').siblings().addClass('errors');
                    _this.addClass('errors');
                    _this.siblings('.tagify').addClass('errors');
                    _this.parents(".input-wrap").find('p').html('Trường dữ liệu này là bắt buộc!');
                }
            });
            if (flag !== true) {
                return false;
            }
            $(".loading-block").addClass("active");
            let _content = $("#cassiopeia-add-keyword-form textarea").val();
            let splitter = _content.split("\n");
            let tags = $("input[name='tags']").val();
            let project_id = $("#project_id").val();
            if(splitter.length>100000){
                $(".loading-block").removeClass("active")
                setModalAlert("Số từ khóa đã vượt quá giới hạn cho phép!");
            }else{
                $.ajax({
                    method: "POST",
                    url: "/cassiopeia/ajax",
                    data: {
                        cmd: "create-keywords-init",
                        pid: project_id,
                        tags:tags,
                    },
                    success: function(result) {
                        if(result.response=="OK"){
                            $(".create-backlink-progress-bar progress").attr("max",splitter.length);
                            $("#modalAddKeyword").modal("hide");
                            $(".loading-block").removeClass("active");
                            $(".modal-create-backlink-progress").addClass("active");
                            cassiopeia_recursion_keyword_create(splitter,result.tids);
                        }else{
                            setModalAlert("Đã xảy ra lỗi!");
                        }
                    }
                });
            }
            return false;
        });
        $(".running").click(function(e) {
            alert("Chương trình đang chạy, vui lòng thử sau!");
        });
        $(".btn-add-keyword-project").click(function(e) {
            $("#modalKeywordProject input[name='project_nid']").val('');
            $("#modalKeywordProject input[name='project_title']").val('');
            $("#modalKeywordProject input[name='project_domain']").val('');
            $("#modalKeywordProject input[name='project_nid']").val(0);
            $("#modalKeywordProject").css({ "display": "flex", "align-items": "center" });
            $("#modalKeywordProject").modal("show");
            $("#modalKeywordProject .modal-title").text("Thêm mới dự án Từ khóa");
            $("#modalKeywordProject button[type=submit]").val("Thêm mới");
        });
        $(".btn-add-backlink-project").click(function(e) {
            var _type = $(this).attr("data-type");
            $("#modalBacklinkProject input[name='project_nid']").val('');
            $("#modalBacklinkProject input[name='project_title']").val('');
            $("#modalBacklinkProject input[name='project_domain']").val('');
            $("#modalBacklinkProject input[name='project_nid']").val(0);
            $("#modalBacklinkProject input[name='project_type']").val(_type);
            $("#modalBacklinkProject").css({ "display": "flex", "align-items": "center" });
            $("#modalBacklinkProject").modal("show");
            $("#modalBacklinkProject .modal-title").text("Thêm mới dự án Backlink");
            $("#modalBacklinkProject button[type=submit]").val("Thêm mới");
        });
        $(".btn-add-content-project").click(function(e) {
            var _type = $(this).attr("data-type");
            $("#modalContentProject input[name='project_nid']").val('');
            $("#modalContentProject input[name='project_title']").val('');
            $("#modalContentProject input[name='project_domain']").val('');
            $("#modalContentProject input[name='project_nid']").val(0);
            $("#modalContentProject input[name='project_type']").val(_type);
            $("#modalContentProject").css({ "display": "flex", "align-items": "center" });
            $("#modalContentProject").modal("show");
            $("#modalContentProject .modal-title").text("Thêm mới dự án Content");
            $("#modalContentProject button[type=submit]").val("Thêm mới");
        });
        $("body").on("click", ".btn-edit-project", function() {
            jQuery("input").removeClass("_required");
            $("#modalBacklinkProject").css({ "display": "flex", "align-items": "center" });
            $("#modalBacklinkProject").modal("show");
            $("#modalBacklinkProject .modal-title").text("Cập nhật dự án");
            $("#modalBacklinkProject .form-buttons button").html("<i class=\"fa fa-check\"></i> Cập nhật");
            var project_id = $(this).attr("data-id");
            $.ajax({
                method: "POST",
                url: "/cassiopeia/ajax",
                data: {
                    cmd: "get-project-detail",
                    project_id: project_id,
                },
                success: function(result) {
                    if (result.response != null) {
                        var data = JSON.parse(result.response);
                        console.log(data);
                        $("#modalBacklinkProject input[name='project_nid']").val(data.nid);
                        $("#modalBacklinkProject input[name='project_title']").val(data.title);
                        $("#modalBacklinkProject input[name='project_domain']").val(data.field_domain['und'][0]['value']);
                    }
                }
            });
        });
    });
})(jQuery);
