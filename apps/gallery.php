<?php  
namespace themeDevFeature\Apps;
if ( ! defined( 'ABSPATH' ) ) die( 'Forbidden' );
/**
 * Class Name : Gallery - Featured video added
 * Class Type : Normal class
 *
 * initiate all necessary classes, hooks, configs
 *
 * @since 1.0.0
 * @access Public
 */
 use \themeDevFeature\Apps\Settings as Settings;
class Gallery extends Settings{

	private $post_type = ['post', 'page'];
	
	private $getGeneral = [];
	
	public function __construct( $load = true ) {
		if( $load ) {
			if( is_admin() ) {
				add_action('add_meta_boxes', [$this, 'next_portfolio_gallery_custom_meta_box' ]);
				
				add_action( 'admin_enqueue_scripts', [ $this, 'next_portfolio_gallery_load_wp_admin_style' ] );
				
				add_action( 'save_post', array( $this, 'next_featured_gallery_save' ), 1, 2  );
				
				
			}
		}
		
		$this->getGeneral = get_option( parent::$general_key );
	}
	
	 /**
     * Custom Post type : static method
     * @since 1.0.0
     * @access public
     */
	 private function post_type(){
		$this->post_type = isset($this->getGeneral['general']['custom_post']) ? (array) array_values($this->getGeneral['general']['custom_post']) : $this->post_type;
		return $this->post_type;
	 }
	 
	
	public function next_portfolio_gallery_load_wp_admin_style() {

		if( get_current_screen()->base !== 'post' )
			//return;

		wp_enqueue_script( 'jquery' );
		wp_enqueue_media();
		wp_enqueue_script('jquery-ui-datepicker');
		wp_enqueue_script('media-upload');
		wp_enqueue_script('thickbox');
		wp_enqueue_style('thickbox');
		wp_enqueue_script( 'next-featured-gallery-js', NEXT_FEATURE_PLUGIN_URL. 'assets/admin/scripts/gallery/gallery_portfolio_admin.js', array( 'jquery' ), NEXT_FEATURE_VERSION, false );
		
		wp_enqueue_style( 'next-featured-gallery-css', NEXT_FEATURE_PLUGIN_URL.'assets/admin/css/gallery/gallery_portfolio_admin.css', false, NEXT_FEATURE_VERSION);

	}
	
	public static function next_featured_gallery_filed(){
		$prefix = 'next_portfolio_';
		$custom_meta_fields = array(
			/*array(
				'label'=> 'Main Image',
				'desc'  => 'This is the main image that is shown in the grid and at the top of the single item page.',
				'id'    => $prefix.'image',
				'type'  => 'media'
			),*/
			array(
				'label'=> 'Gallery Images',
				'desc'  => 'This is the gallery images on the single item page.',
				'id'    => $prefix.'gallery',
				'type'  => 'gallery'
			),
		);
		return $custom_meta_fields;
	 }
	 
	 
	public function next_portfolio_gallery_custom_meta_box(){
		
		if(!isset($this->getGeneral['general']['gallery']['ebable'])){
			return false;
		}
		
		global $post;
		if( is_array(self::post_type() )){
			foreach(self::post_type() as $v):
				if( $post->post_type == $v ): 
					add_meta_box(
							'next_meta_gallery',
							esc_html__('Featured Gallery', 'themedev-next-feature'),
							[$this, 'next_portfolio_gallery_action'],
							$v,
							'side',
							'low'
						);
				endif;
			endforeach;
		}
	 }
	 
	 public function next_portfolio_gallery_action(){
		
		$custom_meta_fields = self::next_featured_gallery_filed();
		global $post;
		// get current post type
		$getPostTYpe = $post->post_type;
		
		// check post type with current post type.
		if( in_array( $getPostTYpe, self::post_type()) ){
			$post_id = $post->ID; 
			include( NEXT_FEATURE_PLUGIN_PATH.'views/admin/gallery/add-gallery.php' );
		}
       
	 }
	 
	 public function next_featured_gallery_save($post_id, $post ){
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}
		// check post id
		if( !empty($post_id) AND is_object($post) ){
			$getPostTYpe = $post->post_type;
			if( in_array( $getPostTYpe, self::post_type()) ){
				$custom_meta_fields = self::next_featured_gallery_filed();
				 foreach ($custom_meta_fields as $field) {
					if( isset( $_POST[$field['id']] ) ){
						$new_meta_value = sanitize_text_field($_POST[$field['id']]);
						$meta_key = $field['id'];
						$meta_value = get_post_meta( $post_id, $meta_key, true );

						if ( $new_meta_value && $meta_value == null ) {
								add_post_meta( $post_id, $meta_key, $new_meta_value, true );
						} elseif ( $new_meta_value && $new_meta_value != $meta_value ) {
								update_post_meta( $post_id, $meta_key, $new_meta_value );
						} elseif ( $new_meta_value == null && $meta_value ) {
								delete_post_meta( $post_id, $meta_key, $meta_value );
						}
					}
				}
			}
		}
	 }
	 
	 public function next_portfolio_get_image_id($image_url){
		global $wpdb;
		$attachment = $wpdb->get_col($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE guid='%s';", $image_url ));
		return isset($attachment[0]) ? $attachment[0] : '';
	 }
	 
	 
}