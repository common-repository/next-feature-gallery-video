<?php
$active_tab = isset($_GET["tab"]) ? $_GET["tab"] : 'general';
?>
 <ul class="nav-tab-wrapper">
	<li><a href="<?php echo esc_url(admin_url().'admin.php?page=next-feature-serv&tab=general');?>" class="nav-tab <?php if($active_tab == 'general'){echo 'nav-tab-active';} ?>"><?php echo esc_html__('General', 'themedev-next-feature');?></a></li>
	<li><a href="<?php echo esc_url(admin_url().'admin.php?page=next-feature-serv&tab=shotrcode');?>" class="nav-tab <?php if($active_tab == 'shotrcode'){echo 'nav-tab-active';} ?>"><?php echo esc_html__('Shortcode', 'themedev-next-feature');?></a></li>
</ul>