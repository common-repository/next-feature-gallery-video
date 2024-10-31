<?php  
namespace themeDevFeature\Apps;
if ( ! defined( 'ABSPATH' ) ) die( 'Forbidden' );
/**
 * Class Name : Featured - Featured video added
 * Class Type : Normal class
 *
 * initiate all necessary classes, hooks, configs
 *
 * @since 1.0.0
 * @access Public
 */
use \themeDevFeature\Apps\Settings as Settings;
 
class Featured extends Settings{

	private $post_type = ['post', 'page'];
	
	private $getGeneral = [];
	
	public function __construct( $load = true ) {

		if( $load ) {

			if( !is_admin() ) {

				// replace the exisiting thumbnail
				//add_filter( 'post_thumbnail_html', array( $this, 'next_featured_replace_thumbnail' ), 99, 5 );
				
				// add scripts
				add_action( 'wp_enqueue_scripts', array( $this, 'next_featured_public_scripts' ) );

				add_filter( 'the_content', [ $this, 'next_featured_replace_content' ] );
				// check if a thumbnail has been set
				
				add_shortcode( 'next-featured', [ $this, 'next_featured_shortcode_content' ] );
			}

			// these hooks only apply to the admin
			if( is_admin() ) {
				
				add_action( 'add_meta_boxes', [ $this, 'next_meta_box_for_featured' ] );
				// add the link to featured image meta box
				
				// create a modal
				add_action( 'admin_footer', array( $this, 'next_featured_render_modal_container' ) );

				// add scripts
				add_action( 'admin_enqueue_scripts', array( $this, 'next_featured_admin_scripts' ) );

				// do ajax to get video information
				add_action( 'wp_ajax_featured_video_get_data', array( $this, 'ajax_render_video_data' ) );
				
				add_action( 'wp_ajax_featured_video_modal', array( $this, 'next_render_modal' ) );

				// save the video url
				add_action( 'save_post', array( $this, 'next_featured_save' ), 1, 2  );

			}
		}
		$this->getGeneral = get_option( parent::$general_key );
		
	}
	
