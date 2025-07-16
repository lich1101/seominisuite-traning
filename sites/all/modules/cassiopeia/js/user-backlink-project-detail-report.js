var footer = jQuery("footer");

function cassiopeiaCreateBacklinkChart() {
    // Removed chart functionality
    return null;
}

function cassiopeiaBackLinkRelChart() {
    // Removed chart functionality
    return null;
}

function cassiopeiaIndexChart() {
    // Removed chart functionality
    return null;
}

(function($) {
    $("document").ready(function(e) {
        let date_filter = $("select[name='date_filter']");
        date_filter.change(function(e) {
            if (date_filter.val() === "other") {
                $(".container-inline-date").addClass("active");
            } else {
                $(".container-inline-date").removeClass("active");
            }
        });
        console.log(date_filter.val());
        if (date_filter.val() === "other") {
            $(".container-inline-date").addClass("active");
        } else {
            $(".container-inline-date").removeClass("active");
        }
        // Disabled chart functions
        // cassiopeiaCreateBacklinkChart();
        // cassiopeiaBackLinkRelChart();
        // cassiopeiaIndexChart();
    });
})(jQuery);
