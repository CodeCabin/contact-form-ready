jQuery(document).ready(function(){

    jQuery("body").on("click", "#cfr_signup_newsletter_btn", function() {

        var a_email = jQuery("#cfr_signup_newsletter").val();
        jQuery("#cfr_signup_newsletter").hide('slow');
        jQuery("#cfr_signup_newsletter_btn").hide('slow');
        jQuery("#cfr_subscribe_div").html("Thank you!");
        var data = {
            action: 'cfr_subscribe',
            prod: 'cfr',
            a_email: a_email
            
        };
        jQuery.post('//ccplugins.co/newsletter-subscription/index.php', data, function(response) {
            returned_data = JSON.parse(response);
            
        });

        var data = {
            action: 'cfr_subscribe',
            security: cfr_sub_nonce
            
        };
        jQuery.post(ajaxurl, data, function(response) {
            
        });


    });
});