	public function next_meta_box_for_featured(){
		
		if(!isset($this->getGeneral['general']['featured']['ebable']) ){
			return false;
		}
		global $post;
		if( is_array( $this->post_type() )){
			foreach($this->post_type() as $v):
				if( $post->post_type == $v ): 
					add_meta_box(
							'next_meta_featured',
							esc_html__('Featured Video', 'themedev-next-feature'),
							[$this, 'next_featured_add_button_video'],
							$v,
							'side',
							'low'
						);
				endif;
			endforeach;
		}
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
	public function next_featured_public_scripts() {

		wp_enqueue_script( 'jquery' );

		wp_enqueue_script( 'next-featured-video-js', NEXT_FEATURE_PLUGIN_URL. 'assets/public/script/featured/featured.video.public.js', array( 'jquery' ), NEXT_FEATURE_VERSION, false);
		
		wp_enqueue_style( 'next-featured-video-css', NEXT_FEATURE_PLUGIN_URL.'assets/public/css/featured/featured.video.public.css', false, NEXT_FEATURE_VERSION);
		
		wp_enqueue_script( 'next-magnific-video-js', NEXT_FEATURE_PLUGIN_URL. 'assets/public/script/lib/jquery.magnific-popup.min.js', array( 'jquery' ), NEXT_FEATURE_VERSION, false);
		
		wp_enqueue_script( 'next-single-video-js', NEXT_FEATURE_PLUGIN_URL. 'assets/public/script/lib/single-page.js', array( 'jquery' ), NEXT_FEATURE_VERSION, false );

	}

	
	public function next_featured_has_video_check( $value, $object_id, $meta_key, $single ) {

		if( $meta_key == '_thumbnail_id' ) {

			$only_single = apply_filters( 'wp_featured_video_singular_only', true );

			if( $only_single ) {

				if( !is_singular() )
					return $value;

			}

			if( $this->has_featured_video( $object_id ) ) 
				$value = true;

		}

		return $value;

	}
	
	public function next_featured_shortcode_content($atts , $content = null){
		$atts = shortcode_atts(
					array(
							'post-id' => 0,
							'gallery-show' => 'yes',
							'video-show' => 'yes',
							'class' => '',
							'id' => '',
						), $atts, 'next-featured' 
				);
		$post_id = isset($atts['post-id']) ? $atts['post-id'] : 0;

		return $this->next_content_featured($post_id, '', $atts);
	}
	
	public function next_featured_replace_content( $content ){
		
		
		if( is_admin() ){
            return '';
        }
        if( is_front_page() || is_home()){
            return $content;
		}
		
		if(!isset($this->getGeneral['general']['featured']['ebable'])){
			return $content;
		}
		
        global $post;
		if( in_array( get_post_type( $post ), $this->post_type())  )
		{ 
			$post_id = $post->ID;
			return $this->next_content_featured($post_id, $content);
			
		}
		return $content;
	}
	
	
	
	
	public function next_content_featured( $post_id = 0, $content = '', $atts = []){
		
		if($post_id == 0){
			global $post;
			$post_id = $post->ID;
		}
		if(!isset($this->getGeneral['general']['featured']['ebable'])){
			return $content;
		}
		
		$content_data = '';
		
		$class_name = isset( $atts['class'] ) ? $atts['class'] : '';
		$gallery_show = isset( $atts['gallery-show'] ) ? $atts['gallery-show'] : 'yes';
		$video_show = isset( $atts['video-show'] ) ? $atts['video-show'] : 'yes';
		
		$html = '';
		$attrs = '';
		
		if($video_show == 'yes'){
			if( $this->has_featured_video( $post_id ) ) {
				
				$size = 'large';	
				$size_array = $this->get_image_sizes( $size );

				$width = isset($size_array['width']) ? $size_array['width'] : 100;
				$height = isset($size_array['height']) ? $size_array['height'] : 100;
				$crop = isset($size_array['crop']) ? $size_array['crop'] : false;

				$height = round( ( $width / 16 ) * 9 );
				
				$ratio = $width / $height;

				$video = $this->get_video_id( $post_id );
				$video_type = $this->get_video_type( $post_id );

				$attr = [];
				
				if( !isset( $attr['class'] ) ){
					$attr = [];
					$attr['class'] = '';
				}
				$attr['class'] .= 'featured-video featured-video-type-'. $video_type .' featured-video-'. ( $crop ? 'crop' : 'normal' );
				$attr['id'] = 'next-featured-video-'. $post_id;
				
				if(is_array($attr) && sizeof($attr) > 0):
					foreach ( $attr as $name => $value ) {
						$attrs .= " $name=" . '"' . $value . '"';
					}
				endif;
				
				$html .= ' <div '.$attrs.'> ';
				if( $video_type == 'youtube' ) {
					$youtube_query = [
						'autoplay' => 0,
						'origin' => get_permalink( $post_id ),
					] ;

					$youtube_query = http_build_query( $youtube_query );

					$html .= '<iframe class="featured-video-iframe" type="text/html" width="'.$width.'"  height="'.$height.'"';
					$html .= 'src="http://www.youtube.com/embed/'.$video.'?'.$youtube_query.'"';
					$html .= 'frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';

				}elseif( $video_type == 'vimeo' ) {
					$vimeo_query = [
						'autoplay' => 0,
						'byline' => 0,
						'portrait' => 0,
						'title' => 0,
						'badge' => 0
					];
					$vimeo_query = http_build_query( $vimeo_query );
					$html .= '<iframe class="featured-video-iframe" width="'.$width.'" height="'.$height.'"';
					$html .= 'src="//player.vimeo.com/video/'.$video.'?'.$vimeo_query.'"';
					$html .= 'frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';

				}elseif( $video_type == 'dailymotion' ) {

					$dailymotion_query = [
						'autoplay' => 0,
						'mute' => 0,
					];

					$dailymotion_query = http_build_query( $dailymotion_query );
					
					$html .= '<iframe class="featured-video-iframe" width="'.$width.'" height="'.$height.'"';
					$html .= 'src="//www.dailymotion.com/embed/video/'.$video.'?'.$dailymotion_query.'"';
					$html .= 'frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';
				}
				$html .= '</div>';
			}
		}
		
		if($gallery_show == 'yes'){
			$gallery_array = explode( ',', get_post_meta($post_id, 'next_portfolio_gallery', true) );
			if (is_array($gallery_array) && sizeof($gallery_array)) {
				$html .= ' <div class="next-featured-gallery" id="next-featured-gallery"> ';
					$html .= '<ul class="next-portfolio-gallery">';
					
					foreach ($gallery_array as $gallery_item) {
						$html .= '<li><a class="next_featured_popup_gallery" href="' . wp_get_attachment_url($gallery_item) . '"><img id="portfolio-item-' . $gallery_item . '" src="' . wp_get_attachment_thumb_url($gallery_item) . '"></a></li>';
					}
					$html .= '</ul>';
				$html .= '</div>';
			}
		}
		
		ob_start();
		require_once( NEXT_FEATURE_PLUGIN_PATH.'views/public/featured/view-featured-content.php' );	
		$content_data = ob_get_contents();
		ob_end_clean();
		
		$style_content = isset( $this->getGeneral['general']['content_position']) ? $this->getGeneral['general']['content_position'] : 'before_content';
		if($style_content == 'after_content'){
			return $content.$content_data;
		}else if($style_content == 'before_content'){
			return $content_data.$content;
		}else if($style_content == 'replace_content'){
			return $content_data;
			
		}else{
			return $content;
		}
		return $content;
	}
	
	public function next_featured_replace_thumbnail($html, $post_id, $post_thumbnail_id, $size, $attr) {
		
		if( is_admin() ){
            return '';
        }
        if( is_front_page() || is_home()){
            return $html;
        }
		
		if(!isset($this->getGeneral['general']['featured']['ebable'])){
			return $html;
		}
		
		global $post;
	
		if( in_array( get_post_type( $post ), $this->post_type()) )
		{ 
			$post_id = $post->ID;
			return $this->next_content_featured($post_id, $html);
			
		  }
		return $html;

	}

	
	
	private function get_image_sizes( $size = '' ) {

		global $_wp_additional_image_sizes;

		$sizes = array();
		$get_intermediate_image_sizes = get_intermediate_image_sizes();

		// Create the full array with sizes and crop info
		foreach( $get_intermediate_image_sizes as $_size ) {

				if ( in_array( $_size, array( 'thumbnail', 'medium', 'large' ) ) ) {

						$sizes[ $_size ]['width'] = get_option( $_size . '_size_w' );
						$sizes[ $_size ]['height'] = get_option( $_size . '_size_h' );
						$sizes[ $_size ]['crop'] = (bool) get_option( $_size . '_crop' );

				} elseif ( isset( $_wp_additional_image_sizes[ $_size ] ) ) {

						$sizes[ $_size ] = array( 
								'width' => $_wp_additional_image_sizes[ $_size ]['width'],
								'height' => $_wp_additional_image_sizes[ $_size ]['height'],
								'crop' =>  $_wp_additional_image_sizes[ $_size ]['crop']
						);

				}

		}

		// Get only 1 size if found
		if ( $size ) {

				if( isset( $sizes[ $size ] ) ) {
						return $sizes[ $size ];
				} else {
						return false;
				}

		}

		return $sizes;
	}

	
	public function next_featured_save($post_id, $post ) {
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}
		// check post id
		if( !empty($post_id) AND is_object($post) ){
			$getPostTYpe = $post->post_type;
			if( in_array( get_post_type( $post ), $this->post_type())){
				if( isset( $_POST['next_featured_video_url'] ) ){
						update_post_meta( $post_id, 'next_featured_video_url', sanitize_text_field($_POST['next_featured_video_url']) );
				}
			}
		}

	}

	
	public function next_featured_admin_scripts() {
		
		if( get_current_screen()->base !== 'post' )
			//return;

		wp_enqueue_script( 'jquery' );

		wp_enqueue_script( 'next-featured-video-js', NEXT_FEATURE_PLUGIN_URL. 'assets/admin/scripts/featured/featured.video.js', array( 'jquery' ), '1.0' );
		wp_localize_script( 'next-featured-video-js', 'Featured_Video', array( 'SetVideo' => __( 'Set featured video', 'themedev-next-feature' ), 'RemoveVideo' => __( 'Remove featured video', 'themedev-next-feature' ) ) );
		
		wp_enqueue_style( 'next-featured-video-css', NEXT_FEATURE_PLUGIN_URL.'assets/admin/css/featured/featured.video.css');

	}

	
	public function instance() {

		return new self();

	}

