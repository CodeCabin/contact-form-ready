var predefined_busy = false;

(function ($) {

    $(document).ready(function () {

        document.addEventListener('formSaved', function () {
            if (!predefined_busy && formBuilder.actions.getData) {
                // TODO event handler logic
                var formData = formBuilder.actions.getData('json');
                var escapeEl = document.createElement('textarea'),
                    code = document.getElementById('markup'),
                    escapeHTML = function (html) {
                        escapeEl.textContent = html;
                        return escapeEl.innerHTML;
                    },
                    addLineBreaks = function (html) {
                        return html.replace(new RegExp('&gt; &lt;', 'g'), '&gt;\n&lt;').replace(new RegExp('&gt;&lt;', 'g'), '&gt;\n&lt;');
                    };

                // Grab markup and escape it
                var markup = jQuery("<div/>");
                // markup.formRender({formData});
                markup.formRender({
                    dataType: 'xml',
                    formData:  formBuilder.actions.getData('xml')
                });

                var decoded = markup.html();

                $("#fb-temp-formdata").val(formData);
                $("#fb-temp-htmldata").val(decoded);
            }
        });

        jQuery('body').on('click keyup cfr_field_update_event', '.build-wrap', function(e){
            if (!predefined_busy && formBuilder.actions.getData) {
                // TODO event handler logic
                var formData = formBuilder.actions.getData('json');
                var escapeEl = document.createElement('textarea'),
                    code = document.getElementById('markup'),
                    escapeHTML = function (html) {
                        escapeEl.textContent = html;
                        return escapeEl.innerHTML;
                    },
                    addLineBreaks = function (html) {
                        return html.replace(new RegExp('&gt; &lt;', 'g'), '&gt;\n&lt;').replace(new RegExp('&gt;&lt;', 'g'), '&gt;\n&lt;');
                    };

                // Grab markup and escape it
                var markup = jQuery("<div/>");

                if(formData.indexOf('{}') !== -1){
                    return;
                }

                markup.formRender({formData});

                var decoded = markup.html();

                $("#fb-temp-formdata").val(formData);
                $("#fb-temp-htmldata").val(decoded);
            }
        });

        var can_continue = false;


        $("body").on("change", "#wpcf_nd_predfined", function (e) {
            var ctype = $(this).val();

            if (ctype !== 'x') {
                if (typeof wpcf_nd_types[ctype].xml_data !== 'undefined') {
                    predefined_busy = true;


                    var tformData = wpcf_nd_types[ctype].xml_data;

                    var escapeEl = document.createElement('textarea'),
                        code = document.getElementById('markup'),
                        escapeHTML = function (html) {
                            escapeEl.textContent = html;
                            return escapeEl.innerHTML;
                        },
                        addLineBreaks = function (html) {
                            return html.replace(new RegExp('&gt; &lt;', 'g'), '&gt;\n&lt;').replace(new RegExp('&gt;&lt;', 'g'), '&gt;\n&lt;');
                        };

                    var formRenderOpts = {
                        formData: tformData,
                        dataType: 'xml'
                    };

                    // Grab markup and escape it
                    var markup = $("<div/>");
                    markup.formRender(formRenderOpts);


                    var decoded = markup.html();


                    $("#fb-temp-formdata").val(tformData);
                    $("#fb-temp-htmldata").val(decoded);


                    fbOptions = {
                        controlPosition: 'left',
                        disableFields: ['autocomplete', 'button', 'access'],
                        editOnAdd: true,
                        formData: tformData,
                        dataType: 'xml',
                        disableHTMLLabels: true,
                        onAddField: function(formData) {
                            jQuery('body').trigger('cfr_field_update_event');
                        },
                        onOpenFieldEdit: function(formData) {
                            jQuery('body').trigger('cfr_field_update_event');
                        },
                        onCloseFieldEdit: function(formData) {
                            jQuery('body').trigger('cfr_field_update_event');
                        },
                    };


                    $('.build-wrap').html('');
                    formBuilder = $('.build-wrap').formBuilder(fbOptions);

                    predefined_busy = false;


                }

            }

        });

        $(function () {
            $("#contact_form_ready_tabs").tabs();
        });

        /* Copy to clipboard */
        var copyToClipboardText = $('.wpcf-shortcode-copy-text');

        $('#wpcf-shortcode-input').on('click', function (event) {
            copyToClipboard($(this).attr('id'));
        });

        function copyToClipboard(id) {
            copyToClipboardText.addClass('is-active');
            var input = document.createElement("input");
            input.setAttribute("value", document.getElementById(id).value);
            document.body.appendChild(input);
            input.select();
            document.execCommand("copy");
            document.body.removeChild(input);
            setTimeout(function () {
                copyToClipboardText.removeClass('is-active');
            }, 800);
        }

        /* Modal window */
        var modalEl = $('#wpcf_nd_modal_el');

        function hideModalCustomization() {
            if (0 === modalEl.val().length) {
                $('.wpcf-modal-customization').removeClass('is-active');
            } else {
                $('.wpcf-modal-customization').addClass('is-active');
            }
        }

        if (modalEl.length) {
            hideModalCustomization();
        }

        modalEl.on('keyup', hideModalCustomization);

        /* Color picker inputs */
       /* Iris replaced with standard color picker*/

    });
})(jQuery);