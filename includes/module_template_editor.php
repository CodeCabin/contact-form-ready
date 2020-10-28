<?php

if (!defined('ABSPATH')) {
	exit;
}

add_action( "wpcf_hook_settings_page_bottom_temp", "wpcf_hook_control_settings_page_bottom_template" , 10, 1 );
function wpcf_hook_control_settings_page_bottom_template($wpcf_nd_settings) {
	

	$wpcf_default_template = wpcf_get_default_template_html();

	if (!isset($wpcf_nd_settings['wpcf_nd_template_html']))
			$wpcf_nd_settings['wpcf_nd_template_html'] = $wpcf_default_template;

	?>

	<h2><?php _e("Email Template","wpcf_nd"); ?></h2>
	<table class='wp-list-table fixed'>
		<tr>
			<td width='100%'>
                <label for="wpcf_nd_template_html" class="wpcf-hidden" style="position: absolute !important; height: 1px; width: 1px; overflow: hidden; clip: rect(1px 1px 1px 1px); clip: rect(1px, 1px, 1px, 1px);"><?php _e( 'Email Template', 'wpcf_nd' ); ?></label>
				<textarea name='wpcf_nd_template_html' style='width:100%; height:300px;' data-editor='html' rows='12' id='wpcf_nd_template_html'><?php echo htmlentities( stripslashes( $wpcf_nd_settings['wpcf_nd_template_html'] ) ); ?></textarea>
                <label for="wpcf_nd_template_html_default" style="display: none;"><?php _e( 'Email Default Template', 'wpcf_nd' ); ?></label>
                <textarea name='wpcf_nd_template_html_default' style='display:none;' id='wpcf_nd_template_html_default'><?php echo htmlentities( stripslashes( $wpcf_default_template ) ); ?></textarea>
				
				<input type='button' id='wpcf_restore_default_template' class='button button-secondary right' value='<?php echo __( "Restore default", "wpcf_nd" ); ?>'/>
			</td>
		</tr>

	</table>

	<?php
}

add_filter( "wpcf_filter_save_settings", "wpcf_filter_control_save_settings_template", 10, 2);
function wpcf_filter_control_save_settings_template($wpcf_nd_settings, $post_data) {
	/*
	$tags = wp_kses_allowed_html("post");
    $tags['html'] = array();
    $tags['body'] = array(
    	"class" => array(),
    	"id" => array()
	);

	*/

	if (isset($post_data['wpcf_nd_template_html'])) {
		//$wpcf_nd_settings['wpcf_nd_template_html'] = wp_kses( $post_data['wpcf_nd_template_html'], $tags );
		$wpcf_nd_settings['wpcf_nd_template_html'] = $post_data['wpcf_nd_template_html'];
	}

	/* prepend the HTML DOCTYPE line to the top as this is stripped out by wp_kses */
	//$wpcf_nd_settings['wpcf_nd_template_html'] = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional //EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">'.$wpcf_nd_settings['wpcf_nd_template_html'];


	return $wpcf_nd_settings;
}



function wpcf_get_default_template_html() {
	$dir = dirname( dirname( __FILE__ ) );
	$template_content_template = file_get_contents($dir."/templates/mail_template.html");
	return $template_content_template;

}



add_action( "admin_enqueue_scripts", "wpcf_custom_scripts_scripts" );
/**
 * Loads the Ace.js editor for the custom scripts
 * @return void
 */
function wpcf_custom_scripts_scripts(){

	if( isset( $_GET['page'] ) && $_GET['page'] == 'wpcf-settings' ){

		wp_enqueue_script( "wpcf-custom-script-tab-ace", "https://cdn.jsdelivr.net/ace/1.2.4/min/ace.js" );
		
	}

}