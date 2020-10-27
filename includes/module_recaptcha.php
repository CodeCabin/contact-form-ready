<?php

if (!defined('ABSPATH')) {
	exit;
}

add_action( "wpcf_user_js", "wpcf_hook_control_user_js", 10, 1);
function wpcf_hook_control_user_js() {
	wp_register_script( 'reCAPTCHA', "https://www.google.com/recaptcha/api.js?onload=wpcf_onloadCallback&render=explicit", true );
    wp_enqueue_script( 'reCAPTCHA' );
}



add_action( "wpcf_hook_settings_page_bottom_anti_spam", "wpcf_hook_control_settings_page_bottom_recpatcha" , 10, 1 );
function wpcf_hook_control_settings_page_bottom_recpatcha($wpcf_nd_settings) {
	
	//ReCaptcha
	if (!isset($wpcf_nd_settings['wpcf_nd_recaptcha_site_key']))
			$wpcf_nd_settings['wpcf_nd_recaptcha_site_key'] = '';
	if (!isset($wpcf_nd_settings['wpcf_nd_recaptcha_secret_key']))
			$wpcf_nd_settings['wpcf_nd_recaptcha_secret_key'] = '';

	//Invisible Recpatcha
	if (!isset($wpcf_nd_settings['wpcf_nd_invisible_recaptcha_site_key'])){
		$wpcf_nd_settings['wpcf_nd_invisible_recaptcha_site_key'] = '';
	}
	if (!isset($wpcf_nd_settings['wpcf_nd_invisible_recaptcha_secret_key'])){
		$wpcf_nd_settings['wpcf_nd_invisible_recaptcha_secret_key'] = '';
	}

	?>

	<h2><?php _e("Anti Spam Settings","wpcf_nd"); ?></h2>
	<table class="wp-list-table fixed" >
		<tbody>
			<tr>
				<td width='250' ><label for='wpcf_nd_enable_recaptcha'><?php _e("Enable reCAPTCHA?","wpcf_nd"); ?></label></td>
				<td><?php
	        $is_checked = (isset($wpcf_nd_settings['wpcf_nd_enable_recaptcha']) && $wpcf_nd_settings['wpcf_nd_enable_recaptcha'] == 1) ? "checked" : "";
	        ?>
            <input type='checkbox' name='wpcf_nd_enable_recaptcha' id='wpcf_nd_enable_recaptcha' value='1' <?php echo $is_checked; ?> /> <span class='description'><?php echo sprintf(__("Click <a href='%s' target='_BLANK'>here</a> to set up your reCAPTCHA profile.","wpcf_nd"),"https://www.google.com/recaptcha/intro/index.html");  ?></span></td>
			</tr>
			<tr>
				<td><label for='wpcf_nd_recaptcha_site_key'><?php _e("Site key","wpcf_nd"); ?></label></td>
				<td><input type='text' name='wpcf_nd_recaptcha_site_key' class='regular-text' id='wpcf_nd_recaptcha_site_key' value='<?php echo $wpcf_nd_settings['wpcf_nd_recaptcha_site_key']; ?>' /></td>
			</tr>
			<tr>
				<td><label for='wpcf_nd_recaptcha_secret_key'><?php _e("Secret key","wpcf_nd"); ?></label></td>
				<td><input type='text' name='wpcf_nd_recaptcha_secret_key' class='regular-text' id='wpcf_nd_recaptcha_secret_key' value='<?php echo $wpcf_nd_settings['wpcf_nd_recaptcha_secret_key']; ?>' /></td>
			</tr>
		</tbody>
		<tbody>
			<tr>
				<td width='250'><label for='wpcf_nd_enable_invisible_recaptcha'><?php _e("Enable Invisible reCAPTCHA?","wpcf_nd"); ?></label></td>
				<td><?php
	        $is_checked = (isset($wpcf_nd_settings['wpcf_nd_enable_invisible_recaptcha']) && $wpcf_nd_settings['wpcf_nd_enable_invisible_recaptcha'] == 1) ? "checked" : "";
	        ?>
            <input type='checkbox' name='wpcf_nd_enable_invisible_recaptcha' id='wpcf_nd_enable_invisible_recaptcha' value='1' <?php echo $is_checked; ?> /> <span class='description'><?php echo sprintf(__("Click <a href='%s' target='_BLANK'>here</a> to set up your reCAPTCHA profile.","wpcf_nd"),"https://www.google.com/recaptcha/intro/index.html");  ?></span></td>
			</tr>
			<tr>
				<td><label for='wpcf_nd_invisible_recaptcha_site_key'><?php _e("Site key","wpcf_nd"); ?></label></td>
				<td><input type='text' name='wpcf_nd_invisible_recaptcha_site_key' class='regular-text' id='wpcf_nd_invisible_recaptcha_site_key' value='<?php echo $wpcf_nd_settings['wpcf_nd_invisible_recaptcha_site_key']; ?>' /></td>
			</tr>
			<tr>
				<td><label for='wpcf_nd_invisible_recaptcha_secret_key'><?php _e("Secret key","wpcf_nd"); ?></label></td>
				<td><input type='text' name='wpcf_nd_invisible_recaptcha_secret_key' class='regular-text' id='wpcf_nd_invisible_recaptcha_secret_key' value='<?php echo $wpcf_nd_settings['wpcf_nd_invisible_recaptcha_secret_key']; ?>' /></td>
			</tr>
		</tbody>			
	</table>
    <div class="wpcf-form-section">
        <div class="wpcf-form-section__row">
            
	        
        </div>
        <div class="wpcf-form-section__row">
            
            
        </div>
        <div class="wpcf-form-section__row">
            
            
        </div>
    </div>
	<?php
}

