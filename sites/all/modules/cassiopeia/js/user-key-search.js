
function cassiopeia_load_key_project(page=1){
    var key = jQuery("input[name='search-key']").val();
    jQuery.ajax({
        method   :"POST",
        url      :"/cassiopeia/ajax",
        data     : {
            cmd : "load-key-project",
            key : key,
            page : page,
        },
        success : function(result){
            if(result.html!=null){
                jQuery(".result").html(result.html)
            }
        }
    });
}
function cassiopeia_get_user_key_search(){
    var project_id = jQuery("select[name='project']").val();
    jQuery.ajax({
        method   :"POST",
        url      :"/cassiopeia/ajax",
        data     : {
            cmd : "get-user-key-search",
            project_id:project_id,
        },
        success : function(result){
            if(result.html!=null){
                jQuery(".result_key_search").html(result.html);
            }
        }
    });
}



(function ($) {
    Drupal.behaviors.user = {
        attach: function () {
            $("input[name='all']").click(function (e) {
                if($(this).is(":checked")){
                    $(".result_key_search input").prop("checked",true);
                }else{
                    $(".result_key_search input").prop("checked",false);
                }
            });
            $(".btn-delete-key").click(function (e) {
                setModalConfirm("Bạn có chắc chắn muốn xóa những từ khóa đã chọn?",function (e) {
                    var project_id = $("select[name='project']").val();
                    var _data = [];
                    $(".result_key_search input").each(function (e) {
                        var _this = $(this);
                        if(_this.is(":checked")){
                            _data.push(_this.val());
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
                            setModalAlert(result.message);
                            cassiopeia_get_user_key_search();
                        }
                    });
                });
            });
        }
    };
    $(document).ready(function () {
        $("body").on("click",".ajax-item",function(e){
            $("#current-page").attr('data-page',$(this).attr("data-page"));
            var page = $("#current-page").attr('data-page');
            cassiopeia_load_key_project(page);
            return false;
        });
        cassiopeia_load_key_project();
        $(".btn-add-project").click(function (e) {
            var _type = $(this).attr("data-type");
            $("#modalProject input[name='project_nid']").val(0);
            $("#modalProject input[name='project_type']").val(_type);
            $("#modalProject").css({"display":"flex","align-items":"center"});
            $("#modalProject").modal("show");
            $("#modalProject .modal-title").text("Thêm mới dự án");
            $("#modalProject button[type=submit]").val("Thêm mới");
        });
        $("#modalProject .form-buttons button").click(function (e) {
            var check = cassiopeia_required_check($(this));
            if(check==false){
                return false;
            }
            var project_id = $("#modalProject input[name='project_nid']").val();
            var project_title = $("input[name='project_title']").val();
            var project_domain = $("input[name='project_domain']").val();
            $.ajax({
                method   :"POST",
                url      :"/cassiopeia/ajax",
                data     : {
                    cmd : "user-project",
                    project_id : project_id,
                    project_type : "key_search",
                    project_title : project_title,
                    project_domain : project_domain,
                },
                success : function(result){
                    $("#modalProject input[name='project_nid']").val("");
                    $("#modalProject input[name='project_title']").val("");
                    $("#modalProject input[name='project_domain']").val("");
                    $("#modalProject").modal("hide");
                    setModalAlert(result.message);
                    cassiopeia_load_key_project();
                }
            });
            return false;
        });
        // $(".btn-key-check").click(function (e) {
        //     var project_id = $("select[name='project']").val();
        //     var _data = [];
        //     $(".result_key_search input").each(function (e) {
        //         var _this = $(this);
        //         if(_this.is(":checked")){
        //             _data.push(_this.attr("data-key"));
        //         }
        //     });
        //     $.ajax({
        //         method   :"POST",
        //         url      :"/cassiopeia/ajax",
        //         data     : {
        //             cmd : "user-key-check",
        //             _data : JSON.stringify(_data),
        //             project_id:project_id,
        //         },
        //         success : function(result){
        //             setModalAlert(result.message);
        //             cassiopeia_get_user_key_search();
        //         }
        //     });
        // });

        $(".btn-delete-project").click(function (e) {
            setModalConfirm("Bạn có chắc chắn muốn xóa dự án này không?",function (e) {
                var project_id = $("select[name='project']").val();
                $.ajax({
                    method   :"POST",
                    url      :"/cassiopeia/ajax",
                    data     : {
                        cmd : "delete-project",
                        project_id : project_id,
                    },
                    success : function(result){
                        setModalAlert(result.message);
                        cassiopeia_load_key_project();
                    }
                });
            });
        });
        $("#modalKeySearch button").click(function (e) {
            var check = cassiopeia_required_check($(this));
            if(check==false){
                return false;
            }
            var key = $("#modalKeySearch input[name='key_search']").val();
            var project_id = $("select[name='project']").val();
            $.ajax({
                method   :"POST",
                url      :"/cassiopeia/ajax",
                data     : {
                    cmd : "user-add-key-search",
                    key : key,
                    project_id : project_id,
                },
                success : function(result){
                    $("#modalKeySearch input[name='key_search']").val("");
                    $("#modalKeySearch").modal("hide");
                    setModalAlert(result.message);
                    cassiopeia_get_user_key_search();
                }
            });
            return false;
        });
        $("select[name='project']").change(function (e) {
            console.log(123);
            var project_id = $(this).val();
            cassiopeia_get_user_key_search();
            $("#modalKeySearch input[name='project_id']").val(project_id);
        });
        $(".btn-add-key").click(function (e) {
            $("#modalKeySearch").css({"display":"flex","align-items":"center"});
            $("#modalKeySearch").modal("show");
            $("#modalKeySearch .modal-title").text("Thêm mới từ khóa");
            $("#modalKeySearch button[type=submit]").val("Thêm mới");
        });

        $("body").on("click",".btn-edit-project",function () {
            cassiopeia_remove_class("_required");
            $("#modalProject").css({"display":"flex","align-items":"center"});
            $("#modalProject").modal("show");
            $("#modalProject .modal-title").text("Cập nhật dự án");
            $("#modalProject .form-buttons button").html("<i class=\"fa fa-check\"></i> Cập nhật");
            var project_id = $(this).attr("data-id");
            $.ajax({
                method   :"POST",
                url      :"/cassiopeia/ajax",
                data     : {
                    cmd : "get-project-detail",
                    project_id : project_id,
                },
                success : function(result){
                    if(result.response!=null){
                        var data = JSON.parse(result.response);
                        console.log(data);
                        $("#modalProject input[name='project_nid']").val(data.id);
                        $("#modalProject input[name='project_title']").val(data.title);
                        $("#modalProject input[name='project_domain']").val(data.domain);
                    }
                }
            });
        });

    });
})(jQuery);