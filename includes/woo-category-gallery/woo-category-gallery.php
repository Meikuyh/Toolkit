<?php
add_action('product_cat_add_form_fields', 'taxonomy_add_new_meta_field', 15, 1);
add_action('product_cat_edit_form_fields', 'taxonomy_edit_meta_field', 15, 1);

//Product Cat Create page
function taxonomy_add_new_meta_field()
{
	$img_placeholder = plugin_dir_url( __FILE__ ).'placeholder.png';
	?>
    <div class="wrap-gallery ovic_categories">
       	<input type="hidden" name="cate_gallery" id="cate_gallery" value="">
       	<input type="hidden" name="cate_gallery_place_holder" value="<?php echo esc_url($img_placeholder); ?>">
        <div class="wrap-input">
        	<h3 class="title-page"><?php esc_html_e('galleries','worldmart-toolkit');?></h3>
        </div>
        <input type='button' class="button-primary" value="Add Images" id="add_gallery"/>
		<input type='button' class="button button-secondary" value="Delete All" id="delete_all_gallery"/>
    </div>
    <?php
}
//Product Cat Edit page
function taxonomy_edit_meta_field($term)
{
	$term_id = $term->term_id;
	$data = get_term_meta($term_id, 'cate_gallery', false);
	if(!empty($data)) $galleries = json_decode($data[0], true);
	?>
	<tr class="form-field">
		<th scope="row" valign="top"><label >Galleries</label></th>
		<td>
			<div class="wrap-gallery">
				<input type="hidden" name="cate_gallery" id="cate_gallery" value='<?php if( !empty($data[0])){ echo $data[0]; } ?>' />
				<div class="wrap-input">
					<?php if (!empty($galleries)) { ?>
						<?php foreach($galleries['items'] as $id => $image) { ?>
							<div class="wrap-element element<?php echo $image['order']; ?>" id="element<?php echo $image['order']; ?>">

								<div class="wrap-option">
									<a class="btn-option btn-modify myprefix_media_manager"><i class="fa fa-wrench" aria-hidden="true"></i></a>
									<a class="btn-option btn-dell"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
								</div>

								<div class="media" data-id="<?php echo $image['id']; ?>" data-order="<?php echo $image['order']; ?>">
									<img src="<?php echo $image['url']; ?>" alt="">
								</div>

							</div>
						<?php } ?>
					<?php } ?>
				</div>
				<input type='button' class="button-primary" value="Add Image" id="add_gallery"/>
				<input type='button' class="button button-secondary" value="Delete All" id="delete_all_gallery"/>
			</div>
		</td>
	</tr>
  	<?php
}
// Save extra taxonomy fields callback function.
function save_gallery($term_id)
{
	$images = filter_input(INPUT_POST, 'cate_gallery');
	update_term_meta($term_id, 'cate_gallery', $images);
}

add_action('edited_product_cat', 'save_gallery', 15, 1);
add_action('create_product_cat', 'save_gallery', 15, 1);

// As you are dealing with plugin settings,
// I assume you are in admin side
add_action( 'admin_enqueue_scripts', 'load_wp_media_files' );
function load_wp_media_files() {
	wp_enqueue_media();
	// Enqueue custom script that will interact with wp.media
	wp_enqueue_script('myprefix_script', plugin_dir_url( __FILE__ ).'js/myscript.js' , array('jquery'), '0.1' );
	wp_enqueue_style('mystyle_enq', plugin_dir_url( __FILE__ ).'css/mystyle.css' , array(), '1.0', 'all');
	wp_enqueue_style('font-awesome', plugin_dir_url( __FILE__ ).'css/font-awesome.css' , array(), '4.7.0', 'all');
}
