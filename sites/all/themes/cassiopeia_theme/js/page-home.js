// var colorArray = {};
// colorArray[0] = "#1f9f4c";
// colorArray.push("#1f9f4c");
// console.log("colorArray", colorArray);
(function($) {
    function cassiopeiaGetKeywordChart(){
        // Removed chart functionality
        return null;
    }
    function cassiopeiaGetBacklinkChart(){
        // Removed chart functionality
        return null;
    }
    function cassiopeiaCreateBacklinkChart() {
        // Removed chart functionality
        return null;
    }

    function cassiopeiaCreateKeywordChart() {
        // Removed chart functionality
        return null;
    }
    $("document").ready(function(e) {
        // Disabled chart functions
        // cassiopeiaGetKeywordChart();
        // cassiopeiaGetBacklinkChart();
        $("input[name='domain']").change(function (e) {
            // cassiopeiaGetBacklinkChart();
        });
        $("input[name='keywords']").change(function (e) {
            // cassiopeiaGetKeywordChart();
        });
        var result = $(".home-backlinks tbody tr").sort(function(a, b) {
            var contentA = $(a).find("td[data-key='title'] a").text();
            var contentB = $(b).find("td[data-key='title'] a").text();
            return (contentA < contentB) ? -1 : (contentA > contentB) ? 1 : 0;
        });
        $(".home-backlinks .home-backlinks table tbody").html(result);
        $(".home-page .home-backlinks .sort").click(function(e) {
            let _this = $(this);
            // $(".home-page .home-backlinks .sort").removeClass("current");
            // _this.addClass("current");
            let direction = _this.attr("data-direction");
            let sort = _this.attr("data-sort");
            if (direction == "DESC") {
                _this.attr("data-direction", "ASC");
                var result = $(".home-backlinks tbody tr").sort(function(a, b) {
                    if (sort == "total") {
                        var contentA = parseInt($(a).find("td[data-key='" + sort + "']").text());
                        var contentB = parseInt($(b).find("td[data-key='" + sort + "']").text());
                    } else if (sort == "created") {
                        var contentA = parseInt($(a).find("td[data-key='" + sort + "']").attr("data-value"));
                        var contentB = parseInt($(b).find("td[data-key='" + sort + "']").attr("data-value"));
                    } else {
                        var contentA = $(a).find("td[data-key='" + sort + "'] .sort-text").text();
                        var contentB = $(b).find("td[data-key='" + sort + "'] .sort-text").text();
                    }
                    return (contentA < contentB) ? -1 : (contentA > contentB) ? 1 : 0;
                });
                $(".home-backlinks table tbody").html(result);
            } else {
                console.log(_this);
                _this.attr("data-direction", "DESC");
                var result = $(".home-backlinks tbody tr").sort(function(a, b) {
                    if (sort == "total") {
                        var contentA = parseInt($(a).find("td[data-key='" + sort + "']").text());
                        var contentB = parseInt($(b).find("td[data-key='" + sort + "']").text());
                    } else if (sort == "created") {
                        var contentA = parseInt($(a).find("td[data-key='" + sort + "']").attr("data-value"));
                        var contentB = parseInt($(b).find("td[data-key='" + sort + "']").attr("data-value"));
                        console.log(contentA);
                    } else {
                        var contentA = $(a).find("td[data-key='" + sort + "'] .sort-text").text();
                        var contentB = $(b).find("td[data-key='" + sort + "'] .sort-text").text();
                    }
                    return (contentA > contentB) ? -1 : (contentA < contentB) ? 1 : 0;
                });
                $(".home-backlinks table tbody").html(result);
            }
            let stt = 1;
            $(".home-backlinks tbody tr").each(function(e) {
                let _this = $(this);
                _this.find("td.col-stt").text(stt);
                stt++;
            });
        });

        $(".home-page .home-keywords .sort").click(function(e) {
            let _this = $(this);
            // $(".home-page .home-keywords .sort").removeClass("current");
            // _this.addClass("current");
            let direction = _this.attr("data-direction");
            let sort = _this.attr("data-sort");
            if (direction == "DESC") {
                _this.attr("data-direction", "ASC");
                var result = $(".home-keywords tbody tr").sort(function(a, b) {
                    if (sort == "total") {
                        var contentA = parseInt($(a).find("td[data-key='" + sort + "']").text());
                        var contentB = parseInt($(b).find("td[data-key='" + sort + "']").text());
                    } else if (sort == "created") {
                        var contentA = parseInt($(a).find("td[data-key='" + sort + "']").attr("data-value"));
                        var contentB = parseInt($(b).find("td[data-key='" + sort + "']").attr("data-value"));
                    } else {
                        var contentA = $(a).find("td[data-key='" + sort + "'] .sort-text").text();
                        var contentB = $(b).find("td[data-key='" + sort + "'] .sort-text").text();
                    }
                    return (contentA < contentB) ? -1 : (contentA > contentB) ? 1 : 0;
                });
                $(".home-keywords table tbody").html(result);
            } else {
                console.log(_this);
                _this.attr("data-direction", "DESC");
                var result = $(".home-keywords tbody tr").sort(function(a, b) {
                    if (sort == "total") {
                        var contentA = parseInt($(a).find("td[data-key='" + sort + "']").text());
                        var contentB = parseInt($(b).find("td[data-key='" + sort + "']").text());
                    } else if (sort == "created") {
                        var contentA = parseInt($(a).find("td[data-key='" + sort + "']").attr("data-value"));
                        var contentB = parseInt($(b).find("td[data-key='" + sort + "']").attr("data-value"));
                        console.log(contentA);
                    } else {
                        var contentA = $(a).find("td[data-key='" + sort + "'] .sort-text").text();
                        var contentB = $(b).find("td[data-key='" + sort + "'] .sort-text").text();
                    }
                    return (contentA > contentB) ? -1 : (contentA < contentB) ? 1 : 0;
                });
                $(".home-keywords table tbody").html(result);
            }
            let stt = 1;
            $(".home-keywords tbody tr").each(function(e) {
                let _this = $(this);
                _this.find("td.col-stt").text(stt);
                stt++;
            });
        });
        // cassiopeiaCreateBacklinkChart();
        // cassiopeiaCreateKeywordChart();

    });
})(jQuery);
