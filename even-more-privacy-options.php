<?
/*
Plugin Name: Even more privacy options
Description: Modifies behaviour of More Privacy Options and Private Feed Keys plugins in multiple ways.
Version: 1.0
Author: Zaantar
Author URI: http://zaantar.eu
Donate Link: http://zaantar.eu/index.php?page=Donate
*/
 
/* additional tweaks of More Privacy Options
	- replace "mail" function by "wp_mail" in "ds_mail_super_admin" function (optional)
	- replace "wp_login_url()"
	  with "apply_filters( 'ds_wp3_private_blog_login_url', wp_login_url() )"
	  in "ds_users_authenticator", "ds_members_authenticator" and "ds_admins_authenticator" functions
*/

/*****************************************************************************\
	I18N
\*****************************************************************************/


define( 'EMPO_TEXTDOMAIN', 'even-more-privacy-options' );


add_action( 'init', 'empo_load_textdomain' );


function empo_load_textdomain() {
	$plugin_dir = basename( dirname(__FILE__) );
	load_plugin_textdomain( EMPO_TEXTDOMAIN, false, $plugin_dir.'/languages' );
}


/*****************************************************************************\
	BLOG PRIVACY SETTINGS
\*****************************************************************************/


define( 'EMPO_UNAUTHORISED_REDIRECT', 'empo_unauthorised_redirect' );
define( 'EMPO_WP_SIGNUP_REDIRECT', 'empo_wp_signup_redirect' );

add_action( 'admin_init', 'empo_settings_api_init' );


function empo_settings_api_init() {
	global $current_blog;
	if( $current_blog->public < 0 ) {
		add_settings_section( 
			'empo_privacy_setting_section',
			__( 'Redirection for unauthorized visitors', EMPO_TEXTDOMAIN ),
			'empo_privacy_settings_section_callback',
			'privacy'
		);
		
		if( $current_blog->public > -4 ) {
			add_settings_field( 
				EMPO_UNAUTHORISED_REDIRECT,
				__( 'Unauthorised redirection URL', EMPO_TEXTDOMAIN ),
				'empo_unauthorised_redirect_setting_callback',
				'privacy',
				'empo_privacy_setting_section'
			);
			register_setting( 'privacy', EMPO_UNAUTHORISED_REDIRECT );
		}
		
		add_settings_field( 
			EMPO_WP_SIGNUP_REDIRECT,
			__( 'Signup redirection URL', EMPO_TEXTDOMAIN ),
			'empo_wp_signup_redirect_setting_callback',
			'privacy',
			'empo_privacy_setting_section'
		);
		register_setting( 'privacy', EMPO_WP_SIGNUP_REDIRECT );
	}	
}


function empo_privacy_settings_section_callback() {
	//echo '<p>'..'</p>';
}


function empo_unauthorised_redirect_setting_callback() {
	echo '<code>'.home_url().'</code>&nbsp;<input name="'.EMPO_UNAUTHORISED_REDIRECT.'" id="'.EMPO_UNAUTHORISED_REDIRECT.'" type="text" class="code" value="'.empo_get_unauthorised_redirect().'" />&nbsp;'.__( 'URL where all unauthorised visitors shall be redirected when trying to view your blog. This page will not be protected, of course. Leave blank to redirect to login page (as default).', EMPO_TEXTDOMAIN );
}


function empo_wp_signup_redirect_setting_callback() {
	echo '<code>'.home_url().'</code>&nbsp;<input name="'.EMPO_WP_SIGNUP_REDIRECT.'" id="'.EMPO_WP_SIGNUP_REDIRECT.'" type="text" class="code" value="'.empo_get_wp_signup_redirect().'" />&nbsp;'.__( 'Signup page redirection. Leave blank for default value.', EMPO_TEXTDOMAIN );
}


function empo_get_unauthorised_redirect() {
	return get_option( EMPO_UNAUTHORISED_REDIRECT, '' );
}

function empo_get_wp_signup_redirect() {
	return get_option( EMPO_WP_SIGNUP_REDIRECT, '' );
}


/*****************************************************************************\
	WP_SIGNUP_LOCATION FILTER
\*****************************************************************************/


add_filter( 'wp_signup_location', 'empo_wp_signup_filter' );


function empo_wp_signup_filter( $default_location ) {
	global $current_blog;
	if( $current_blog->public < 0 ) {
		$redirect = empo_get_wp_signup_redirect();
		if( !empty( $redirect ) ) {
			return $redirect;
		} else {
			return $default_location;
		}
	}
}


/*****************************************************************************\
	UNAUTHORISED VISITOR REDIRECTION
\*****************************************************************************/
	

add_filter( 'ds_wp3_private_blog_login_url', 'empo_unauthorised_redirection_filter' );


function empo_unauthorised_redirection_filter( $default_location ) {
	global $current_blog;
	if( $current_blog->public < 0 ) {
		$redirect = empo_get_unauthorised_redirect();
		if( !empty( $redirect ) ) {
			return $redirect;
		} else {
			return $default_location;
		}
	}
}


add_action( 'init', 'empo_exclude_redirection_pages' );


