function cassiopeia_load_backlink_project(page = 1) {
    var key = jQuery("input[name='search-key']").val();
    jQuery.ajax({
        method: "POST",
        url: "/cassiopeia/ajax",
        data: {
            cmd: "load-backlink-project",
            key: key,
            page: page,
        },
        success: function(result) {
            if (result.html != null) {
                jQuery(".result").html(result.html)
            }
        }
    });
}

function sortUnorderedList(ul, sortDescending) {
    if (typeof ul == "string")
        ul = document.getElementById(ul);

    // Idiot-proof, remove if you want
    if (!ul) {
        alert("The UL object is null!");
        return;
    }

    // Get the list items and setup an array for sorting
    var lis = ul.getElementsByTagName("a");
    var vals = [];

    // Populate the array
    for (var i = 0, l = lis.length; i < l; i++)
        vals.push(lis[i].innerHTML);

    // Sort it
    vals.sort();

    // Sometimes you gotta DESC
    if (sortDescending)
        vals.reverse();

    // Change the list on the page
    for (var i = 0, l = lis.length; i < l; i++)
        lis[i].innerHTML = vals[i];
}
(function($) {
    $("document").ready(function(e) {
        var typingTimer;
        $(".sort").click(function(e) {
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
            console.log("sort",sort);
            var result = $(".result tbody tr").sort(function(a, b) {
                switch (sort) {
                    case "title":
                    case "domain":
                        var contentA = removeAccents($(a).find("td[data-key='" + sort + "']").attr("data-value").trim().replaceAll(" ", "")).toLowerCase();
                        var contentB = removeAccents($(b).find("td[data-key='" + sort + "']").attr("data-value").trim().replaceAll(" ", "").toLowerCase());
                        break;
                    case "article_count":
                    case "word_count":
                    case "point":
                    case "1_5":
                    case "1_10":
                    case "1_30":
                    case "1_100":
                    case "AVG":
                    case "changed":
                        var contentA = ($(a).find("td[data-key='" + sort + "']").attr("data-value"));
                        var contentB = ($(b).find("td[data-key='" + sort + "']").attr("data-value"));
                        if (contentA.length < 1) {
                            contentA = 0;
                        } else {
                            contentA = parseFloat(contentA);
                        }
                        if (contentB.length < 1) {
                            contentB = 0;
                        } else {
                            contentB = parseFloat(contentB);
                        }
                        break;
                }
                if (direction == "DESC") {
                    return (contentA > contentB) ? -1 : (contentA < contentB) ? 1 : 0;
                } else {
                    return (contentA < contentB) ? -1 : (contentA > contentB) ? 1 : 0;
                }
            });
            $(".result table tbody").html(result);
            let stt = 1;
            $(".result tbody tr").each(function(e) {
                let _this = $(this);
                _this.find("td[data-title='Stt']").text(stt);
                stt++;
            });
        });
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
                // console.log("key", key);
                if (key != 40 && key != 39 && key != 38 && key != 37) {
                    var _key = _this.val().trim().replaceAll(" ", "").toLowerCase();
                    _key = removeAccents(_key);
                    $(".result tbody tr").each(function(e) {
                        let _this = $(this);
                        let _text = _this.find(".project-title").text();
                        _text = _text.replaceAll(" ", "").toLowerCase();
                        _text = removeAccents(_text);
                        if (_text.indexOf(_key) != -1) {
                            _this.removeClass("inactive");
                        } else {
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
        $(".page-search button.btn-search-project").click(function(e) {
            let key = $(".page-search input").val().trim().replaceAll(" ", "").toLowerCase();
            key = removeAccents(_key);
            $(".result tbody tr").each(function(e) {
                let _this = $(this);
                let _text = _this.find(".project-title").text();
                _text = _text.replaceAll(" ", "").toLowerCase();
                _text = _text.replaceAll(" ", "").toLowerCase();
                _text = removeAccents(_text);
                if (_text.indexOf(key) != -1) {
                    _this.removeClass("inactive");
                } else {
                    _this.addClass("inactive");
                }
            });
            return false;
        });
        $(".selectAll").change(function(e) {
            if ($(this).is(":checked")) {
                $("tbody input").prop("checked", true);
            } else {
                $("tbody input").prop("checked", false);
            }
        });
        var result = $(".home-backlinks tbody tr").sort(function(a, b) {
            var contentA = $(a).find("td[data-key='title'] a").text();
            var contentB = $(b).find("td[data-key='title'] a").text();
            return (contentA < contentB) ? -1 : (contentA > contentB) ? 1 : 0;
        });
        $(".home-backlinks .home-backlinks table tbody").html(result);

        $(".btn-export").click(function(e) {
            // $(".loading-block").addClass("active");
            let _this = $(this);
            if (_this.hasClass("excel")) {
                let projectSelected = [];
                $('input:checkbox[name="nid[]"]:checked').each(function() {
                    projectSelected.push($(this).val());
                });
                if (projectSelected.length) {
                    $(".form-export").submit();
                } else {
                    alert('Chọn dự án cần xuất báo cáo!');
                    return false
                }
            } else {
                setModalConfirm("Bạn cần nâng cấp gói để sử dụng chức năng này!", function(e) {
                    location.href = "/price-board";
                })
                return false;
            }
            // $(".loading-block").removeClass("active");
        });


        $("body").on("click", ".ajax-item", function(e) {
            $("#current-page").attr('data-page', $(this).attr("data-page"));
            var page = $("#current-page").attr('data-page');
            cassiopeia_load_backlink_project(page);
            return false;
        });

        $(".btn-add-project").click(function(e) {
            var _type = $(this).attr("data-type");
            $("#modalProject input[name='project_nid']").val('');
            $("#modalProject input[name='project_title']").val('');
            $("#modalProject input[name='project_domain']").val('');
            $("#modalProject input[name='project_nid']").val(0);
            $("#modalProject input[name='project_type']").val(_type);
            $("#modalProject").css({
                "display": "flex",
                "align-items": "center"
            });
            $("#modalProject").modal("show");
            $("#modalProject .modal-title").text("Thêm mới dự án");
            $("#modalProject button[type=submit]").val("Thêm mới");
        });
        $("body").on("click", ".btn-delete-project", function() {
            var project_id = $(this).attr("data-id");
            var project_name = $(this).attr("data-name");
            setModalConfirm("Bạn có chắc chắn muốn xóa dự án này không?", function(e) {
                let project_name = $("input[name='confirm']").val().trim();
                $.ajax({
                    method: "POST",
                    url: "/cassiopeia/ajax",
                    data: {
                        cmd: "delete-project",
                        project_id: project_id,
                        project_name: project_name,
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
                            alert("Tên dự án không đúng!");
                        }
                    }
                });
            }, true,project_name);
        });
        $("body").on("click", ".btn-edit-project", function() {
            jQuery("input").removeClass("_required");
            $("#modalProject").css({
                "display": "flex",
                "align-items": "center"
            });
            $("#modalProject").modal("show");
            $("#modalProject .modal-title").text("Cập nhật dự án");
            $("#modalProject .form-buttons button").html("<i class=\"fa fa-check\"></i> Cập nhật");
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
                        $("#modalProject input[name='project_nid']").val(data.nid);
                        $("#modalProject input[name='project_title']").val(data.title);
                        $("#modalProject input[name='project_domain']").val(data.field_domain['und'][0]['value']);
                    }
                }
            });
        });
        $("#modalProject .form-buttons button").click(function(e) {
            $(".loading-block").addClass("active");
        });
    });
})(jQuery);