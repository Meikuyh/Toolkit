<?php
class worldmart_quote_box_widget extends WP_Widget
{

	function __construct()
    {
        /* Widget settings. */
        $widget_ops = array( 'classname' => 'worldmart-quote-box-widget', 'description' => esc_html__('A widget that displays your quote to sidebar box quote', 'worldmart') );
        /* Create the widget. */
        parent::__construct( 'worldmart_qoute_box_widget', esc_html__('Worldmart: Quote box', 'worldmart'), $widget_ops );

        add_action( 'admin_print_scripts', array( $this, 'load_wp_media_files') );
    }


	function load_wp_media_files(){
		wp_enqueue_script('jquery');
		wp_enqueue_media();
		wp_enqueue_script('quote_box_script', trailingslashit ( plugin_dir_url( __FILE__ ) ).'js/quote_box.js' , array('jquery'), '0.1', true);
		wp_enqueue_style('quote_box_style', trailingslashit ( plugin_dir_url( __FILE__ ) ).'css/quote_box.css' , array(), '1.0', 'all');
	}


    /**form
    *==========================
    */
    function form( $instance){
    	$defaults = array( 
    		'title' => esc_html__( 'Quote of the Day', 'worldmart'),
            'content' => '',
    		'media-item' => '',
            );
        $instance = wp_parse_args( (array) $instance, $defaults ); 
        ?>

		<p>
            <label for="<?php echo esc_attr($this->get_field_id( 'title' )); ?>"><?php esc_html_e('Title:', 'worldmart'); ?></label>
            <input  type="text" class="widefat" id="<?php echo esc_attr($this->get_field_id( 'title' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'title' )); ?>" value="<?php echo balanceTags($instance['title']); ?>"  />
        </p>

    	<p>
    		<label for="<?php echo esc_attr($this->get_field_id( 'content' )); ?>"><?php esc_html_e('Content:', 'worldmart'); ?></label>
			<textarea class="widefat" rows="10" cols="15" id="<?php echo esc_attr($this->get_field_id( 'content' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'content' )); ?>"><?php echo balanceTags($instance['content']); ?></textarea>
		</p>

		<p class="wap-media-box">
            <label for="<?php echo esc_attr($this->get_field_id( 'media-item' )); ?>" class="lbl-media-quote-box"><?php esc_html_e('Input an image: ', 'worldmart'); ?></label>
            <input type="hidden" name="<?php echo esc_attr($this->get_field_name( 'media-item' )); ?>" value="<?php echo esc_attr($instance['content']); ?>" id="<?php echo esc_attr($this->get_field_id( 'media-item')); ?>">
            <img src="<?php echo esc_url($instance['media-item']); ?>" style=" margin: 10px; display: inline-block;">
        </p>
        <p class="wrap-control">
            <a id="upload-media-quote-box-121212" class="button upload-media-quote-box">Upload</a>
            <a id="delete-button" class="button delete-media-quote-box">Clear</a>
        </p>
        <?php

    }


    /*update form
    *==========================
    */
    function update( $new_instance, $old_instance){
    	$instance = $old_instance;
    	$instance['title'] = (!empty($new_instance['title'])) ? esc_html($new_instance['title']) :'';
        $instance['content'] = (!empty($new_instance['content'])) ? esc_html($new_instance['content']) :'';
    	$instance['media-item'] = (!empty($new_instance['media-item'])) ? $new_instance['media-item'] :'';

    	return $instance;
    }


    /*show in froned
    *==========================
    */
    function widget( $args, $instance){
    	extract($args);
    	$title = apply_filters('widget_title', $instance['title']);
        echo balanceTags($before_widget);
        if ( trim( $title ) != '' ) { echo $before_title . esc_html($title) . $after_title; }
    	?>
            <?php if( !empty($instance['media-item']) ){ ?>
            <div class="media">
                <figure>
                    <img src="<?php echo esc_url($instance['media-item'] );?>" alt="<?php echo esc_attr($title); ?>">
                </figure>
            </div>
            <?php } ?>
            <p class="content quote_text"><?php echo esc_html($instance['content']); ?></p>
    	<?php
    	echo balanceTags($after_widget);
    }

}

function worldmart_register_quote_box_widget() {
    register_widget( 'worldmart_quote_box_widget' );
}
add_action('widgets_init','worldmart_register_quote_box_widget');