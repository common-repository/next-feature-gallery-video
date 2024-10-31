jQuery(document).ready(function() {

	var meta_image_frame;
	var meta_mobileimage_frame;

	jQuery('#next_portfolio_image_button').click(function(e){

			// Prevents the default action from occuring.
			e.preventDefault();

			// If the frame already exists, re-open it.
			if ( meta_image_frame ) {
					meta_image_frame.open();
					return;
			}

			// Sets up the media library frame
			meta_image_frame = wp.media.frames.meta_image_frame = wp.media({
					title: wfp_portfolio_image.title,
					button: { text:  wfp_portfolio_image.button },
					library: { type: 'image' },
			});

			meta_image_frame.on('select', function(){

			var media_attachment = meta_image_frame.state().get('selection').first().toJSON();
			jQuery('#next_portfolio_image').val(media_attachment.url);
			jQuery('.next_portfolio_image_container').append('<span class="next_portfolio_close"></span>');
			if (typeof media_attachment.sizes.thumbnail === 'undefined') {
				jQuery('#next_portfolio_image_src').attr('src', media_attachment.url);	
			} else {
				jQuery('#next_portfolio_image_src').attr('src', media_attachment.sizes.thumbnail.url);	
			}
				});

			meta_image_frame.open();
	});

	jQuery('#next_portfolio_mobileimage_button').click(function(e){

			e.preventDefault();
			if ( meta_mobileimage_frame ) {
					meta_mobileimage_frame.open();
					return;
			}

			meta_mobileimage_frame = wp.media.frames.meta_mobileimage_frame = wp.media({
					title: wfp_portfolio_mobileimage.title,
					button: { text:  wfp_portfolio_mobileimage.button },
					library: { type: 'image' },
			});

			meta_mobileimage_frame.on('select', function(){

				var media_attachment = meta_mobileimage_frame.state().get('selection').first().toJSON();

				jQuery('#next_portfolio_mobileimage').val(media_attachment.url);
				jQuery('.next_portfolio_mobileimage_container').append('<span class="next_mobileportfolio_close"></span>');
				if (typeof media_attachment.sizes.thumbnail === 'undefined') {
					jQuery('#next_portfolio_mobileimage_src').attr('src', media_attachment.url);
				} else {
					jQuery('#next_portfolio_mobileimage_src').attr('src', media_attachment.sizes.thumbnail.url);
				}
			});

			meta_mobileimage_frame.open();
	});

	var meta_gallery_frame;
	jQuery('#next_portfolio_gallery_button').click(function(e){
		e.preventDefault();
		if ( meta_gallery_frame ) {
				meta_gallery_frame.open();
				return;
		}
		meta_gallery_frame = wp.media.frames.meta_gallery_frame = wp.media({
				frame: "post",
				state: "next-portfolio-gallery",
				title: next_portfolio_gallery.title,
				button: { text:  next_portfolio_gallery.button },
				library: { type: 'image' },
				multiple: true
		 });

		meta_gallery_frame.states.add([
			new wp.media.controller.Library({
				id:         'next-portfolio-gallery',
				title:      'Select Images for Featured Gallery',
				priority:   20,
				toolbar:    'main-gallery',
				filterable: 'uploaded',
				library:    wp.media.query( meta_gallery_frame.options.library ),
				multiple:   meta_gallery_frame.options.multiple ? 'add' : false,
				editable:   true,
				allowLocalEdits: true,
				displaySettings: true,
				displayUserSettings: true
			}),
		]);

		meta_gallery_frame.on('open', function() {
			var selection = meta_gallery_frame.state('next-portfolio-gallery').get('selection');
			//var library = meta_gallery_frame.state('gallery-edit').get('library');
			var ids = jQuery('#next_portfolio_gallery').val();
			if (ids) {
				idsArray = ids.split(',');
				idsArray.forEach(function(id) {
					attachment = wp.media.attachment(id);
					attachment.fetch();
					selection.add( attachment ? [ attachment ] : [] );
				});
				
			}
		});

		meta_gallery_frame.on('ready', function() {
			jQuery( '.media-modal' ).addClass( 'no-sidebar' );
			//fixBackButton();
		});
		
		meta_gallery_frame.on('update', function() {
		//meta_gallery_frame.on('select', function() {
			//alert();
			var imageIDArray = [];
			var imageHTML = '';
			var metadataString = '';
			imagesdata = meta_gallery_frame.state('next-portfolio-gallery').get('selection');
			imageHTML += '<ul class="next_portfolio_gallery_list">';
			
			imagesdata.each(function(attachment) {
				console.debug(attachment.attributes);
				imageIDArray.push(attachment.attributes.id);
				
				if (typeof attachment.attributes.sizes.thumbnail === 'undefined') {
					imageHTML += '<li><div class="next_portfolio_gallery_container"><span class="next_portfolio_gallery_close"><img id="'+attachment.attributes.id+'" src="'+attachment.attributes.url+'"></span></div></li>';
				} else {
					imageHTML += '<li><div class="next_portfolio_gallery_container"><span class="next_portfolio_gallery_close"><img id="'+attachment.attributes.id+'" src="'+attachment.attributes.sizes.thumbnail.url+'"></span></div></li>';
				}
			});
			imageHTML += '</ul>';
			metadataString = imageIDArray.join(",");
			if (metadataString) {
				jQuery("#next_portfolio_gallery").val(metadataString);
				jQuery("#next_portfolio_gallery_src").html(imageHTML);
				setTimeout(function(){
					//ajaxUpdateTempMetaData();
				},0);
			}
		}); 
		meta_gallery_frame.open();
	});

	jQuery(document.body).on('click', '.next_portfolio_close', function(event){
		event.preventDefault();
		if (confirm('Are you sure you want to remove this image?')) {
				jQuery('.next_portfolio_image_container').remove();
				jQuery("#wfp_portfolio_image").val('');
		}
	});

	jQuery(document.body).on('click', '.wfp_mobileportfolio_close', function(event){
		event.preventDefault();
		if (confirm('Are you sure you want to remove this image?')) {
				jQuery('.next_portfolio_mobileimage_container').remove();
				jQuery("#wfp_portfolio_mobileimage").val('');
		}
	});


	jQuery(document.body).on('click', '.next_portfolio_gallery_close', function(event){
		event.preventDefault();
		if (confirm('Are you sure you want to remove this image?')) {
			var removedImage = jQuery(this).children('img').attr('id');
			var oldGallery = jQuery("#next_portfolio_gallery").val();
			var newGallery = oldGallery.replace(','+removedImage,'').replace(removedImage+',','').replace(removedImage,'');
			jQuery(this).parents().eq(1).remove();
			jQuery("#next_portfolio_gallery").val(newGallery);
		}
	});
});
