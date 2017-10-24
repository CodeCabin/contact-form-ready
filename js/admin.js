var predefined_busy = false;

(function ($) {

    $(document).ready(function () {

        document.addEventListener('formSaved', function () {
            if (!predefined_busy) {
                console.log(formBuilder);
                // TODO event handler logic
                var formData = formBuilder.formData;
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
                    render: false,
                    formData: formData
                };

                // Grab markup and escape it
                var markup = escapeHTML(new FormRenderFn(formRenderOpts).markup);


                var decoded = $("<div/>").html(addLineBreaks(markup)).text();


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
                        render: false,
                        formData: tformData
                    };

                    // Grab markup and escape it
                    var markup = escapeHTML(new FormRenderFn(formRenderOpts).markup);


                    var decoded = $("<div/>").html(addLineBreaks(markup)).text();


                    $("#fb-temp-formdata").val(tformData);
                    $("#fb-temp-htmldata").val(decoded);


                    fbOptions = {
                        controlPosition: 'left',
                        disableFields: ['autocomplete', 'button', 'file', 'access'],
                        editOnAdd: true,
                        formData: tformData
                    };


                    $('.build-wrap').html('');
                    formBuilder = $('.build-wrap').formBuilder(fbOptions).data('formBuilder');

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


    });
})(jQuery);