	public function next_featured_add_button_video(){
		if(!isset($this->getGeneral['general']['featured']['ebable'])){
			return false;
		}
		
		global $post;
		// get current post type
		$getPostTYpe = $post->post_type;
		
		// check post type with current post type.
		if( in_array($getPostTYpe, $this->post_type()) ){
			$post_id = $post->ID; 
			include( NEXT_FEATURE_PLUGIN_PATH.'views/admin/featured/add-video.php' );
		}
	}
	
	
	private function get_video_url($post_id) {

		$video = get_post_meta( $post_id, 'next_featured_video_url', true );

		return $video;

	}

	private function get_video_id($post_id) {

		$video = $this->get_video_url($post_id);

		if( empty($video) || !$video ) {
			return false;
		}

		$data = $this->next_get_video_data_new($video);

		return isset($data['id']) ? $data['id'] : '';
	}

	
	private function get_video_type($post_id) {

		$video = $this->get_video_url($post_id);

		if( empty($video) || !$video ) {
			return false;
		}

		$data = $this->next_get_video_data_new($video);

		return isset($data['type']) ? $data['type'] : '';
	}

	
	public function get_video_thumbnail($post_id) {

		$video = $this->get_video_url($post_id);

		if( empty($video) || !$video ) {
			return false;
		}

		$data = $this->next_get_video_data_new($video);

		return isset($data['thumbnail']) ? $data['thumbnail'] : '';
	}

	
	public function has_featured_video($post_id) {

		$video = get_post_meta( $post_id, 'next_featured_video_url', true );

		if( empty($video) || !$video ) {
			return false;
		}
		return true;
	}

