if (typeof (window.cassiopeia_tagifies) == 'undefined') {
    window.cassiopeia_tagifies = {};
}
(function ($) {
    Drupal.behaviors.cassiopeia_guest_post_domain_change_form = {
        attach: function (context, settings) {
            $('.cassiopeia-guest-post-domain-change-form', context).once('cassiopeia-guest-post-domain-change-form',function () {
                var typingTimer;
                $("document").ready(function (e) {
                    $("#modalContent").addClass("modal-cassiopeia-guest-post-domain-change-form");
                    let str = "<div class=\"loading-block active\">\n" +
                        "    <div class=\"loading-block-container\">\n" +
                        "        <div class=\"lds-css ng-scope\">\n" +
                        "            <div class=\"lds-spin\" style=\"width:100%;height:100%\"><div><div></div></div><div><div></div></div><div><div></div></div><div><div></div></div><div><div></div></div><div><div></div></div><div><div></div></div><div><div></div></div></div>\n" +
                        "        </div>\n" +
                        "    </div>\n" +
                        "</div>";
                    let real_image = $("input[name='files[excel_file]']");
                    let fake_image = $('.fake-image');
                    fake_image.on('click',function(e){
                        real_image.click();
                    });
                    real_image.change(function (e) {
                        const file = this.files[0];
                        if(file===undefined){
                            $(".cassiopeia-guest-post-domain-change-form .file-zone button").removeClass("active");
                        }else{
                            if (file){
                                $(".cassiopeia-guest-post-domain-change-form .file-zone button").addClass("active");
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
                    $(".cassiopeia-guest-post-domain-change-form .targets span.text").click(function (e) {
                        $(".list-of-target").toggleClass("active");
                    });
                    $.fn.guestPostChangeDomainAlert = function (data) {
                       $(".error-message").html(JSON.parse(data));
                       $(".error-message").addClass("active");
                    }
                });
            });
        },
        detach: function(context, settings, trigger) {
            $('.cassiopeia-guest-post-domain-change-form', context).removeOnce('cassiopeia-guest-post-domain-change-form', function() {});
        }
    };
})(jQuery);