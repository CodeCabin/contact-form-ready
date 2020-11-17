jQuery("document").ready(function(){
    jQuery( "#sola_cfr_tabs" ).tabs({ active: 0 });
});

// Matthew Code - Start //

function wpcf_OpenPrivacyTab(){
    /*
    var orig_id;
    for (orig_id = 1; orig_id < 5; orig_id++) {
        if(orig_id == 4){
            jQuery("[aria-labelledby=\"ui-id-"+ orig_id + "\"]").attr('tabindex', 0);

            jQuery("[aria-labelledby=\"ui-id-"+ orig_id + "\"]").addClass("ui-tabs-active ui-state-active ui-state-focus");
            jQuery("[aria-labelledby=\"ui-id-"+ orig_id + "\"]").attr("aria-selected", true);
            jQuery("[aria-labelledby=\"ui-id-"+ orig_id + "\"]").attr("aria-expanded", true);
            //jQuery("[aria-labelledby=\"ui-id-"+ orig_id + "\"]").attr("onblur","wpcf_ClosePrivacyTab()");
            jQuery("[id=\"tabs-" +  orig_id + "\"]").attr('aria-hidden', false);
            jQuery("[id=\"tabs-" +  orig_id + "\"]").css('display', 'block');

        } else{
            jQuery("[aria-labelledby=\"ui-id-"+ orig_id + "\"]").attr('tabindex', -1);

            jQuery("[aria-labelledby=\"ui-id-"+ orig_id + "\"]").removeClass("ui-tabs-active ui-state-active");
            jQuery("[aria-labelledby=\"ui-id-"+ orig_id + "\"]").attr("aria-selected", false);
            jQuery("[aria-labelledby=\"ui-id-"+ orig_id + "\"]").attr("aria-expanded", false);
            jQuery("[id=\"tabs-" +  orig_id + "\"]").attr('aria-hidden', true);
            jQuery("[id=\"tabs-" +  orig_id + "\"]").css('display', 'none');
        }
    } 
    */

    jQuery("[aria-labelledby]").on("click", function(){
        var id = jQuery(this).attr('aria-labelledby');
        id = id.replace('ui-id-', '');
        if(parseInt(id) === 4){
            jQuery("[aria-labelledby=\"ui-id-"+ orig_id + "\"]").attr('tabindex', 0);

            jQuery("[aria-labelledby=\"ui-id-"+ orig_id + "\"]").addClass("ui-tabs-active ui-state-active ui-state-focus");
            jQuery("[aria-labelledby=\"ui-id-"+ orig_id + "\"]").attr("aria-selected", true);
            jQuery("[aria-labelledby=\"ui-id-"+ orig_id + "\"]").attr("aria-expanded", true);
            //jQuery("[aria-labelledby=\"ui-id-"+ orig_id + "\"]").attr("onblur","wpcf_ClosePrivacyTab()");
            jQuery("[id=\"tabs-" +  orig_id + "\"]").attr('aria-hidden', false);
            jQuery("[id=\"tabs-" +  orig_id + "\"]").css('display', 'block');
        } else {
            jQuery("[aria-labelledby=\"ui-id-"+ orig_id + "\"]").attr('tabindex', -1);

            jQuery("[aria-labelledby=\"ui-id-"+ orig_id + "\"]").removeClass("ui-tabs-active ui-state-active");
            jQuery("[aria-labelledby=\"ui-id-"+ orig_id + "\"]").attr("aria-selected", false);
            jQuery("[aria-labelledby=\"ui-id-"+ orig_id + "\"]").attr("aria-expanded", false);
            jQuery("[id=\"tabs-" +  orig_id + "\"]").attr('aria-hidden', true);
            jQuery("[id=\"tabs-" +  orig_id + "\"]").css('display', 'none');
        }
    });
};

function wpcf_ClosePrivacyTab(){
    jQuery("[aria-labelledby=\"ui-id-4\"]").attr('tabindex', -1);

    jQuery("[aria-labelledby=\"ui-id-4\"]").removeClass("ui-tabs-active ui-state-active ui-state-focus");
    jQuery("[aria-labelledby=\"ui-id-4\"]").attr("aria-selected", false);
    jQuery("[aria-labelledby=\"ui-id-4\"]").attr("aria-expanded", false);
    jQuery("[id=\"tabs-4\"]").attr('aria-hidden', true);
    jQuery("[id=\"tabs-4\"]").css('display', 'none');
}

// Matthew Code - End //