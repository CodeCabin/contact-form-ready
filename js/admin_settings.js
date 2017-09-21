jQuery(document).ready(function() {


    jQuery("body").on("click", "#wpcf_restore_default_template", function() {
        var proceed = confirm(wpcf_nd_confirm_restore_template_string);
        if(proceed) {


            var def_template = jQuery("#wpcf_nd_template_html_default").val();
            var editor = ace.edit('wpcf_nd_template_html'); 
            editor.getSession().setValue(def_template);


        }
    });


    if( typeof ace !== 'undefined' ) {
            jQuery('textarea[data-editor]').each(function() {
                var textarea = jQuery(this);

                if (textarea.attr('id') === 'wpcf_nd_template_html') {

                    var mode = textarea.data('editor');                
                    var editDiv = jQuery('<div>', {
                        position: 'absolute',
                        width: '100%',
                        height: '250px',
                        'class': textarea.attr('class'),
                        'id' : textarea.attr('id')

                    }).insertBefore(textarea);
                    textarea.css('display', 'none');
                    var editor = ace.edit(editDiv[0]);            
                    editor.getSession().setValue(textarea.val());
                    editor.getSession().setMode("ace/mode/" + mode);
                    editor.setTheme("ace/theme/twilight");
                    textarea.closest('form').submit(function() {
                        textarea.val(editor.getSession().getValue());
                    })
                }

            });
    }

    
});
