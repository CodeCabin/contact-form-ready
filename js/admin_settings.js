jQuery(document).ready(function() {

    var wpcfCodeMirror = CodeMirror.fromTextArea(document.getElementById('wpcf_nd_template_html'), {
        matchBrackets: true,
        lineNumbers: true,
        styleActiveLine: true,
        autoCloseTags: true,
        mode: "xml",
        htmlMode: true
    });
    wpcfCodeMirror.setOption("theme", "monokai");
    setTimeout(function () {
        wpcfCodeMirror.refresh();
    }, 1);

    jQuery("body").on("click", "#wpcf_restore_default_template", function() {
        var proceed = confirm(wpcf_nd_confirm_restore_template_string);
        if(proceed) {
            var def_template = jQuery("#wpcf_nd_template_html_default").val();

            jQuery('#wpcf_nd_template_html').html(def_template);
            wpcfCodeMirror.getDoc().setValue(def_template);
        }
    });
    
});
