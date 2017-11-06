<?php
/*
  Plugin Name: Contact Form Ready
  Plugin URI: http://contactformready.com
  Description: The easiest to use Contact Form plugin for WordPress with a drag and drop interface.
  Version: 1.13
  Author: NickDuncan
  Author URI: http://nickduncan.co.za
 */


/**
 * 1.13 - 2017-09-21
 * Tested on WordPress 4.8.2
 * Fixed small typo
 *
 * 1.12 - 2017-01-26 - Medium priority
 * Added an option to set the "reply to" address as the user's email address
 * Fixed a bug that caused some settings to not be carried over correctly
 * Fixed a bug that caused the mailchimp and the recaptcha modules to conflict with one another
 * Fixed a bug that caused the AJAX method to not work if there were multiple contact forms with required fields on one page
 * Added the ability for you to edit the newsletter that gets sent out when a contact form is submitted
 *
 * 1.11 - 2017-01-20 - Medium priority
 * More extensions added
 * Bug Fix: Form validation now works as expected
 * Bug Fix: Added a class to the thank you message when sending the form via Ajax
 * Bug Fix: Fixed a bug that caused the 'Send Confirmation Email' to never be disabled
 * Bug Fix: Fixed a bug that prevented punctuation and placeholders from being kept in the form
 * Enhancement: You can now specify a subject line and message for each form instead of globally
 * Bug Fix: Helper text now works as expected
 * Enhancement: New hooks added to the contact form editor
 * Enhancement: Contact form settings are now in tabs for ease of use
 *
 * 1.10 - 2017-01-10
 * Added an "email" field to the drag and drop contact form builder
 * Fixed the bugs with single quotes in email content showing backslashes
 * Updated the code to suit the new reCaptcha (I'm not a robot)
 * Fixed the bug that caused the documentation link to go to a 404 page
 * Added functionality so that you may change the from name and from email address when a contact form is sent
 * Fixed a bug that caused broken HTML to be displayed where checkboxes were used (within the sent email)
 * Fixed a bug that caused the rows in the textareas to not be applied or saved
 * 
 * 1.09 - 2016-12-23
 * Fixed a bug that caused reCAPTCHA to not be disabled
 * 
 * 1.08 - 2016-12-23
 * Built in additional extension data and a page for extensions
 * 
 * 1.07 - 2016-12-23
 * You can now send your contact form via AJAX
 * 
 * 1.06 - 2016-12-22 
 * Fixed the bug that stopped placeholders from working
 * Fixed a bug that caused the user message to be inserted into the admin message
 * 
 * 1.05 - 2016-11-30
 * Fixed a bug that caused the email address to be left out of the email that is sent to the administrator
 * Added a way for users to vote on new features
 * Fixed a stray open tag
 * Added a welcome panel with dsocumentation links for new users
 * Fixed a bug that caused the 'headers already sent' message to appear
 * Code recatoring to eliminate php warnings
 * 
 * 1.04 - 2016-11-29
 * Added Google reCAPTCHA
 * Fixed the bug that stopped the "skip" button from working on the welcome page
 * Updated the predefined contact forms to suit the new reCAPTCHA inclusion
 * 
 * 1.03 - 2016-11-28
 * Fixed a bug that caused a stray open form tag when outputting the contact form
 * Updated FormBuilder to 1.24.2
 * Added predefined contact forms in the contact form builder
 *  - Simple contact form
 *  - Hotel check in form
 *  - Restaurant booking form
 *  - Support request contact form
 *  - How did you hear about us
 *  - NPS Score
 * Fixed a bug that caused the nonce field to be emailed along with the message
 * Added support for shortcodes
 * Fixed a bug that caused hidden text inputs to hide within the contact form builder
 * 
 * 1.02 - 2016-11-28
 * Added a welcome page
 * 
 * 1.01 - 2016-11-26
 * Added a nonce to the contact form
 * Code refacotring in the send process
 *  - Ensured multiple lines (textareas) carry over into the email
 *  - Remove the "Sent by Nifty Contact Forms" link at the bottom of each email
 * Added submission and view counters for each contact form
 * Added the contact form shortcode to the list view
 *
 *
 * 1.00 - 2016-11-xx
 * Launch
 *
 * 
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}
global $wpcf_thank_you;
global $wpcf_error_message;
include "includes/module_customizer.php";
include "includes/module_recaptcha.php";
include "includes/module_integrations.php";
include "includes/module_template_editor.php";
include "includes/module_subscribe.php";
include "includes/integration_wp_live_chat_support.php";

class WP_Contact_Form_ND{

	var $current_version;

	public function __construct(){

		$this->current_version = "1.13";

		$this->upload_dir =(defined('WP_CONTENT_DIR')) ? WP_CONTENT_DIR . '/uploads' : ABSPATH . 'wp-content' . $this->DS() . 'uploads';
		$this->upload_url =(defined('WP_CONTENT_URL')) ? WP_CONTENT_URL . '/uploads' : get_option('siteurl') . '/wp-content/uploads';
		 
		$this->admin_scripts();
		$this->user_scripts();
		add_action( 'admin_menu', array( $this, 'wpcf_nd_menu_items' ) );
		add_action( 'admin_init', array( $this, 'wpcf_nd_save_settings' ) );

    	add_shortcode( 'cform-nd', array( $this , 'wpcf_tag' ) );

		add_action( "wpcf_nd_general_settings", array($this, 'view_general_settings'),10 ); 

		register_activation_hook( __FILE__, array($this, 'plugin_activate') );
		add_action( "init", array($this, "check_versions") );
		add_action( "init", array($this, "create_post_type") );
		add_action( "plugins_loaded", array($this, "load_plugin_textdomain") );
		add_filter( "wpcf_nd_html_control", array( $this, "wpcf_nd_filter_control_html_control" ), 10, 2 );
		add_action( 'save_post', array( $this, 'wpcf_nd_save_meta_box' ) );
		add_action( 'save_post', array( $this, 'wpcf_nd_save_meta_box_control' ) );

		add_action( 'add_meta_boxes', array( $this, 'wpcf_nd_add_events_metaboxes' ) );
		add_action( 'add_meta_boxes', array( $this, 'wpcf_nd_add_cf_control_metabox' ) , 1 );
		add_action( 'add_meta_boxes', array( $this, 'wpcf_nd_add_cf_support_metabox' ) , 1 );

		add_action( 'wp_head' , array( $this, 'wpcf_nd_control_post' ) , 10 );


		add_action( 'wp_ajax_wpcf_close_welcome', array( $this, 'wpcf_nd_ajax_callback' ) );
		add_action( 'wp_ajax_wpcf_nd_send_ajax', array( $this, 'wpcf_nd_ajax_callback_front' ) );
		add_action( 'wp_ajax_nopriv_wpcf_nd_send_ajax', array( $this, 'wpcf_nd_ajax_callback_front' ) );


		add_action( "admin_head", array($this, "wpcf_nd_api_post" ) , 10 );
		add_action( "activated_plugin", array($this, "redirect_on_activate") );


		add_action( "wpcf_nd_main_post_control", array( $this, "hook_control_main_post_control" ), 10 );

		add_filter( "wpcf_nd_email_wrapper", array( $this, "wpcf_nd_email_wrapper_control" ) , 10 , 1 );

		add_filter( 'manage_contact-forms-nd_posts_columns', array( $this, 'set_custom_edit_columns' ) ) ;
		add_action( 'manage_contact-forms-nd_posts_custom_column' , array( $this, 'custom_column' ) , 10, 2  ) ;

	
		add_filter( 'views_edit-contact-forms-nd', array( $this, 'wpcf_nd_view_message_welcome' ), 10, 1 );

		add_filter( 'wp_mail_from', array( $this, 'wpcf_nd_filter_control_from_mail_headers_from_address' ), 10, 1 );
		add_filter( 'wp_mail_from_name', array( $this, 'wpcf_nd_filter_control_from_mail_headers_from_name' ), 10, 1 );

	}

	function load_plugin_textdomain() {
	    $plugin_dir = basename( dirname( __FILE__ ) ) . '/languages/';
		load_plugin_textdomain( 'wpcf_nd', false, $plugin_dir );
    }

	function wpcf_nd_ajax_callback_front() {
	    $check = check_ajax_referer('wpcf_nd_front', 'security');
	    if ($check == 1) {
	    	 if ($_POST['action'] == "wpcf_nd_send_ajax" ) {
	        	do_action( "wpcf_nd_main_post_control" );
	        	global $wpcf_error_message;
	        	if ($wpcf_error_message) {
	        		echo "<div class='wpcf-nd-error-message'>".$wpcf_error_message."</div>";
	        	} else {
	        		echo "1";
	        	}
	        	wp_die();
	        }

	    }

	}


	function wpcf_nd_ajax_callback() {

	    /* encoding error fixed 3 march 2015 - albert */
	    /* url_decode() shouldn't be used */
	    
	    
	    
	    global $wpdb;
	    $check = check_ajax_referer('wpcf_close_welcome', 'security');

	    if ($check == 1) {

	        if ($_POST['action'] == "wpcf_close_welcome") {
				
				update_user_meta( get_current_user_id(), 'wpcf_nd_hide_welcome_block', true );
			    wp_die();
	        }

	    }


	}


	function redirect_on_activate( $plugin ) {
		if( $plugin == plugin_basename( __FILE__ ) ) {
			if (!get_option("wpcf_nd_first_time")) {
		    	update_option("wpcf_nd_first_time",true);
		    	@ob_flush();
				@ob_end_flush();
				@ob_end_clean();
		    	exit( wp_redirect( admin_url( 'admin.php?page=wpcf-settings&action=wpcf_nd_welcome' ) ) );
		    	
		    }
		}
	}

	function wpcf_nd_control_post() {
		if ( isset( $_POST['wpcf_nd_submit'] ) ) {

			if ( ! isset( $_POST['wpcf_nonce_field'] ) || ! wp_verify_nonce( $_POST['wpcf_nonce_field'], 'wpcf_nd' ) ) {
				// invalid nonce
			} else {

				do_action( "wpcf_nd_main_post_control" );
				
		    }
	        
		}

	}

	function hook_control_main_post_control() {
		/**
		 * Find out if we can continue or not, based on filter outputs
		 * @var boolean
		 */
		$continue = apply_filters( "wpcf_filter_continue_form_post_handling", true, $_POST);
		if ($continue !== true) {
			global $wpcf_error_message;
			$wpcf_error_message = $continue;
			return;
		}

		$user_email = '';
		$user_name = '';


        $body = "<table width='100%'>";
        $txt_only = "";

        foreach ( $_POST as $key => $val ) {

        	if ( is_array( $val ) ) {
        		// checkbox
        		$tmpbody = "<table>";
        		$txt_only_sub = '';
        		foreach ( $val as $k => $v ) {
		            $tmpbody .= "<tr>";
		            $tmpbody .= "	<td width='50%' align='left' valign='top'><strong>". esc_attr( $k ). "</strong></td><td align='left'>: ". esc_attr( $v ) ."</td>";
		            $tmpbody .= "</tr>";
		            $txt_only_sub .= esc_attr( $k ). " : ". esc_attr( $v )."\r\n"; 
        		}
        		$tmpbody .= "</table>";
	            $body .= "<tr>";
	            $body .= "	<td width='50%' align='right' valign='top'><strong>". esc_attr( $key ). "</strong></td><td align='left'>" . $tmpbody ."</td>";
	            $body .= "</tr>";
	            $txt_only .= esc_attr( $key ). " : ". $txt_only_sub."\r\n"; 

        	} else {

        		/* try get the user's name */
        		if ( strpos( $key, 'name-field' ) !== false ) {
        			$user_name = sanitize_text_field( $val );
        		}

	        	if ( $key == "wpcf_nd_send_id" ) {
	        		$cfid = intval( sanitize_text_field( $val ));

	        	} else if ( $key == "wpcf_nd_submit" ) {

				} else if ( $key == 'wpcf_nonce_field' || $key == 'security' || $key == 'action' || $key == 'g-recaptcha-response' ) {

				} else if ( strpos( $key, 'email' ) !== false ) {
					$user_email = sanitize_text_field( $val );
		            $body .= "<tr>";
		            $body .= "<td width='50%' align='right' valign='top'><strong>". esc_attr( $key ). "</strong></td><td align='left'>: ". $user_email . "</td>";
		            $body .= "</tr>";							
		            $txt_only .= esc_attr( $key ) . " : " . $user_email . "\r\n"; 
				} else {

					$include = apply_filters( "wpcf_fiter_exclude_certain_fields", true, $key, $val );
					if ($include) {
						/* normal field */
			            $body .= "<tr>";
			            $body .= "<td width='50%' align='right' valign='top'><strong>". esc_attr( $key ). "</strong></td><td align='left'>: ". nl2br( esc_attr( $val ) ). "</td>";
			            $body .= "</tr>";
			            $txt_only .= esc_attr( $key ) . " : " . esc_attr( $val ) . "\r\n"; 

			        }
		        }
		    }
        }
        $body .= "</table>";

		$this->increase_submissions( $cfid );


        $data = array(
        	'user_email' => $user_email,
        	'user_name' => $user_name,
        	'from_form' => $cfid,
        	'uri' => $_SERVER['REQUEST_URI'],
        	'post_data' => $_POST
    	);

    	$this->send_to_integrations( $cfid, $txt_only, $data );
        $this->wpcf_send_email( $cfid , $body , $data );
    	
    	$wpcf_nd_redirect_uri = get_post_meta( $cfid, 'wpcf_nd_redirect_uri', true );
    	if ($wpcf_nd_redirect_uri) {
    		echo "<script>window.location = \"".get_option("siteurl") . $wpcf_nd_redirect_uri. "\"</script>";
    		exit();
    	}
    	global $wpcf_thank_you;


    	$wpcf_nd_settings = get_option( "wpcf_nd_settings" );
    	if ( isset( $wpcf_nd_settings['wpcf_nd_thank_you_text'] ) ) {
    		$wpcf_thank_you = esc_attr($wpcf_nd_settings['wpcf_nd_thank_you_text']);
    	} else {
    		$wpcf_thank_you = __("Thank you. Your message has been sent.","wpcf_nd");
    	}
	}



	function wpcf_nd_api_post() {


		$this->create_contact_form_types();



		if (isset($_POST['action']) && $_POST['action'] == 'wpcf_nd_submit_find_us') {
		    if (function_exists('curl_version')) {

		        $request_url = "http://www.contactformready.com/apif/rec.php";
		        $ch = curl_init();
		        curl_setopt($ch, CURLOPT_URL, $request_url);
		        curl_setopt($ch, CURLOPT_POST, 1);
		        curl_setopt($ch, CURLOPT_POSTFIELDS, $_POST);
		        curl_setopt($ch, CURLOPT_REFERER, $_SERVER['HTTP_HOST']);
		        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		        $output = curl_exec($ch);
		        curl_close($ch);
		    	echo "<script>window.location = \"".admin_url( 'edit.php?post_type=contact-forms-nd' ). "\"</script>";
				exit();

		    }

		}

	    if (isset($_POST['action']) && $_POST['action'] == 'wpcf_nd_skip_find_us') {
	    	echo "<script>window.location = \"".admin_url( 'edit.php?post_type=contact-forms-nd' ). "\"</script>";
			exit();
	    }

	}

	function increase_submissions($cfid) {
        $submissions = intval(get_post_meta( $cfid , 'cform_submissions' , true )); 
        $submissions++;
        update_post_meta(intval($cfid), 'cform_submissions', $submissions);
	}
	function increase_views($cfid) {
        $views = intval(get_post_meta( $cfid , 'cform_views' , true )); 
        $views++;
        update_post_meta(intval($cfid), 'cform_views', $views);
	}

	function wpcf_nd_set_html_mail_content_type() {
		return 'text/html';
	}



	function wpcf_nd_filter_control_from_mail_headers_from_address( $email ) {
		$wpcf_nd_settings = get_option( "wpcf_nd_settings" );

		if (!isset($wpcf_nd_settings['wpcf_nd_email_from_address']))
			$wpcf_nd_settings['wpcf_nd_email_from_address'] = get_option( 'admin_email' ); 

		return $wpcf_nd_settings['wpcf_nd_email_from_address'];

	}


	function wpcf_nd_filter_control_from_mail_headers_from_name( $email ) {
		$wpcf_nd_settings = get_option( "wpcf_nd_settings" );

		if (!isset($wpcf_nd_settings['wpcf_nd_email_from_name']))
			$wpcf_nd_settings['wpcf_nd_email_from_name'] = get_option( 'blogname' ); 

		return $wpcf_nd_settings['wpcf_nd_email_from_name'];

	}
	

	function wpcf_send_email($cfid,$body,$sent_data) {
		add_filter( 'wp_mail_content_type', array( $this, 'wpcf_nd_set_html_mail_content_type' ) );

		$wpcf_nd_settings = get_option( "wpcf_nd_settings" );

    	$sendto = get_post_meta( $cfid, 'wpcf_nd_send_to', true );

    	if (!$sendto) {
    		//set admin email as defaul email address if nothing was set
    		$sendto = get_option( 'admin_email' );

    	} else {
    		if (is_array($sendto)) {
    			$sendto = implode( "," , $sendto );
    		}
    	}


    	$header = sprintf("<a href='%s'>%s</a>",get_option('siteurl'),get_option('blogname'));

    	$orig_body = $body;

    	// SEND TO ADMIN
    	$wpcf_nd_settings = get_post_meta( $cfid, 'wpcf_nd_email_sending_settings', true ); 

		if( $wpcf_nd_settings == '' ){

			/**
			 * Either new user, or updated to the latest version
			 */
			
			$wpcf_nd_settings = get_option("wpcf_nd_settings");
			$subject_admin = isset( $wpcf_nd_settings['wpcf_nd_subject_admin'] ) ? $wpcf_nd_settings['wpcf_nd_subject_admin'] : $wpcf_nd_settings['wpcf_nd_subject_admin'] = __("New Contact Form Submission","wpcf_nd");
			$message_admin = isset( $wpcf_nd_settings['wpcf_nd_message_admin'] ) ? $wpcf_nd_settings['wpcf_nd_message_admin'] : $wpcf_nd_settings['wpcf_nd_message_admin'] = __("A new message has been received.","wpcf_nd");
			$send_to_user = isset( $wpcf_nd_settings['wpcf_nd_send_to_user'] ) ? $wpcf_nd_settings['wpcf_nd_send_to_user'] : '';
			$cfr_email_subject_user = isset( $wpcf_nd_settings['wpcf_nd_subject_user'] ) ? $wpcf_nd_settings['wpcf_nd_subject_user'] : __("Contact Form Submission Received","wpcf_nd");
			$cfr_email_body_user = isset( $wpcf_nd_settings['wpcf_nd_message_user'] ) ? $wpcf_nd_settings['wpcf_nd_message_user'] : __( "Thank you for your message. We will respond to you as soon as possible." , "wpcf_nd" );;

		} else {

			$subject_admin = isset( $wpcf_nd_settings['wpcf_nd_subject_admin'] ) ? $wpcf_nd_settings['wpcf_nd_subject_admin'] : $wpcf_nd_settings['wpcf_nd_subject_admin'] = __("New Contact Form Submission","wpcf_nd");
			$message_admin = isset( $wpcf_nd_settings['wpcf_nd_message_admin'] ) ? $wpcf_nd_settings['wpcf_nd_message_admin'] : $wpcf_nd_settings['wpcf_nd_message_admin'] = __("A new message has been received.","wpcf_nd");
			$send_to_user = isset( $wpcf_nd_settings['wpcf_nd_send_to_user'] ) ? $wpcf_nd_settings['wpcf_nd_send_to_user'] : '';
			$cfr_email_subject_user = isset( $wpcf_nd_settings['wpcf_nd_subject_user'] ) ? $wpcf_nd_settings['wpcf_nd_subject_user'] : __("Contact Form Submission Received","wpcf_nd");
			$cfr_email_body_user = isset( $wpcf_nd_settings['wpcf_nd_message_user'] ) ? $wpcf_nd_settings['wpcf_nd_message_user'] : __( "Thank you for your message. We will respond to you as soon as possible." , "wpcf_nd" );;

		}

		$data = array(
			'message' => $body,
			'footer' => '',
			'logo' => $header,
			'header' => stripslashes( esc_html( $message_admin ) )
		);
		$body = apply_filters( "wpcf_nd_email_wrapper" , $data );		
    	

		if ( isset( $sent_data['user_email'] ) && isset( $wpcf_nd_settings['wpcf_nd_send_as_user'] ) && $wpcf_nd_settings['wpcf_nd_send_as_user'] == 1 ) {
			$headers = array(
			  	'Reply-To: <'.$sent_data['user_email'].'>',
			);
		} else {
			$headers = array();
		}

    	$headers = apply_filters("wpcf_nd_filter_mail_headers",$headers,$cfid);
    	$attachments = array();
    	$attachments = apply_filters("wpcf_nd_filter_mail_attachments",$attachments,$cfid);




    	@wp_mail( $sendto , stripslashes( $subject_admin ) , $body , $headers , $attachments );
    	

    	// SEND TO USER?
    	if ( $send_to_user !== '' && $send_to_user == '1' && $sent_data['user_email'] != false) { 

			$data = array(
				'message' => $orig_body,
				'footer' => '',
				'logo' => $header,
				'header' => stripslashes( esc_html( $cfr_email_body_user ) )
			);
			$body = apply_filters( "wpcf_nd_email_wrapper" , $data );

	    	@wp_mail( $sent_data['user_email'] , stripslashes( $cfr_email_subject_user ) , $body , $headers , $attachments );
	    }



	}

	function wpcf_nd_add_events_metaboxes() {
		add_meta_box('wpcf_nd_main', __('Contact Form Data','wpcf_nd'), array( $this, 'wpcf_main_content' ), 'contact-forms-nd', 'normal', 'default');
	}


	function wpcf_nd_add_cf_control_metabox( $post ) {
		add_meta_box('wpcf_nd_main_control', __('Contact Form Settings','wpcf_nd'), array( $this, 'wpcf_main_control' ), 'contact-forms-nd', 'normal', 'default');
	}

	function wpcf_nd_add_cf_support_metabox( $post ) {
		add_meta_box('wpcf_nd_main_ext', __('Save all form submissions','wpcf_nd'), array( $this, 'wpcf_main_ext_metabox' ), 'contact-forms-nd', 'side', 'default');
		add_meta_box('wpcf_nd_main_support', __('Need help?','wpcf_nd'), array( $this, 'wpcf_main_support' ), 'contact-forms-nd', 'side', 'default');
	}
	function wpcf_main_support( $post ) {
		echo "<p>".sprintf(__( "Browse the <a href='%s' target='_BLANK'>documentation</a>.", "wpcf_nd" ),'http://www.contactformready.com/documentation/')."</p>";
		echo "<p>".sprintf(__( "Or get in touch with <a href='%s'>nick@codecabin.co.za</a>", "wpcf_nd" ), 'mailto:nick@codecabin.co.za'). "</p>";

	}


	function wpcf_main_ext_metabox( $post ) {
		echo "<p>".sprintf(__( "Store all form submissions using the new <a href='%s' target='_BLANK'>Stored Submission Extension</a> from CFR.", "wpcf_nd" ),'http://www.contactformready.com/extensions/stored-submissions/?utm_source=plugin&utm_medium=link&utm_campaign=meta_store')."</p>";
		echo "<p>".sprintf(__( "Browse more extensions <a href='%s'>here</a>.", "wpcf_nd" ), 'http://www.contactformready.com/extensions/?utm_source=plugin&utm_medium=link&utm_campaign=meta_ext'). "</p>";

	}



	function wpcf_main_control( $post ) {

		// We'll use this nonce field later on when saving.
    	wp_nonce_field( 'wpcf_nd_add_cf_control_metabox', 'wpcf_nd_nonce_control' );

    	$sendto = get_post_meta( $post->ID, 'wpcf_nd_send_to', true );
    	if (!$sendto) {
    		$sendto = get_option("admin_email");
    	} else {
    		if (is_array($sendto)) { 
    			$sendto = implode( "," , $sendto );
    		}
    	}
    	$wpcf_nd_redirect_uri = get_post_meta( $post->ID, 'wpcf_nd_redirect_uri', true );
		$submit_string = get_post_meta( $post->ID, 'wpcf_nd_submit_string' , true );
		$form_type = get_post_meta( $post->ID, 'wpcf_nd_submit_type' , true );
		if (!$submit_string || $submit_string === null) {
			$submit_string = __("Send","wpcf_nd");
		}
		if (!$form_type || $form_type === null) {
			$form_type = '0';
		}

		$wpcf_nd_settings = get_option( "wpcf_nd_settings" );

		$tabs_array = array(
			'basic-settings' => array(
				'name' => __( 'Basic Settings', 'wpcf_nd' ),
				'icon' => ''						
			),
			'advanced-settings' => array(
				'name' => __( 'Advanced Settings', 'wpcf_nd' ),
				'icon' => ''
			),
			'themes' => array(
				'name' => __( 'Themes', 'wpcf_nd' ),
				'icon' => ''
			)
		);

		$tabs_array = apply_filters( 'cfr_form_settings_tabs', $tabs_array );

		?>

		<div id="contact_form_ready_tabs">
			<ul>
				<?php
					if( $tabs_array ){
						foreach( $tabs_array as $key => $val ){
							echo "<li><a href='#".$key."'>".$val['icon']." ".$val['name']."</a></li>";
						}
					}
				?>
			</ul>
			<div id='basic-settings'>

				<h2><?php _e("Basic Settings","wpcf_nd"); ?></h2>

				<table class='form-table'>
			    	<?php do_action( "wpcf_hook_form_builder_table_top", $post, $wpcf_nd_settings ); ?>
			    	<tr>
			    		<td width='250'><label for='wpcf_nd_shortcode'><?php _e("Shortcode","wpcf_nd"); ?></label></td>
			    		<td>
			    			<input id="wpcf-shortcode-input" type='text' readonly value='[cform-nd id="<?php echo $post->ID; ?>"]' />
                            <span class="wpcf-shortcode-copy-text"><?php _e( 'Copied to clipboard', 'wpcf_nd' ) ?></span>
			    			<p class='description'><?php _e("Copy this to your post or page to show the contact form","wpcf_nd"); ?></p>

			    		</td>
			    	</tr>
			    	<tr>
			    		<td valign='top'><label for='wpcf_nd_send_to'><?php _e("Send emails to","wpcf_nd"); ?></label></td>
			    		<td>
			    			<input type='text' value='<?php echo $sendto; ?>' id='wpcf_nd_send_to' name='wpcf_nd_send_to' class='regular-text' />
			    			<p class='description'><?php _e("Multiple emails separated by commas","wpcf_nd"); ?></p>

			    		</td>
			    	</tr>
			    	<tr>
			    		<td valign='top'><label for='wpcf_nd_redirect_uri'><?php _e("Redirect to URL after submit","wpcf_nd"); ?></label></td>
			    		<td>
			    			<input type='text' value='<?php echo $wpcf_nd_redirect_uri; ?>' id='wpcf_nd_redirect_uri' name='wpcf_nd_redirect_uri' class='regular-text code' />
			    			<p class='description'><?php _e("Example: /thank-you <br>Leave blank for no redirect","wpcf_nd"); ?></p>
			    		</td>
			    	</tr>
			    	<tr>
			    		<td><label for='wpcf_nd_submit_string'><?php _e( "Submit button value", "wpcf_nd" ); ?></label></td>
			    		<td><input type='text' value='<?php echo $submit_string; ?>' id='wpcf_nd_submit_string' name='wpcf_nd_submit_string' /></td>
			    	</tr>
			    	<tr>
			    		<td><label for='wpcf_nd_submit_type'><?php _e( "Form type", "wpcf_nd" ); ?></label></td>
			    		<td>
			    			<select id="wpcf_nd_submit_type" name="wpcf_nd_submit_type">
			    				<option value='0' <?php if ($form_type == '0') { echo 'selected'; } ?>><?php _e( "Normal", "wpcf_nd" ); ?></option>
			    				<option value='1' <?php if ($form_type == '1') { echo 'selected'; } ?>><?php _e( "AJAX (avoids page reloading)", "wpcf_nd" ); ?></option>
			    			</select>
			    		</td>
			    	</tr>
			    	<?php do_action( "wpcf_hook_form_builder_table_bottom", $post, $wpcf_nd_settings ); ?>
			    </table>

			</div>

			<div id='advanced-settings'>

				<?php 

					$wpcf_nd_settings = get_post_meta( $post->ID, 'wpcf_nd_email_sending_settings', true ); 
					
					if( $wpcf_nd_settings == '' ){

						/**
						 * Either new user, or updated to the latest version
						 */
						
						$wpcf_nd_settings = get_option("wpcf_nd_settings");

						$cfr_email_subject_admin = ( isset($wpcf_nd_settings['wpcf_nd_subject_admin'] ) ) ? $wpcf_nd_settings['wpcf_nd_subject_admin'] : "";
						$cfr_email_body_admin = ( isset($wpcf_nd_settings['wpcf_nd_message_admin'] ) ) ? $wpcf_nd_settings['wpcf_nd_message_admin'] : "";
						
						$cfr_send_to_user = isset( $wpcf_nd_settings['wpcf_nd_send_to_user'] ) ? $wpcf_nd_settings['wpcf_nd_send_to_user'] : '';
						
						$cfr_email_subject_user = ( isset($wpcf_nd_settings['wpcf_nd_subject_user'] ) ) ? $wpcf_nd_settings['wpcf_nd_subject_user'] : "";
						$cfr_email_body_user = ( isset($wpcf_nd_settings['wpcf_nd_message_user'] ) ) ? $wpcf_nd_settings['wpcf_nd_message_user'] : "";

					} else {

						$cfr_email_subject_admin = isset( $wpcf_nd_settings['wpcf_nd_subject_admin'] ) ? stripslashes( esc_html( $wpcf_nd_settings['wpcf_nd_subject_admin'] ) ) : '';
						$cfr_email_body_admin = isset( $wpcf_nd_settings['wpcf_nd_message_admin'] ) ? stripslashes( esc_html( $wpcf_nd_settings['wpcf_nd_message_admin'] ) ) : '';
						
						$cfr_send_to_user = isset( $wpcf_nd_settings['wpcf_nd_send_to_user'] ) ? stripslashes( esc_html( $wpcf_nd_settings['wpcf_nd_send_to_user'] ) ) : '';
						
						$cfr_email_subject_user = isset( $wpcf_nd_settings['wpcf_nd_subject_user'] ) ? stripslashes( esc_html( $wpcf_nd_settings['wpcf_nd_subject_user'] ) ) : '';
						$cfr_email_body_user = isset( $wpcf_nd_settings['wpcf_nd_message_user'] ) ? stripslashes( esc_html( $wpcf_nd_settings['wpcf_nd_message_user'] ) ) : '';

					}

				?>

				<h2><?php _e("Themes","wpcf_nd"); ?></h2>

				<table class='form-table'>					
					<tr>
						<td width='250'><?php _e("Email subject (admin)","wpcf_nd"); ?></td>
						<td><input type='text' name='wpcf_nd_subject_admin' class='regular-text' id='wpcf_nd_subject_admin' value='<?php echo $cfr_email_subject_admin ?>' /></td>
					</tr>
					<tr>
						<td><?php _e("Email body (admin)","wpcf_nd"); ?></td>
						<td><input type='text' name='wpcf_nd_message_admin' class='regular-text' id='wpcf_nd_message_admin' value='<?php echo $cfr_email_body_admin ?>' /></td>
					</tr>


					<tr>
						<td><?php _e("Send confirmation email to user?","wpcf_nd"); ?></td>
						
						<?php $is_checked = $cfr_send_to_user == 1 ? "checked" : ""; ?>

						<td><input type='checkbox' name='wpcf_nd_send_to_user' id='wpcf_nd_send_to_user' value='1' <?php echo $is_checked; ?> /></td>
					</tr>
					<tr>
						<td><?php _e("Email subject (user)","wpcf_nd"); ?></td>
						<td><input type='text' name='wpcf_nd_subject_user' class='regular-text' id='wpcf_nd_subject_user' value='<?php echo $cfr_email_subject_user; ?>' /></td>
					</tr>
					<tr>
						<td><?php _e("Email body (user)","wpcf_nd"); ?></td>
						<td><input type='text' name='wpcf_nd_message_user' class='regular-text' id='wpcf_nd_message_user' value='<?php echo $cfr_email_body_user; ?>' /></td>
					</tr>

					<tr>
						<td><?php _e("Set the reply-to as the user's email address","wpcf_nd"); ?></td>
						<?php
						 $is_checked = (isset($wpcf_nd_settings['wpcf_nd_send_as_user']) && $wpcf_nd_settings['wpcf_nd_send_as_user'] == 1) ? "checked" : "";
						?>
						<td><input type='checkbox' name='wpcf_nd_send_as_user' id='wpcf_nd_send_as_user' value='1' <?php echo $is_checked; ?> /> <span class='description'><?php echo __( "Will only be set if the form has a valid email field.")  ?></span></td>
					</tr>


					<?php do_action( "contact_form_ready_settings_bottom_advanced" ); ?>

				</table>

			</div>			

            <div id='themes'>

				<?php
				$wpcf_nd_theme = get_post_meta( $post->ID, 'wpcf_nd_theme', true );
				if ( ! isset( $wpcf_nd_theme ) ) {
					$wpcf_nd_theme = 'default';
				}
				?>

                <h2><?php _e("Themes","wpcf_nd"); ?></h2>

                <ul class="wpcf-admin-img-list">
                    <li class="wpcf-admin-img-item" data-cf-theme="default">
                        <h3 class="wpcf-admin-img-heading"><?php _e("Default Theme","wpcf_nd"); ?></h3>
                        <a href="#" class="wpcf-admin-img-link">
                            <img src="<?php echo plugins_url( plugin_basename( dirname( __FILE__ ) ) ) . "/images/themes/default.png" ?>"
                                 alt="<?php _e("Default Theme","wpcf_nd"); ?>" class="wpcf-admin-img-thumb"/>
                        </a>
                    </li>
                    <li class="wpcf-admin-img-item" data-cf-theme="slick">
                        <h3 class="wpcf-admin-img-heading"><?php _e("Slick Theme","wpcf_nd"); ?></h3>
                        <a href="#" class="wpcf-admin-img-link">
                            <img src="<?php echo plugins_url( plugin_basename( dirname( __FILE__ ) ) ) . "/images/themes/slick.png" ?>"
                                 alt="<?php _e("Slick Theme","wpcf_nd"); ?>" class="wpcf-admin-img-thumb"/>
                        </a>
                    </li>
                    <li class="wpcf-admin-img-item" data-cf-theme="modern">
                        <h3 class="wpcf-admin-img-heading"><?php _e("Modern Theme","wpcf_nd"); ?></h3>
                        <a href="#" class="wpcf-admin-img-link">
                            <img src="<?php echo plugins_url( plugin_basename( dirname( __FILE__ ) ) ) . "/images/themes/modern.png" ?>"
                                 alt="<?php _e("Modern Theme","wpcf_nd"); ?>" class="wpcf-admin-img-thumb"/>
                        </a>
                    </li>
                </ul>
                <input class="wpcf-admin-option-theme" type="hidden" name="wpcf_nd_theme" value="<?php echo $wpcf_nd_theme; ?>" />

            </div>


            <?php
				$wpcf_nd_settings = get_option("wpcf_nd_settings");
				do_action( "contact_form_ready_settings_content", $post, $wpcf_nd_settings );
			?>

		</div>	  



        <!-- / Components -->
        <?php
	}

	 
	/**
	 * Meta box display callback.
	 *
	 * @param WP_Post $post Current post object.
	 */
	function wpcf_main_content( $post ) {

		// We'll use this nonce field later on when saving.
    	wp_nonce_field( 'wpcf_nd_add_events_metaboxes', 'wpcf_nd_nonce' );

    	$htmls = get_post_meta( $post->ID, 'wpcf_nd_html_data', true );
    	$formdata = get_post_meta( $post->ID, 'wpcf_nd_form_data', true );
    	$formdata = preg_replace('/>\s+</', "><", $formdata);

		$contact_form_types = get_option("wpcf_nd_contact_forms");




		?>
	    <script>
		    //var tmpdata = '<?php echo trim($formdata); ?>';
		    //var tmpformData = JSON.stringify(tmpdata);

		</script>

		<form action='' method='POST' novalidate>


        <!-- Building Form. -->
        <div class="col-sm-12">
            <h3 class="tagline"><?php _e("Select from a predefined list","wpcf_nd"); ?></h3>
            <select name='wpcf_nd_predfined' id='wpcf_nd_predfined'>
            	<option value='x'><?php _e("Please select","wpcf_nd"); ?></option>
            	<?php
            		
            		foreach ($contact_form_types as $key => $data) {
            			echo "\t<option value='".$key."'>".$data['title']."</option>".PHP_EOL;
            		}
            	?>
            </select>
        </div>
        <p>&nbsp;</p>

        <hr />

        <p>&nbsp;</p>


    	
    		<!-- YES -->
        <!-- Building Form. -->
        <div class="col-sm-12">
            <h3 class="tagline"><?php _e("Drag and drop the elements to create your contact form.","wpcf_nd"); ?></h3>
            <section id="main_content" class="inner">
			    <div class="build-wrap"></div>
			    <div class="render-wrap"></div>
            </section>
        </div>
		

         <textarea id="fb-temp-formdata" name="fb-temp-formdata" style='width:100%; display:none;'><?php echo $formdata; ?></textarea>
         <textarea id="fb-temp-htmldata" name="fb-temp-htmldata" style='width:100%; display:none;'><?php echo htmlentities($htmls); ?></textarea>

        <!-- / Components -->

        </form>
        <?php
        
	} 
	 
	/**
	 * Save meta box content.
	 *
	 * @param int $post_id Post ID
	 */
	function wpcf_nd_save_meta_box( $post_id ) {

	 // Bail if we're doing an auto save
	    if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	     
	    // if our nonce isn't there, or we can't verify it, bail
	    if( !isset( $_POST['wpcf_nd_nonce'] ) || !wp_verify_nonce( $_POST['wpcf_nd_nonce'], 'wpcf_nd_add_events_metaboxes' ) )  {
	    	return;
	    }
	     
	    // if our current user can't edit this post, bail
	    if( !@current_user_can( 'edit_post' ) ) return;	


	    $allowed_html = array(
	    	 'a' => array(
		        'href' => array(),
		        'title' => array()
		    ),
		    'br' => array(),
		    'em' => array(),
		    'strong' => array(),
		    'select' => array(
		    	'class' => array(),
		    	'type' => array(),
	    		'required' => array(),
	    		'aria-required' => array(),
	    		'style' => array(),
	    		'data-other-id' => array(),
		    	'name' => array(),
		    	'id' => array(),
		    	'value' => array()
	    		),
		    'option' => array(
		    	'value' => array(),
		    	'id' => array()
		    	),
		    'input' => array(
		    	'class' => array(),
		    	'type' => array(),
		    	'subtype' => array(),
		    	'name' => array(),
		    	'placeholder' => array(),
	    		'aria-required' => array(),
	    		'maxlength' => array(),
	    		'style' => array(),
	    		'data-other-id' => array(),
		    	'id' => array(),
	    		'required' => array(),
		    	'value' => array()
		    	),
		    'h1' => array(
		    	'class' => array(),
		    	'type' => array(),
		    	'subtype' => array(),
		    	'id' => array() 
		    	),
		    'h2' => array(
		    	'class' => array(),
		    	'type' => array(),
		    	'subtype' => array(),
		    	'id' => array() 
		    	),
		    'br' => array(),
		    'p' => array(
		    	'class' => array(),
		    	'type' => array(),
		    	'subtype' => array(),
		    	'id' => array()
		    	),
		    'label' => array(
		    	'for' => array(),
		    	'id' => array(),
		    	'class' => array()
		    	),
		    'div' => array(
		    	'class' => array()
	    		),
		    'span' => array(
		    	'class' => array(),
		    	'tooltip' => array()
	    	),
		    'textarea' => array(
		    	'class' => array(),
	    		'required' => array(),
		    	'type' => array(),
		    	'name' => array(),
		    	'maxlength' => array(),
		    	'rows' => array(),
		    	'id' => array(),
		    	'placeholder' => array()
		    	)
	    );

	    $allowed_xml = array(
	    	'form-template' => array(),
	    	'fields' => array(),
	    	'field' => array(
	    		'class' => array(),
	    		'aria-required' => array(),
	    		'style' => array(),
	    		'id' => array(),
	    		'data-other-id' => array(),
	    		'label' => array(),
	    		'description' => array(),
	    		'rows' => array(),
	    		'enable-other' => array(),
	    		'maxlength' => array(),
	    		'placeholder' => array(),
	    		'name' => array(),
	    		'required' => array(),
	    		'type' => array(),
	    		'subtype' => array()
    		),
    		'option' => array(
    			'value' => array()
			)


    	);


		// Make sure your data is set before trying to save it
	    if( isset( $_POST['fb-temp-htmldata'] ) ) {
	    	$post_html_data = $_POST['fb-temp-htmldata'];
	    	$post_html_data = str_replace(array("\r", "\n"), '', $post_html_data);
	        update_post_meta( $post_id, 'wpcf_nd_html_data', wp_kses( $post_html_data , $allowed_html ) );
	    }
	    if( isset( $_POST['fb-temp-formdata'] ) )
	        update_post_meta( $post_id, 'wpcf_nd_form_data', wp_kses( $_POST['fb-temp-formdata'] , $allowed_xml ) );

	    
	}

	function wpcf_nd_save_meta_box_control( $post_id ) {



	 // Bail if we're doing an auto save
	    if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	     
	    // if our nonce isn't there, or we can't verify it, bail
	    if( !isset( $_POST['wpcf_nd_nonce_control'] ) || !wp_verify_nonce( $_POST['wpcf_nd_nonce_control'], 'wpcf_nd_add_cf_control_metabox' ) )  {
	    	return;
	    }
	     
	    // if our current user can't edit this post, bail
	    if( !@current_user_can( 'edit_post' ) ) return;	


		// Make sure your data is set before trying to save it
	    if( isset( $_POST['wpcf_nd_send_to'] ) ) {
			$email_array = explode( "," , $_POST['wpcf_nd_send_to'] );
			foreach ($email_array as $key => $val) {
				$email_array[$key] = sanitize_email( $val );
			}
     	   	update_post_meta( $post_id, 'wpcf_nd_send_to', $email_array );
	    }
	    if( isset( $_POST['wpcf_nd_submit_string'] ) )
	        update_post_meta( $post_id, 'wpcf_nd_submit_string', sanitize_text_field( $_POST['wpcf_nd_submit_string'] ) );
	    if( isset( $_POST['wpcf_nd_redirect_uri'] ) )
	        update_post_meta( $post_id, 'wpcf_nd_redirect_uri', sanitize_text_field( $_POST['wpcf_nd_redirect_uri'] ) );
	    if( isset( $_POST['wpcf_nd_submit_type'] ) )
	        update_post_meta( $post_id, 'wpcf_nd_submit_type', sanitize_text_field( $_POST['wpcf_nd_submit_type'] ) );

	    /**
	     * Email details
	     */
	    $wpcf_nd_settings = array();
		if (isset($_POST['wpcf_nd_email_from_address'])) 
			$wpcf_nd_settings['wpcf_nd_email_from_address'] = sanitize_text_field( $_POST['wpcf_nd_email_from_address'] );

		if (isset($_POST['wpcf_nd_email_from_name'])) 
			$wpcf_nd_settings['wpcf_nd_email_from_name'] = sanitize_text_field( $_POST['wpcf_nd_email_from_name'] );

		if (isset($_POST['wpcf_nd_subject_admin'])) 
			$wpcf_nd_settings['wpcf_nd_subject_admin'] = sanitize_text_field( $_POST['wpcf_nd_subject_admin'] );

		if (isset($_POST['wpcf_nd_subject_user'])) 
			$wpcf_nd_settings['wpcf_nd_subject_user'] = sanitize_text_field( $_POST['wpcf_nd_subject_user'] );
		
		if ( isset( $_POST['wpcf_nd_send_to_user'] ) && $_POST['wpcf_nd_send_to_user'] == '1' ){
			$wpcf_nd_settings['wpcf_nd_send_to_user'] = intval(sanitize_text_field( $_POST['wpcf_nd_send_to_user'] ));
		} else {
			$wpcf_nd_settings['wpcf_nd_send_to_user'] = 0;
		}



		if (isset($_POST['wpcf_nd_message_user']))  {
			$wpcf_nd_settings['wpcf_nd_message_user'] = sanitize_text_field( $_POST['wpcf_nd_message_user'] );
		} else {
			$wpcf_nd_settings['wpcf_nd_message_user'] = '';
		}


		if (isset($_POST['wpcf_nd_send_as_user'])  && $_POST['wpcf_nd_send_as_user'] == '1' ) {
			$wpcf_nd_settings['wpcf_nd_send_as_user'] = intval(sanitize_text_field( $_POST['wpcf_nd_send_as_user'] ));
		} else {
			$wpcf_nd_settings['wpcf_nd_send_as_user'] = 0;
		}


		if (isset($_POST['wpcf_nd_message_admin'])) 
			$wpcf_nd_settings['wpcf_nd_message_admin'] = sanitize_text_field( $_POST['wpcf_nd_message_admin'] );

		/**
		 * Themes settings
		 */
		if (isset($_POST['wpcf_nd_theme']))
			update_post_meta( $post_id, 'wpcf_nd_theme', sanitize_text_field( $_POST['wpcf_nd_theme'] ) );

		update_post_meta( $post_id, 'wpcf_nd_email_sending_settings', $wpcf_nd_settings );

	    do_action ( "wpcf_nd_hook_save_meta_box_control", $post_id, $_POST );
	    
	}



	function check_versions() {

		$current_option = get_option("wpcf_nd_version");
		

		if ($current_option !== $this->current_version) {
			$this->handle_defaults();
			$this->create_contact_form_types(true);
			update_option("wpcf_nd_version",$this->current_version);
		}
	}
	function create_post_type() {
		
	    $labels = array(
	        'name' => __('Contact Forms', 'wpcf_nd'),
	        'singular_name' => __('Contact Form', 'wpcf_nd'),
	        'add_new' => __('New Contact Form', 'wpcf_nd'),
	        'add_new_item' => __('Add New Contact Form', 'wpcf_nd'),
	        'edit_item' => __('Edit Contact Form', 'wpcf_nd'),
	        'new_item' => __('New Contact Form', 'wpcf_nd'),
	        'all_items' => __('All Contact Forms', 'wpcf_nd'),
	        'view_item' => __('View Contact Form', 'wpcf_nd'),
	        'search_items' => __('Search Contact Forms', 'wpcf_nd'),
	        'not_found' => __('No contact forms found', 'wpcf_nd'),
	        'not_found_in_trash' => __('No contact forms in the Trash', 'wpcf_nd'),
	        'menu_name' => __('Contact Forms', 'wpcf_nd')
	    );
	    $args = array(
	        'labels' => $labels,
	        'description' => __('Contact Forms', 'wpcf_nd'),
	        'public' => true,
	        'menu_position' => 5,
	        'hierarchical' => false,
	        'rewrite' => array('slug' => 'contact-forms-nd'),
	        'supports' => array('title','custom-meta'),
	        'publicly_queryable' => false,
	        'exclude_from_search' => false,
	        'query_var' => true,
	        'has_archive' => true,
	        'register_meta_box_cb' => array($this, 'wpcf_nd_add_events_metaboxes' )
	    );
	    if (post_type_exists('contact-forms-nd')) {

	    } else {
	        register_post_type('contact-forms-nd', $args);
		    flush_rewrite_rules();

	    }

	}

	function plugin_activate() {
		$this->handle_defaults();
		$this->create_contact_form_types();
	}

	function create_contact_form_types($force = false) {
		/* contact form types */
		if (!get_option("wpcf_nd_contact_forms") || $force) {
			$contact_form_type = array(
				1 => array(
					"title" => __("Simple contact form","wpcf_nd"),
					"xml_data" => '<form-template><fields><field type="header" subtype="h1" label="Contact us" class="header"></field><field type="text" label="Name" subtype="text" class="form-control text-input" name="name-field"></field><field type="text" subtype="email" label="Email" class="form-control text-input" name="email-field"></field><field type="textarea" label="Your message" class="form-control text-area" name="message"></field></fields></form-template>'
				),
				2 => array(
					"title" => __("Booking form (hotel)", "wpcf_nd"),
					"xml_data" => '<form-template><fields><field type="header" subtype="h1" label="Book now" class="header"></field><field type="paragraph" subtype="p" label="This is a customizable paragraph field" class="paragraph"></field><field type="text" label="Name" subtype="text" class="form-control text-input" name="name-field"></field><field type="text" subtype="email" label="Email" class="form-control text-input" name="email-field"></field><field type="select" label="Guests" class="form-control" name="guestqty">	<option label="1" value="1" selected="true">1</option><option label="2" value="2">2</option><option label="3" value="3">3</option><option label="4" value="4">4</option><option label="5" value="5">5</option><option label="6" value="6">6</option><option label="7" value="7">7</option><option label="8" value="8">8</option><option label="9" value="9">9</option></field><field type="date" label="Check in date" class="calendar" name="check-in-date"></field><field type="date" label="Check out date" class="calendar" name="check-out-date"></field><field type="textarea" label="Additional comments" class="form-control text-area" name="additional-comments"></field></fields></form-template>'
				),
				3 => array(
					"title" => __("Booking form (restaurant)", "wpcf_nd"),
					"xml_data" => '<form-template><fields><field type="header" subtype="h1" label="Book now" class="header"></field><field type="paragraph" subtype="p" label="This is a customizable paragraph field" class="paragraph"></field><field type="text" label="Name" subtype="text" class="form-control text-input" name="name-field"></field><field type="text" subtype="email" label="Email" class="form-control text-input" name="email-field"></field><field type="number" label="Guests" min="1" max="100" step="1" class="form-control" name="guests"></field><field type="date" label="Booking date" class="calendar" name="booking-date"></field><field type="select" label="Occasion" class="form-control" name="occasion"><option label="No occasion" value="none">No occasion</option><option label="Birthday" value="birthday" selected="true">Birthday</option><option label="Engagement" value="engagement">Engagement</option><option label="Anniversary" value="anniversary">Anniversary</option><option label="Meeting" value="meeting">Meeting</option></field><field type="textarea" label="Additional comments" class="form-control text-area" name="additional-comments"></field></fields></form-template>'
				),
				4 => array(
					"title" => __("Support form", "wpcf_nd"),
					"xml_data" => '<form-template><fields><field type="header" subtype="h1" label="Support query" class="header"></field><field type="paragraph" subtype="p" label="Please enter your details below and an agent will get in touch with you as soon as possible" class="paragraph"></field><field type="text" label="Name" subtype="text" class="form-control text-input" name="name-field"></field><field type="text" subtype="email" label="Email" class="form-control text-input" name="email-field"></field><field type="radio-group" label="Department" class="radio-group" name="department"><option label="Sales" value="sales" selected="true">Sales</option><option label="Technical Support" value="technical-support">Technical Support</option><option label="Accounts" value="accounts">Accounts</option></field><field type="textarea" label="Describe your issue" class="form-control" name="message"></field></fields></form-template>'
				),
				5 => array(
					"title" => __("How did you hear about us", "wpcf_nd"),
					"xml_data" => '<form-template><fields><field type="header" subtype="h1" label="How did you hear about us?" class="header"></field><field type="text" label="Name" subtype="text" class="form-control text-input" name="name-field"></field><field type="text" subtype="email" label="Email" class="form-control text-input" name="email-field"></field><field type="select" label="How did you hear about us?" class="form-control" name="hearaboutus"><option label="Radio" value="radio" selected="true">Radio</option><option label="Television" value="television">Television</option><option label="Online Advertising" value="online-advertising">Online Advertising</option><option label="Billboard" value="billboard">Billboard</option><option label="Newspaper ad" value="newspaper-ad">Newspaper ad</option><option label="Friend" value="friend">Friend</option><option label="Search Engine" value="search-engine">Search Engine</option></field><field type="textarea" label="Additional feedback" class="form-control text-area" name="additional-feedback"></field></fields></form-template>'
				),
				6 => array(
					"title" => __("NPS Score", "wpcf_nd"),
					"xml_data" => '<form-template><fields><field type="header" subtype="h1" label="How likely are you to suggest our brand to your friends or family?" class="header"></field><field type="text" label="Name" subtype="text" class="form-control text-input" name="name-field"></field><field type="text" subtype="email" label="Email" class="form-control text-input" name="email-field"></field><field type="radio-group" label="How likely are you to suggest our brand to your friends or family?" class="radio-group" name="nps-score"><option label="1 - Not likely at all" value="1">1 - Not likely at all</option><option label="2" value="2">2</option><option label="3" value="3">3</option><option label="4" value="4">4</option><option label="5" value="5">5</option><option label="6" value="6">6</option><option label="7" value="7">7</option><option label="8" value="8">8</option><option label="9" value="9">9</option><option label="10 - Extremely likely" value="10">10 - Extremely likely</option></field><field type="textarea" label="Additional feedback" class="form-control text-area" name="additional-feedback"></field></fields></form-template>'
				)
			);
			update_option("wpcf_nd_contact_forms",$contact_form_type);
		}

	}


	function handle_defaults() {
		$wpcf_nd_settings = get_option("wpcf_nd_settings");
		$wpcf_nd_styling = get_option("wpcf_nd_styling");
		/**
		 * defaults here
		 */

		if (!isset($wpcf_nd_settings['wpcf_nd_email_from_address']))
			$wpcf_nd_settings['wpcf_nd_email_from_address'] = get_option( 'admin_email' ); 

		if (!isset($wpcf_nd_settings['wpcf_nd_email_from_name']))
			$wpcf_nd_settings['wpcf_nd_email_from_name'] = get_option( 'blogname' ); 

		if (!isset($wpcf_nd_settings['wpcf_nd_subject_admin']))
			$wpcf_nd_settings['wpcf_nd_subject_admin'] = __("New Contact Form Response","wpcf_nd"); 

		if (!isset($wpcf_nd_settings['wpcf_nd_subject_user']))
			$wpcf_nd_settings['wpcf_nd_subject_user'] = __("Contact Form Response Received","wpcf_nd"); 
		
		
		if (!isset($wpcf_nd_settings['wpcf_nd_message_admin']))
			$wpcf_nd_settings['wpcf_nd_message_admin'] = __("A new message has been received.","wpcf_nd"); 

		if (!isset($wpcf_nd_settings['wpcf_nd_message_user']))
			$wpcf_nd_settings['wpcf_nd_message_user'] = __("Thank you for your message. We will respond to you as soon as possible.","wpcf_nd"); 

		if (!isset($wpcf_nd_settings['wpcf_nd_thank_you_text']))
			$wpcf_nd_settings['wpcf_nd_thank_you_text'] = __("Thank you. Your message has been sent and we will be in touch as soon as possible.","wpcf_nd"); 


		update_option("wpcf_nd_settings",$wpcf_nd_settings);

		if (!isset($wpcf_nd_styling['wpcf_nd_enable_style']))
			$wpcf_nd_styling['wpcf_nd_enable_style'] = 0;

		if (!isset($wpcf_nd_styling['wpcf_nd_label_font_size']))
			$wpcf_nd_styling['wpcf_nd_label_font_size'] = "16";

		if (!isset($wpcf_nd_styling['wpcf_nd_label_font_weight']))
			$wpcf_nd_styling['wpcf_nd_label_font_weight'] = "600";

		if (!isset($wpcf_nd_styling['wpcf_nd_label_color']))
			$wpcf_nd_styling['wpcf_nd_label_color'] = "#222222";

		if (!isset($wpcf_nd_styling['wpcf_nd_input_bg_color']))
			$wpcf_nd_styling['wpcf_nd_input_bg_color'] = "transparent";

		if (!isset($wpcf_nd_styling['wpcf_nd_input_border_color']))
			$wpcf_nd_styling['wpcf_nd_input_border_color'] = "#222222";

		if (!isset($wpcf_nd_styling['wpcf_nd_input_border_focus_color']))
			$wpcf_nd_styling['wpcf_nd_input_border_focus_color'] = "#333333";

		if (!isset($wpcf_nd_styling['wpcf_nd_input_font_size']))
			$wpcf_nd_styling['wpcf_nd_input_font_size'] = "16";

		if (!isset($wpcf_nd_styling['wpcf_nd_input_font_color']))
			$wpcf_nd_styling['wpcf_nd_input_font_color'] = "#666666";

		if (!isset($wpcf_nd_styling['wpcf_nd_submit_bg_color']))
			$wpcf_nd_styling['wpcf_nd_submit_bg_color'] = "#222222";

		if (!isset($wpcf_nd_styling['wpcf_nd_submit_bg_hover_color']))
			$wpcf_nd_styling['wpcf_nd_submit_bg_hover_color'] = "#666666";

		if (!isset($wpcf_nd_styling['wpcf_nd_submit_font_size']))
			$wpcf_nd_styling['wpcf_nd_submit_font_size'] = "14";

		if (!isset($wpcf_nd_styling['wpcf_nd_submit_font_color']))
			$wpcf_nd_styling['wpcf_nd_submit_font_color'] = "#ffffff";

		if (!isset($wpcf_nd_styling['wpcf_nd_submit_font_weight']))
			$wpcf_nd_styling['wpcf_nd_submit_font_weight'] = "700";

		if (!isset($wpcf_nd_styling['wpcf_nd_submit_text_transform']))
			$wpcf_nd_styling['wpcf_nd_submit_text_transform'] = "none";

		update_option("wpcf_nd_styling",$wpcf_nd_styling);
	}

	


	private function admin_scripts() {
		add_action('admin_print_scripts', array($this, 'load_admin_scripts'));
		add_action('admin_print_styles', array($this, 'load_admin_styles'));
	}
	private function user_scripts() {
		add_action('wp_head', array($this, 'load_user_scripts'));
		
	}

	function load_user_scripts() {
			$wpcf_nonce = wp_create_nonce("wpcf_nd_front");
       		$wpcf_ajaxurl = admin_url('admin-ajax.php');

       		$wpcf_nd_settings = get_option("wpcf_nd_settings");

       		wp_enqueue_script( 'jquery' );
       		wp_enqueue_script( 'jquery-ui-core' );
       		wp_enqueue_script( 'jquery-ui-tooltip' );

       		wp_enqueue_style( 'cfr-jquery-ui', '//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css"' );

	        wp_register_script( 'contact-form-ready', plugins_url(plugin_basename(dirname(__FILE__)))."/js/user.js", true, $this->current_version );
	        wp_enqueue_script( 'contact-form-ready' );
	        wp_localize_script( 'contact-form-ready', 'wpcf_nd_nonce', $wpcf_nonce );
	        wp_localize_script( 'contact-form-ready', 'wpcf_nd_ajaxurl', $wpcf_ajaxurl );

	        if (!isset($wpcf_nd_settings['wpcf_nd_message_user']))
			  $wpcf_nd_settings['wpcf_nd_message_user'] = __("Thank you for your message. We will respond to you as soon as possible.","wpcf_nd"); 

			wp_localize_script( 'contact-form-ready', 'wpcf_nd_ajax_thank_you', stripslashes( esc_html( $wpcf_nd_settings['wpcf_nd_message_user'] ) ) );
			wp_localize_script( 'contact-form-ready', 'wpcf_nd_ajax_sending', __("Sending...","wpcf_nd") );
			
			do_action( "wpcf_user_js" );


	}

	
	function load_admin_scripts() {
	 	global $post_type;
	 	global $post;
	    if( "contact-forms-nd" == $post_type ) {

	    	wp_enqueue_script( 'jquery' );
	    	wp_enqueue_script( 'jquery-ui-core' );
	    	wp_enqueue_script( 'jquery-ui-tabs' );	    	

	        wp_register_script( 'form-builder-js', plugins_url(plugin_basename(dirname(__FILE__)))."/assets/formbuilder/js/helpers.js", true );
	        wp_enqueue_script( 'form-builder-js' );
	        wp_register_script( 'form-builder-js1', plugins_url(plugin_basename(dirname(__FILE__)))."/assets/formbuilder/js/events.js", true );
	        wp_enqueue_script( 'form-builder-js1' );
	        wp_register_script( 'form-builder-js2', plugins_url(plugin_basename(dirname(__FILE__)))."/assets/formbuilder/js/kc-toggle.js", true );
	        wp_enqueue_script( 'form-builder-js2' );
	        wp_register_script( 'form-builder-js3', plugins_url(plugin_basename(dirname(__FILE__)))."/assets/formbuilder/js/polyfills.js", true );
	        wp_enqueue_script( 'form-builder-js3' );
	        wp_register_script( 'form-builder-js4', plugins_url(plugin_basename(dirname(__FILE__)))."/assets/formbuilder/js/utils.js", true );
	        wp_enqueue_script( 'form-builder-js4' );
	        wp_register_script( 'form-builder-js6', plugins_url(plugin_basename(dirname(__FILE__)))."/assets/formbuilder/js/form-builder.js", true );
	        wp_enqueue_script( 'form-builder-js6' );

	        wp_register_script( 'form-builder-site-js', plugins_url(plugin_basename(dirname(__FILE__)))."/assets/formbuilder/js/site.js", true );
	        wp_enqueue_script( 'form-builder-site-js' );

	        wp_register_script( 'form-builder-render-js', plugins_url(plugin_basename(dirname(__FILE__)))."/assets/formbuilder/js/form-render.min.js", true );
	        wp_enqueue_script( 'form-builder-render-js' );

	        wp_register_script( 'wpcf-admin', plugins_url(plugin_basename(dirname(__FILE__)))."/js/admin.js", true );
	        wp_enqueue_script( 'wpcf-admin' );

    		$contact_form_types = get_option("wpcf_nd_contact_forms");

			wp_localize_script( 'wpcf-admin', 'wpcf_nd_types', $contact_form_types );

			if ( is_object( $post ) ) {
				$formdata = get_post_meta( $post->ID, 'wpcf_nd_form_data', true );
	    		$formdata = preg_replace('/>\s+</', "><", $formdata);
				wp_localize_script( 'wpcf-admin', 'tmpformData', $formdata );
			} else {
				wp_localize_script( 'wpcf-admin', 'tmpformData', '' );
			}


			
		    


		}
		if ( isset( $_GET['page'] ) && $_GET['page'] == 'wpcf-settings') {
	        wp_register_script( 'wpcf-admin-settings', plugins_url(plugin_basename(dirname(__FILE__)))."/js/admin_settings.js", true );
	        wp_enqueue_script( 'wpcf-admin-settings' );
	        wp_localize_script( 'wpcf-admin-settings', 'wpcf_nd_confirm_restore_template_string', __( 'Are you sure you want to restore the default newsletter template?', 'wpcf_nd' ) );
		}
		if ( isset( $_GET['page'] ) && $_GET['page'] == 'wpcf-styling') {
			wp_register_script( 'wpcf-admin-styling', plugins_url(plugin_basename(dirname(__FILE__)))."/js/admin_styling.js", true );
			wp_enqueue_script( 'wpcf-admin-styling' );
		}
	}
	function load_admin_styles() {
	 	global $post_type;
	    if( "contact-forms-nd" == $post_type || (isset($_GET['page']) && ( $_GET['page'] == "wpcf-extensions" || $_GET['page'] == "wpcf-styling" )) ) {
	        wp_register_style( 'form-builder-css', plugins_url(plugin_basename(dirname(__FILE__)))."/assets/formbuilder/css/form-builder.min.css", true );
	        wp_enqueue_style( 'form-builder-css' );
	        wp_register_style( 'wpcf-nd-css', plugins_url(plugin_basename(dirname(__FILE__)))."/css/admin.css", true, $this->current_version );
	        wp_enqueue_style( 'wpcf-nd-css' );

	        wp_enqueue_style( 'contact-form-ready-jquery-ui', '//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css' );

		}
	}


	private function DS() {
		if(strtoupper(substr(PHP_OS, 0, 3)) == 'WIN') {
			return '\\';
		} else {
			return '/';
			
		}
	}

	public function wpcf_nd_menu_items(){
        add_submenu_page('edit.php?post_type=contact-forms-nd', __('Settings', 'wpcf_nd'), __('Settings', 'wpcf_nd'), 'manage_options', 'wpcf-settings',  array( $this , 'wpcf_settings_page' ) );
        add_submenu_page('edit.php?post_type=contact-forms-nd', __('Styling', 'wpcf_nd'), __('Styling', 'wpcf_nd'), 'manage_options', 'wpcf-styling',  array( $this , 'wpcf_styling_page' ) );
        add_submenu_page('edit.php?post_type=contact-forms-nd', __('Extensions', 'wpcf_nd'), __('Extensions', 'wpcf_nd'), 'manage_options', 'wpcf-extensions',  array( $this , 'wpcf_extensions_page' ) );
	}

	function wpcf_nd_save_settings() {
	}

	function wpcf_extensions_page() {
		?>
		<h1><?php echo __( "Contact Form Ready Extensions", "wpcf_nd" ); ?></h1>
		<div class="wpcf-extension">
			<h3 class="wpcf-extension-title">Stored Submissions</h3>
			<a href="http://www.contactformready.com/extensions/stored-submissions/?utm_source=plugin&amp;utm_medium=link&amp;utm_campaign=storedsub" title="Stored Submissions Extension" target="_BLANK">
				<img width="256" height="256" src="<?php echo plugins_url(plugin_basename(dirname(__FILE__)))."/images/storedsubm.png" ?>" class="attachment-showcase wp-post-image" alt="Stored Submissions" title="Stored Submissions">
			</a>
			<p></p>
			<p></p>
			<div class="wpcf-extension-label-box"></div>
			<p>Price: <em>$4.99 once off</em></p>
			<p>Save all sent form data in WordPress.</p>
			<a href="http://www.contactformready.com/extensions/stored-submissions/?utm_source=plugin&amp;utm_medium=link&amp;utm_campaign=storedsub" title="Stored Submissions Extension" class="button-secondary" target="_BLANK">Get this add-on</a>
		</div>

		<div class="wpcf-extension">
			<h3 class="wpcf-extension-title">MailChimp</h3>
			<a href="http://www.contactformready.com/extensions/mailchimp/?utm_source=plugin&amp;utm_medium=link&amp;utm_campaign=mailchimp" title="Mailchimp Extension" target="_BLANK">
				<img width="256" height="256" src="<?php echo plugins_url(plugin_basename(dirname(__FILE__)))."/images/MailChimp.png" ?>" class="attachment-showcase wp-post-image" alt="MailChimp" title="MailChimp">
			</a>
			<p></p>
			<p></p>
			<div class="wpcf-extension-label-box"></div>
			<p>Price: <em>$4.99 once off</em></p>
			<p>Store submission email addresses to a MailChimp subscriber list</p>			
			<a href="http://www.contactformready.com/extensions/mailchimp/?utm_source=plugin&amp;utm_medium=link&amp;utm_campaign=mailchimp" title="MailChimp Extension" class="button-secondary" target="_BLANK">Get this add-on</a>
		</div>

		<div class="wpcf-extension">
			<h3 class="wpcf-extension-title">Slack Notifications</h3>
			<a href="http://www.contactformready.com/extensions/slack-notifications/?utm_source=plugin&amp;utm_medium=link&amp;utm_campaign=slack" title="Slack Notifications Extension" target="_BLANK">
				<img width="256" height="256" src="<?php echo plugins_url(plugin_basename(dirname(__FILE__)))."/images/Slack.png" ?>" class="attachment-showcase wp-post-image" alt="Slack Notifications" title="Slack Notifications">
			</a>
			<p></p>
			<p></p>
			<div class="wpcf-extension-label-box"></div>
			<p>Price: <em>$4.99 once off</em></p>
			<p>Get notified via Slack when a new submission is received</p>			
			<a href="http://www.contactformready.com/extensions/slack-notifications/?utm_source=plugin&amp;utm_medium=link&amp;utm_campaign=slack" title="Slack Notifications Extension" class="button-secondary" target="_BLANK">Get this add-on</a>
		</div>

		<div class="wpcf-extension">
			<h3 class="wpcf-extension-title">PDF Submissions</h3>
			<a href="http://www.contactformready.com/extensions/pdf-submissions/?utm_source=plugin&amp;utm_medium=link&amp;utm_campaign=pdf" title="PDF Submissions Extension" target="_BLANK">
				<img width="256" height="256" src="<?php echo plugins_url(plugin_basename(dirname(__FILE__)))."/images/pdf.png" ?>" class="attachment-showcase wp-post-image" alt="PDF Submissions" title="PDF Submissions">
			</a>
			<p></p>
			<p></p>
			<div class="wpcf-extension-label-box"></div>
			<p>Price: <em>$4.99 once off</em></p>
			<p>Store all sent form data in a PDF file</p>			
			<a href="http://www.contactformready.com/extensions/pdf-submissions/?utm_source=plugin&amp;utm_medium=link&amp;utm_campaign=pdf" title="PDF Submissions" class="button-secondary" target="_BLANK">Get this add-on</a>
		</div>

		<div class="wpcf-extension">
			<h3 class="wpcf-extension-title">ClickSend Text Notifications</h3>
			<a href="http://www.contactformready.com/extensions/clicksend-text-notifications/?utm_source=plugin&amp;utm_medium=link&amp;utm_campaign=clicksend" title="ClickSend Text Notifications Extension" target="_BLANK">
				<img width="256" height="256" src="<?php echo plugins_url(plugin_basename(dirname(__FILE__)))."/images/ClickSend.png" ?>" class="attachment-showcase wp-post-image" alt="ClickSend Text Notifications" title="ClickSend Text Notifications">
			</a>
			<p></p>
			<p></p>
			<div class="wpcf-extension-label-box"></div>
			<p>Price: <em>$4.99 once off</em></p>
			<p>Get notified via text message via ClickSend as soon as a new submission is received</p>			
			<a href="http://www.contactformready.com/extensions/clicksend-text-notifications/?utm_source=plugin&amp;utm_medium=link&amp;utm_campaign=clicksend" title="ClickSend Text Notifications" class="button-secondary" target="_BLANK">Get this add-on</a>
		</div>

		<div class="wpcf-extension">
			<h3 class="wpcf-extension-title">BulkSMS Text Notifications</h3>
			<a href="http://www.contactformready.com/extensions/bulksms-text-notifications/?utm_source=plugin&amp;utm_medium=link&amp;utm_campaign=bulksms" title="ClickSend Text Notifications Extension" target="_BLANK">
				<img width="256" height="256" src="<?php echo plugins_url(plugin_basename(dirname(__FILE__)))."/images/BulkSMS.png" ?>" class="attachment-showcase wp-post-image" alt="BulkSMS Text Notifications" title="BulkSMS Text Notifications">
			</a>
			<p></p>
			<p></p>
			<div class="wpcf-extension-label-box"></div>
			<p>Price: <em>$4.99 once off</em></p>
			<p>Get notified via text message via BulkSMS as soon as a new submission is received</p>			
			<a href="http://www.contactformready.com/extensions/bulksms-text-notifications/?utm_source=plugin&amp;utm_medium=link&amp;utm_campaign=bulksms" title="BulkSMS Text Notifications" class="button-secondary" target="_BLANK">Get this add-on</a>
		</div>

		<div class="wpcf-extension">
			<h3 class="wpcf-extension-title">Clickatell Text Notifications</h3>
			<a href="http://www.contactformready.com/extensions/clickatell-text-notifications/?utm_source=plugin&amp;utm_medium=link&amp;utm_campaign=clickatell" title="ClickSend Text Notifications Extension" target="_BLANK">
				<img width="256" height="256" src="<?php echo plugins_url(plugin_basename(dirname(__FILE__)))."/images/ClickaTell.png" ?>" class="attachment-showcase wp-post-image" alt="Clickatell Text Notifications" title="Clickatell Text Notifications">
			</a>
			<p></p>
			<p></p>
			<div class="wpcf-extension-label-box"></div>
			<p>Price: <em>$4.99 once off</em></p>
			<p>Get notified via text message via Clickatell as soon as a new submission is received</p>			
			<a href="http://www.contactformready.com/extensions/clickatell-text-notifications/?utm_source=plugin&amp;utm_medium=link&amp;utm_campaign=clickatell" title="Clickatell Text Notifications" class="button-secondary" target="_BLANK">Get this add-on</a>
		</div>

		<div class="wpcf-extension">
			<h3 class="wpcf-extension-title">Zendesk</h3>
			<a href="http://www.contactformready.com/extensions/zendesk/?utm_source=plugin&amp;utm_medium=link&amp;utm_campaign=zendesk" title="Zendesk Extension" target="_BLANK">
				<img width="256" height="256" src="<?php echo plugins_url(plugin_basename(dirname(__FILE__)))."/images/Zendesk.png" ?>" class="attachment-showcase wp-post-image" alt="Zendesk Extension" title="Zendesk Extension">
			</a>
			<p></p>
			<p></p>
			<div class="wpcf-extension-label-box"></div>
			<p>Price: <em>$4.99 once off</em></p>
			<p>Convert submitted form data into a ticket on Zendesk</p>			
			<a href="http://www.contactformready.com/extensions/zendesk/?utm_source=plugin&amp;utm_medium=link&amp;utm_campaign=zendesk" title="Zendesk Extension" class="button-secondary" target="_BLANK">Get this add-on</a>
		</div>

		<div class="wpcf-extension">
			<h3 class="wpcf-extension-title">Nifty Desk</h3>
			<a href="http://www.contactformready.com/extensions/nifty-desk/?utm_source=plugin&amp;utm_medium=link&amp;utm_campaign=nifty" title="Nifty Desk Extension" target="_BLANK">
				<img width="256" height="256" src="<?php echo plugins_url(plugin_basename(dirname(__FILE__)))."/images/niftyd.png" ?>" class="attachment-showcase wp-post-image" alt="Nifty Desk Extension" title="Nifty Desk Extension">
			</a>
			<p></p>
			<p></p>
			<div class="wpcf-extension-label-box"></div>
			<p>Price: <em>$4.99 once off</em></p>
			<p>Convert submitted form data into a ticket on Nifty Desk</p>			
			<a href="http://www.contactformready.com/extensions/nifty-desk/?utm_source=plugin&amp;utm_medium=link&amp;utm_campaign=nifty" title="Nifty Desk Extension" class="button-secondary" target="_BLANK">Get this add-on</a>
		</div>


		<?php
	}

	function wpcf_styling_page() {
		$wpcf_nd_styling = get_option("wpcf_nd_styling");

		if (!isset($wpcf_nd_styling['wpcf_nd_enable_style']))
			$wpcf_nd_styling['wpcf_nd_enable_style'] = 0;

		if (!isset($wpcf_nd_styling['wpcf_nd_label_font_size']))
			$wpcf_nd_styling['wpcf_nd_label_font_size'] = "16";

		if (!isset($wpcf_nd_styling['wpcf_nd_label_font_weight']))
			$wpcf_nd_styling['wpcf_nd_label_font_weight'] = "600";

		if (!isset($wpcf_nd_styling['wpcf_nd_label_color']))
			$wpcf_nd_styling['wpcf_nd_label_color'] = "#222222";

		if (!isset($wpcf_nd_styling['wpcf_nd_input_bg_color']))
			$wpcf_nd_styling['wpcf_nd_input_bg_color'] = "transparent";

		if (!isset($wpcf_nd_styling['wpcf_nd_input_border_color']))
			$wpcf_nd_styling['wpcf_nd_input_border_color'] = "#222222";

		if (!isset($wpcf_nd_styling['wpcf_nd_input_border_focus_color']))
			$wpcf_nd_styling['wpcf_nd_input_border_focus_color'] = "#333333";

		if (!isset($wpcf_nd_styling['wpcf_nd_input_font_size']))
			$wpcf_nd_styling['wpcf_nd_input_font_size'] = "16";

		if (!isset($wpcf_nd_styling['wpcf_nd_input_font_color']))
			$wpcf_nd_styling['wpcf_nd_input_font_color'] = "#666666";

		if (!isset($wpcf_nd_styling['wpcf_nd_submit_bg_color']))
			$wpcf_nd_styling['wpcf_nd_submit_bg_color'] = "#222222";

		if (!isset($wpcf_nd_styling['wpcf_nd_submit_bg_hover_color']))
			$wpcf_nd_styling['wpcf_nd_submit_bg_hover_color'] = "#666666";

		if (!isset($wpcf_nd_styling['wpcf_nd_submit_font_size']))
			$wpcf_nd_styling['wpcf_nd_submit_font_size'] = "14";

		if (!isset($wpcf_nd_styling['wpcf_nd_submit_font_color']))
			$wpcf_nd_styling['wpcf_nd_submit_font_color'] = "#ffffff";

		if (!isset($wpcf_nd_styling['wpcf_nd_submit_font_weight']))
			$wpcf_nd_styling['wpcf_nd_submit_font_weight'] = "700";

		if (!isset($wpcf_nd_styling['wpcf_nd_submit_text_transform']))
			$wpcf_nd_styling['wpcf_nd_submit_text_transform'] = "none";

		if (isset($_POST['wpcf_submit_save_styling'])) {

			if ( isset( $_POST['wpcf_nd_enable_style'] ) ){
		        $wpcf_nd_styling['wpcf_nd_enable_style'] = 1;
	        } else {
				$wpcf_nd_styling['wpcf_nd_enable_style'] = 0;
			}

            if ( isset( $_POST['wpcf_nd_label_font_size'] ) ){
                $wpcf_nd_styling['wpcf_nd_label_font_size'] = sanitize_text_field( $_POST['wpcf_nd_label_font_size'] );
            } else {
                $wpcf_nd_styling['wpcf_nd_label_font_size'] = '';
            }

	        if ( isset( $_POST['wpcf_nd_label_font_weight'] ) ){
		        $wpcf_nd_styling['wpcf_nd_label_font_weight'] = sanitize_text_field( $_POST['wpcf_nd_label_font_weight'] );
	        } else {
		        $wpcf_nd_styling['wpcf_nd_label_font_weight'] = '';
	        }

	        if ( isset( $_POST['wpcf_nd_label_color'] ) ){
		        $wpcf_nd_styling['wpcf_nd_label_color'] = sanitize_text_field( $_POST['wpcf_nd_label_color'] );
	        } else {
		        $wpcf_nd_styling['wpcf_nd_label_color'] = '';
	        }

			if ( isset( $_POST['wpcf_nd_input_bg_color'] ) ){
				$wpcf_nd_styling['wpcf_nd_input_bg_color'] = sanitize_text_field( $_POST['wpcf_nd_input_bg_color'] );
			} else {
				$wpcf_nd_styling['wpcf_nd_input_bg_color'] = '';
			}

			if ( isset( $_POST['wpcf_nd_input_border_color'] ) ){
				$wpcf_nd_styling['wpcf_nd_input_border_color'] = sanitize_text_field( $_POST['wpcf_nd_input_border_color'] );
			} else {
				$wpcf_nd_styling['wpcf_nd_input_border_color'] = '';
			}

			if ( isset( $_POST['wpcf_nd_input_border_focus_color'] ) ){
				$wpcf_nd_styling['wpcf_nd_input_border_focus_color'] = sanitize_text_field( $_POST['wpcf_nd_input_border_focus_color'] );
			} else {
				$wpcf_nd_styling['wpcf_nd_input_border_focus_color'] = '';
			}

			if ( isset( $_POST['wpcf_nd_input_font_size'] ) ){
				$wpcf_nd_styling['wpcf_nd_input_font_size'] = sanitize_text_field( $_POST['wpcf_nd_input_font_size'] );
			} else {
				$wpcf_nd_styling['wpcf_nd_input_font_size'] = '';
			}

			if ( isset( $_POST['wpcf_nd_input_font_color'] ) ){
				$wpcf_nd_styling['wpcf_nd_input_font_color'] = sanitize_text_field( $_POST['wpcf_nd_input_font_color'] );
			} else {
				$wpcf_nd_styling['wpcf_nd_input_font_color'] = '';
			}

			if ( isset( $_POST['wpcf_nd_submit_bg_color'] ) ){
				$wpcf_nd_styling['wpcf_nd_submit_bg_color'] = sanitize_text_field( $_POST['wpcf_nd_submit_bg_color'] );
			} else {
				$wpcf_nd_styling['wpcf_nd_submit_bg_color'] = '';
			}

			if ( isset( $_POST['wpcf_nd_submit_bg_hover_color'] ) ){
				$wpcf_nd_styling['wpcf_nd_submit_bg_hover_color'] = sanitize_text_field( $_POST['wpcf_nd_submit_bg_hover_color'] );
			} else {
				$wpcf_nd_styling['wpcf_nd_submit_bg_hover_color'] = '';
			}

			if ( isset( $_POST['wpcf_nd_submit_font_size'] ) ){
				$wpcf_nd_styling['wpcf_nd_submit_font_size'] = sanitize_text_field( $_POST['wpcf_nd_submit_font_size'] );
			} else {
				$wpcf_nd_styling['wpcf_nd_submit_font_size'] = '';
			}

			if ( isset( $_POST['wpcf_nd_submit_font_color'] ) ){
				$wpcf_nd_styling['wpcf_nd_submit_font_color'] = sanitize_text_field( $_POST['wpcf_nd_submit_font_color'] );
			} else {
				$wpcf_nd_styling['wpcf_nd_submit_font_color'] = '';
			}

			if ( isset( $_POST['wpcf_nd_submit_font_weight'] ) ){
				$wpcf_nd_styling['wpcf_nd_submit_font_weight'] = sanitize_text_field( $_POST['wpcf_nd_submit_font_weight'] );
			} else {
				$wpcf_nd_styling['wpcf_nd_submit_font_weight'] = '';
			}

			if ( isset( $_POST['wpcf_nd_submit_text_transform'] ) ){
				$wpcf_nd_styling['wpcf_nd_submit_text_transform'] = sanitize_text_field( $_POST['wpcf_nd_submit_text_transform'] );
			} else {
				$wpcf_nd_styling['wpcf_nd_submit_text_transform'] = '';
			}

            $wpcf_nd_styling = apply_filters("wpcf_filter_save_styling", $wpcf_nd_styling, $_POST);

            update_option( "wpcf_nd_styling" , $wpcf_nd_styling );
            echo "<span class='update-nag below-h1'>Styling saved</span>";

        }
        ?>

        <form action='' method='POST' name='wpcf_styling_form'>
            <h1><?php _e("Contact form styling","wpcf_nd"); ?></h1>


            <h2><?php _e("Contact Form Styling","wpcf_nd"); ?></h2>

            <div class="wpcf-admin-enable-style-wrapper">
                <label for="wpcf_nd_enable_style"><?php _e( "Enable custom styling", "wpcf_nd" ); ?></label>
                <input type="checkbox" name="wpcf_nd_enable_style" class="" id="wpcf_nd_enable_style" value="1" <?php checked( $wpcf_nd_styling['wpcf_nd_enable_style'], 1, true ); ?> />
            </div>

            <div class="wpcf-admin-enable-table-wrapper">
                <table class='wp-list-table striped fixed wpcf-admin-table'>
                    <thead>
                    <tr>
                        <th><?php _e( "Label", "wpcf_nd" ); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td width='250'><?php _e("Font size","wpcf_nd"); ?></td>
                        <td><input type='number' name='wpcf_nd_label_font_size' class='regular-text wpcf-nd-small-input' id='wpcf_nd_label_font_size' value='<?php echo$wpcf_nd_styling['wpcf_nd_label_font_size']; ?>' min="10" max="62" /></td>
                    </tr>
                    <tr>
                        <td width='250'><?php _e("Font weight","wpcf_nd"); ?></td>
                        <td>
                            <select name='wpcf_nd_label_font_weight' id='wpcf_nd_label_font_weight'>
                                <option value="300" <?php echo ( '300' === $wpcf_nd_styling['wpcf_nd_label_font_weight'] ) ? 'selected' : ''; ?>>300</option>
                                <option value="400" <?php echo ( '400' === $wpcf_nd_styling['wpcf_nd_label_font_weight'] ) ? 'selected' : ''; ?>>400</option>
                                <option value="600" <?php echo ( '600' === $wpcf_nd_styling['wpcf_nd_label_font_weight'] ) ? 'selected' : ''; ?>>600</option>
                                <option value="700" <?php echo ( '700' === $wpcf_nd_styling['wpcf_nd_label_font_weight'] ) ? 'selected' : ''; ?>>700</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td width='250'><?php _e("Font color","wpcf_nd"); ?></td>
                        <td><input type='text' name='wpcf_nd_label_color' class='regular-text wpcf-nd-small-input' id='wpcf_nd_label_color' value='<?php echo stripslashes(esc_html($wpcf_nd_styling['wpcf_nd_label_color'])); ?>' /></td>
                    </tr>
                    </tbody>
                </table>

                <table class='wp-list-table striped fixed wpcf-admin-table'>
                    <thead>
                        <tr>
                            <th><?php _e( "Input", "wpcf_nd" ); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td width='250'><?php _e("Background color","wpcf_nd"); ?></td>
                            <td><input type='text' name='wpcf_nd_input_bg_color' class='regular-text wpcf-nd-small-input' id='wpcf_nd_input_bg_color' value='<?php echo stripslashes(esc_html($wpcf_nd_styling['wpcf_nd_input_bg_color'])); ?>' /></td>
                        </tr>
                        <tr>
                            <td width='250'><?php _e("Border color","wpcf_nd"); ?></td>
                            <td><input type='text' name='wpcf_nd_input_border_color' class='regular-text wpcf-nd-small-input' id='wpcf_nd_input_border_color' value='<?php echo stripslashes(esc_html($wpcf_nd_styling['wpcf_nd_input_border_color'])); ?>' /></td>
                        </tr>
                        <tr>
                            <td width='250'><?php _e("Border hover color","wpcf_nd"); ?></td>
                            <td><input type='text' name='wpcf_nd_input_border_focus_color' class='regular-text wpcf-nd-small-input' id='wpcf_nd_input_border_focus_color' value='<?php echo stripslashes(esc_html($wpcf_nd_styling['wpcf_nd_input_border_focus_color'])); ?>' /></td>
                        </tr>
                        <tr>
                            <td width='250'><?php _e("Font size","wpcf_nd"); ?></td>
                            <td><input type='number' name='wpcf_nd_input_font_size' class='regular-text wpcf-nd-small-input' id='wpcf_nd_input_font_size' value='<?php echo stripslashes(esc_html($wpcf_nd_styling['wpcf_nd_input_font_size'])); ?>' min="10" max="62" /></td>
                        </tr>
                        <tr>
                            <td width='250'><?php _e("Font color","wpcf_nd"); ?></td>
                            <td><input type='text' name='wpcf_nd_input_font_color' class='regular-text wpcf-nd-small-input' id='wpcf_nd_input_font_color' value='<?php echo stripslashes(esc_html($wpcf_nd_styling['wpcf_nd_input_font_color'])); ?>' /></td>
                        </tr>
                    </tbody>
                </table>

                <table class='wp-list-table striped fixed wpcf-admin-table'>
                    <thead>
                    <tr>
                        <th><?php _e( "Submit button", "wpcf_nd" ); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td width='250'><?php _e("Background color","wpcf_nd"); ?></td>
                        <td><input type='text' name='wpcf_nd_submit_bg_color' class='regular-text wpcf-nd-small-input' id='wpcf_nd_submit_bg_color' value='<?php echo stripslashes(esc_html($wpcf_nd_styling['wpcf_nd_submit_bg_color'])); ?>' /></td>
                    </tr>
                    <tr>
                        <td width='250'><?php _e("Background hover color","wpcf_nd"); ?></td>
                        <td><input type='text' name='wpcf_nd_submit_bg_hover_color' class='regular-text wpcf-nd-small-input' id='wpcf_nd_submit_bg_hover_color' value='<?php echo stripslashes(esc_html($wpcf_nd_styling['wpcf_nd_submit_bg_hover_color'])); ?>' /></td>
                    </tr>
                    <tr>
                        <td width='250'><?php _e("Font size","wpcf_nd"); ?></td>
                        <td><input type='number' name='wpcf_nd_submit_font_size' class='regular-text wpcf-nd-small-input' id='wpcf_nd_submit_font_size' value='<?php echo stripslashes(esc_html($wpcf_nd_styling['wpcf_nd_submit_font_size'])); ?>' min="10" max="62" /></td>
                    </tr>
                    <tr>
                        <td width='250'><?php _e("Font color","wpcf_nd"); ?></td>
                        <td><input type='text' name='wpcf_nd_submit_font_color' class='regular-text wpcf-nd-small-input' id='wpcf_nd_submit_font_color' value='<?php echo stripslashes(esc_html($wpcf_nd_styling['wpcf_nd_submit_font_color'])); ?>' /></td>
                    </tr>
                    <tr>
                        <td width='250'><?php _e("Font weight","wpcf_nd"); ?></td>
                        <td>
                            <select name='wpcf_nd_submit_font_weight' id='wpcf_nd_submit_font_weight'>
                                <option value="300" <?php echo ( '300' === $wpcf_nd_styling['wpcf_nd_submit_font_weight'] ) ? 'selected' : ''; ?>>300</option>
                                <option value="400" <?php echo ( '400' === $wpcf_nd_styling['wpcf_nd_submit_font_weight'] ) ? 'selected' : ''; ?>>400</option>
                                <option value="600" <?php echo ( '600' === $wpcf_nd_styling['wpcf_nd_submit_font_weight'] ) ? 'selected' : ''; ?>>600</option>
                                <option value="700" <?php echo ( '700' === $wpcf_nd_styling['wpcf_nd_submit_font_weight'] ) ? 'selected' : ''; ?>>700</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td width='250'><?php _e("Text transform","wpcf_nd"); ?></td>
                        <td>
                            <select name='wpcf_nd_submit_text_transform' id='wpcf_nd_submit_text_transform'>
                                <option value="none" <?php echo ( 'normal' === $wpcf_nd_styling['wpcf_nd_submit_text_transform'] ) ? 'selected' : ''; ?>><?php _e( "none", "wpcf_nd" ); ?></option>
                                <option value="uppercase" <?php echo ( 'uppercase' === $wpcf_nd_styling['wpcf_nd_submit_text_transform'] ) ? 'selected' : ''; ?>><?php _e( "uppercase", "wpcf_nd" ); ?></option>
                            </select>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <?php do_action( "wpcf_hook_styling_page_bottom", $wpcf_nd_styling ); ?>

            <input class="wpcf-submit-save-styling" type='submit' value='Save styling' name='wpcf_submit_save_styling' />
        </form>

    <?php
	}

	function wpcf_settings_page() {
		$wpcf_nd_settings = get_option("wpcf_nd_settings");


		if (isset($_GET['page']) && $_GET['page'] == 'wpcf-settings' && isset($_GET['action']) && $_GET['action'] == 'wpcf_nd_welcome') {

	        if (class_exists("APC_Object_Cache")) {
	            /* do nothing here as this caches the "first time" option and the welcome page just loads over and over again. quite annoying really... */
	        }  else { 
	            update_option('nifty-bu-first-time', true);
	            include('templates/welcome.php');
	        }
        

		} else {
			if (isset($_POST['wpcf_submit_save_settings'])) {

				if ( isset( $_POST['wpcf_nd_email_from_address'] ) ){
					$wpcf_nd_settings['wpcf_nd_email_from_address'] = sanitize_text_field( $_POST['wpcf_nd_email_from_address'] ); 					
				} else {
					$wpcf_nd_settings['wpcf_nd_email_from_address'] = ''; 
				}


				if ( isset( $_POST['wpcf_nd_email_from_name'] ) ){
					$wpcf_nd_settings['wpcf_nd_email_from_name'] = sanitize_text_field( $_POST['wpcf_nd_email_from_name'] ); 					
				} else {
					$wpcf_nd_settings['wpcf_nd_email_from_name'] = ''; 
				}


				if (isset($_POST['wpcf_nd_thank_you_text'])) 
					$wpcf_nd_settings['wpcf_nd_thank_you_text'] = sanitize_text_field( $_POST['wpcf_nd_thank_you_text'] );


				$wpcf_nd_settings = apply_filters("wpcf_filter_save_settings", $wpcf_nd_settings, $_POST);
				
				update_option( "wpcf_nd_settings" , $wpcf_nd_settings );
				echo "<span class='update-nag below-h1'>Settings saved</span>";

			}


			if (!isset($wpcf_nd_settings['wpcf_nd_email_from_address']))
				$wpcf_nd_settings['wpcf_nd_email_from_address'] = get_option( 'admin_email' ); 

			if (!isset($wpcf_nd_settings['wpcf_nd_email_from_name']))
				$wpcf_nd_settings['wpcf_nd_email_from_name'] = get_option( 'blogname' ); 



			?>

			<form action='' method='POST' name='wpcf_settings_form'>
				<h1><?php _e("Contact form settings","wpcf_nd"); ?></h1>
				

				<h2><?php _e("Contact Form Settings","wpcf_nd"); ?></h2>

				<table class='wp-list-table widefat striped fixed'>
					<tr>
						<td width='250'><?php _e("Email from address","wpcf_nd"); ?></td>
						<td><input type='text' name='wpcf_nd_email_from_address' class='regular-text' id='wpcf_nd_email_from_address' value='<?php echo$wpcf_nd_settings['wpcf_nd_email_from_address']; ?>' /></td>
					</tr>
					<tr>
						<td width='250'><?php _e("Email from name","wpcf_nd"); ?></td>
						<td><input type='text' name='wpcf_nd_email_from_name' class='regular-text' id='wpcf_nd_email_from_name' value='<?php echo $wpcf_nd_settings['wpcf_nd_email_from_name']; ?>' /></td>
					</tr>
					<tr>
						<td width='250'><?php _e("Thank you text","wpcf_nd"); ?></td>
						<td><input type='text' name='wpcf_nd_thank_you_text' class='regular-text' id='wpcf_nd_thank_you_text' value='<?php echo stripslashes(esc_html($wpcf_nd_settings['wpcf_nd_thank_you_text'])); ?>' /></td>
					</tr>
				</table>
				
				<?php do_action( "wpcf_hook_settings_page_bottom", $wpcf_nd_settings ); ?>

				<input type='submit' value='Save settings' name='wpcf_submit_save_settings' />
			</form>




			<?php
		}
	}

	function wpcf_nd_email_wrapper_control($data) {
		$wpcf_nd_settings = get_option("wpcf_nd_settings");
		if (!isset($wpcf_nd_settings['wpcf_nd_template_html'])) {
			$wpcf_nd_settings['wpcf_nd_template_html'] = wpcf_get_default_template_html();
		}


		$template_content_template = stripslashes( $wpcf_nd_settings['wpcf_nd_template_html'] );


		//$dir = dirname(__FILE__);
		//$template_content_template = file_get_contents($dir."/templates/mail_template.html");

		$template_content_template = str_replace("{header}",$data['header'],$template_content_template);
		$template_content_template = str_replace("{message}",$data['message'],$template_content_template);
		$template_content_template = str_replace("{footer}",$data['footer'],$template_content_template);
		$template_content_template = str_replace("{logo}",$data['logo'],$template_content_template);

		return $template_content_template;



	}
	function set_custom_edit_columns($columns) {


	    $columns['shortcode'] = __( 'Shortcode', 'wpcf_nd' );
	    $columns['views'] = __( 'Views', 'wpcf_nd' );
	    $columns['submissions'] = __( 'Submissions', 'wpcf_nd' );
    
	    return $columns;
	}

	function custom_column( $column, $post_id ) {
	    switch ( $column ) {

	        case 'shortcode' :
	            echo '<pre>[cform-nd id="'.$post_id.'"]</pre>';
	            break;
	        case 'views' :
	            $views = intval(get_post_meta( $post_id , 'cform_views' , true )); 
	            echo $views;
	            break;
	        case 'submissions' :
	            $views = intval(get_post_meta( $post_id , 'cform_submissions' , true )); 
	            echo $views;
	            break;


	    }
	}

	/**
	 * Enqueue user styles on the front end
	 * 
	 * @return void
	 */
	public static function enqueue_user_styles() {
        wp_register_style( 'contact-form-ready', plugins_url(plugin_basename(dirname(__FILE__)))."/css/front-end.css", true );
        wp_enqueue_style( 'contact-form-ready' );

		$wpcf_nd_styling = get_option("wpcf_nd_styling");

		if ( 0 === $wpcf_nd_styling['wpcf_nd_enable_style'] ) {
			return;
		}

		$css = '';
		if (
			$wpcf_nd_styling['wpcf_nd_label_font_size'] !== '16' ||
			$wpcf_nd_styling['wpcf_nd_label_font_weight'] !== '600' ||
			$wpcf_nd_styling['wpcf_nd_label_color'] !== '#222222'
        ) {
			$css .= ".wpcf_nd label {";
			if ( $wpcf_nd_styling['wpcf_nd_label_font_size'] !== '16' ) {
				$css .= "font-size: " . esc_attr( $wpcf_nd_styling['wpcf_nd_label_font_size'] ) . "px;";
			}
			if ( $wpcf_nd_styling['wpcf_nd_label_font_weight'] !== '600' ) {
				$css .= "font-weight: " . esc_attr( $wpcf_nd_styling['wpcf_nd_label_font_weight'] ) . ";";
			}
			if ( $wpcf_nd_styling['wpcf_nd_label_color'] !== '#222222' ) {
				$css .= "color: " . esc_attr( $wpcf_nd_styling['wpcf_nd_label_color'] ) . ";";
			}
			$css .= "}";
		}

		if (
			$wpcf_nd_styling['wpcf_nd_input_bg_color'] !== 'transparent' ||
			$wpcf_nd_styling['wpcf_nd_input_border_color'] !== '#222222' ||
			$wpcf_nd_styling['wpcf_nd_input_border_focus_color'] !== '#333333' ||
			$wpcf_nd_styling['wpcf_nd_input_font_size'] !== '16' ||
			$wpcf_nd_styling['wpcf_nd_input_font_color'] !== '#666666'
		) {
			$css .= ".wpcf_nd input[type='text'],.wpcf_nd textarea,.wpcf_nd input[type='email'],.wpcf_nd input[type='number'],.wpcf_nd input[type='date'] {";
			if ( $wpcf_nd_styling['wpcf_nd_input_bg_color'] !== 'transparent' ) {
				$css .= "background-color: " . esc_attr( $wpcf_nd_styling['wpcf_nd_input_bg_color'] ) . ";";
			}
			if ( $wpcf_nd_styling['wpcf_nd_input_border_color'] !== '#222222' ) {
				$css .= "border-color: " . esc_attr( $wpcf_nd_styling['wpcf_nd_input_border_color'] ) . ";";
			}
			if ( $wpcf_nd_styling['wpcf_nd_input_font_size'] !== '16' ) {
				$css .= "font-size: " . esc_attr( $wpcf_nd_styling['wpcf_nd_input_font_size'] ) . "px;";
			}
			if ( $wpcf_nd_styling['wpcf_nd_input_border_color'] !== '#222222' ) {
				$css .= "color: " . esc_attr( $wpcf_nd_styling['wpcf_nd_input_border_color'] ) . ";";
			}
			$css .= "}";
		}

		if ( $wpcf_nd_styling['wpcf_nd_input_border_focus_color'] !== '#333333' ) {
			$css .= ".wpcf_nd input[type='text']:focus,.wpcf_nd textarea:focus,.wpcf_nd input[type='email']:focus,.wpcf_nd input[type='number']:focus,.wpcf_nd input[type='date']:focus {border-color: " . esc_attr( $wpcf_nd_styling['wpcf_nd_input_border_focus_color'] ) . ";}";
		}

		if (
			$wpcf_nd_styling['wpcf_nd_submit_bg_color'] !== '#222222' ||
			$wpcf_nd_styling['wpcf_nd_submit_font_size'] !== '14' ||
			$wpcf_nd_styling['wpcf_nd_submit_font_color'] !== '#ffffff' ||
			$wpcf_nd_styling['wpcf_nd_submit_font_weight'] !== '600' ||
			$wpcf_nd_styling['wpcf_nd_submit_text_transform'] !== 'none'
		) {
			$css .= ".wpcf_nd .wpcf_nd_submit {";
			if ( $wpcf_nd_styling['wpcf_nd_submit_bg_color'] !== '#222222' ) {
				$css .= "background-color: " . esc_attr( $wpcf_nd_styling['wpcf_nd_submit_bg_color'] ) . ";";
			}
			if ( $wpcf_nd_styling['wpcf_nd_submit_font_size'] !== '14' ) {
				$css .= "font-size: " . esc_attr( $wpcf_nd_styling['wpcf_nd_submit_font_size'] ) . "px;";
			}
			if ( $wpcf_nd_styling['wpcf_nd_submit_font_color'] !== '#ffffff' ) {
				$css .= "color: " . esc_attr( $wpcf_nd_styling['wpcf_nd_submit_font_color'] ) . ";";
			}
			if ( $wpcf_nd_styling['wpcf_nd_submit_font_weight'] !== '600' ) {
				$css .= "font-weight: " . esc_attr( $wpcf_nd_styling['wpcf_nd_submit_font_weight'] ) . ";";
			}
			if ( $wpcf_nd_styling['wpcf_nd_submit_text_transform'] !== 'none' ) {
				$css .= "text-transform: " . esc_attr( $wpcf_nd_styling['wpcf_nd_submit_text_transform'] ) . ";";
			}
			$css .= "}";
		}

		if ( $wpcf_nd_styling['wpcf_nd_submit_bg_hover_color'] !== '#666666' ) {
			$css .= ".wpcf_nd .wpcf_nd_submit:hover {background-color: " . esc_attr( $wpcf_nd_styling['wpcf_nd_submit_bg_color'] ) . ";}";
		}

		if ( '' !== $css ) {
			wp_add_inline_style( 'contact-form-ready', $css );
		}
	}

	/**
	 * Shortcode handler
	 * 
	 * @param  array $atts 	Shortcode attributes
	 * @return string 		Shortcode output
	 */
	function wpcf_tag( $atts ) {
		
		if (isset($atts['id'])) {

	    	global $wpcf_thank_you;
	    	global $wpcf_error_message;
	    	if ($wpcf_thank_you) { 

    			return "<div class='wpcf-nd-thank-you'>".stripslashes(esc_html($wpcf_thank_you))."</div>";

			} else {

				$this->enqueue_user_styles();

				$this->increase_views(intval($atts['id']));

				$html_data = get_post_meta( $atts['id'], 'wpcf_nd_html_data' , true );
				$send_to = get_post_meta( $atts['id'], 'wpcf_nd_send_to' , true );
				$form_type = get_post_meta( $atts['id'], 'wpcf_nd_submit_type' , true );
				if (!$form_type || $form_type === null) {
					$form_type = '0';
				}

				wp_localize_script( 'contact-form-ready', 'wpcf_nd_form_type', $form_type );

				$wpcf_nd_redirect_uri = get_post_meta( $atts['id'], 'wpcf_nd_redirect_uri', true );
		    	if ($wpcf_nd_redirect_uri) {
		    		wp_localize_script( 'contact-form-ready', 'wpcf_nd_form_redirect', get_option("siteurl") . $wpcf_nd_redirect_uri );
		    	}

				$submit_string = get_post_meta( $atts['id'], 'wpcf_nd_submit_string' , true );
				if (!$submit_string || $submit_string === null) {
					$submit_string = __("Send","wpcf_nd");
				}

				$theme = get_post_meta( $atts['id'], 'wpcf_nd_theme', true );

				$data = array(
					"cfid" => $atts['id'],
					"submit_string" => $submit_string,
					"sendto" => $send_to,
                    "theme" => $theme
				);

				$js_overrides = apply_filters( "wpcf_js_overrides_front_end", "" );

			    $html_data = str_replace(array("\r", "\n"), '', $html_data);
				$html_data = apply_filters( "wpcf_nd_html_control" , $html_data, $data );
				$style = '<style>.form-control { clear: both; display: block; }</style>';

				$other_data = '';
				if ($wpcf_error_message) 
					$other_data = "<div class='wpcf-nd-error-message'>".$wpcf_error_message."</div>";

				return $style.$other_data.do_shortcode($html_data);
			}
		}

	}

	function wpcf_nd_filter_control_html_control( $html_data, $data ) {
		$random_identifier = rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9);
		$theme = 'wpcf_nd--' . $data['theme'];
		$form_start = '<div class="wpcf_wrapper">'.PHP_EOL;
		$form_start .= '<form action="" method="POST" name="wpcf_nd" id="wpcf_nd" class="wpcf_nd wpcf_nd_'.$random_identifier.' '. $theme .'" cfid="'.$random_identifier.'">'.PHP_EOL;
		$id_string = "			<input type='hidden' value='".esc_attr($data['cfid'])."' name='wpcf_nd_send_id' id='wpcf_nd_send_id' />".PHP_EOL;
		$nonce_string = '			'.wp_nonce_field( 'wpcf_nd', 'wpcf_nonce_field', false, false ).PHP_EOL;

		$other_data = apply_filters( "wpcf_filter_other_form_data_frontend", "", $random_identifier, $data );
		$submit_button_status = apply_filters( "wpcf_filter_submit_button_initial_status", "" );
		$submit_string = PHP_EOL."<p><input type='submit' ".$submit_button_status." value='".esc_attr($data['submit_string'])."' name='wpcf_nd_submit' id='wpcf_nd_submit' class='wpcf_nd_submit wpcf_nd_submit_".$random_identifier."' cfid='".$random_identifier."' /></p>".PHP_EOL;

		$form_end = '</form>'.PHP_EOL;
		$form_end .= '</div>'.PHP_EOL;
		return $form_start.$id_string.$nonce_string.$html_data.$other_data.$submit_string.$form_end;
	}




	function wpcf_nd_view_message_welcome( $content ) {
		$classes = 'welcome-block';

		$check = get_user_meta( get_current_user_id(), 'wpcf_nd_hide_welcome_block', true );
		if ( strlen($check) == 0 ) {  

	?>
	<div id="welcome-block" class="<?php echo esc_attr( $classes ); ?>">
		<?php wp_nonce_field( 'wpcf7-welcome-block-nonce', 'welcomepanelnonce', false ); ?>
		<a class="welcome-block-close" href="#"><?php echo esc_html( __( 'Dismiss', 'wpcf_nd' ) ); ?></a>

		<div class="welcome-block-content">
			<div class="welcome-block-column-container">
				<div class="welcome-block-column">
					<h3><?php echo _e( 'Welcome to Contact Form Ready', 'wpcf_nd' ); ?></h3>
					<p class="message"><?php echo __( "Thank you for installing Contact Form Ready! We are constantly updating and improving our plugin. Please vote for the feature you would like to see next by using the voting feature to the right.", 'wpcf_nd' ); ?></p>
					<p><a href='<?php echo admin_url("post-new.php?post_type=contact-forms-nd"); ?>' class='button button-primary'><?php echo __( "Create a form now", "wpcf_nd" ); ?></a>
				</div>

				<div class="welcome-block-column">
					<h3><?php echo __( 'Getting Started', 'wpcf_nd' ); ?></h3>
					<ul>
						<li><a href='http://www.contactformready.com/news/contact-form-ready-launched/?utm_source=plugin&utm_medium=click&utm_campaign=intro' target='_BLANK'><?php echo __('Introduction', 'wpcf_nd'); ?></a></li>
						<li><a href='http://www.contactformready.com/documentation/creating-your-first-contact-form/?utm_source=plugin&utm_medium=click&utm_campaign=welcome_create_first' target='_BLANK'><?php echo __('Creating your first contact form', 'wpcf_nd'); ?></a></li>
						<li><a href='http://www.contactformready.com/documentation/adding-recaptcha-to-your-contact-form/?utm_source=plugin&utm_medium=click&utm_campaign=recaptcha' target='_BLANK'><?php echo __('Adding reCAPTCHA to your form', 'wpcf_nd'); ?></a></li>
						<li>&nbsp;</li>
						<li><a href='<?php echo admin_url( 'edit.php?post_type=contact-forms-nd&page=wpcf-extensions' ); ?>'><?php echo __('Browse CFR Extensions', 'wpcf_nd'); ?></a></li>
					</ul>
					<p>&nbsp;</p>
					<h3><?php echo __( 'Need help?', 'wpcf_nd' ); ?></h3>
					<ul>
						<li><a href='http://www.contactformready.com/contact-me/?utm_source=plugin&utm_medium=click&utm_campaign=needhelp' target='_BLANK'><?php echo __('Get help now', 'wpcf_nd'); ?></a></li>
						<li></li>
					</ul>
				</div>

				<div class="welcome-block-column">
					<h3><?php echo esc_html( __( 'Vote for the features you want next!', 'wpcf_nd' ) ); ?></h3>
					<?php
     
			
					$wpcf_close_welcome_nonce = wp_create_nonce("wpcf_close_welcome");

				

				?>
				
					<script>

						jQuery(document).ready(function() {


							jQuery(document).on( "click", ".welcome-block-close", function() {
								jQuery("#welcome-block").toggle();
								var data = {
									'security' : '<?php echo $wpcf_close_welcome_nonce; ?>',
									'action': 'wpcf_close_welcome'
								}
								jQuery.post( ajaxurl, data, function(response){});
							})

							jQuery(document).on( "click", ".wpcf_nd_vote", function() {

								var feature = jQuery(this).attr('feature');
								var currentvotes = jQuery(".wpcf_vote_count_"+feature).html();
								currentvotes++;
								var data = {
									'vote_pull' : 'vote',
									'feature' : feature,
									'action': 'cast_vote'
								}
								jQuery(this).before('<em>Thank you!</em>');
								jQuery(this).toggle();
								jQuery(".wpcf_vote_count_"+feature).html(currentvotes);


								jQuery.post( 'http://contactformready.com/apif/vote_pull.php', data, function(response){
									
									
								});

							}); 

							var data = {
								'vote_pull' : 'vote_pull',
								'action': 'wpcf_nd_votes'
							}


							jQuery.post( 'http://contactformready.com/apif/vote_pull.php', data, function(response){
								response = JSON.parse(response);
								ret_html = '<table><thead><th>Feature</th><th></th><th></th></thead>';
								for (var key in response) {
								  if (response.hasOwnProperty(key)) {
								    current_feature = response[key];
								    if (typeof current_feature.title !== "undefined") {

								    	ret_html = ret_html+'<tr><td>'+current_feature.title+'</td><td><span class="wpcf_vote_count_'+current_feature.featureid+'">'+current_feature.votes + '</span> ' +(current_feature.votes > 1 ? 'votes' : 'vote &nbsp;' ) + '</td><td><a href="javascript:void(0);" class="button button-secondary wpcf_nd_vote dashicons dashicons-thumbs-up" feature="'+current_feature.featureid+'"> </a></td>';
								    }
								  }
								}
								jQuery(".wpcf_nd_vote_wrapper").html(ret_html);
							});
								
						});

					</script>

					<div class='wpcf_nd_vote_wrapper'>

						<img src='<?php echo plugins_url('/images/ajax-loader.gif', __FILE__); ?>' width='20' />

					<?php    


					?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php
	} 
	return $content;
	}

	function send_to_integrations( $cfid, $body, $data ) {
		$array = array(
			'cfid' => $cfid,
			'body' => $body,
			'data' => $data,
		);
		do_action( "wpcf_send_integrations" ,$array);


	
	}

}

$contact_form_nd = new WP_Contact_Form_ND();