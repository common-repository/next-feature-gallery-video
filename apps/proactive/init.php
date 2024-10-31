<?php 
namespace themeDevFeature\Apps\Proactive;
defined( 'ABSPATH' ) || exit;
/**
 * Global Icons class.
 *
 * @since 1.0.0
 */
class Init{
    private static $instance;

   
    public function _init() {        
        if(current_user_can('manage_options')){
            Notices::instance()->_init();
        }  
    }


    public function get_edd_api(){
        return 'http://api.themedev.net/';
    }

    public static function instance(){
		if (!self::$instance){
            self::$instance = new self();
        }
        return self::$instance;
	}
}