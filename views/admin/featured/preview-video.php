<div class="next-fundrasing-meta-featured">
	<div class="video-data-item next-fundrasing" data-video="<?php echo esc_url($url); ?>" data-thumb="<?php echo $thumb; ?>">
		<?php if(strlen($thumb) > 4){?>
		<div class="video-thumbnail">
			<span class="dashicons dashicons-video-alt3"></span>
			<img src="<?php echo esc_url($thumb); ?>" alt="<?php echo esc_attr($title); ?>">
		</div>
		<?php }?>
		<div class="video-information">
			<h3><?php echo esc_html($title); ?></h3>
			<div class="video-type"><?php echo esc_html($data['type']); ?></div>
			<button class="button-primary" id="insert-video"><?php _e( 'Set Video', 'themedev-next-feature' ); ?></button>
		</div>
	</div>
</div>