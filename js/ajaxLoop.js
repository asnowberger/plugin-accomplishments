// ajaxLoop.js
// @link : http://code.tutsplus.com/articles/getting-loopy-ajax-powered-loops-with-jquery-and-wordpress--wp-23232

jQuery(function($) {
    var page = 1;
    var loading = true;
    var $window = $(window);
    var $content = $('body.blog #content');
    
    var load_posts = function() {
        $.ajax({
            type        : "GET",
            data        : { numPosts : 1, pageNumber : page },
            dataType    : "html",
            url         : "http://www."
        });
    }
});

