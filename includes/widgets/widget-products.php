<?php
class worldmart_products_widget extends WP_Widget
{
    function __construct()
    {
        /* Widget settings. */
        $widget_ops = array( 'classname' => 'worldmart_products product_list', 'description' => esc_html__('A widget that displays your custom product list', 'worldmart') );
        /* Create the widget. */
        parent::__construct( 'worldmart_products', esc_html__('Worldmart: Custom products', 'worldmart'), $widget_ops );
    }
    function widget( $args, $instance )
    {
        extract( $args );
        $title = apply_filters('worldmart_product_widget_title', $instance['title'] );
        $number = $instance['number'];
        $categories = $instance['categories'];
        $produc_type = $instance['sort_by'];
        $btn = apply_filters( 'worldmart_product_btn', __('View All', 'worldmart') );
        if($produc_type == 'top_rate'){
            $query_args = array(
                'posts_per_page' => $number,
                'no_found_rows'  => 1,
                'post_status'    => 'publish',
                'post_type'      => 'product',
                'meta_key'       => '_wc_average_rating',
                'orderby'        => 'meta_value_num',
                'order'          => 'DESC',
                'meta_query'     => WC()->query->get_meta_query(),
                'tax_query'      => WC()->query->get_tax_query(),
            );
        }elseif ($produc_type == 'new'){
            $query_args = array(
                'post_type' => 'product',
                'stock' => 1,
                'posts_per_page' => $number,
                'orderby' => 'date',
                'order' => 'DESC');

        }else {
            $query_args = array(
                'post_type' => 'product',
                'stock' => 1,
                'posts_per_page' => $number,
                'orderby' => 'rand',
                'order' => 'DESC',
                );
        }
        if($instance['categories']){
            $query_args['product_cat'] = $instance['categories'];
        }
        $loop = new WP_Query($query_args);
        if( $loop->have_posts()){
            echo balanceTags($before_widget);
            if ( $title ) {
                echo balanceTags($before_title . esc_html($title). $after_title);
            }?>
            <ul class="product_list_widget">
                <?php

                    while ($loop->have_posts()){
                        $loop->the_post();
                        wc_get_template( 'content-widget-product.php', array( 'show_rating' => true ) );
                    }
                ?>

            </ul>
            <?php
            echo balanceTags($after_widget);
        }


    }
    function update( $new_instance, $old_instance )
    {
        $instance = $old_instance;
        $instance['title'] = strip_tags( $new_instance['title'] );
        $instance['number'] = ( $new_instance['number'] );
        $instance['categories'] = strip_tags($new_instance['categories']);
        $instance['sort_by'] = ( $new_instance['sort_by'] );
        return $instance;
    }
    function form( $instance )
    {
        $defaults = array(
            'title'         =>  esc_html__('Custom product list', 'worldmart'),
            'number'        =>  '2',
            'categories'    =>  'all',
            'sort_by'       =>  'random',
        );
        $instance = wp_parse_args( (array) $instance, $defaults ); ?>

        <p>
            <label for="<?php echo esc_attr($this->get_field_id( 'title' )); ?>"><?php esc_html_e('Title:', 'worldmart'); ?></label>
            <input  type="text" class="widefat" id="<?php echo esc_attr($this->get_field_id( 'title' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'title' )); ?>" value="<?php echo balanceTags($instance['title']); ?>"  />
        </p>

        <p>
            <?php
                $args = array('taxonomy' => 'product_cat', 'orderby' => 'name');
                $categories = get_categories( $args );
            ?>
            <label for="<?php echo $this->get_field_id('categories'); ?>"><?php _e( 'Filter by Category:' , 'worldmart' ); ?></label>
            <select id="<?php echo $this->get_field_id('categories'); ?>" name="<?php echo $this->get_field_name('categories'); ?>" class="widefat categories" style="width:100%;">
                <option value='0' <?php if ( 'all' == $instance['categories'] ) echo 'selected="selected"'; ?>>all categories</option>
                <?php foreach( $categories as $category ) { ?>
                    <option value='<?php echo $category->cat_name; ?>' <?php if ($category->cat_name == $instance['categories']) echo 'selected="selected"'; ?>><?php echo $category->cat_name; ?></option>
                <?php } ?>
            </select>

        </p>

        <p>

            <label for="<?php echo $this->get_field_id('sort_by'); ?>"><?php _e( 'Product type:' , 'worldmart' ); ?></label>
            <select id="<?php echo $this->get_field_id('sort_by'); ?>" name="<?php echo $this->get_field_name('sort_by'); ?>" class="widefat categories" style="width:100%;">
                <option value='random' <?php if ( 'random' == $instance['sort_by'] ) echo 'selected="selected"'; ?>>Random</option>
                <option value='new' <?php if ( 'new' == $instance['sort_by'] ) echo 'selected="selected"'; ?>>New</option>
                <option value='top_rate' <?php if ( 'top_rate' == $instance['sort_by'] ) echo 'selected="selected"'; ?>>Top rate</option>
            </select>

        </p>

        <p>
            <label for="<?php echo esc_attr($this->get_field_id( 'number' )); ?>"><?php esc_html_e('Max number:', 'worldmart'); ?></label>
            <input  type="number" class="widefat" id="<?php echo esc_attr($this->get_field_id( 'number' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'number' )); ?>" value="<?php echo balanceTags($instance['number']); ?>"  />
        </p>

        <?php
    }
}
add_action( 'widgets_init', 'worldmart_register_worldmart_products_widget' );
function worldmart_register_worldmart_products_widget() {
    register_widget( 'worldmart_products_widget' );
}