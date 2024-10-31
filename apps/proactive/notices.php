<?php 
namespace themeDevFeature\Apps\Proactive;
defined( 'ABSPATH' ) || exit;

/**
 * Global Icons class.
 *
 * @since 1.0.0
 */
class Notices{
    private static $instance;

    public function _init() {        
		add_action('init', [ $this, '_generate_ads_set'] );

        add_action(	'admin_footer', [ $this, '__enqueue_scripts' ], 9999);
        add_action( 'wp_ajax_themedevnotice-ajax', [ $this, '__dismiss' ] );

		add_action( 'admin_notices', [ $this, '_generate_ads'], 110);
       
    }
	public function _generate_ads_set(){
		
	}
    public function _generate_ads( ){
		
		$screen = get_current_screen();
		$key = 'nextfeature';
		
		$transient   = get_transient( '__set_ads_next', '');
		$transient_time   = get_transient( 'timeout___set_ads_next', 1 );
		
		if( !empty($transient) && $transient != $key ){
			return;
		}
		
		if(  false === $transient || empty( $transient ) ){
			set_transient( '__set_ads_next', $key , 86400 );
		}

		$ads = get_transient('__ads_center__', '');
        if (  false === $ads || empty( $ads ) ) {
			$ads = $this->_get_ads();
			if(!empty($ads)){
				set_transient( '__ads_center__', $ads , 86400 );
			}
		}
		if(empty($ads) ){
			return;
		}

        foreach($ads as $k=>$v):
           if( !empty($k) ){
            $data['id'] = isset($v->id) ? $v->id : '';
			$data['class'] = isset($v->class) ? $v->class : '';
			$data['styles'] = isset($v->styles) ? $v->styles : '';
            $data['name'] = isset($v->name) ? $v->name : '';
            $data['title'] = isset($v->title) ? $v->title : '';
            $data['des'] = isset($v->des) ? $v->des : '';
            $data['url'] = isset($v->url) ? $v->url : '';
            $data['img_url'] = isset($v->img_url) ? $v->img_url : '';
            $data['start_date'] = isset($v->start_date) ? $v->start_date : '';
			$data['end_date'] = isset($v->end_date) ? $v->end_date : '';
			$support = isset($v->support) ? $v->support : ['intro_pages'];
			
			if( !empty($data['start_date']) ){
				if( $data['start_date'] >= time() ){
					continue;
				}
			}

			if( !empty($data['end_date'])){
				if( $data['end_date'] <= time()){
					continue;
				}
			}
			
             if( in_array($screen->id , $support) || $k == 'intro_pages' || $k == $key){
					self::push(
						[
							'id'          => 'themedev-'.$data['id'],
							'type'        => 'info',
							'dismissible' => true,
							'return'	  => $data,
							'message'     => esc_html__($data['des'], 'next-addons' ),
						]
					);
              }
 
           }
		endforeach;
    }


    public function _get_ads(){
		$current_user = wp_get_current_user();

		$parmas['plugin'] = 'nextfeature';
		$parmas['email'] = isset($current_user->user_email) ? $current_user->user_email : get_option( 'admin_email' );
		$parmas['name'] = isset($current_user->display_name ) ? $current_user->display_name  : get_option( 'blogname' );
		
        $url = $this->get_edd_api().'/ads?'. http_build_query($parmas, '&');
        $args = array(
            'timeout'     => 60,
            'redirection' => 3,
            'httpversion' => '1.0',
            'blocking'    => true,
            'sslverify'   => true,
        ); 
		$res = wp_remote_get( $url, $args );
		
		if ( is_wp_error( $res ) ) {
			return;
		}
		if(!isset($res['body'])){
			return;
		}
        return (object) json_decode(
            (string) $res['body']
        ); 
    }

    public function get_edd_api(){
        return Init::instance()->get_edd_api();
    }

