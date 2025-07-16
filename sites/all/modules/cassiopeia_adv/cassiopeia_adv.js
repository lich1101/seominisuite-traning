jQuery.fn.vjustify=function() {
    var maxHeight=0;
    this.each(function(){
        if (this.offsetHeight>maxHeight) {maxHeight=this.offsetHeight;}
    });
    this.each(function(){
        //jQuery(this).height(maxHeight + "px");
      jQuery(this).css("min-height", maxHeight);
        if (this.offsetHeight>maxHeight) {
            //jQuery(this).height((maxHeight-(this.offsetHeight-maxHeight))+"px");
            jQuery(this).css("min-height", (maxHeight-(this.offsetHeight-maxHeight)));
        }
    });
};
function runOnLoad(f) {
    if (runOnLoad.loaded) f();    // If already loaded, just invoke f() now.
    else runOnLoad.funcs.push(f); // Otherwise, store it for later
}

runOnLoad.funcs = []; // The array of functions to call when the document loads
runOnLoad.loaded = false; // The functions have not been run yet.

runOnLoad.run = function() {
    if (runOnLoad.loaded) return;  // If we've already run, do nothing

    for(var i = 0; i < runOnLoad.funcs.length; i++) {
        try { runOnLoad.funcs[i](); }
        catch(e) { /* An exception in one function shouldn't stop the rest */ }
    }

    runOnLoad.loaded = true; // Remember that we've already run once.

    delete runOnLoad.funcs;  // But don't remember the functions themselves.
    delete runOnLoad.run;    // And forget about this function too!
};

// Register runOnLoad.run() as the onload event handler for the window
if (window.addEventListener)
    window.addEventListener("load", runOnLoad.run, false);
else if (window.attachEvent) window.attachEvent("onload", runOnLoad.run);
else window.onload = runOnLoad.run;

jQuery(document).ready(function($) {

});

(function ($) {
  Drupal.behaviors.customer_ad = {
    attach: function () {
      $(".slide-quang-cao-auto").each(function(){
        $(this).css("position", "relative");
        $(this).css("overflow", "hidden");
        $(this).children(".slide-ad-item").css("position", "absolute");
        $(this).children(".slide-ad-item").hide();
        $(this).children(".slide-ad-item:first").show();
        var id = $(this).attr("id");
        self.setInterval(function(){slide_quang_cao(id)}, 4000);
      });
    }
  };

  slide_quang_cao = function(id){
    $("#" + id + " .slide-ad-item").each(function(){
      var index = $("#" + id + " .slide-ad-item").index($(this));
      if($(this).is(":visible")){
        var next_show;
        if(index == $("#" + id + " .slide-ad-item").size() - 1){
          next_show = 0;
        }else{
          next_show = index + 1;
        }
        $(this).css("z-index", "0");
        $("#" + id + " .slide-ad-item").eq(next_show).css("z-index", "9");
        $("#" + id + " .slide-ad-item").eq(next_show).animate({opacity: "show"}, 600);
        $(this).animate({opacity: "hide"}, 1000);
        return false;
      }
    });
  }

  var toptintuc = 0;
  runOnLoad(function() {
    $(".wrap-page-dm-article-21 .image").vjustify();
    //jQuery("#iddivAdLeft").css("left", offset.left - jQuery("#iddivAdLeft").width() - 10);
    //jQuery("#iddivAdRight").css("left", offset.left + 1010);
    //
    //jQuery(window).resize(function() {
    //  jQuery("#iddivAdLeft").css("left", offset.left - jQuery("#iddivAdLeft").width() - 10);
    //  jQuery("#iddivAdRight").css("left", offset.left + 1010);
    //});
    //
    //
    //jQuery("#divAdPop_overlay").click(function(){
    //  jQuery("#iddivAdPop").remove();
    //});
    //jQuery("#link-clode-pop").click(function(){
    //  jQuery("#iddivAdPop").remove();
    //});
    //
    //var showpopup = jQuery.cookie("showpopup");
    //var showpopup_time = jQuery.cookie("showpopup_time");
    //if(jQuery.now() - showpopup_time > 180000 || showpopup_time == ''){
    //  showpopup = 0;
    //}
    //if(showpopup == 1){
    //  jQuery("#iddivAdPop").hide();
    //}else{
    //  jQuery("#divAdPop_overlay").show();
    //  jQuery("#iddivAdPop_inner").css("top", (jQuery(window).height() - jQuery("#iddivAdPop_inner").height())/2);
    //  jQuery("#iddivAdPop_inner").css("left", (jQuery(window).width() - jQuery("#iddivAdPop_inner").width())/2);
    //  jQuery.cookie("showpopup", 1);
    //  jQuery.cookie("showpopup_time", jQuery.now());
    //}

    custom_menu_bay();
    //customer_tintuchome();
    $(window).scroll(function(){
      custom_menu_bay();
      //customer_tintuchome();
    });
  });
})(jQuery);