add_filter( "wpcf_filter_save_settings", "wpcf_filter_control_save_settings_recaptcha", 10, 2);
function wpcf_filter_control_save_settings_recaptcha($wpcf_nd_settings, $post_data) {
	if (isset($post_data['wpcf_nd_enable_invisible_recaptcha'])) {
		$wpcf_nd_settings['wpcf_nd_enable_invisible_recaptcha'] = sanitize_text_field( $post_data['wpcf_nd_enable_invisible_recaptcha'] );
	} else {
		$wpcf_nd_settings['wpcf_nd_enable_invisible_recaptcha'] = 0;
	}

	if (isset($post_data['wpcf_nd_enable_recaptcha'])) {
		$wpcf_nd_settings['wpcf_nd_enable_recaptcha'] = sanitize_text_field( $post_data['wpcf_nd_enable_recaptcha'] );
		$wpcf_nd_settings['wpcf_nd_enable_invisible_recaptcha'] = 0;
	} else {
		$wpcf_nd_settings['wpcf_nd_enable_recaptcha'] = 0;
	}

	//Check if the user have activated both reCaptcha options
	if (isset($post_data['wpcf_nd_enable_recaptcha']) && isset($post_data['wpcf_nd_enable_invisible_recaptcha'])) {
		?>
		<!-- Disable invisible recaptcha and display warning -->
		<div class="update-nag notice notice-error is-dismissible" style="border-color: #dd0000; max-width: 600px;margin-left: 0;">
		<p><strong><?php _e( 'Warning - You have enabled reCaptcha and Invisible reCaptcha', 'wpcf_nd' ); ?></strong></p>
		<p><?php _e( 'We have now disabled Invinsible reCaptcha.', 'wpcf_nd' ); ?></p>
	</div>
	<?php
}


	if (isset($post_data['wpcf_nd_invisible_recaptcha_site_key'])) 
		$wpcf_nd_settings['wpcf_nd_invisible_recaptcha_site_key'] = sanitize_text_field( $post_data['wpcf_nd_invisible_recaptcha_site_key'] );

	if (isset($post_data['wpcf_nd_recaptcha_site_key'])) 
		$wpcf_nd_settings['wpcf_nd_recaptcha_site_key'] = sanitize_text_field( $post_data['wpcf_nd_recaptcha_site_key'] );

	if (isset($post_data['wpcf_nd_invisible_recaptcha_secret_key'])) 
		$wpcf_nd_settings['wpcf_nd_invisible_recaptcha_secret_key'] = sanitize_text_field( $post_data['wpcf_nd_invisible_recaptcha_secret_key'] );

	if (isset($post_data['wpcf_nd_recaptcha_secret_key'])) 
		$wpcf_nd_settings['wpcf_nd_recaptcha_secret_key'] = sanitize_text_field( $post_data['wpcf_nd_recaptcha_secret_key'] );

	return $wpcf_nd_settings;
}

/*
* DEPRECATED
 */

