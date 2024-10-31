<h3><?php echo esc_html__('Shortcode', 'themedev-ebay-advanced-search');?></h3>

<div class="short-code-section" style="cursor: copy;" onclick="themedev_featured_copy_link(this)" themedev-link='[next-featured post-id="1" class=""]'>
	<pre style="cursor: copy;">[next-featured post-id="1" class=""]</pre>
</div>
<div class="short-code-section" style="cursor: copy;" onclick="themedev_featured_copy_link(this)" themedev-link='[next-featured post-id="1" gallery-show="yes" video-show="yes" class=""]'>
	<pre style="cursor: copy;">[next-featured post-id="1" gallery-show="yes" video-show="yes" class=""]</pre>
</div>

<h3><?php echo esc_html__('Custom Function', 'themedev-ebay-advanced-search');?></h3>
<div class="short-code-section" style="cursor: copy;">
	
	<pre style="cursor: copy;">
	$post_id = 1;
	$attr = ['class' => '', 'gallery-show' => 'yes', 'video-show' => 'yes'];
	if( function_exists('__next_featured') ){
		echo __next_featured( $post_id, $attr);
	}
	</pre>
</div>