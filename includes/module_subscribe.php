<?php
/**
 * Plugin action links filter
 *
 * @param array   $links
 * @return array
 */
add_filter( 'network_admin_plugin_action_links_contact-form-ready/contact-form-ready.php', 'cfr_plugin_action_links' );
add_filter( 'plugin_action_links_contact-form-ready/contact-form-ready.php', 'cfr_plugin_action_links' );
function cfr_plugin_action_links( $links ) {
    
    array_unshift( $links,
        '<a class="edit" href="' . admin_url('edit.php?post_type=contact-forms-nd&page=wpcf-settings') . '">' . __( 'Settings', 'wpcf_nd' ) . '</a>' );
    array_unshift( $links,
        '<a class="" target="_BLANK" href="http://www.contactformready.com/extensions/?utm_source=plugin&utm_medium=link&utm_campaign=extensions">' . __( 'Extensions', 'wpcf_nd' ) . '</a>' );


    return $links;
}

add_action( 'wp_ajax_cfr_subscribe','cfr_ajax_subscribe');

function cfr_ajax_subscribe() {
    $check = check_ajax_referer( 'cfr_subscribe', 'security' );
    if ( $check == 1 ) {
        if ( $_POST['action'] == 'cfr_subscribe' ) {
            $uid = get_current_user_id();
            update_user_meta( $uid, 'cfr_subscribed', true);

        }
    }
}

add_action ( 'admin_head', 'cfr_plugin_row_js' );
function cfr_plugin_row_js(){
    $current_page = get_current_screen();

    if ( $current_page->base == 'plugins' ) {
        wp_register_script( 'cfr_plugin_row_js', plugins_url(plugin_basename(dirname(dirname(__FILE__)))).'/js/cfr_plugin_row.js', array( 'jquery-ui-core' ) );
        wp_enqueue_script( 'cfr_plugin_row_js' );
        wp_localize_script( 'cfr_plugin_row_js', 'cfr_sub_nonce', wp_create_nonce("cfr_subscribe") );
    }
}


/**
 * Adds the email subscription field below the plugin row on the plugins page
 * 
 */
add_filter( 'plugin_row_meta', 'cfr_plugin_row', 4, 10 );
function cfr_plugin_row( $plugin_meta, $plugin_file, $plugin_data, $status ) {

    if ( $plugin_file == "contact-form-ready/contact-form-ready.php") {
        $check = get_user_meta(get_current_user_id(),"cfr_subscribed");

        if (!$check) {
            $ret = '<div style="margin-top:10px; color:#333; display:block; white-space:normal;">';
            $ret .= '<form>';
            $ret .= '<p><label for="cfr_signup_newsletter" style="font-style:italic; margin-bottom:5px;">' . __( 'Sign up to our newsletter and get information on the latest updates, beta versions and specials.', 'wpcf_nd' ) . '</label></p>';
            $ret .= '<span id="cfr_subscribe_div">';
            $ret .= '<input type="text" name="cfr_signup_newsletter" id="cfr_signup_newsletter" value="'.get_option( 'admin_email' ).'"></option>';
            $ret .= '<input type="button" class="button button-primary"  id="cfr_signup_newsletter_btn" name="cfr_signup_newsletter_btn" value="' . __( 'Sign up', 'wpcf_nd' ) . '" />';
            $ret .= '<span>';
            $ret .= '</form>';
            $ret .= '</div>';
            array_push( $plugin_meta, $ret );
        }
    }
    return $plugin_meta;
}