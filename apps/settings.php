<?php
namespace themeDevFeature\Apps;
if( ! defined( 'ABSPATH' )) die( 'Forbidden' );
class Settings{
	
	 /**
     * Custom post type
     * Method Description: Set custom post type
     * @since 1.0.0
     * @access public
     */
	
	public static $general_key = '__next_featured_general_data';
	
	public function __construct($load = true){
		if($load){
			add_action('admin_menu', [ $this, 'themeDev_feature_admin_menu' ]);
			
			// Load script file for settings page
			add_action( 'admin_enqueue_scripts', [$this, 'themedev_feature_script_loader_admin' ] );
			
			add_action( 'wp_enqueue_scripts', [$this, 'themedev_feature_script_loader_public' ] );
		}
	}
	
	/**
     * Public method : themeDev_add_custom_post
     * Method Description: Create custom post type
     * @since 1.0.0
     * @access public
     */
	public function themeDev_feature_admin_menu(){
		add_menu_page(
            esc_html__( 'Next Feature', 'themedev-next-feature' ),
            esc_html__( 'Next Feature', 'themedev-next-feature' ),
            'manage_options',
            'next-feature-serv',
            [ __CLASS__, 'themedev_next_serv_settings'],
            'dashicons-format-video',
            6
        );
		
		 add_submenu_page( 'next-feature-serv', esc_html__( 'Support', 'themedev-next-feature' ), esc_html__( 'Support', 'themedev-next-feature' ), 'manage_options', 'next-featured-support', [ __CLASS__ ,'themedev_next_serv_settings_supports'], 11);
	}
	/**
	 * Method Name: themedev_next_serv_settings
	 * Description: Next Settings
	 * Warning when the site doesn't have a minimum required PHP version.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public static function themedev_next_serv_settings(){
		$message_status = 'No';
		$message_text = 'No';
		$active_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'general';
			
		if($active_tab == 'general'){
			// general options
			if(isset($_POST['themedev-featured-general'])){
				$options = isset($_POST['themedev']) ? self::sanitize($_POST['themedev']) : [];
				
				if(update_option( self::$general_key, $options)){
					$message_status = 'yes';
					$message_text = __('Successfully save general data.', 'themedev-next-services');
				}
			}
		}
		//  get general data
		$getGeneral = get_option( self::$general_key, '');
		//print_r($getGeneral);
		include ( NEXT_FEATURE_PLUGIN_PATH.'/views/admin/settings.php');
	}
	/**
	 * Method Name: themedev_next_serv_settings_supports
	 * Description: Next Support Page
	 * Warning when the site doesn't have a minimum required PHP version.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public static function themedev_next_serv_settings_supports(){
		include ( NEXT_FEATURE_PLUGIN_PATH.'/views/admin/supports.php');
	}
	/**
     * Public method : sanitize
     * Method Description: Sanitize for Review
     * @since 1.0.0
     * @access public
     */
	public static function sanitize($value, $senitize_func = 'sanitize_text_field'){
        $senitize_func = (in_array($senitize_func, [
                'sanitize_email', 
                'sanitize_file_name', 
                'sanitize_hex_color', 
                'sanitize_hex_color_no_hash', 
                'sanitize_html_class', 
                'sanitize_key', 
                'sanitize_meta', 
                'sanitize_mime_type',
                'sanitize_sql_orderby',
                'sanitize_option',
                'sanitize_text_field',
                'sanitize_title',
                'sanitize_title_for_query',
                'sanitize_title_with_dashes',
                'sanitize_user',
                'esc_url_raw',
                'wp_filter_nohtml_kses',
            ])) ? $senitize_func : 'sanitize_text_field';
        
        if(!is_array($value)){
            return $senitize_func($value);
        }else{
            return array_map(function($inner_value) use ($senitize_func){
                return self::sanitize($inner_value, $senitize_func);
            }, $value);
        }
	}
    
    public static function __cpt_list(){
		//global $wp_post_types;
		
		$list = [];
		$exclude = [ 'attachment', 'elementor_library', 'xs_review' ]; 
		$post_types_objects = get_post_types(
		  [
			'public' => true,
		  ], 'objects'
		);
	   
		foreach ( $post_types_objects as $cpt_slug => $post_type ) {
			 if ( in_array( $cpt_slug, $exclude ) ) {
			  continue;
		  }
			 $list[$cpt_slug] = ucfirst($post_type->labels->name);
		 }
		return $list;
    }
    
