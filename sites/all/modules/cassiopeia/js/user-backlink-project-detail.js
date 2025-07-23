function getDomainFromHref(href) {
  let c = "";
  if (href !== undefined) {
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

(function ($) {
  var start = false;
  var recheckTotal = 0;
  var recheckCount = 0;
  var recheck = [];
  var waitingIDS = [];
  var now = 0;
  var $listOfID = $("#listOfID");
  var $checkedItems = $("#checkedItems");
  var $totalItems = $("#totalItems");
  var footer = jQuery("footer");

  function renderBacklinkRow(value, stt) {
    let is_in_content = "-";
    if (value.is_in_content == 1) {
      is_in_content = "Yes";
    } else if (value.is_in_content == 0) {
      is_in_content = "No";
    }
    let indexed = "-";
    if (value.indexed == 1) {
      indexed = "Yes";
    } else if (value.indexed == 0) {
      indexed = "No";
    } else {
      indexed = "-";
    }
    let tags = "";
    let tels = "";
    if (value.tags !== null) {
      tags = value.tags;
    }

    let status = "";
    let errorCode = "";
    let hasError = "";
    let errorMessage = "";
    if (value.status !== null) {
      if (value.status != 200 && value.status != "RAW") {
        switch (value.status) {
          case "404":
            errorCode = 404;
            errorMessage = "Lỗi " + errorCode + ": Nguồn đặt Backlink không tồn tại!";
            hasError = "hasError";
            break;
          case "999":
            errorCode = "error";
            errorMessage = "Lỗi " + errorCode + ": Nguồn đặt Backlink không hợp lệ!";
            hasError = "hasError";
            break;
          case "500":
            errorCode = 500;
            errorMessage = "Lỗi " + errorCode + ": Nguồn đặt Backlink không hoạt động!";
            hasError = "hasError";
            break;
          case "TIME_OUT":
            errorCode = "5xx";
            errorMessage = "Lỗi " + errorCode + ": Vượt quá thời gian quy định!";
            hasError = "hasError";
            break;
          case "ERROR":
            // errorCode = 5xx;
            errorMessage = "Lỗi kết nối: Không thể kết nối trang nguồn!";
            hasError = "hasError";
            break;
          case "NONE":
            // errorCode = 5xx;
            errorMessage = "Lỗi: Không tìm thấy backlink!";
            hasError = "hasError";
            break;
          case "CANNOT_INSERT":
            // errorCode = 5xx;
            errorMessage = "Lỗi: Không thể lưu bản ghi!";
            hasError = "hasError";
            break;
          case "RAW":
            // errorCode = 5xx;
            errorMessage = "Chưa quét!";
            break;
          default:
            errorCode = value.status;
            errorMessage = "Lỗi " + errorCode;
            hasError = "hasError";

        }
        errorCode = "Fail <i title='" + errorMessage + "' class=\"fa fa-info-circle\" ></i>";
      } else if (value.status == "RAW") {
        errorCode = " <i title='Chưa quét!' class=\"fa fa-info-circle\" ></i>";
      } else {
        if (value.id == null) {
          hasError = "hasError";
          errorCode = "Fail <i title='Không tìm thấy backlink!' class=\"fa fa-info-circle\" ></i>";
        } else {
          errorCode = "Success";
        }
      }
    } else {
      errorCode = " <i title='Chưa quét!' class=\"fa fa-info-circle\" ></i>";
    }
    let _plus = "";
    let _class = "";
    if (value.nid_count > 1) {
      _class = "hasChild";
      _plus = "<i class='fa fa-plus'></i>";
    }

    // Luôn không chọn checkbox khi tải trang
    let checked = "";
    let t = "<tr data-nid='" + value.bid + "' class='" + _class + " " + hasError + "'>" +
      "<td class='col-expand' data-title='expand'>" + _plus + "</td>\n" +
      "    <td class='col-checkbox' data-title='select'>" +
      "<label class=\"mask-chekbox\">\n" +
      "    <input " + checked + " data-stt='" + stt + "' type=\"checkbox\" name=\"select\" class=\"\" data-domain=\"\" value='" + value.bid + "' data-backlink-source='" + value.source + "' >\n" +
      "    <i class=\"fa-regular fa-square\"></i>\n" +
      "</label>" +
      "</td>\n" +
      "    <td class='col-stt' style='white-space: nowrap'>" + stt + "</td>\n" +
      "    <td class='col-source ' width='300px'>" +
      "<div class=\"d-flex space-between\">\n" +
      "                                            <span class=\"domain\" target=\"_blank\" rel=\"\\\" href='" + value.source + "' title='" + value.source + "'>\n" + value.source +
      "                                            </span>\n" +
      "                                            <a class=\"\" href='" + value.source + "' title='" + value.source + "' target=\"_blank\">\n" +
      "                                                <i class=\"fa fa-external-link\"></i>\n" +
      "                                            </a>\n" +
      "                                        </div>" +
      "</td>\n" +
      "    <td class='col-rel'><span class='loading-item'></span><span class='td-content'>" + value.rel + "</span></td>\n" +
      "    <td class='col-anchor'><span class='loading-item'></span><span class='td-content' title='" + value.anchor_text + "'>" + value.anchor_text + "</span></td>\n" +
      "    <td class='col-url '>" +
      "<span class='loading-item'></span>" +
      "<div class=\"d-flex space-between\">\n" +
      "<span class='td-content' title='" + value.url + "'>" + value.url + "</span>" +
      "<a  href='" + value.url + "' title='" + value.url + "' target='_blank'> \n" +
      "<i  class='fa fa-external-link'></i> \n" +
      "</a>" +
      "</div>" +
      "</td>\n" +
      "    <td class='col-indexed'><span class='loading-item'></span><span class='td-content " + indexed + "'>" + indexed + "</span></td>\n" +
      "    <td class='col-tag' style='    word-break: unset;'><div class='line-break-2' title='" + tags + "'>" + tags + "</div></td>\n";
    if (value.admin == 1) {
      tels = value.tels;
      t += "    <td class='col-tag' style='    word-break: unset;'><div class='line-break-2' title='" + tels + "'>" + tels + "</div></td>\n";
    }
    t += "    <td class='col-created'>" + value.date_created + "</td>\n" +
      "    <td class='col-status'><span class='loading-item'></span><span class='td-content'><span title='" + errorMessage + "'>" + errorCode + "</span></span></td>\n" +
      "</tr>";
    return t;
  }

  function loadBacklinkDetail() {
    $(".loading-block").addClass("active");
    let from_date = $("input[name='from_date']").val();
    let to_date = $("input[name='to_date']").val();
    let sort = $("#sorting").attr("data-sort");
    let direction = $("#sorting").attr("data-direction");
    let option = $("#option").attr("data-option");
    let pid = $("#project_id").val();
    let url = $("input[name='url']").val();
    let tag = $("select[name='tag']").val();
    let indexed = $("select[name='indexed']").val();
    let status = $("select[name='status']").val();
    let data;
    let html = "";
    let stt = 1;
    $.ajax({
      method: "POST",
      url: "/cassiopeia/ajax",
      data: {
        cmd: "load-backlink-detail",
        pid: pid,
        url: url,
        option: option,
        sort: sort,
        direction: direction,
        from_date: from_date,
        to_date: to_date,
        tag: tag,
        indexed: indexed,
        status: status,
      },
      success: function (result) {
        if (result.total === 0) {
          $(".btn-export").addClass("btn-disable");
        }
        data = JSON.parse(result.response);
        $.each(data, function (index, value) {
          t = renderBacklinkRow(value, stt);
          html += t;
          stt++;
        });
        $("tbody.result").html(html);
        $(".loading-block").removeClass("active");
      }
    });
  }

  const modalTagOption = $("#modalTagOption");
  const modalAddBacklink = $("#modalAddBacklink");
  const modalTag = $("#modalTags");
  const modalTagTitle = $("#modalTags .modal-title");

  function isScreenLockSupported() {
    return ('wakeLock' in navigator);
  }

  async function getScreenLock() {
    // if(isScreenLockSupported()){
    //     let screenLock;
    //     try {
    //         screenLock = await navigator.wakeLock.request('screen');
    //         console.log("screenLock",screenLock);
    //     } catch(err) {
    //         console.log(err.name, err.message);
    //     }
    //     return screenLock;
    // }
    // The wake lock sentinel.
    let wakeLock = null;

    // Function that attempts to request a screen wake lock.
    const requestWakeLock = async () => {
      try {
        wakeLock = await navigator.wakeLock.request("screen");
        wakeLock.addEventListener('release', () => {
          console.log('Screen Wake Lock released:', wakeLock.released);
        });
        console.log('Screen Wake Lock released:', wakeLock.released);
      } catch (err) {
        console.error(`${err.name}, ${err.message}`);
      }
    };

    // Request a screen wake lock…
    await requestWakeLock();
    // …and release it again after 5s.
    window.setTimeout(() => {
      wakeLock.release();
      wakeLock = null;
    }, 300000);
  }

  $("document").ajaxComplete(function (e) {

  });
  $("document").ready(function (e) {
    getScreenLock();

    // Kiểm tra tham số URL để xác định xem có cần chọn dòng mặc định không
    var urlParams = new URLSearchParams(window.location.search);
    var noSelect = urlParams.get('noselect');

    // Thêm sự kiện lắng nghe để đóng thông báo "Chương trình đang chạy"
    document.addEventListener("DOMContentLoaded", function() {
      // Kiểm tra xem có cần đóng thông báo không
      if (typeof window.closeScanningMessage === 'function') {
        window.closeScanningMessage();
      }
    });

    // Thêm hàm toàn cục để các extension có thể gọi để đóng thông báo
    window.closeScanningMessage = function() {
      try {
        // Gửi thông báo đến extension để đóng popup "Chương trình đang chạy"
        document.dispatchEvent(new CustomEvent("ScanCompleted", {detail: "completed"}));
        console.log("Đã gửi tín hiệu đóng thông báo quét");
      } catch (e) {
        console.error("Không thể đóng thông báo quét:", e);
      }
    };

    console.log("a", $("select[name='check-type']"));
    $("select[name='check-type']").change(function (e) {
      let _this = $(this);
      console.log("_this.val()", _this.val());
      if (_this.val() === "check_indexed") {
        $(".form-item-captcha-resolve").removeClass("hidden");
      } else {
        $(".form-item-captcha-resolve").addClass("hidden");
      }
    });
    $(".btn-export").click(function (e) {

      let _this = $(this);
      let project_id = $("#project_id").val();
      let data = [];
      if (_this.hasClass("excel")) {
        $(".result tbody input").each(function (e) {
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
            data: JSON.stringify(data),
          },
          success: function (result) {
            location.href = "/quan-ly-backlink/du-an/" + project_id + "/export?request_time=" + result.request_time;
          }
        });

        return false;
      } else {
        setModalConfirm("Bạn cần nâng cấp gói để sử dụng chức năng này!", function (e) {
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
    var dateFormat = "dd-mm-yy",
      from = $("input[name='from_date']")
        .datepicker({
          defaultDate: "+1w",
          changeMonth: true,
          numberOfMonths: 1,
          dateFormat: dateFormat
        })
        .on("change", function () {
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
        .on("change", function () {
          from.datepicker("option", "maxDate", getDate(this));
          from.datepicker("option", "dateFormat", dateFormat);
          // loadBacklinkDetail();
        });

    function getDate(element) {
      var date;
      try {
        date = jQuery.datepicker.parseDate(dateFormat, element.value);
      } catch (error) {
        // console.log("error",error);
        date = null;
      }

      return date;
    }

    var typingTimer;

    function _loadBacklinksByKeyword(_key) {

      $(".result tbody tr").each(function (e) {
        let _this = $(this);
        let _text = _this.find(".domain").text();
        _text = _text.replaceAll(" ", "").toLowerCase();
        _text = removeAccents(_text);
        if (_text.indexOf(_key) != -1) {
          _this.removeClass("inactive");
        } else {
          _this.addClass("inactive");
        }
      });
      // $(".loading-block").removeClass("active");
    }

    $(".page-search input").keyup(function (e) {
      var _this = $(this);
      clearTimeout(typingTimer);
      typingTimer = setTimeout(function () {
        var key = null;
        if (e.which != null) {
          key = e.which;
        } else {
          key = e.keyCode;
        }
        console.log("key", key);
        if (key != 40 && key != 39 && key != 38 && key != 37) {
          var _key = _this.val().trim().replaceAll(" ", "").toLowerCase();
          _key = removeAccents(_key);
          _loadBacklinksByKeyword(_key);
        }
      }, 300);
      e.stopPropagation();
    });
    $('.btn-clear-text').on('click', function (e) {
      e.stopPropagation();
      $('.page-search .input-group-search>input[type="text"]').val('');
      $(".loading-block").addClass("active");
      setTimeout(async function () {
        await _loadBacklinksByKeyword("");
        $(".loading-block").removeClass("active");
      }, 500)
      return false;
    });
    $(".page-search input").keydown(function (e) {
      clearTimeout(typingTimer);
    });
    $(".btn-scroll").click(function (e) {
      let element = $("tr[data-nid=568]").position().top;
      $(".t-body").scrollTop(
        element
      );
    });
    $(".sort").click(function (e) {
      $(".sort").removeClass("current");
      $(this).addClass("current");
      let sort = $(this).attr("data-sort");
      let direction = $(this).attr("data-direction");
      if (direction == "ASC") {
        $(this).attr("data-direction", "DESC")
      } else {
        $(this).attr("data-direction", "ASC");
      }
      $("#sorting").attr("data-sort", sort);
      $("#sorting").attr("data-direction", $(this).attr("data-direction"));
      loadBacklinkDetail();
    });
    $(".backlink-status ul li a").click(function (e) {
      $(".backlink-status ul li a").removeClass("active");
      $(this).addClass("active");
      let option = $(this).attr("data-option");
      $("#option").attr("data-option", option);
      loadBacklinkDetail();
    });
    $("body").on("click", ".hasChild .col-expand .fa", function (e) {
      let _this = $(this);
      let element = $("tr").has(_this);
      console.log(element);
      let stt = element.find(".col-stt").text();
      let nid = element.attr("data-nid");
      if (element.hasClass("active")) {
        $(".child[data-nid=" + nid + "]").remove();
        element.find(".fa").removeClass("fa-minus").addClass("fa-plus");
        element.removeClass("active");
      } else {
        $.ajax({
          method: "POST",
          url: "/cassiopeia/ajax",
          data: {
            cmd: "loadBackLinkChildren",
            nid: nid,
            stt: stt,
          },
          success: function (result) {
            $(".child[data-nid=" + nid + "]").remove();
            element.find(".fa").removeClass("fa-plus").addClass("fa-minus");
            $(result.html).replaceAll(".hasChild[data-nid=" + nid + "]");
          }
        });
      }
    });
    loadBacklinkDetail();

    // Đảm bảo không có checkbox nào được chọn mặc định sau khi tải trang
    setTimeout(function() {
      $("tbody input[type='checkbox']").prop('checked', false);
    }, 500);

    $(".progress-bar-block .close-button").click(function (e) {
      // Gửi tín hiệu đóng thông báo trước khi tải lại trang
      if (typeof window.closeScanningMessage === 'function') {
        window.closeScanningMessage();
      }
      // Đợi một chút trước khi tải lại trang để có thời gian đóng thông báo
      setTimeout(function() {
        location.reload();
      }, 500);
    });
    $(".progress-bar-block .close").click(function (e) {
      if (confirm("Bạn có muốn dừng việc quét backlink?")) {
        // Gửi tín hiệu đóng thông báo trước khi tải lại trang
        if (typeof window.closeScanningMessage === 'function') {
          window.closeScanningMessage();
        }
        // Đợi một chút trước khi tải lại trang để có thời gian đóng thông báo
        setTimeout(function() {
          location.reload();
        }, 500);
      }
    });
    $("#modalTags button").click(function (e) {
      $(".loading-block").addClass("active");
      let nid = $("#project_id").val();
      var tags = $("#modalTags input.tagify-input").val();
      var option = modalTagOption.val();
      var data = [];
      $(".result tbody input").each(function (e) {
        var _this = $(this);
        if (_this.is(":checked")) {
          data.push(_this.val());
        }
      });
      if (data.length > 0) {
        $.ajax({
          method: "POST",
          url: "/cassiopeia/ajax",
          data: {
            cmd: "add-remove-backlinks-to-tags",
            data: JSON.stringify(data),
            tags: tags,
            option: option,
            nid: nid,
          },
          success: function (result) {
            $(".loading-block").removeClass("active");
            location.reload();
          }
        });
      }
    });
    var _tags = JSON.parse($("#tags").val());
    console.log("_tags", _tags);
    var _whitelist = "";
    var obj = [];
    $.each(_tags, function (index, value) {
      var temp = {};
      temp['id'] = index;
      temp['value'] = value;
      _whitelist += "," + value;
      // obj[index] = value;
      obj.push(value);
    });
    var $input = $('input.tagify-input').tagify({
      whitelist: obj
    })
      .on('add', function (e, tagName) {
        console.log('JQEURY EVENT: ', 'added', tagName)
      })
      .on("invalid", function (e, tagName) {
        console.log('JQEURY EVENT: ', "invalid", e, ' ', tagName);
      });
    let jqTagify = $input.data('tagify');
    // console.log(jqTagify);
    $(".btn-delete").click(function (e) {
      let data = [];
      $("tbody input").each(function (e) {
        let _this = $(this);
        if (_this.is(":checked")) {
          data.push(_this.val());
        }
      });
      if (data.length === 0) {
        setModalAlert("Bạn chưa chọn backlink!");
      } else {
        setModalConfirm("Bạn có chắc chắn muốn xóa những backlinks đã chọn?", function (e) {
          $.ajax({
            method: "POST",
            url: "/cassiopeia/ajax",
            data: {
              cmd: "user-backlink-bulk",
              _data: JSON.stringify(data),
              option: "delete_backlink",
            },
            success: function (result) {
              location.reload();
            }
          });
        });
      }
    });
    $(".form-bulk button").click(async function (e) {
      let option = $(".form-bulk select").val();
      let data = [];
      $("tbody input").each(function (e) {
        let _this = $(this);
        if (_this.is(":checked")) {
          data.push(_this.val());
        }
      });
      switch (option) {
        case "get_tels":
          if (footer.hasClass("seoToolExtension") !== true) {
            installExtension();
          } else {
            if (data.length === 0) {
              setModalAlert("Bạn chưa chọn backlink!");
            } else {
              await $(".loading-block").addClass("active");
              $(".result").addClass("isLoading");
              $.ajax({
                method: "POST",
                url: "https://seominisuite.com/cassiopeia/ajax",
                data: {cmd: "user-backlink-check-response-complete", value: 0},
                success: function (result) {
                }
              });
              $(".progress-bar-block").addClass("active");
              let totalBacklinks = 0;
              let checkedBacklinks = 0;
              let domain = $("#project_domain").val();
              domain = getDomainFromHref(domain);
              let data = {};
              let stt = 0;
              let index = 0;
              let slice = {};
              let cache = {};
              $("tbody input").each(function (e) {
                let _this = $(this);
                if (_this.is(":checked")) {
                  totalBacklinks++;
                  let _parent = $("tr").has(_this);
                  _parent.addClass("checking");
                  let temp = {};
                  temp["recheck"] = false;
                  temp["index"] = index;
                  temp["id"] = _this.val();
                  temp["stt"] = _this.attr("data-stt");
                  temp["source"] = _this.attr("data-backlink-source");
                  temp["domain"] = domain;
                  slice[index] = temp;
                  if ((index + 1) % 5 === 0) {
                    data[stt] = slice;
                    slice = {};
                    stt++
                  }
                  index++
                }
                data[stt] = slice
              });
              console.log("data", data);
              $(".loading-block").removeClass("active");
              $totalItems.val(totalBacklinks);
              $checkedItems.val(checkedBacklinks);
              $(".progress-bar-block progress").attr("max", totalBacklinks);
              $(".progress-bar-block .totalBacklinks").text(totalBacklinks);
              $("input").prop("checked", false);
              setTimeout(function () {
                document.dispatchEvent(new CustomEvent('getTels', {
                  detail: data
                }));
              }, 500);
            }


            // console.log("stringArray",stringArray);

          }
          break;
        case "check_baclink":
          if (footer.hasClass("seoToolExtension") !== true) {
            installExtension();
          } else {
            if (data.length === 0) {
              setModalAlert("Bạn chưa chọn backlink!");
            } else {
              await $(".loading-block").addClass("active");
              setTimeout(function () {
                document.dispatchEvent(new CustomEvent('CheckBacklink', {
                  detail: '123'
                }));
              }, 500);
            }
          }
          break;
        case "check_indexed":
          if (footer.hasClass("seoToolExtension") !== true) {
            installExtension();
          } else {
            $("#totalItems").val(0);
            if (data.length === 0) {
              setModalAlert("Bạn chưa chọn backlink!");
            } else {
              let captcha_resolve = $("select[name='captcha-resolve']").val();
              console.log("captcha_resolve", captcha_resolve);
              if (captcha_resolve === "auto") { // giải captcha tự động
                jQuery.ajax({
                  method: "POST",
                  url: "/cassiopeia-captcha/resolve/get-info",
                  success: function (result) {
                    // let response = JSON.parse(result);
                    console.log("result", result);
                    if (result.remaining < 1) {
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
                              $(".loading-block").addClass("active");
                              setTimeout(function () {
                                document.dispatchEvent(new CustomEvent('CheckIndexed', {
                                  detail: '123'
                                }));
                              }, 500);
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
                    } else {
                      $(".loading-block").addClass("active");
                      setTimeout(function () {
                        document.dispatchEvent(new CustomEvent('CheckIndexed', {
                          detail: '123'
                        }));
                      }, 500);
                    }
                  }
                });
              } else {
                $(".loading-block").addClass("active");
                setTimeout(function () {
                  document.dispatchEvent(new CustomEvent('CheckIndexed', {
                    detail: '123'
                  }));
                }, 500);
              }

            }
          }
          break;
        case "delete_backlink":
          if (data.length === 0) {
            setModalAlert("Bạn chưa chọn backlink!");
          } else {
            setModalConfirm("Bạn có chắc chắn muốn xóa những backlinks đã chọn?", function (e) {
              $.ajax({
                method: "POST",
                url: "/cassiopeia/ajax",
                data: {
                  cmd: "user-backlink-bulk",
                  _data: JSON.stringify(data),
                  option: option,
                },
                success: function (result) {
                  location.reload();
                }
              });
            });
          }
          break;
        case "backlink_tag":
          if (data.length === 0) {
            setModalAlert("Bạn chưa chọn backlink!");
          } else {
            modalTagOption.val("ADD");
            modalTagTitle.text("Thêm vào tags");
            modalTag.modal("show");
            modalTag.css("display", "flex");
            modalTag.css("align-items", "center");
          }

          break;
        case "backlink_remove_form_tag":
          if (data.length === 0) {
            setModalAlert("Bạn chưa chọn backlink!");
          } else {
            modalTagOption.val("DELETE");
            modalTagTitle.text("Xóa khỏi tags");
            modalTag.modal("show");
            modalTag.css("display", "flex");
            modalTag.css("align-items", "center");
          }

          break;
      }

    });
    // input check all
    $(".selectAll").change(function (e) {
      if ($(this).is(":checked")) {
        $("tbody input").prop("checked", true);
        // $("tbody tr").addClass("tr-checked");
      } else {
        $("tbody input").prop("checked", false);
        // $("tbody tr").removeClass("tr-checked");
      }
    });

    // Chọn khoảng checkbox
    $(".select-range-btn").click(function (e) {
      e.preventDefault();
      var totalRows = $("tbody tr").length;
      $("#currentTotal").text(totalRows);
      $("#rangeFrom").val(1);
      $("#rangeTo").val(totalRows);
      $("#modalSelectRange").modal("show");
    });

    // Áp dụng chọn khoảng
    $("#applyRange").click(function (e) {
      applyRangeSelection();
    });

    // Hàm áp dụng chọn khoảng
    function applyRangeSelection() {
      var from = parseInt($("#rangeFrom").val());
      var to = parseInt($("#rangeTo").val());
      var totalRows = $("tbody tr").length;

      // Validate input
      if (isNaN(from) || isNaN(to)) {
        alert("Vui lòng nhập số hợp lệ!");
        return;
      }

      if (from < 1 || to < 1) {
        alert("STT phải lớn hơn 0!");
        return;
      }

      if (from > totalRows || to > totalRows) {
        alert("STT không được vượt quá tổng số bản ghi (" + totalRows + ")!");
        return;
      }

      if (from > to) {
        alert("STT bắt đầu không được lớn hơn STT kết thúc!");
        return;
      }

      // Bỏ chọn tất cả trước
      $("tbody input[type='checkbox']").prop("checked", false);
      $(".selectAll").prop("checked", false);

      // Chọn khoảng
      var selectedCount = 0;
      $("tbody tr").each(function(index) {
        var stt = index + 1;
        if (stt >= from && stt <= to) {
          $(this).find("input[type='checkbox']").prop("checked", true);
          selectedCount++;
        }
      });

      // Hiển thị thông báo
      if (selectedCount > 0) {
        // alert("Đã chọn " + selectedCount + " bản ghi từ STT " + from + " đến STT " + to);
      }

      // Đóng modal
      $("#modalSelectRange").modal("hide");
    }

    // Phím tắt Enter để áp dụng
    $("#modalSelectRange input").keypress(function(e) {
      if (e.which == 13) { // Enter key
        applyRangeSelection();
      }
    });

    $(".btn-add-backlink").click(function (e) {
      modalAddBacklink.modal("show");
      modalAddBacklink.css("display", "flex");
      modalAddBacklink.css("align-items", "center");
    });
    // add new backlinks
    // $("#modalAddBacklink button").click(function (e) {
    //     var _content = $("#modalAddBacklink textarea").val();
    //     var lines = [];
    //     $.each(_content.split(/\n/), function(i, line){
    //         if(line){
    //             lines.push(line);
    //         } else {
    //             lines.push("");
    //         }
    //     });
    //     var project_id = $("#project_id").val();
    //     $.ajax({
    //         method   :"POST",
    //         url      :"/cassiopeia/ajax",
    //         data     : {
    //             cmd : "user-add-backlink",
    //             _data : JSON.stringify(lines),
    //             project_id:project_id,
    //         },
    //         success : function(result){
    //             if(result.response=="OK"){
    //                 setModalAlert(result.message,function (e) {
    //                     // location.reload();
    //                 });
    //             }else{
    //                 setModalAlert(result.message);
    //             }
    //         }
    //     });
    //     return false;
    // });
    document.addEventListener("getTelsResponse", function (e) {
      let response = e.detail;
      let data = response.data;
      let currentObj = response.currentObj;
      let responseObject = response.responseObject;
      let listOfID = [];
      let running = true;
      if ($listOfID.attr("data-value") !== "") {
        listOfID = JSON.parse($listOfID.attr("data-value"))
      }
      if (listOfID.length > 0) {
        if (listOfID.includes(currentObj.id) !== true) {
          listOfID.push(currentObj.id);
          $listOfID.attr("data-value", JSON.stringify(listOfID));
          getTelsResponse(currentObj, responseObject, running);
        } else {
          getTelsResponse(currentObj, responseObject, running);
        }
      } else {
        listOfID.push(currentObj.id);
        $listOfID.attr("data-value", JSON.stringify(listOfID));
        getTelsResponse(currentObj, responseObject, running);
      }
    });

    async function getTelsResponse(currentObj, responseObject, running) {
      try {
        let checkedItems = $checkedItems.val();
        let totalItems = $totalItems.val();
        if (parseInt(checkedItems) <= parseInt(totalItems) && parseInt(totalItems) !== 0) {
          checkedItems++;
          $checkedItems.val(checkedItems);
          $(".progress-bar-block progress").val(checkedItems);
          $(".progress-bar-block .checkedBacklinks").text(checkedItems)
        }
        let __data = {};
        __data['id'] = responseObject.id;
        if (responseObject.status === 200) {
          let responseText = responseObject.responseText[0];
          let dom_nodes;
          try {
            dom_nodes = $($.parseHTML(responseText));
          } catch (e) {
            console.error("Lỗi khi phân tích HTML:", e);
            dom_nodes = $("<div></div>");
          }
          let aNodes = dom_nodes.find("a[href^='tel:']");

          let splitter1 = [];
          try {
            splitter1 = responseText.split("<body");
          } catch (e) {
            console.error("Lỗi khi tách chuỗi:", e);
            splitter1 = ["", ""];
          }

          let footerText = splitter1.length > 1 ? splitter1[1] : "";

          const tel_prefixes = ["09", "+84", "08", "07", "03"];
          const exclude_tel_prefixes = ["031", "032"];
          const strippedString = footerText.replace(/(<([^>]+)>)/gi, "<|>");
          let stringArray = strippedString.split("<|>");
          let tels = {};

          if (aNodes.length > 0) {
            aNodes.each(function () {
              try {
                let phoneNumber = $(this).attr('href').replace('tel:', ''); // Lấy số điện thoại
                let flag = true;
                exclude_tel_prefixes.forEach(function (e_value, e_index) {
                  let a = phoneNumber.indexOf(e_value);
                  if (a == 0) {
                    flag = false;
                  }
                });
                if (flag) {
                  tels[phoneNumber] = phoneNumber;
                }
              } catch (e) {
                console.error("Lỗi khi xử lý số điện thoại:", e);
              }
            });
          }

          if (stringArray && stringArray.length > 0) {
            stringArray.forEach(function (value, index) {
              if (value && value.length > 9) {
                tel_prefixes.forEach(function (_value, _index) {
                  try {
                    let regex_string = "/" + _value + "/g";
                    let count = value.split(_value).length - 1;
                    if (count > 0) {
                      let tel_positions = new Array();
                      for (let i = 1; i <= count; i++) {
                        if (i > 1) {
                          tel_positions[i] = value.indexOf(_value, tel_positions[i - 1] + 1);
                        } else {
                          tel_positions[i] = value.indexOf(_value);
                        }
                        if (i > 1 && (tel_positions[i] - tel_positions[i - 1] < 8)) {
                          continue;
                        }
                        let tel = value.slice(tel_positions[i], parseInt(tel_positions[i]) + 15);
                        tel = tel.replace(/\D/g, '');
                        let flag = true;
                        exclude_tel_prefixes.forEach(function (e_value, e_index) {
                          if (parseInt(tel.indexOf(e_value)) != 0) {
                            exclude_tel_prefixes.forEach(function (e_value, e_index) {
                              let a = tel.indexOf(e_value);
                              if (a == 0) {
                                flag = false;
                              }
                            });
                          }
                        });
                        if (flag) {
                          if (tel.length > 9) {
                            tels[tel] = tel;
                          }
                        }
                      }
                    }
                  } catch (e) {
                    console.error("Lỗi khi xử lý số điện thoại từ văn bản:", e);
                  }
                });
              }
            });
          }

          console.log("tels", tels);
          __data["tels"] = tels;
        }

        // Gọi AJAX để lưu dữ liệu
        $.ajax({
          method: "POST",
          url: Drupal.settings.basePath + "cassiopeia/ajax",
          data: {cmd: "user-backlink-get-tels-response", responseObject: JSON.stringify(__data)},
          success: function (result) {
            console.log("result", result);
            if (result.response != "FAIL") {
              try {
                let html = "";
                $.each(JSON.parse(result.values), function (index, value) {
                  t = renderBacklinkRow(value, currentObj.stt);
                  html += t;
                });
                $(html).replaceAll(".page-user-backlink-project-detail tr[data-nid=" + currentObj.id + "]");
                let element = $("tr[data-nid=" + currentObj.id + "]");
                if (element.length > 0) {
                  $(".t-body").scrollTop(element.position().top);
                }
              } catch (e) {
                console.error("Lỗi khi cập nhật UI:", e);
              }
            }
          },
          error: function(xhr, status, error) {
            console.error("Lỗi khi gửi AJAX request:", error);
          }
        });

      } catch (e) {
        console.error("Lỗi trong hàm getTelsResponse:", e);
      }

      // Tiếp tục quét các liên kết còn lại
      try {
        let listOfID = JSON.parse($listOfID.attr("data-value"));
        let checkedItems = $checkedItems.val();
        let totalItems = $totalItems.val();

        if (running) {
          if (parseInt(checkedItems) == parseInt(totalItems)) {
            if (recheck.length > 0) {
              recheckTotal = recheck.length;
              $totalItems.val(recheckTotal);
              $checkedItems.val(0);
              $(".progress-bar-block progress").attr("max", recheckTotal);
              $(".progress-bar-block .totalBacklinks").text(recheckTotal);
              let newdata = {};
              let stt = 0;
              let slice = {};
              $.each(recheck, function (index, _value) {
                slice[index] = _value;
                if ((index + 1) % 5 === 0) {
                  newdata[stt] = slice;
                  slice = {};
                  stt++;
                }
              });
              newdata[stt] = slice;
              setTimeout(function () {
                document.dispatchEvent(new CustomEvent('getTels', {
                  detail: newdata
                }));
              }, 1000); // Tăng thời gian chờ lên 1000ms
            } else {
              $totalItems.val(0);
              $checkedItems.val(0);
              $(".progress-bar-block").removeClass("active");
              $(".result").removeClass("loading");
              setTimeout(function () {
                $.ajax({
                  method: "POST",
                  url: Drupal.settings.basePath + "cassiopeia/ajax",
                  data: {
                    cmd: "user-backlink-get-tels-response-complete",
                    value: 1,
                    listOfID: JSON.stringify(listOfID),
                    responseObject: JSON.stringify(responseObject)
                  },
                  success: function (result) {
                    // Hiển thị thông báo hoàn thành
                    try {
                      if (typeof Drupal !== 'undefined' && typeof Drupal.behaviors.messages !== 'undefined') {
                        Drupal.behaviors.messages.showMessage("Quét số điện thoại hoàn tất!", "status");
                      } else {
                        // Hiển thị thông báo bằng alert hoặc toast

                      }
                    } catch (e) {
                      console.log("Quét số điện thoại hoàn tất!");
                    }

                    // Dừng các tiến trình đang chạy
                    start = false;
                    running = false;

                    // Gửi sự kiện để đóng thông báo của extension
                    setTimeout(function() {
                      // Gửi tín hiệu đến extension để đóng thông báo
                      document.dispatchEvent(new CustomEvent("ScanCompleted", {detail: "completed"}));

                      // Tải lại trang sau 1 giây nhưng không chọn dòng nào
                      setTimeout(function() {
                        window.location.reload();
                      }, 1000);
                    }, 500);

                    if (result.response == "EXPIRED") {
                      setTimeout(function () {
                        document.dispatchEvent(new CustomEvent("UserExpired", {detail: "123"}));
                      }, 500);
                    } else if (result.response == "LIMITED") {
                      // Không làm gì thêm
                    }
                  },
                  error: function(xhr, status, error) {
                    console.error("Lỗi khi gửi AJAX request hoàn thành:", error);
                    // Tự động tải lại trang để khắc phục lỗi
                    setTimeout(function() {
                      window.location.reload();
                    }, 1000);
                  }
                });
              }, 1000); // Tăng thời gian chờ lên 1000ms
            }
          }
        }
      } catch (e) {
        console.error("Lỗi khi xử lý danh sách ID:", e);
      }
    }

    // Không còn tự động chọn dòng đầu tiên mỗi khi tải trang
    $("#user-backlink-detail-form tbody tr td input[type='checkbox']").bind('click',function(e){
      if(this.checked) {
        $(this).parents('tr').addClass('selected');
      }else{
        $(this).parents('tr').removeClass('selected');
      }
    });

    // Xử lý chọn khoảng trực tiếp trên bảng
    $("#inlineApplyRange").click(function () {
      var from = parseInt($("#inlineRangeFrom").val());
      var to = parseInt($("#inlineRangeTo").val());
      var totalRows = $("tbody tr").length;

      if (isNaN(from) || isNaN(to)) {
        alert("Vui lòng nhập số hợp lệ!");
        return;
      }
      if (from < 1 || to < 1) {
        alert("STT phải lớn hơn 0!");
        return;
      }
      if (from > totalRows || to > totalRows) {
        alert("STT không được vượt quá tổng số bản ghi (" + totalRows + ")!");
        return;
      }
      if (from > to) {
        alert("STT bắt đầu không được lớn hơn STT kết thúc!");
        return;
      }
      // Bỏ chọn tất cả trước
      $("tbody input[type='checkbox']").prop("checked", false);
      $(".selectAll").prop("checked", false);
      // Chọn khoảng
      var selectedCount = 0;
      $("tbody tr").each(function(index) {
        var stt = index + 1;
        if (stt >= from && stt <= to) {
          $(this).find("input[type='checkbox']").prop("checked", true);
          selectedCount++;
        }
      });
      if (selectedCount > 0) {
        // alert("Đã chọn " + selectedCount + " bản ghi từ STT " + from + " đến STT " + to);
      }
    });
    // Hỗ trợ phím Enter cho input
    $("#inlineRangeFrom, #inlineRangeTo").keypress(function(e) {
      if (e.which == 13) {
        $("#inlineApplyRange").click();
      }
    });
  });
})(jQuery);
