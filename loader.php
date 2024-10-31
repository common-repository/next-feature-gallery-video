<?php
namespace themeDevFeature; 

if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Plugin autoloader.
 * Handles dynamically loading classes only when needed. This is the main loader file in plugin.
 *
 * @since 1.0.0
 */
class Loader {
	
	/**
     * Run_plugin static method.
     * autoloader loads all the classes needed to run the plugin.
     * @since 1.0.0
     * @access plugic
     */
	
	public static function run_plugin_social(){
		spl_autoload_register( [ __CLASS__, 'the_autoload_class' ] );
	}
	
	/**
	 * Autoload Class.
	 * For a given class, check if it exist and load it.
	 *
	 * @since 1.0.0
	 * @access private
	 * @param string $class Class name.
	 * @return : void
	 */
	private static function the_autoload_class( $load_class ){
		if ( 0 !== strpos( $load_class, 'themeDevFeature' ) ) {
            return;
        }
        $file_name = strtolower(
								preg_replace(
									[ '/\bthemeDevFeature\\\/', '/([a-z])([A-Z])/', '/_/', '/\\\/' ],
									[ '', '$1-$2', '-', DIRECTORY_SEPARATOR],
									$load_class
								)
							);
       
		$file = NEXT_FEATURE_PLUGIN_PATH . $file_name . '.php';
		// If a file is found
        if ( file_exists( $file ) ) {
            require_once( $file );
        }
	}
}
