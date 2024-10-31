<?php
if( ! defined( 'ABSPATH' )) die( 'Forbidden' );
/**
 * Plugin Name: Next Feature Gallery & Video
 * Description: The most Advanced Feature Gallery & Video for WodrPress post. Selected Multiple Images for Gallery for post and Set a Featured Video of Youtube, Vimeo, DailyMotion Video for psot. 
 * Plugin URI: http://products.themedev.net/next-feature
 * Author: ThemeDev
 * Version: 1.0.3
 * Author URI: http://themedev.net/
 *
 * Text Domain: themedev-next-feature
 *
 * @package NextFeature 
 * @category Free
 * Domain Path: /languages/
 * License: GPL2+
 */
/**
 * Defining static values as global constants
 * @since 1.0.0
 */
define( 'NEXT_FEATURE_VERSION', '1.0.3' );
define( 'NEXT_FEATURE_PREVIOUS_STABLE_VERSION', '1.0.2' );

define( 'NEXT_FEATURE_KEY', 'themedev-next-feature' );

define( 'NEXT_FEATURE_DOMAIN', 'themedev-next-feature' );

define( 'NEXT_FEATURE_FILE_', __FILE__ );
define( "NEXT_FEATURE_PLUGIN_PATH", plugin_dir_path( NEXT_FEATURE_FILE_ ) );
define( 'NEXT_FEATURE_PLUGIN_URL', plugin_dir_url( NEXT_FEATURE_FILE_ ) );

// initiate actions
add_action( 'plugins_loaded', 'themedev_feature_load_plugin_textdomain' );
/**
 * Load eBay Search textdomain.
 * @since 1.0.0
 * @return void
 */
function themedev_feature_load_plugin_textdomain() {
	load_plugin_textdomain( 'themedev-next-feature', false, basename( dirname( __FILE__ ) ) . '/languages'  );
	// add action page hook
	add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'themedev_feature_action_links' );

		
	/**
	 * Load Next Review Loader main page.
	 * @since 1.0.0
	 * @return plugin output
	 */
	require_once( NEXT_FEATURE_PLUGIN_PATH .'init.php');
	new \themeDevFeature\Init();

}


// added custom link
function themedev_feature_action_links($links){
	$links[] = '<a href="' . admin_url( 'admin.php?page=next-feature-serv' ).'"> '. __('Settings', 'themedev-next-feature').'</a>';
	return $links;
}

