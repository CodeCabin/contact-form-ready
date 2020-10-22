(function($){

    $(document).ready(function () {

        var styleTableWrapper = $('.wpcf-admin-styling-wrapper'),
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

        /* Preview form */
        var colorInput = $('.wpcf-color-input'),
            previewLabels = $('.wpcf-admin-preview-label'),
            previewInputs = $('.wpcf-admin-preview-input'),
            previewSubmit = $('.wpcf-admin-preview-submit');


        /* Iris replaced with standard color picker */

        $('#wpcf_nd_label_font_size').on('change', function (event) {
            previewLabels.css('font-size', $(this).val() + 'px');
        });

        $('#wpcf_nd_label_font_weight').on('change', function (event) {
            previewLabels.css('font-weight', $(this).val());
        });

        $('#wpcf_nd_input_font_size').on('change', function (event) {
            previewInputs.css('font-size', $(this).val() + 'px');
        });

        $('#wpcf_nd_submit_font_size').on('change', function (event) {
            previewSubmit.css('font-size', $(this).val() + 'px');
        });

        $('#wpcf_nd_submit_font_weight').on('change', function (event) {
            previewSubmit.css('font-weight', $(this).val());
        });

        $('#wpcf_nd_submit_text_transform').on('change', function (event) {
            previewSubmit.css('text-transform', $(this).val());
        });

       $(".wpcf-color-input").each(function(){
            var row = $(this).parent().parent();
 
            row.append('<td><div class="wpcf-live-color-preview"></div></td>');
            row.find(".wpcf-live-color-preview").css("background", $(this).val());
        });
 
         
        $('.wpcf-live-color-preview').on('click', function(){
            var row = $(this).parent().parent();
 
            row.find('.wpcf-color-input').click(); 
        });
    });

    jQuery("document").ready(function(){
        jQuery( "#sola_cfr_styling_tabs" ).tabs({ active: 0 });
    });

})(jQuery);