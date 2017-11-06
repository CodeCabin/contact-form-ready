(function($){

    $(document).ready(function () {

        var styleTableWrapper = $('.wpcf-admin-enable-table-wrapper'),
            enableStylingInput = $('#wpcf_nd_enable_style');

        if (enableStylingInput.is(':checked')) {
            styleTableWrapper.addClass('is-active');
        } else {
            styleTableWrapper.removeClass('is-active');
        }

        enableStylingInput.on('change', function (event) {
            event.preventDefault();

            var that = $(this);

            if (that.is(':checked')) {
                styleTableWrapper.addClass('is-active');
            } else {
                styleTableWrapper.removeClass('is-active');
            }
        });

    });


})(jQuery);