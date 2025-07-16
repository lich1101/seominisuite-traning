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
(function($) {
    $(document).ready(function(e) {
        const modalTagOption =  $("#modalTagOption");
        const modalTag = $("#modalTags");
        const modalTagTitle = $("#modalTags .modal-title");
        cassiopeiaTagify();
        $("button.form-submit").click(function (e) {
           $(".loading-block").addClass("active");
        });
        $(".sort").click(function (e) {
            let _this = $(this);
           let sort_by = _this.attr("data-sort");
           let sort_direction = _this.attr("data-direction");
            sort_direction = sort_direction=="DESC"?"ASC":"DESC";
           // console.log("sort_direction",sort_direction);
            $("#cassiopeia-content-article-filter-form select[name='sort_by']").val(sort_by);
            $("#cassiopeia-content-article-filter-form select[name='sort_direction']").val(sort_direction);
            $("#cassiopeia-content-article-filter-form").submit();
        });
        $(".nav-buttons .btn-next").not("disabled").click(function (e) {
           let current_page = parseInt($("input[name='page']").val());
            $("input[name='page']").val(current_page+1);
            $("#cassiopeia-content-article-filter-form").submit();
        });
        $(".nav-buttons .btn-prev").not("disabled").click(function (e) {
           let current_page = parseInt($("input[name='page']").val());
           console.log("current_page",current_page);
            $("input[name='page']").val(current_page-1);
            $("#cassiopeia-content-article-filter-form").submit();
        });
        $("select[name='item_per_page']").change(function (e) {
            let val = $(this).val();
            $("input[name='page']").val(1);
            $("input[name='limit']").val(val);
            $("#cassiopeia-content-article-filter-form").submit();
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
                        cmd : "content-article-change-tags",
                        data : JSON.stringify(data),
                        tags : tags,
                        option : option,
                        id : nid,
                    },
                    success : function(result){
                        $(".loading-block").removeClass("active");
                        location.reload();
                    }
                });
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
                case "addToTags" :
                    if (data.length === 0) {
                        setModalAlert("Bạn chưa chọn bài viết!");
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
                        setModalAlert("Bạn chưa chọn bài viết!");
                    } else {
                        modalTagOption.val("DELETE");
                        modalTagTitle.text("Xóa khỏi tags");
                        modalTag.modal("show");
                        modalTag.css("display","flex");
                        modalTag.css("align-items","center");
                    }

                    break;
                    case "deleteContentArticles" :
                    if (data.length === 0) {
                        setModalAlert("Bạn chưa chọn bài viết!");
                    } else {
                        setModalConfirm("Bạn có chắc chắn muốn xóa các bài viết đã chọn không?", function(e) {
                            $.ajax({
                                method: "POST",
                                url: "/cassiopeia/ajax",
                                data: {
                                    cmd: "delete-content-articles",
                                    data: data,
                                },
                                success: function(result) {
                                    if(result.response=="OK"){
                                        $("input[name='confirm']").val("");
                                        $("#modalConfirm").modal("hide");
                                        $("#modalConfirm .btn-ok").unbind();
                                        location.reload();
                                    }else{
                                        $("#modalConfirm .modal-confirm input").addClass("errors");
                                        $("#modalConfirm .modal-confirm input").val("");
                                        alert("Đã xảy ra lỗi!");
                                    }
                                }
                            });
                        }, false);
                    }

                    break;
            }

        });
        $(".modal-tag-manager .close").click(function (e) {
            $(".modal-tag-manager").removeClass("active");
        });
        $(".btn-tag-manager").click(function (e) {
            $(".modal-tag-manager").addClass("active");
        });
        $(".article-rank li").click(function (e) {
           let rank = $(this).attr('data-rank');
           $("select[name='rank']").val(rank);
           $("form#cassiopeia-content-article-filter-form").submit();
        });
        var dateFormat = "dd-mm-yy",
            from = $("input[name='from_date']")
                .datepicker({
                    defaultDate: "+1w",
                    changeMonth: true,
                    numberOfMonths: 1,
                    dateFormat: dateFormat
                })
                .on("change", function() {
                    to.datepicker("option", "minDate", getDate((this)));
                    to.datepicker("option", "dateFormat", dateFormat);
                    // loadBacklinkDetail();
                }),
            to = $("input[name='to_date']").datepicker({
                defaultDate: "+1w",
                changeMonth: true,
                numberOfMonths: 1,
                dateFormat: dateFormat
            })
                .on("change", function() {
                    from.datepicker("option", "maxDate", getDate(this));
                    from.datepicker("option", "dateFormat", dateFormat);
                    // loadBacklinkDetail();
                });
        $("body").on("click", ".btn-delete-content-article", function() {
            var id = $(this).attr("data-id");
            var project_name = $(this).attr("data-name");
            setModalConfirm("Bạn có chắc chắn muốn xóa bài viết này không?", function(e) {
                let project_name = $("input[name='confirm']").val().trim();
                $.ajax({
                    method: "POST",
                    url: "/cassiopeia/ajax",
                    data: {
                        cmd: "delete-content-article",
                        id: id,
                    },
                    success: function(result) {
                        if(result.response=="OK"){
                            $("input[name='confirm']").val("");
                            $("#modalConfirm").modal("hide");
                            $("#modalConfirm .btn-ok").unbind();
                            location.reload();
                        }else{
                            $("#modalConfirm .modal-confirm input").addClass("errors");
                            $("#modalConfirm .modal-confirm input").val("");
                            alert("Đã xảy ra lỗi!");
                        }
                    }
                });
            }, false,project_name);
        });
    });
})(jQuery);