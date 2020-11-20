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
        
        $('#wpcf_nd_label_color').on('change', function (event) {
            previewLabels.css('color', $(this).val());
        });

        $('#wpcf_nd_input_bg_color').on('change', function (event) {
            previewInputs.css('background-color', $(this).val());
        });

        var wpcf_style_input_border_color = $('#wpcf_nd_input_border_color').val();
        $('#wpcf_nd_input_border_color').on('change', function (event) {
            previewInputs.css('border-color', $(this).val());
            wpcf_style_input_border_color = $(this).val();
        });

        var wpcf_style_input_border_focus_color = $('#wpcf_nd_input_border_focus_color').val();
        $('#wpcf_nd_input_border_focus_color').on('change', function (event) {
            wpcf_style_input_border_focus_color = $(this).val();
        });

        $('.wpcf-admin-preview-input').on('mouseover', function (event) {
            previewInputs.css('border-color', wpcf_style_input_border_focus_color);
        });

        $('.wpcf-admin-preview-input').on('mouseleave', function (event) {
            previewInputs.css('border-color', wpcf_style_input_border_color);
        });

        $('#wpcf_nd_input_font_color').on('change', function (event) {
            previewInputs.css('color', $(this).val());
        });

        var wpcf_style_submit_bg_color = $('#wpcf_nd_submit_bg_color').val();
        $('#wpcf_nd_submit_bg_color').on('change', function (event) {
            previewSubmit.css('background-color', $(this).val());
            wpcf_style_submit_bg_color = $(this).val();
        });

        var wpcf_style_submit_bg_hover_color = $('#wpcf_nd_submit_bg_hover_color').val();
        $('#wpcf_nd_submit_bg_hover_color').on('change', function (event) {
            wpcf_style_submit_bg_hover_color = $(this).val();
        });

        $('.wpcf-admin-preview-submit').on('mouseover', function (event) {
            previewSubmit.css('background-color', wpcf_style_submit_bg_hover_color);
        });

        $('.wpcf-admin-preview-submit').on('mouseleave', function (event) {
            previewSubmit.css('background-color', wpcf_style_submit_bg_color);
        });

        $('#wpcf-admin-preview-darkmode-checkbox').on('change', function(event) {
            if (!$('#wpcf-admin-preview-darkmode-checkbox').attr('checked')){
                $('#wpcf-admin-preview-darkmode-checkbox').attr('checked', 'checked');
            } else {
                $('#wpcf-admin-preview-darkmode-checkbox').removeAttr('checked');
            }

            if ($('#wpcf-admin-preview-darkmode-checkbox').attr('checked')){
                $('.wpcf-admin-preview-form').css('background-color', '#333');
                $('#wpcf-admin-preview-darkmode-note').css('color', '#ddd');
                $('#wpcf-admin-preview-darkmode-label').css('color', '#fff' );
            } else {
                $('.wpcf-admin-preview-form').css('background-color', '#fff');
                $('#wpcf-admin-preview-darkmode-note').css('color', '#333');
                $('#wpcf-admin-preview-darkmode-label').css('color', '#000' );
            }
        });
    });

    jQuery("document").ready(function(){
        jQuery( "#sola_cfr_styling_tabs" ).tabs({ active: 0 });
    });

})(jQuery);