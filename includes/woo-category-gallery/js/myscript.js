(function ($) {
    "use strict";

	$(document).ready( function($) {

		if( $('table.wp-list-table').length ){
			var _img_placeholder = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAACCAYAAACZgbYnAAAAE0lEQVQImWP4////f4bLly//BwAmVgd1/w11/gAAAABJRU5ErkJggg==';
			if($('.ovic_categories input[name=cate_gallery_place_holder]').length && $('.ovic_categories input[name=cate_gallery_place_holder]').val() != ''){
				_img_placeholder = $('.ovic_categories input[name=cate_gallery_place_holder]').val();
			}
			$('table.wp-list-table td.thumb img').each(function(index, el) {
				var _src = $(this).attr('src');
				if(typeof _src == 'undefined' || _src ==''){
					$(this).attr('src',_img_placeholder);
				}	
			});
		}
	});

	var $document = $(document),
		count_elm = $('input[name="myprefix_image_id"]').length ;

	/**
	 * add new element button
	 * */
	$document.on('click', '#kute_add_gallery_110', function(event) {
	 	event.preventDefault();
		var image_frame;

		if( image_frame ){
			image_frame.open();
			return;
		}

		/*Define image_frame as wp.media object*/
		image_frame = wp.media.frames.gallery = wp.media({
			title: 'Choose images',
			multiple: true,
			button: { text: 'Use images'}
		});

		image_frame.on('select', function(){
			var order 		= get_order(),
				selection 	= image_frame.state().get('selection');

			selection.map( function( attachment ) {
				attachment = attachment.toJSON();
				add_element(order, attachment.id, attachment.name, attachment.type, attachment.url);
				order ++;
			});
		});
		image_frame.open();
	 });

	/**
	* edit button
	* */
	$document.on('click', '.btn-option.btn-modify', function(e){
		e.preventDefault();
		var html_element 	= $(this).closest('.wrap-element'),
			order 			= html_element.find('.media').attr('data-order'),
			image_frame;

		if( image_frame ){
			image_frame.open();
			return;
		}

		/*Define image_frame as wp.media object*/
		image_frame = wp.media({
			title: 'Choose an image',
			button: {
				text: 'Use image'
			},
			multiple: false
		});

		image_frame.on('select', function(){
			var selection = image_frame.state().get('selection').first().toJSON();
			count_elm++;
			if( html_element.length ){
				update_data(order, selection.id, selection.name, selection.type, selection.url);
				update_html_info(html_element, selection.id, selection.name, selection.type, selection.url);
			}
		});
		image_frame.open();
	});

	/**
	* call gallery frame
	* */
	function call_gallery_frame(){
		var id = $(this).closest('.wrap-element').attr('id'),
			image_frame;
		if( image_frame ){
			image_frame.open();
			return;
		}

		/*Define image_frame as wp.media object*/
		image_frame = wp.media({
			title: 'Choose an image',
			button: {
				text: 'Use image'
			},
			multiple: false
		});

		image_frame.on( 'select' , function(){
			var selection =  image_frame.state().get('selection').first().toJSON();
			count_elm++;
			if(id.length > 0){
				update_data(id, selection.id, selection.url);
			}
		});

		image_frame.open();

	}

	/**
	* add element function
	* add new a gallery element
	* */
	function add_element( order , img_id , img_name , img_type , img_url ){
        var html = '<div class="wrap-element element'+order+'" id="element'+order+'">';
        html +='<div class="wrap-option">';
        html +='<a class="btn-option btn-modify myprefix_media_manager"><i class="fa fa-wrench" aria-hidden="true"></i></a>';
        html +='<a class="btn-option btn-dell"><i class="fa fa-trash-o" aria-hidden="true"></i></a>';
        html +='</div>';
		html +='<div class="media" data-id="'+img_id+'" data-order="'+order+'"><img src="'+img_url+'" alt="'+img_name+'"></div>';
        html +='</div>';
        $('.wrap-gallery .wrap-input').append( html );
		add_new_item( order, img_id, img_name, img_type, img_url );
    }


	/**
	* add new item in json string
	* */
	function add_new_item( order , img_id , img_name , img_type , img_url ){
		var obj 	= $('input[name="cate_gallery"]').val(),
			new_obj = {'id': img_id, 'name': img_name, 'type': img_type, 'url': img_url, 'order': order},
			json 	= {items: [{'id': img_id, 'name': img_name, 'type': img_type, 'url': img_url, 'order': order}]};
		if( obj !== ''){
			json = JSON.parse(obj);
			json.items.push(new_obj);
		}
		$('input[name="cate_gallery"]').val( JSON.stringify( json ) );
	}


	/**
	 * delete button
	 * */
	$document.on( 'click', '.btn-option.btn-dell', function(){
		var $this = $(this),
			order = $this.closest('.wrap-element').find('.media').attr('data-order'),
			list  = $('input[name="cate_gallery"]').val();

		list = JSON.parse( list )
		for( var i = 0 ; i < list.items.length ; i++ ){
			if( list.items[i].order == order ){
				list.items.splice( i , 1 );
				break;
			}
		}
		$('input[name="cate_gallery"]').val(JSON.stringify(list));
		$this.closest('.wrap-element').remove();
	});

	/**
	* delete all button
	* */
	$document.on('click', '#kute_delete_all_gallery_110', function(event) {
		event.preventDefault();
		var  delete_all = confirm("Do you want deleting all images");
		if ( delete_all ){
			$(this).closest('.wrap-gallery').find('.wrap-input .wrap-element').remove();
			$('input[name="cate_gallery"]').val('');
		}
	});

	/**
	* update data function
	*
	* */
	function update_data(order, new_img_id, img_name, img_type, img_url){
	var list 	= $('input[name="cate_gallery"]').val(),
		new_obj = {'id': new_img_id, 'name': img_name, 'type': img_type, 'url': img_url},
		json 	= JSON.parse( list );
		for( var i = 0 ; i < json.items.length; i++ ){
			if( json.items[i].order == order ){
				json.items[i].id 	= new_img_id;
				json.items[i].name 	= img_name;
				json.items[i].type 	= img_type;
				json.items[i].url 	= img_url;
			}
		}
		$('input[name="cate_gallery"]').val( JSON.stringify( json ) );
	}

    /**
	 * update_html_info function
	 * */
	function update_html_info( html_element , img_id , img_name , img_type , img_url ){
		html_element.find('.media').attr('data-id',img_id).find('img').attr({src: img_url, alt: img_name});
	}

	/**
	 * get order function
	 * get a new order able*/
	function get_order(){
		var $lists 		= $('input[name="cate_gallery"]').val(),
			max_order 	= 0;
		if( $lists.length ){
			var json = JSON.parse( $lists );
			for(var i = 0 ; i < json.items.length ; i++){
				if( json.items[i].order > max_order){
					max_order = json.items[i].order;
				}
			}
		}
		return max_order + 1 ;
	}
})(jQuery);