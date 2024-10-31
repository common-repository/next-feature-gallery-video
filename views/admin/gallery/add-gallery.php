<div class="next-fundrasing featured-gallery-metabox-container">
	<div class="next-gallery-container">
		<?php
		foreach ($custom_meta_fields as $field):
			$meta = get_post_meta($post->ID, $field['id'], true);
			switch($field['type']) {
				case 'media':
					$close_button = null;
					if ($meta) {
							$close_button = '<span class="next_portfolio_close"></span>';
					}
					echo '<input id="next_portfolio_image" type="hidden" name="next_portfolio_image" value="' . esc_attr($meta) . '" />
					<div class="next_portfolio_image_container">' . $close_button . '<img id="next_portfolio_image_src" src="' . wp_get_attachment_thumb_url($this->next_portfolio_get_image_id($meta)) . '"></div>
					<input id="next_portfolio_image_button" class="button button-primary button-large" type="button" value="Add Image" />';
					break;
				case 'gallery':
					$meta_html = '';
					if ($meta) {
							$meta_html .= '<ul class="next_portfolio_gallery_list">';
							$meta_array = explode(',', $meta);
							foreach ($meta_array as $meta_gall_item) {
									$meta_html .= '<li><div class="next_portfolio_gallery_container"><span class="next_portfolio_gallery_close"><img id="' . esc_attr($meta_gall_item) . '" src="' . wp_get_attachment_thumb_url($meta_gall_item) . '"></span></div></li>';
									
							}
							$meta_html .= '</ul>';
					}
					echo '<input id="next_portfolio_gallery" type="hidden" name="next_portfolio_gallery" value="' . esc_attr($meta) . '" />
					<span id="next_portfolio_gallery_src">' . $meta_html . '</span>
					<div class="next_gallery_button_container"><input id="next_portfolio_gallery_button" class="button button-primary button-large" type="button" value="Add Gallery" /></div>';
				break;
		?>
		
		<?php 
			}
		endforeach;
		?>
	</div>
</div>