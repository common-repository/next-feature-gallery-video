<div class="next-fundrasing featured-video-metabox-container">
	<div class="featured_video " id="featured_video">
		<?php
		if( $this->has_featured_video( $post_id ) ) {
			echo '<span class="dashicons dashicons-video-alt3" ></span><img src="'. esc_url($this->get_video_thumbnail( $post_id )) .'">';
		}
		?>
	</div>
	<div id="thumbnail-change-toggle">
		<?php
		if( ! $this->has_featured_video( $post_id ) )
			echo '<p class="hide-if-no-js"><a href="#" id="next-set-featured-video">'. __( 'Set featured video', 'themedev-next-feature' ) .'</a></p>';
		else
			echo '<p class="hide-if-no-js"><a href="#" id="next-remove-featured-video">'. __( 'Remove featured video', 'themedev-next-feature' ) .'</a></p>';
		?>
	</div>
	<input type="hidden" value="<?php echo esc_url($this->get_video_url( $post_id )) ?>" name="next_featured_video_url" id="next_featured_video_url">
	
</div>