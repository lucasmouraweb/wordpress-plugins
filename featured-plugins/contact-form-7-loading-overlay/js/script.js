jQuery(document).ready(function($) {
    $('.wpcf7-form').on('submit', function() {
        $('.cf7-loading-overlay').fadeIn();
    });

    $(document).on('wpcf7submit', function(event) {
        $('.cf7-loading-overlay').fadeOut();
    });
});