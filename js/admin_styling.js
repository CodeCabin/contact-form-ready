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


        $('#wpcf_nd_label_color').iris({
            change: function (event, ui) {
                previewLabels.css('color', ui.color.toString());
            }
        });

        $('#wpcf_nd_input_bg_color').iris({
            change: function (event, ui) {
                previewInputs.css('background', ui.color.toString());
            }
        });

        $('#wpcf_nd_input_border_color').iris({
            change: function (event, ui) {
                previewInputs.css('border-color', ui.color.toString());
            }
        });

        $('#wpcf_nd_input_font_color').iris({
            change: function (event, ui) {
                previewInputs.css('color', ui.color.toString());
            }
        });

        $('#wpcf_nd_submit_bg_color').iris({
            change: function (event, ui) {
                previewSubmit.css('background', ui.color.toString());
            }
        });

        $('#wpcf_nd_submit_font_color').iris({
            change: function (event, ui) {
                previewSubmit.css('color', ui.color.toString());
            }
        });

        $('#wpcf_nd_input_border_focus_color').iris({
            change: function (event, ui) {
                previewInputs.mouseenter(function() {
                    $(this).css('border-color', ui.color.toString());
                }).mouseleave(function() {
                    previewInputs.css('border-color', jQuery('#wpcf_nd_input_border_color').val());
                });
            }
        });

        $('#wpcf_nd_submit_bg_hover_color').iris({
            change: function (event, ui) {
                previewSubmit.mouseenter(function() {
                    previewSubmit.css('background', ui.color.toString());
                }).mouseleave(function() {
                    previewSubmit.css('background', jQuery('#wpcf_nd_submit_bg_color').val());
                });
            }
        });

        $('.wpcf-styling-form').click(function (event) {
            var targets = $(".wpcf-color-input, .iris-picker, .iris-picker-inner, .wpcf-submit-save-styling, .wpcf-admin-enable-style-wrapper");
            if (!targets.is(event.target) && targets.has(event.target).length === 0) {
                colorInput.iris('hide');

                return false;
            }
        });

        colorInput.click(function (event) {
            colorInput.iris({hide: true});
            $(this).iris('show');
            return false;
        });

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

    });


})(jQuery);