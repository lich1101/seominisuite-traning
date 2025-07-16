var footer = jQuery("footer");
(function($) {
    $("document").ready(function(e) {
        $("div.none-duplicate").click(function (e) {
            $(".total-result-item").removeClass("active");
            $(this).addClass("active");
           $(".table-result tr").each(function (e) {
              let _this = $(this);
              if(_this.find("td.check-result").attr("data-value")!=0){
                  _this.hide();
              }else{
                  _this.show();
              }
           });
        });
        $("div.duplicate").click(function (e) {
            $(".total-result-item").removeClass("active");
            $(this).addClass("active");
            $(".table-result tr").each(function (e) {
                let _this = $(this);
                if(_this.find("td.check-result").attr("data-value")==0){
                    _this.hide();
                }else{
                    _this.show();
                }
            });
        });
        $("div.all").click(function (e) {
            $(".total-result-item").removeClass("active");
            $(this).addClass("active");
            $(".table-result tr").show();
        });
        $(".btn-export").click(function (e) {
            let _this = $(this);
            if(_this.hasClass("excel")){
                let data = [];
                $(".table-result tr").each(function (e) {
                    let _this = $(this);
                    let temp = {};
                    temp['stt'] = _this.find("td.stt").text();
                    temp['text'] = _this.find("td.part-of-content").text();
                    temp['sources'] = _this.find("td.sources").text();
                    temp['result'] = _this.find("td.check-result").attr("data-duplicate");
                    data.push(temp);
                });
                if(data.length>0){
                    $.ajax({
                        method   : "POST",
                        url      :"/cassiopeia/ajax",
                        data     : {
                            cmd : "export-duplicate",
                            data:JSON.stringify(data),
                        },
                        success : function(result){
                            location.href = "/kiem-tra-dao-van/export";
                        }
                    });
                }
            }else{
                setModalConfirm("Bạn cần nâng cấp gói để sử dụng chức năng này!",function (e) {
                    location.href = "/price-board";
                })
                return false;
            }

        });
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
            var result = $(".table-result tr").sort(function(a, b) {
                switch (sort) {
                    case "result":
                        var contentA = $(a).find("td[data-key='" + sort + "']").attr("data-value");
                        var contentB = $(b).find("td[data-key='" + sort + "']").attr("data-value");
                        break;
                }
                if (direction == "DESC") {
                    return (contentA > contentB) ? -1 : (contentA < contentB) ? 1 : 0;
                } else {
                    return (contentA < contentB) ? -1 : (contentA > contentB) ? 1 : 0;
                }
            });
            $(".table-result").html(result);
            let stt=1;
            $(".table-result tr").each(function (e) {
                let _this = $(this);
                _this.find("td.stt").text(stt);
                stt++;
            });
        });
        $(".btn-check-duplicate-content").click(function(e) {
            if ($("#footer").hasClass("seoToolExtension") !== true) {
                installExtension();
                return false;
            }
            // let content = tinyMCE.activeEditor.getContent();
            // let textContent = $(".fr-element.fr-view").html();
            let content = tinyMCE.activeEditor.getContent();
            content = content.replace(/&nbsp;/g,' ');
            let textContent = $(content).text();
            // let textContent = $(content).text();
            if(textContent.trim().length<1){
                alert("Bạn chưa nhập text!");
                return false;
            }
            // textContent = stripta
            textContent = textContent.replace(/(<([^>]+)>)/gi, ".");;

            // console.log("textContent",textContent);
            // let splitter_1 = textContent.split("/");
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
                        tempString = tempString.trim().replace("&nbsp;"," ");
                        tempHTML += "<tr><td data-sort='string' data-id='" + id + "' class='part-of-content text-left' title='"+tempString+"'>" + tempString + "</td><td data-key='source' class='sources'></td><td data-key='result' class='check-result'></td></tr>";
                        id++;
                        stt++;
                    }
                    _count++;
                } else {
                    if (childSplitter.length > 3) {
                        _count++;
                        temp = temp.trim().replace("&nbsp;"," ");;
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
          console.log("captcha_resolve",captcha_resolve);
          if(captcha_resolve==="auto"){ // giải captcha tự động
            jQuery.ajax({
              method: "POST",
              url: "/cassiopeia-captcha/resolve/get-info",
              success: function (result) {
                // let response = JSON.parse(result);
                console.log("result",result);
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
                        action:  function () {
                          $("select[name='captcha-resolve']").val("manual");
                          $(".table-result").html(tempHTML);
                          document.dispatchEvent(new CustomEvent('CheckDuplicateContent', { detail: '123' }));
                          $(".btn-export").removeClass("btn-disable");
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
                  $(".table-result").html(tempHTML);
                  document.dispatchEvent(new CustomEvent('CheckDuplicateContent', { detail: '123' }));
                  $(".btn-export").removeClass("btn-disable");
                }
              }
            });
          }else{
            $(".table-result").html(tempHTML);
            document.dispatchEvent(new CustomEvent('CheckDuplicateContent', { detail: '123' }));
            $(".btn-export").removeClass("btn-disable");
          }

        });
        var $input = $('input.tagify-input').tagify({
            whitelist: ['']
        })
    });
})(jQuery);