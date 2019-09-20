<?php

if (!defined('ABSPATH')) {
	exit;
}

add_action( "wplc_hook_offline_custom_fields_integration_settings", "cfnd_hook_control_offline_custom_fields_integration_settings", 2 );


function cfnd_hook_control_offline_custom_fields_integration_settings() {
	remove_action( 'wplc_hook_offline_custom_fields_integration_settings' , 'wplc_hook_control_offline_custom_fields_integration_settings', 10 );
	$wpcf_integrations = get_option("wpcf_integrations");
	/*
		Get a list of all the contact forms	
	 */

	?>
          	<?php
	           
			    query_posts(array( 
				        'post_type' => 'contact-forms-nd',
				        'post_status' => 'publish',
				        'posts_per_page' => -1 
				    ) ); 
				if (have_posts()) { 
					echo "<select id='wpcf_wplc_integration' name='wpcf_wplc_integration'>";
					echo "<option value='0'>" . __( "Please select", "wpcf_nd" ) . "</option>";
					while (have_posts()) : the_post();
						$the_title = get_the_title();
						if ($the_title == "") { $the_title = __("Unnamed contact form", "wpcf_nd"); }
						$selected = '';
						if ( isset($wpcf_integrations['wpcf_wplc_integration']) && intval($wpcf_integrations['wpcf_wplc_integration']) == get_the_ID() ) { $selected = 'selected="selected"'; }
					    echo '<option value="'.get_the_ID().'" '.$selected.'>'.$the_title.' (#'.get_the_ID().')</option>';
					endwhile;

					echo "</select>";
					echo '<p class="description">'.sprintf( __("Replaces the normal offline message form with the above contact form created by <a href='%s' target='_BLANK'>Contact Form Ready</a>.","wpcf_nd"), 'https://wordpress.org/plugins/contact-form-ready/' ).'</p>';
      		
				} else {
					echo '<p class="description">'.sprintf( __("There are no contact forms to use. Please <a href='%s'>create one first</a>.","wpcf_nd"),admin_url('post-new.php?post_type=contact-forms-nd' ) ).'</p>';
				}

				?>
  		  


	<?php
}


add_action("wplc_hook_admin_settings_save", "cfnd_integration_settings_save_hooked", 10, 1);
/**
 * Save 'Contact From Ready Integration' settings
 * @return void
 */
function cfnd_integration_settings_save_hooked(){
  $wpcf_integrations = get_option("wpcf_integrations");
  if (!$wpcf_integrations) {
  	$wpcf_integrations = array();
  }
  if (isset($_POST['wpcf_wplc_integration'])) { $wpcf_integrations['wpcf_wplc_integration'] = esc_attr($_POST['wpcf_wplc_integration']); }
  update_option( "wpcf_integrations", $wpcf_integrations );
  
}


add_filter( "wplc_filter_2nd_layer_modify", "wpcfnd_filter_2nd_layer_modify" , 10, 1);
/**
 * Modifies the output of the offline messag form
 *
 * This is where we replace it with our contact form
 * 
 * @param  array 	$data 	Data array used in the filter
 * @return 
 */
function wpcfnd_filter_2nd_layer_modify($data) {
	if (isset($data['logged_in'])) {
		if ($data['logged_in']) {
			/**
			 * Do nothing as the user is logged in and the offline message form shouldnt be displayed
			 */
			return $data['ret_msg'];
		} else {
			/**
			 * User is offline, lets modify the offline message form.
			 */
			$wpcf_integrations = get_option("wpcf_integrations");
			if ( isset($wpcf_integrations['wpcf_wplc_integration']) && $wpcf_integrations['wpcf_wplc_integration'] !== '0') {
				$cform_id = esc_attr($wpcf_integrations['wpcf_wplc_integration']);
				$checker = do_shortcode("[cform-nd id='".$cform_id."']");
				return $checker;
			} else {
				/**
				 * No contact form selected, display normal offline message form from WPLCS
				 */
				return $data['ret_msg'];
			}
			
		}
	} else {
		/** do nothing */
	}
	return $data['ret_msg'];

}

add_action( 'wplc_hook_push_js_to_front', 'wpcfnd_wplc_hook_push_js_to_front', 10 );
function wpcfnd_wplc_hook_push_js_to_front() {
	$wpcf_integrations = get_option("wpcf_integrations");
	if ( isset($wpcf_integrations['wpcf_wplc_integration']) && $wpcf_integrations['wpcf_wplc_integration'] !== '0') {
		global $contact_form_nd;
		if (isset($contact_form_nd)) {
			$contact_form_nd::enqueue_user_styles();
		}
	}
}

add_filter( "wpcf_send_integrations", "wpcf_hook_control_send_integrations_wplc", 10, 1 );
function wpcf_hook_control_send_integrations_wplc( $data ) {
	$wpcf_integrations = get_option("wpcf_integrations");
	if ( isset($wpcf_integrations['wpcf_wplc_integration']) && $wpcf_integrations['wpcf_wplc_integration'] !== '0') {
		if ($data['cfid'] == intval($wpcf_integrations['wpcf_wplc_integration']) ) {
			/** this is the contact form that WPLCS is using */
			if (isset($data['data']['user_email'])) { $email = $data['data']['user_email']; } else { $email = "unknown"; }
			wplc_store_offline_message('Sent by Contact Form Ready', $email, $data['body'] );
		}
	}
	
}