//add_filter( "wpcf_filter_other_form_data_frontend", "wpcf_filter_control_other_form_data_frontend_recaptcha", 10, 1);
function wpcf_filter_control_other_form_data_frontend_recaptcha( $html_data ) {
	$wpcf_nd_settings = get_option( "wpcf_nd_settings" );
	if (isset($wpcf_nd_settings['wpcf_nd_enable_recaptcha']) && $wpcf_nd_settings['wpcf_nd_enable_recaptcha'] == '1' && isset($wpcf_nd_settings['wpcf_nd_recaptcha_site_key'])) {

		require_once(plugin_dir_path(dirname(__FILE__)).'assets/recaptcha/recaptchalib.php');
	    return recaptcha_get_html($wpcf_nd_settings['wpcf_nd_recaptcha_site_key']);
	}
	return "";
}


add_filter( "wpcf_filter_submit_button_initial_status", "wpcf_filter_control_recapatcha_submit_button_initial_status", 10, 1 );
function wpcf_filter_control_recapatcha_submit_button_initial_status( $initial_status ) {
	$wpcf_nd_settings = get_option( "wpcf_nd_settings" );
	if (isset($wpcf_nd_settings['wpcf_nd_enable_recaptcha']) && $wpcf_nd_settings['wpcf_nd_enable_recaptcha'] == '1' && isset($wpcf_nd_settings['wpcf_nd_recaptcha_site_key'])) {
		/* return "disabled"; */
		
		/**
		 * decided against disabling the submit button in case there are JS errors and validation doesnt occur correctly. Lets rather handle this via PHP instead
		 */

	}
	return "";

}

add_filter( "wpcf_filter_submit_button_initial_status", "wpcf_filter_control_invisible_recapatcha_submit_button_initial_status", 10, 1 );
function wpcf_filter_control_invisible_recapatcha_submit_button_initial_status( $initial_status ) {
	$wpcf_nd_settings = get_option( "wpcf_nd_settings" );
	if (isset($wpcf_nd_settings['wpcf_nd_enable_invisible_recaptcha']) && $wpcf_nd_settings['wpcf_nd_enable_invisible_recaptcha'] == '1' && isset($wpcf_nd_settings['wpcf_nd_invisible_recaptcha_site_key'])) {
		/* return "disabled"; */
		
		/**
		 * decided against disabling the submit button in case there are JS errors and validation doesnt occur correctly. Lets rather handle this via PHP instead
		 */

	}
	return "";

}


add_filter( "wpcf_filter_other_form_data_frontend", "wpcf_filter_control_other_form_data_frontend_recaptcha_v2", 10, 3);
function wpcf_filter_control_other_form_data_frontend_recaptcha_v2( $html_data, $cfid, $form_data ) {
	$wpcf_nd_settings = get_option( "wpcf_nd_settings" );
    $captch = "";
	if (isset($wpcf_nd_settings['wpcf_nd_enable_recaptcha']) && $wpcf_nd_settings['wpcf_nd_enable_recaptcha'] == '1' && isset($wpcf_nd_settings['wpcf_nd_recaptcha_site_key'])) {

        $captch =  '<div class="g-recaptcha wpcf_g_recaptcha wpcf_g_recaptcha_'.$cfid.'" id="wpcf_g_recaptcha_'.$cfid.'"></div>';
	}
    return $html_data.$captch;
}

add_filter( "wpcf_filter_other_form_data_frontend", "wpcf_filter_control_other_form_data_frontend_invisible_recaptcha_v2", 10, 3);
function wpcf_filter_control_other_form_data_frontend_invisible_recaptcha_v2( $html_data, $cfid, $form_data ) {
	$wpcf_nd_settings = get_option( "wpcf_nd_settings" );
    $captch = "";
	if (isset($wpcf_nd_settings['wpcf_nd_enable_invisible_recaptcha']) && $wpcf_nd_settings['wpcf_nd_enable_invisible_recaptcha'] == '1' && isset($wpcf_nd_settings['wpcf_nd_invisible_recaptcha_site_key'])) {

        $captch =  '<div class="g-recaptcha wpcf_g_recaptcha wpcf_g_recaptcha_'.$cfid.'" data-size="invisible" id="wpcf_g_recaptcha_'.$cfid.'"></div>';
	}
    return $html_data.$captch;
}

