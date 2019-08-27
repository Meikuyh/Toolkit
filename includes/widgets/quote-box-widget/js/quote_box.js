jQuery(document).ready( function($) {
	/**
	 * add new element button
	 * */
    jQuery("a#upload-media-quote-box-121212").click(function(e)
	{
		e.preventDefault();
		var image_frame;
		if(image_frame){
			image_frame.open();
			return;
		}

		// Define image_frame as wp.media object
		image_frame = wp.media.frames.gallery = wp.media({
			title: 'Choose images',
			multiple: false,
			button: { text: 'Use images'}
		});

		image_frame.on('select', function()
		{
			var selection =  image_frame.state().get('selection').first().toJSON();
			fill_data(selection.url);
		});
		image_frame.open();
	});

    function fill_data(data){
    	jQuery("a#upload-media-quote-box-121212").closest('div.widget-content').find(".wap-media-box input[type='hidden']").val(data);
    	jQuery("a#upload-media-quote-box-121212").closest('div.widget-content').find(".wap-media-box img").attr('src', data);
    }
    jQuery("a#delete-button").click(function(e){
    	// e.preventDefault();
        jQuery("a#delete-button").closest('div.widget-content').find(".wap-media-box input[type='hidden']").val('');
        jQuery("a#delete-button").closest('div.widget-content').find(".wap-media-box img").attr('src', '');
	});
});