    public static function kses( $raw ) {

		$allowed_tags = array(
			'a'								 => array(
				'class'	 => array(),
				'href'	 => array(),
				'rel'	 => array(),
				'title'	 => array(),
			),
			'abbr'							 => array(
				'title' => array(),
			),
			'b'								 => array(),
			'blockquote'					 => array(
				'cite' => array(),
			),
			'cite'							 => array(
				'title' => array(),
			),
			'code'							 => array(),
			'del'							 => array(
				'datetime'	 => array(),
				'title'		 => array(),
			),
			'dd'							 => array(),
			'div'							 => array(
				'class'	 => array(),
				'title'	 => array(),
				'style'	 => array(),
			),
			'dl'							 => array(),
			'dt'							 => array(),
			'em'							 => array(),
			'h1'							 => array(
				'class' => array(),
			),
			'h2'							 => array(
				'class' => array(),
			),
			'h3'							 => array(
				'class' => array(),
			),
			'h4'							 => array(
				'class' => array(),
			),
			'h5'							 => array(
				'class' => array(),
			),
			'h6'							 => array(
				'class' => array(),
			),
			'i'								 => array(
				'class' => array(),
			),
			'img'							 => array(
				'alt'	 => array(),
				'class'	 => array(),
				'height' => array(),
				'src'	 => array(),
				'width'	 => array(),
			),
			'li'							 => array(
				'class' => array(),
			),
			'ol'							 => array(
				'class' => array(),
			),
			'p'								 => array(
				'class' => array(),
			),
			'q'								 => array(
				'cite'	 => array(),
				'title'	 => array(),
			),
			'span'							 => array(
				'class'	 => array(),
				'title'	 => array(),
				'style'	 => array(),
			),
			'iframe'						 => array(
				'width'			 => array(),
				'height'		 => array(),
				'scrolling'		 => array(),
				'frameborder'	 => array(),
				'allow'			 => array(),
				'src'			 => array(),
			),
			'strike'						 => array(),
			'br'							 => array(),
			'strong'						 => array(),
			'data-wow-duration'				 => array(),
			'data-wow-delay'				 => array(),
			'data-wallpaper-options'		 => array(),
			'data-stellar-background-ratio'	 => array(),
			'ul'							 => array(
				'class' => array(),
			),
		);

		if ( function_exists( 'wp_kses' ) ) { // WP is here
			return wp_kses( $raw, $allowed_tags );
		} else {
			return $raw;
		}
    }
    
    public static function _encode_json( $str = ''){
        return json_encode($str, JSON_UNESCAPED_UNICODE);
    }
	/**
     *  ebay_settings_script_loader .
     * Method Description: Settings Script Loader
     * @since 1.0.0
     * @access public
     */
    public function themedev_feature_script_loader_public(){
        wp_register_script( 'themedev_feature_settings_script', NEXT_FEATURE_PLUGIN_URL. 'assets/public/script/public-script.js', array( 'jquery' ), NEXT_FEATURE_VERSION, false);
        wp_enqueue_script( 'themedev_feature_settings_script' );
		
		wp_register_style( 'themedev_feature_settings_css_public', NEXT_FEATURE_PLUGIN_URL. 'assets/public/css/public-style.css', false, NEXT_FEATURE_VERSION);
        wp_enqueue_style( 'themedev_feature_settings_css_public' );

     }
	 
	 /**
     *  ebay_settings_script_loader .
     * Method Description: Settings Script Loader
     * @since 1.0.0
     * @access public
     */
    public function themedev_feature_script_loader_admin(){
        wp_register_script( 'themedev_feature_settings_script_admin', NEXT_FEATURE_PLUGIN_URL. 'assets/admin/scripts/admin-settings.js', array( 'jquery' ), NEXT_FEATURE_VERSION, false);
        wp_enqueue_script( 'themedev_feature_settings_script_admin' );
		
		wp_register_style( 'themedev_feature_settings_css_admin', NEXT_FEATURE_PLUGIN_URL. 'assets/admin/css/admin-style.css', false, NEXT_FEATURE_VERSION);
		wp_enqueue_style( 'themedev_feature_settings_css_admin' );
		
		wp_register_style( 'themedev_ads', NEXT_FEATURE_PLUGIN_URL. 'assets/admin/css/ads.css', false, NEXT_FEATURE_VERSION);
		wp_enqueue_style( 'themedev_ads' );

     }
	
}