
<form action="<?php echo esc_url(admin_url().'admin.php?page=next-feature-serv&tab=general');?>" method="post" >
	<h3><?php echo esc_html__('General Options', 'themedev-next-feature');?></h3>
	
	<div class="<?php echo esc_attr('themeDev-form');?>">
		<div class="flex-form">
			<div class="left-div">
				<label class="inline-label">
					<?php echo esc_html__('Enable Featured Videos ', 'themedev-next-feature');?>
				</label>
			</div>
			<div class="right-div">
				<?php
				$loginEbnalbe = isset($getGeneral['general']['featured']['ebable']) ? 'Yes' : 'No';
				if( !isset($getGeneral['general']) ){
					//$loginEbnalbe = 'Yes';
				}
				?>
				<input type="checkbox" name="themedev[general][featured][ebable]" <?php echo ($loginEbnalbe == 'Yes') ? 'checked' : ''; ?> class="themedev-switch-input" value="Yes" id="themedev-enable_featured"> 
				<label class="themedev-checkbox-switch" for="themedev-enable_featured">
					<span class="themedev-label-switch" data-active="ON" data-inactive="OFF"></span>
				</label>
				
				<span class="themedev-document-info block"> <?php echo esc_html__('Enable featured videos for YouTube, Vimeo and Dailymotion.', 'themedev-next-feature');?></span>
				
			</div>
		</div>
	</div>
	<div class="<?php echo esc_attr('themeDev-form');?>">
		<div class="flex-form">
			<div class="left-div">
				<label class="inline-label">
					<?php echo esc_html__('Enable Featured Gallery ', 'themedev-next-feature');?>
				</label>
			</div>
			<div class="right-div">
				<?php
				$loginEbnalbe = isset($getGeneral['general']['gallery']['ebable']) ? 'Yes' : 'No';
				if( !isset($getGeneral['general']) ){
					//$loginEbnalbe = 'Yes';
				}
				?>
				<input type="checkbox" name="themedev[general][gallery][ebable]" <?php echo ($loginEbnalbe == 'Yes') ? 'checked' : ''; ?> class="themedev-switch-input" value="Yes" id="themedev-enable_gallery"> 
				<label class="themedev-checkbox-switch" for="themedev-enable_gallery">
					<span class="themedev-label-switch" data-active="ON" data-inactive="OFF"></span>
				</label>
				
				<span class="themedev-document-info block"> <?php echo esc_html__('Enable featured gallery for Multiple Images.', 'themedev-next-feature');?></span>
				
			</div>
		</div>
	</div>
	<div class="<?php echo esc_attr('themeDev-form');?>">
		<div class="flex-form">
			<div class="left-div">
				<label class="inline-label">
					<?php echo esc_html__('Select Custom Post Type ', 'themedev-next-feature');?>
				</label>
			</div>
			<div class="right-div">
				<ul class="next-custom-post-ul">
				<?php
				$custom_post = isset($getGeneral['general']['custom_post']) ? $getGeneral['general']['custom_post'] : [];
				
				$cpt = self::__cpt_list();
				foreach ( $cpt as $k=>$v ) {
				  ?>
				  <li>
					<input type="checkbox" <?php echo (in_array($k, $custom_post)) ? 'checked' : '';?> name="themedev[general][custom_post][]" id="custom_post_<?php echo $k;?>" value="<?php echo $k;?>">
					<label for="custom_post_<?php echo $k;?>"><?php echo $v;?></label>
				  </li>
				  <?php
				}
				?>
				</ul>
				<span class="themedev-document-info block"> <?php echo esc_html__('Enable featured gallery for Multiple Images.', 'themedev-next-feature');?></span>
				
			</div>
		</div>
	</div>
	
	<div class="<?php echo esc_attr('themeDev-form');?>">
		<div class="flex-form">
			<div class="left-div">
				<label class="inline-label">
					<?php echo esc_html__('Select Content Position ', 'themedev-next-feature');?>
				</label>
			</div>
			<div class="right-div">
				<ul class="next-custom-post-ul">
				<?php
				$content_position = isset($getGeneral['general']['content_position']) ? $getGeneral['general']['content_position'] : 'before_content';
				
				  ?>
				  <li>
					<input type="radio" <?php echo ($content_position == 'before_content') ? 'checked' : '';?> name="themedev[general][content_position]" id="custom_post_before_content" value="before_content">
					<label for="custom_post_before_content"><?php echo 'Before Content';?></label>
				  </li>
				  <li>
					<input type="radio" <?php echo ($content_position == 'after_content') ? 'checked' : '';?> name="themedev[general][content_position]" id="custom_post_after_content" value="after_content">
					<label for="custom_post_after_content"><?php echo 'After Content';?></label>
				  </li>
				  <li>
					<input type="radio" <?php echo ($content_position == 'replace_content') ? 'checked' : '';?> name="themedev[general][content_position]" id="custom_post_replace_content" value="replace_content">
					<label for="custom_post_replace_content"><?php echo 'Replace Content';?></label>
				  </li>
				</ul>
				<span class="themedev-document-info block"> <?php echo esc_html__('Enable featured gallery for Multiple Images.', 'themedev-next-feature');?></span>
				
			</div>
		</div>
	</div>
	<div class="<?php echo esc_attr('themeDev-form');?>">
		<button type="submit" name="themedev-featured-general" class="themedev-submit"> <?php echo esc_html__('Save ', 'themedev-next-feature');?></button>
	</div>
</form>