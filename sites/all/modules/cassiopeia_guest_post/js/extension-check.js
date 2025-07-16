/**
 * Extension Check và Error Handling cho SEO MiniSuite
 */
(function ($) {
    'use strict';

    // Kiểm tra extension có được cài đặt không
    function checkExtensionInstalled() {
        return $("footer").hasClass("seoToolExtension");
    }

    // Hiển thị thông báo cài đặt extension
    function showExtensionInstallMessage() {
        if (typeof setModalAlert === 'function') {
            setModalAlert(
                "Extension SEO MiniSuite chưa được cài đặt hoặc chưa hoạt động.<br>" +
                "Vui lòng cài đặt extension trước khi sử dụng tính năng này!",
                function () {
                    // Redirect to extension download page
                    window.open('https://chrome.google.com/webstore/detail/seo-minisuite/extension-id', '_blank');
                },
                "<h3 class='margin-0'>Cần cài đặt Extension</h3>"
            );
        } else {
            alert("Extension SEO MiniSuite chưa được cài đặt hoặc chưa hoạt động. Vui lòng cài đặt extension trước khi sử dụng!");
        }
    }

    // Validate dữ liệu trước khi gửi
    function validateArticleData(article) {
        var errors = [];

        if (!article.title || article.title.trim() === '') {
            errors.push("Tiêu đề bài viết không được để trống");
        }

        if (!article.content || article.content.trim() === '') {
            errors.push("Nội dung bài viết không được để trống");
        }

        if (!article.website || article.website === "_none") {
            errors.push("Vui lòng chọn website để đăng bài");
        }

        if (!article.wp_category || article.wp_category.length === 0) {
            errors.push("Vui lòng chọn ít nhất một danh mục");
        }

        return errors;
    }

    // Xử lý lỗi chung
    function handleError(error, context) {
        console.error('Error in ' + context + ':', error);

        var errorMessage = "Đã xảy ra lỗi";
        if (context) {
            errorMessage += " trong " + context;
        }
        errorMessage += ". Vui lòng thử lại!";

        if (typeof setModalAlert === 'function') {
            setModalAlert(errorMessage, function () {}, "<h3 class='margin-0'>Lỗi</h3>");
        } else {
            alert(errorMessage);
        }

        // Remove loading indicator
        $(".loading-block").remove();
    }

    // Export functions to global scope
    window.ExtensionChecker = {
        checkInstalled: checkExtensionInstalled,
        showInstallMessage: showExtensionInstallMessage,
        validateArticle: validateArticleData,
        handleError: handleError
    };

})(jQuery);
