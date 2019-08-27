jQuery(document).ready( function($) {
    /**
     * add new element button
     * */
    jQuery(document).on('click', 'a.layered_upload_media_button', function(e){
        e.preventDefault();
        var _this = jQuery(this),
            image_frame;
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
            _this.parent().find("input.worldmart_layered_img_attach").val(selection.url);
            _this.parent().find("img.worldmart_layered_img_show").attr('src', selection.url);
        });
        image_frame.open();
        _this.closest('.worldmart_layered_container').find('input[type=text]').trigger('change');
    });
    jQuery(document).on('click', 'a.layered_delete_media_button', function(e){
        e.preventDefault();
        var _this = jQuery(this);
        _this.parent().find("input.worldmart_layered_img_attach").val('');
        _this.parent().find("img.worldmart_layered_img_show").attr('src', '');
        _this.closest('.worldmart_layered_container').find('input[type=text]').trigger('change');
    });

});