    /**
	 * Dismiss Notice.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function __dismiss() {
		
		$id   = ( isset( $_POST['id'] ) ) ? sanitize_key($_POST['id']) : '';
		$time = ( isset( $_POST['time'] ) ) ? sanitize_text_field($_POST['time']) : '';
		$meta = ( isset( $_POST['meta'] ) ) ? sanitize_text_field($_POST['meta']) : '';
		
		$key_ = $id.'_'.get_current_user_id();

		if ( ! empty( $id ) ) {
			if ( 'user' === $meta ) {
				update_user_meta( get_current_user_id(), $id, true );
			} else {
				set_transient( $key_, true, $time );
			}
			wp_send_json_success();
		}
		wp_send_json_error();
	}

	public function __enqueue_scripts() {
		echo "
			<script>
			jQuery(document).ready(function ($) {
				$( '.themedevpro-notice.is-dismissible' ).on( 'click', '.notice-dismiss', function() {
					
					_this 		= $( this ).parents( '.themedev-pro-active' );
					var id 	= 	_this.attr( 'id' ) || '';
					var time 	= _this.attr( 'dismissible-time' ) || '';
					var meta 	= _this.attr( 'dismissible-meta' ) || '';
					
					$.ajax({
						url: ajaxurl,
						type: 'POST',
						data: {
							action 	: 'themedevnotice-ajax',
							id 		: id,
							meta 	: meta,
							time 	: time
						},
						success: function (response) {
							
						}
					});
			
				});
			
			});
			</script>
		";
	}

	/**
	 * Show Notices
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function push($notice) {

		$defaults = [
			'id'               => 'themedevpro',
			'type'             => 'info',
			'show_if'          => true,
			'message'          => '',
			'class'            => 'themedev-pro-active',
			'dismissible'      => false,
			'dismissible-meta' => 'transient',
			'dismissible-time' => WEEK_IN_SECONDS,
			'data'             => '',
		];

		$notice = wp_parse_args( $notice, $defaults );

		$classes = [ 'themedevpro-notice', 'notice' ];

		$classes[] = $notice['class'];
		if ( isset( $notice['type'] ) ) {
			$classes[] = 'notice-' . $notice['type'];
		}

		if ( true === $notice['dismissible'] ) {
			$classes[] = 'is-dismissible';

			$notice['data'] = ' dismissible-time=' . esc_attr( $notice['dismissible-time'] ) . ' ';
		}

		$notice_id    = $notice['id'];
		
		$notice['classes'] = implode( ' ', $classes );

		$notice['data'] .= ' dismissible-meta=' . esc_attr( $notice['dismissible-meta'] ) . ' ';
		if ( 'user' === $notice['dismissible-meta'] ) {
			$expired = get_user_meta( get_current_user_id(), $notice_id, true );
		} elseif ( 'transient' === $notice['dismissible-meta'] ) {
			$key_ = $notice_id.'_'.get_current_user_id();
			$expired = get_transient( $key_ );
		}

		if ( isset( $notice['show_if'] ) ) {
			if ( true === $notice['show_if'] ) {
				if ( false === $expired || empty( $expired ) ) {
					self::markup($notice);
				}
			}
		} else {
			self::markup($notice);
		}
	}

	public static function markup( $notice = [] ) {
        $data = isset($notice['return']) ? $notice['return'] : [];
		$img_url = isset($data['img_url']) ? $data['img_url'] : '';
		$url = isset($data['url']) ? $data['url'] : '';
		$title = isset($data['title']) ? $data['title'] : '';
		$desc = isset($data['des']) ? $data['des'] : '';
		$styles = isset($data['styles']) ? $data['styles'] : '';

		?>
	
		<div id="<?php echo esc_attr( $notice['id'] ); ?>" class="themedevnotices <?php echo esc_attr( $notice['classes'] ); ?>" <?php echo $notice['data']; ?> style="background-image: url(<?php echo $img_url;?>); <?php echo $styles;?>" >
			<h2 class="nxheading-notices"> <a href="<?php echo esc_url($url);?>" target="_blank"><?php echo $title; ?> </a> </h2>
			<p class="nxdetails-notices">
				<?php echo $desc; ?>
			</p>

		</div>
		<?php
	}

    public static function instance(){
		if (!self::$instance){
            self::$instance = new self();
        }
        return self::$instance;
	}
}