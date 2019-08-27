<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
class Worldmart_Terms_Of_Service extends WC_Widget {
    public function __construct() {
        $this->widget_cssclass    = 'worldmart-terms-of-service term-service-item';
        $this->widget_description = esc_html__( 'Shows terms of service', 'worldmart' );
        $this->widget_id          = 'worldmart_terms_of_service';
        $this->widget_name        = esc_html__( 'Worldmart: Terms Of Service', 'worldmart' );
        parent::__construct();
    }

    public function update( $new_instance, $old_instance ) {
        $this->init_settings();
        return parent::update( $new_instance, $old_instance );
    }
    public function form( $instance ) {
        $defaults = array(
            'font_icon' => '',
            'title' => '',
            'sub_title' => '',
            'desc' => '',
            'url' => '#',
            );
        $instance = wp_parse_args( (array) $instance, $defaults );
        ?>
        <div >
            <p>
                <label for="<?php echo $this->get_field_id( 'font_icon' ); ?>"><?php esc_html_e('Font icon class:', 'worldmart'); ?></label>
                <input  type="text" class="widefat " id="<?php echo $this->get_field_id( 'font_icon' ); ?>" name="<?php echo $this->get_field_name( 'font_icon' ); ?>" value="<?php echo esc_attr($instance['font_icon']) ; ?>"  />
            </p>
            <p>
                <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php esc_html_e('Title:', 'worldmart'); ?></label>
                <input  type="text" class="widefat " id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr($instance['title']) ; ?>"  />
            </p>
            <p>
                <label for="<?php echo $this->get_field_id( 'sub_title' ); ?>"><?php esc_html_e('Subtitle:', 'worldmart'); ?></label>
                <input  type="text" class="widefat " id="<?php echo $this->get_field_id( 'sub_title' ); ?>" name="<?php echo $this->get_field_name( 'sub_title' ); ?>" value="<?php echo esc_attr($instance['sub_title']) ; ?>"  />
            </p>
            <p>
                <label for="<?php echo esc_attr($this->get_field_id( 'desc' )); ?>"><?php esc_html_e('Describer:', 'worldmart'); ?></label>
                <textarea class="widefat" rows="5" cols="15" id="<?php echo esc_attr($this->get_field_id( 'desc' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'desc' )); ?>"><?php echo balanceTags($instance['desc']); ?></textarea>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id( 'url' ); ?>"><?php esc_html_e('Link to page:', 'worldmart'); ?></label>
                <input  type="text" class="widefat " id="<?php echo $this->get_field_id( 'url' ); ?>" name="<?php echo $this->get_field_name( 'url' ); ?>" value="<?php echo esc_attr($instance['url']) ; ?>"  />
            </p>
        </div>
<?php

    }

    public function init_settings() {
        $this->settings = array(
            'title' => array(
                'type'  => 'text',
                'std'   => '',
                'label' => esc_html__( 'Title', 'worldmart' )
            ),
            'sub_title' => array(
                'type'  => 'text',
                'std'   => '',
                'label' => esc_html__( 'Subtitle', 'worldmart' )
            ),
            'desc' => array(
                'type'  => 'text',
                'std'   => '',
                'label' => esc_html__( 'Describer', 'worldmart' )
            ),
            'font_icon' => array(
                'type'  => 'text',
                'std'   => 0,
                'label' => esc_html__( 'Font icon class', 'worldmart' )
            ),
            'url' => array(
                'type'  => 'text',
                'std'   => '#',
                'label' => esc_html__( 'Link to page', 'worldmart' )
            ),
        );
    }

    public function widget( $args, $instance ) {
        $html = '';
        if($instance['title'])
            $html .='<h3 class="big-title">'.esc_html($instance['title']).'</h3>';
        if($instance['sub_title'])
            $html .='<span class="small-title">'.esc_html($instance['sub_title']).'</span>';
        if($instance['desc'])
            $html .='<p class="desc-text">'.esc_html($instance['desc']).'</p>';
        $star_link = $end_link ='';
        if($instance['url']){
            $star_link = '<a href="'.esc_attr($instance['url']).'">';
            $end_link ='</a>';
        }
        echo balanceTags($args['before_widget']);
        ?>
        <?php echo worldmart_output($star_link);?>
        <?php if($instance['font_icon']): ?>
            <div class="wrap-banner"><i class="<?php echo esc_attr($instance['font_icon']); ?>"></i></div>
        <?php endif;?>
        <?php if($html):?>
            <div class="wrap-text-content"><?php echo worldmart_output($html); ?></div>
        <?php endif;?>
        <?php echo worldmart_output($end_link);?>
        <?php
        echo balanceTags($args['after_widget']);
    }

}
add_action( 'widgets_init', 'register_worldmart_terms_of_service' );
function register_worldmart_terms_of_service() {
    register_widget( 'Worldmart_Terms_Of_Service' );
}