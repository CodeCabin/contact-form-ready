jQuery(function(){

    jQuery(document).ready(function(){
        jQuery( "#sola_cfr_tabs" ).tabs({ active: 0 });
    });

    jQuery('#GDPR_privacy_settings_button').on('click', function(){
        jQuery('.wpcf_gdpr_notice').hide();
        jQuery('.ui-tabs-nav #ui-id-4').trigger('click');
    });

    jQuery("body").on("click", '.ui-tabs-nav li', function(){
        var tabId = jQuery(this).attr('aria-controls');
        tabId = tabId.replace('tabs-', '');
        tabId = parseInt(tabId);
        switch(tabId){
            case 4:
                jQuery('.wpcf_gdpr_notice').hide();
                break;
            default: 
                jQuery('.wpcf_gdpr_notice').show();
                break;
        } 
    });
    
});