	private function next_get_video_data_new( $url ) {
		$return = [];
		
		$youtube = 'https://www.youtube.com/oembed?url=%s&format=json';
		$vimeo = 'http://vimeo.com/api/v2/video/%s.json';
		
		$dailymotion = 'https://api.dailymotion.com/video/%s';
		$url_dailythubnil = 'https://www.dailymotion.com/thumbnail/video/%s';
		
		if( strpos( $url, 'youtube.com' ) !== false || stripos( $url, 'youtu.be' ) !== false ) {
			$service = 'youtube';	
		}elseif( strpos( $url, 'vimeo.com' ) !== false ) {
			$service = 'vimeo';
		}elseif( strpos( $url, 'dailymotion.com' ) !== false ) {
			$service = 'dailymotion';
		}else{
			$service = '';
		}
		if( $service == 'youtube' ) {
			$data_url = sprintf( $youtube, $url );			
			if(stripos( $url, 'youtu.be' ) !== false){
				$id = substr( parse_url( $url, PHP_URL_PATH ), 1 );
				$id = strlen($id) > 0 ? $id : time();
			}else if(strpos( $url, 'youtube.com' ) !== false){
				parse_str( parse_url( $url, PHP_URL_QUERY ), $query );
				if( !isset( $query['v'] ) ){
					return false;
				}
				$id = $query['v'];
				$id = strlen($id) > 0 ? $id : time();
			}			
			$response = wp_remote_get( $data_url );
			if ( is_array( $response ) && ! is_wp_error( $response ) ) {
				$data    = json_decode($response['body']); 
			}else{
				return false;
			}
			if( !isset($data->thumbnail_url) ){
				return false;
			}
			$return = array( 'thumbnail' => $data->thumbnail_url, 'id' => $id, 'type' => 'youtube', 'title' => $data->title, 'html' => $data->html );
		}

		if( $service == 'vimeo' ) {
			$id = substr( parse_url( $url, PHP_URL_PATH ), 1 );
			if( ! is_numeric( $id ) ) {
				return false;
			}
			$data_url = sprintf( $vimeo, $id );
			$response = wp_remote_get( $data_url );
			if ( is_array( $response ) && ! is_wp_error( $response ) ) {
				$data    = json_decode($response['body']); 
			}else{
				return false;
			}			
			if( !isset($data[0]->thumbnail_large) ){
				return false;
			}
			$id = strlen($id) > 0 ? $id : $data[0]->id;			
			$return = array( 'thumbnail' => $data[0]->thumbnail_large, 'id' => $id, 'type' => 'vimeo', 'title' => $data[0]->title, 'html' => $data[0]->url  );
		}
		
		if( $service == 'dailymotion' ){
			$id = substr( parse_url( $url, PHP_URL_PATH ), 1 );
		    $explode = explode('/', $id);
			$id = end($explode);
			$id = strlen($id) > 0 ? $id : time();			
			$data_url = sprintf( $dailymotion, $id );
			$response = wp_remote_get( $data_url );
			
			$thubnil = sprintf( $url_dailythubnil, $id );
 
			if ( is_array( $response ) && ! is_wp_error( $response ) ) {
				$data    = json_decode($response['body']); 
			}else{
				return false;
			}
			
			if( !isset($data->title) ){
				return false;
			}
			
			$id = strlen($id) > 0 ? $id : $data->id;
			$return = array( 'thumbnail' => $thubnil, 'id' => $id, 'type' => 'dailymotion', 'title' => $data->title, 'html' => ''  );
		}
		return $return;

	}
	
	public function next_featured_render_modal_container() {
		echo '<div id="next-featured-video-modal-container"></div>';
	}

	
	public function next_render_modal( ) {
		$video = isset($_POST['url']) ? sanitize_text_field($_POST['url']) : '';
		include( NEXT_FEATURE_PLUGIN_PATH.'views/admin/featured/add-video-modal.php' );
	}

	
	public function ajax_render_video_data( $ajax = true, $url = null ) {
		if( $ajax || $ajax == '' ){
			$url = isset($_POST['url']) ? sanitize_text_field($_POST['url']) : $url;
		}
		$data = $this->next_get_video_data_new( $url );
		if( $data ) {
			$title = $data['title']; 
			$thumb = $data['thumbnail'];
			include( NEXT_FEATURE_PLUGIN_PATH.'views/admin/featured/preview-video.php' );
		}else {
			_e( 'This is not a valid video URL. Please try another URL.', 'themedev-next-feature' );
		}
	
		if( $ajax || $ajax == '' ){
			die();
		}
	}

}


if(!function_exists('__next_featured')){
	function __next_featured( $post_id = 0, $attr = [] ){
		$featured = new \themeDevFeature\Apps\Featured(false);
		return $featured->next_content_featured($post_id, '', $attr);
	}
}