add_action( "wpcf_user_js", "wpcf_filter_control_js_overrides_front_end", 10, 1);
function wpcf_filter_control_js_overrides_front_end( $js_overrides ) {
	$wpcf_nd_settings = get_option( "wpcf_nd_settings" );
	if (isset($wpcf_nd_settings['wpcf_nd_enable_recaptcha']) && $wpcf_nd_settings['wpcf_nd_enable_recaptcha'] == '1' && isset($wpcf_nd_settings['wpcf_nd_recaptcha_site_key'])) {
		wp_localize_script( 'contact-form-ready', 'wpcf_recaptcha_enabled', '1' );
		wp_localize_script( 'contact-form-ready', 'wpcf_recaptcha_api', $wpcf_nd_settings['wpcf_nd_recaptcha_site_key'] );
	}
	return;
}

add_action( "wpcf_user_js", "wpcf_filter_control_js_invisible_overrides_front_end", 10, 1);
function wpcf_filter_control_js_invisible_overrides_front_end( $js_overrides ) {
	$wpcf_nd_settings = get_option( "wpcf_nd_settings" );
	if (isset($wpcf_nd_settings['wpcf_nd_enable_invisible_recaptcha']) && $wpcf_nd_settings['wpcf_nd_enable_invisible_recaptcha'] == '1' && isset($wpcf_nd_settings['wpcf_nd_invisible_recaptcha_site_key'])) {
		
		wp_localize_script( 'contact-form-ready', 'invisible_recaptcha_enabled', '1' );
		wp_localize_script( 'contact-form-ready', 'wpcf_invisible_recaptcha_api', $wpcf_nd_settings['wpcf_nd_invisible_recaptcha_site_key'] );
	}
	return;
}






/*
DEPRECATED
 */
//add_filter( "wpcf_filter_continue_form_post_handling", "wpcf_filter_continue_form_post_handling", 10, 2);
function wpcf_filter_continue_form_post_handling( $continue, $post_data ) {
	$wpcf_nd_settings = get_option( "wpcf_nd_settings" );
	if (isset($wpcf_nd_settings['wpcf_nd_enable_recaptcha']) && $wpcf_nd_settings['wpcf_nd_enable_recaptcha'] == '1' && isset($wpcf_nd_settings['wpcf_nd_recaptcha_secret_key'])) {
		require_once(plugin_dir_path(dirname(__FILE__)).'assets/recaptcha/recaptchalib.php');
		$privatekey = "your_private_key";
		$resp = recaptcha_check_answer ($wpcf_nd_settings['wpcf_nd_recaptcha_secret_key'],
		                        $_SERVER["REMOTE_ADDR"],
		                        $post_data["recaptcha_challenge_field"],
		                        $post_data["recaptcha_response_field"]);



		if (!$resp->is_valid) {
			return __( "The reCAPTCHA wasn't entered correctly. Please try sagain.", "wpcf_nd" ). " Error: ".$resp->error;
		} else {
		 	return true;
		}
	}
	return true;
  }


add_filter( "wpcf_filter_continue_form_post_handling", "wpcf_filter_continue_form_post_handling_v2", 10, 2);
function wpcf_filter_continue_form_post_handling_v2( $continue, $post_data ) {
	$wpcf_nd_settings = get_option( "wpcf_nd_settings" );
	if (isset($wpcf_nd_settings['wpcf_nd_enable_recaptcha']) && $wpcf_nd_settings['wpcf_nd_enable_recaptcha'] == '1' && isset($wpcf_nd_settings['wpcf_nd_recaptcha_secret_key'])) {

		$response = isset( $_POST['g-recaptcha-response'] ) ? esc_attr( $_POST['g-recaptcha-response'] ) : '';

		$remote_ip = $_SERVER["REMOTE_ADDR"];

		$request = wp_remote_get(
			'https://www.google.com/recaptcha/api/siteverify?secret=' . $wpcf_nd_settings['wpcf_nd_recaptcha_secret_key'] . '&response=' . $response . '&remoteip=' . $remote_ip
		);

		$response_body = wp_remote_retrieve_body( $request );

		$result = json_decode( $response_body, true );

		if ($result['success'] == false) {
			return __("reCAPTCHA failed. Please tick the reCAPTCHA checkbox to prove you are human.", "wpcf_nd" );
		}

		return $result['success'];


		
	}
	return true;
  }



  add_filter( "wpcf_fiter_exclude_certain_fields", "wpcf_fiter_control_exclude_certain_fields_recpatcha", 10, 3);
  function wpcf_fiter_control_exclude_certain_fields_recpatcha( $continue, $key, $val ) {
  	if ( $key == "recaptcha_challenge_field" ) {
  		return false;
  	}
  	if ( $key == 'recaptcha_response_field' ) {
  		return false;
  	}
  	

  	return true;
  	
  	
  }