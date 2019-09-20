<?php 
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="about-wrap">
    <h1 style='margin-right: 0;'><?php _e( "Welcome to Contact Form Ready", "wpcf_nd" ); ?></h1>

    <div class="about-text"><?php _e( "Contact Form Ready is the easiest to use drag and drop contact form builder, with amazing features.", "wpcf_nd" ); ?></div>

    <a class="button-primary" style='padding:5px; padding-right:15px; padding-left:15px; height:inherit;'
       href="edit.php?post_type=contact-forms-nd"><?php echo __( "Skip intro and create new form", "wpcf_nd" ); ?></a>

	<?php

	if ( isset( $_GET['action'] ) && $_GET['action'] === 'wpcf_nd_welcome' ) {
		$welcome_active = 'nav-tab-active';
		$credits_active = '';
	} else if ( isset( $_GET['action'] ) && $_GET['action'] == 'wpcf_nd_credits' ) {
        $credits_active = 'nav-tab-active';
        $welcome_active = '';
	}

	?>
    <h2 class="nav-tab-wrapper wp-clearfix" style="margin-top: 40px;">
        <a href="admin.php?page=wpcf-settings&action=wpcf_nd_welcome"
           class="nav-tab <?php echo $welcome_active; ?>"><?php _e( "Welcome", "wpcf_nd" ); ?></a>
        <a href="admin.php?page=wpcf-settings&action=wpcf_nd_credits"
           class="nav-tab <?php echo $credits_active; ?>"><?php _e( "Credits", "wpcf_nd" ); ?></a>

    </h2>
	<?php if ( isset( $_GET['action'] ) && $_GET['action'] == 'wpcf_nd_welcome' ) { ?>

        <div class="welcome-block-column">
            <h3><?php echo __( 'Getting Started', 'wpcf_nd' ); ?></h3>
            <ul>
                <li>
                    <a href='http://www.contactformready.com/news/contact-form-ready-launched/?utm_source=plugin&utm_medium=click&utm_campaign=intro'
                       target='_BLANK'><?php echo __( 'Introduction', 'wpcf_nd' ); ?></a></li>
                <li>
                    <a href='http://www.contactformready.com/documentation/creating-your-first-contact-form/?utm_source=plugin&utm_medium=click&utm_campaign=welcome_create_first'
                       target='_BLANK'><?php echo __( 'Creating your first contact form', 'wpcf_nd' ); ?></a></li>
                <li>
                    <a href='http://www.contactformready.com/documentation/adding-recaptcha-to-your-contact-form/?utm_source=plugin&utm_medium=click&utm_campaign=recaptcha'
                       target='_BLANK'><?php echo __( 'Adding reCAPTCHA to your form', 'wpcf_nd' ); ?></a></li>
                <li>&nbsp;</li>
                <li>
                    <a href='<?php echo admin_url( 'edit.php?post_type=contact-forms-nd&page=wpcf-extensions' ); ?>'><?php echo __( 'Browse CFR Extensions', 'wpcf_nd' ); ?></a>
                </li>
            </ul>
            <p>&nbsp;</p>
            <h3><?php echo __( 'Need help?', 'wpcf_nd' ); ?></h3>
            <ul>
                <li>
                    <a href='http://www.contactformready.com/contact-me/?utm_source=plugin&utm_medium=click&utm_campaign=needhelp'
                       target='_BLANK'><?php echo __( 'Get help now', 'wpcf_nd' ); ?></a></li>
                <li></li>
            </ul>
        </div>

        <div class="welcome-block-column">
            <h3 style="font-size: 1.4em;"><?php _e("How did you find us?","sola"); ?></h3>
            <form method="post" class="welcome-block--form" name="wpcf_nd_find_us_form" style="font-size: 16px;">
            <div  style="text-align: left; width:275px;">
                    <p>
                <input type="radio" name="wpcf_nd_find_us" id="wordpress" value='repository'>
                <label for="wordpress">
                    <?php _e('WordPress.org plugin repository ', 'wpcf_nd'); ?>
                        </label><br/>
                <input type='text' placeholder="<?php _e('Search Term', 'wpcf_nd'); ?>" name='wpcf_nd_nl_search_term' style='margin-top:5px; margin-left: 23px; width: 100%  '>
                    </p>
                    <p>
                <input type="radio" name="wpcf_nd_find_us" id="search_engine" value='search_engine'>
                <label for="search_engine">
                    <?php _e('Google or other search Engine', 'wpcf_nd'); ?>
                </label>
                    </p>
                    <p>
                <input type="radio" name="wpcf_nd_find_us" id="friend" value='friend'>
                <label for='friend'>
                    <?php _e('Friend recommendation', 'wpcf_nd'); ?>
                </label>
                    </p>
                    <p>
                <input type="radio" name="wpcf_nd_find_us" id='other' value='other'>
                <label for='other'>
                    <?php _e('Other', 'wpcf_nd'); ?>
                </label>
                <textarea placeholder="<?php _e('Please Explain', 'wpcf_nd'); ?>" style='margin-top:5px; margin-left: 23px; width: 100%' name='wpcf_nd_nl_findus_other_url'></textarea>
                    </p>
            </div>
            <div>
                
            </div>
            <div>
                
            </div>
            <div style='margin-top: 20px;'>
                    <button name='action' value='wpcf_nd_submit_find_us' class="button-primary" style="height: inherit;margin-bottom: 10px;padding: 5px;padding-right: 15px;padding-left: 15px;"><?php _e('Submit', 'wpcf_nd'); ?></button>
            </div>
        </form> 
            <style>
                .welcome-block--form p { margin-top: 0; margin-bottom: 10px; font-size: 15px; }
            </style>
</div>
	<?php } elseif ( isset( $_GET['action'] ) && $_GET['action'] == 'wpcf_nd_credits' ) { ?>
        <p class="about-description"><?php _e( "Contact Form Support is supported by an international team of developers.", "wpcf_nd" ); ?></p>
        <ul class="wp-people-group " id="wp-people-group-project-leaders">

            <li class="wp-person" id="wp-person-nickduncan">
                <a href="https://padmiserofiles.wordpress.org/nickduncan/" target="_BLANK" class="web"><img
                            src="https://avatars3.githubusercontent.com/u/16645118?s=460&v=4"
                            srcset="https://avatars3.githubusercontent.com/u/16645118?s=460&v=4" class="gravatar"
                            alt="">
                    Nick Duncan</a>
                <span class="title"><?php _e( "Founder", "wpcf_nd" ); ?>, <?php _e( "Developer", "wpcf_nd" ); ?>
                    , <?php _e( "Support", "wpcf_nd" ); ?></span>
            </li>
            <li class="wp-person" id="wp-person-jarek">
                <a href="https://github.com/JarekCodeCabin" target="_BLANK" class="web"><img
                            src="https://avatars3.githubusercontent.com/u/25925938?s=460&v=4"
                            srcset="https://avatars3.githubusercontent.com/u/25925938?s=460&v=4 2x" class="gravatar"
                            alt="">
                    Jarek Kacprzak</a>
                <span class="title"><?php _e( "Project Manager", "wpcf_nd" ); ?>, <?php _e( "Developer", "wpcf_nd" ); ?>, <?php _e( "Support", "wpcf_nd" ); ?>
                    , <?php _e( "Testing", "wpcf_nd" ); ?></span>
            </li>
        </ul>
        <a class="button-primary" style='padding:5px; padding-right: 15px; padding-left: 15px; height:inherit;'
           href="edit.php?post_type=contact-forms-nd"><?php echo __( "OK! Let's start", "wpcf_nd" ); ?></a>
	<?php } ?>
</div>
<script>
    jQuery('.wp-heading-inline, .wrap > a').css('display', 'none');
    jQuery('.about-wrap').parent('.wrap').addClass('wrap-welcome');
</script>
<style>
    .about-wrap .about-text { min-height: 40px; }
    .wrap-welcome #posts-filter { display: none; }
    .welcome-block-column { width: 50%; float: left; }
    .wpcf_nd_vote_wrapper table { width: 100%; }
    .wpcf_nd_vote_wrapper table tbody tr:nth-child(odd) { background-color: #e8e8e8; }
    .wpcf_nd_vote_wrapper table tbody td { padding: 5px; }
    .wpcf_nd_vote_wrapper table thead th { text-align: left; padding: 5px; }
</style>