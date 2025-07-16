(function ($) {

    $(document).ready(function () {
        let _checkbox = $("input[type=checkbox]");
        _checkbox.change(function (e) {
          let _this = $(this);
          if(_this.val()=="all"){
              if(_this.is(":checked")){
                  _checkbox.prop("checked","checked");
              }else{
                  _checkbox.prop("checked",false);
              }
          }
       });
       $(".btn-website-get-categories").click(function (e) {
           let data = [];
           _checkbox.each(function (e) {
              let _this = $(this);
               if(_this.is(":checked")){
                   if(_this.val()!="all"){
                       let _temp = {};
                       _temp['id'] = _this.val();
                       _temp['domain'] = _this.attr('data-domain');
                       data.push(_temp);
                   }
               }
           });
           if(data.length>0){
                $(".page-admin-manager-guest-post-website .progress-block progress").attr("value",0);
                $(".page-admin-manager-guest-post-website .progress-block progress").attr("max",data.length);
                $(".page-admin-manager-guest-post-website .progress-block").addClass("active");
               document.dispatchEvent(new CustomEvent('Guest_Post_Website_Get_Categories', {detail: JSON.stringify(data)}));
           }else{
               setModalAlert("Bạn chưa chọn website!");
           }
       });
        document.addEventListener("Guest_Post_Website_Get_Categories_Complete", function (e) {
            let currentValue = $(".page-admin-manager-guest-post-website .progress-block progress").attr("value");
            let maxValue = $(".page-admin-manager-guest-post-website .progress-block progress").attr("max");
            currentValue = parseInt(currentValue)+1;
            $(".page-admin-manager-guest-post-website .progress-block progress").attr("value",currentValue);
            console.log("currentValue",currentValue);
            console.log("maxValue",maxValue);
            $.ajax({
                method: "POST",
                url: "/cassiopeia_guest_post/ajax",
                data: {
                    cmd: "Guest_Post_Website_Get_Categories_Complete",
                    node: e.detail,
                },
                success: function (result) {
                    if(currentValue==maxValue){
                        $(".page-admin-manager-guest-post-website .progress-block").removeClass("active");
                        location.reload();
                    }`  `
                }
            });
        });
    });
})(jQuery);