function empo_exclude_redirection_pages() {
	global $current_blog, $ds_more_privacy_options;
	if( isset( $ds_more_privacy_options ) && ( $current_blog->public < 0 ) ) {
		$ex1 = empo_get_unauthorised_redirect();
		$ex2 = empo_get_wp_signup_redirect();
		$ex = array();
		if( !empty( $ex1 ) ) {
			$ex[] = $ex1;
			$ex[] = $ex1.'/';
		}
		if( !empty( $ex2 ) ) {
			$ex[] = $ex2;
			$ex[] = $ex2.'/';
		}
		foreach( $ex as $ex_page ) {
			if( $_SERVER['REQUEST_URI'] == $ex_page ) {
				switch( $current_blog->public ) {
				case -1:
					remove_action('template_redirect', array(&$ds_more_privacy_options, 'ds_users_authenticator'));
					break;
				case -2:
					remove_action('template_redirect', array(&$ds_more_privacy_options, 'ds_members_authenticator'));
					break;
				case -3:
					remove_action('template_redirect', array(&$ds_more_privacy_options, 'ds_admins_authenticator'));
					break;
				}
			}
		}
	}
}


/*****************************************************************************\
	MISC TWEAKS
\*****************************************************************************/


remove_filter( 'login_url', 'ds_my_login_page_redirect' );


add_action( 'init', 'empo_remove_login_form_messages' );


function empo_remove_login_form_messages() {
	global $current_blog, $ds_more_privacy_options;
	if( isset( $ds_more_privacy_options ) && ( $current_blog->public < 0 ) ) {
		switch( $current_blog->public ) {
		case -1:
			remove_action('login_form', array(&$ds_more_privacy_options, 'registered_users_login_message')); 
			break;
		case -2:
			remove_action('login_form', array(&$ds_more_privacy_options, 'registered_members_login_message')); 
			break;
		case -3:
			remove_action('login_form', array(&$ds_more_privacy_options, 'registered_admins_login_message'));
			break;
		}
	}
}


add_action( 'wp_authenticate', 'empo_members_only_feed_keys_authenticate', 8 );
add_action( 'template_redirect', 'empo_members_only_feed_keys_authenticate', 8 );


function empo_members_only_feed_keys_authenticate() {
	global $current_user, $wpdb;
	if( is_feed() && !$current_user->ID && !empty( $_GET['feedkey'] ) ) {
		$feedkey = $_GET['feedkey'];
		$members_only_opt = get_option('members_only_options');
		$user_id = $wpdb->get_var( $wpdb->prepare( "SELECT user_id FROM $wpdb->usermeta WHERE meta_value = %s", $feedkey ));
		if( $user_id && $members_only_opt['feed_access'] == 'feedkeys' ) //If Feed Key is found and using Feed Keys
		{
			remove_all_actions( 'wp_authenticate' );
			wp_set_current_user( $user_id );
			if( defined( 'suh_log' ) ) {
				suh_log( "Using legacy Members Only feed key.", 3 );
			}			
		}	
	}
}


add_action('show_user_profile', 'empo_edit_user_profile');


function empo_edit_user_profile() {
	global $current_blog;
	if( function_exists( 'private_feed_keys_filter_link' ) && ( $current_blog->public < 0 ) ) {
		$key = get_bloginfo( 'rss2_url' );
		?>
		<h3><?php _e( 'Your private RSS feed key for this blog', EMPO_TEXTDOMAIN ); ?></h3>
		<p><code><a href="<?php echo $key; ?>"><?php echo $key; ?></a></code></p>
		<?php
	}	
}


add_action( 'init', 'empo_replace_mail_notification_with_wls' );


function empo_replace_mail_notification_with_wls() {
	if( defined( 'WLS' ) && function_exists( 'suh_log' ) ) {
		global $ds_more_privacy_options;
		remove_action( 'update_blog_public', array(&$ds_more_privacy_options,'ds_mail_super_admin'));
		add_action( 'update_blog_public', 'empo_update_blog_public_log' );
	}
}


function empo_update_blog_public_log() {
	global $current_blog, $blog_id;
	$blog_public_old = empo_blog_privacy_setting_description( $current_blog->public );
	$blog_public_new = empo_blog_privacy_setting_description( get_blog_option( $blog_id, 'blog_public' ) );
	suh_log( 'Changing blog privacy setting from "'.$blog_public_old.'" to "'.$blog_public_new.'".', 2 );
}


function empo_blog_privacy_setting_description( $setting ) {
	switch( $setting ) {
	case '1':
		return 'visible';
	case '0':
		return 'no search';
	case '-1':
		return 'network users only';
	case '-2':
		return 'members only';
	case '-3':
		return 'site admins only';
	case '-4':
		return 'custom privacy management';
	default:
		return 'unknown value ('.$setting.')';
	}
}
		

/*****************************************************************************\
	PRIVACY FEED KEYS ONLY
\*****************************************************************************/


add_action( 'blog_privacy_selector', 'empo_add_privacy_options', 11 );


function empo_add_privacy_options() {
	?>
	<br/>
	<input id="blog-private-4" type="radio" name="blog_public" value="-4" <?php checked( '-4', get_option('blog_public') ); ?> />
	<label for="blog-private-4"><?php _e( 'Privacy managed by other plugin - allow only private feed keys', EMPO_TEXTDOMAIN ); ?></label>
	<?php
}

?>
