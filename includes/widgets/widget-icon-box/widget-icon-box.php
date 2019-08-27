<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
class Worldmart_Icon_Box_Widget extends WC_Widget {
    public function __construct() {
        $this->widget_cssclass    = 'worldmart-icon-box-widget icon-box-item';
        $this->widget_description = esc_html__( 'Shows icon box content', 'worldmart' );
        $this->widget_id          = 'worldmart_icon_box';
        $this->widget_name        = esc_html__( 'Worldmart: Icon box', 'worldmart' );
        parent::__construct();
        add_action( 'admin_print_scripts', array( $this, 'enqueue_resource') );
    }

    public function enqueue_resource(){
        wp_enqueue_script('jquery');
        wp_enqueue_script('icon_box_script', trailingslashit ( plugin_dir_url( __FILE__ ) ).'js/plugin.js' , array('jquery'), '0.1', true);
        wp_enqueue_style('icon_box_style', trailingslashit ( plugin_dir_url( __FILE__ ) ).'css/style.css' , array(), '1.0', 'all');
    }

    public function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $instance['content'] = (!empty($new_instance['content'])) ? $new_instance['content'] : '';
        return $instance;
    }
    public function form( $instance ) {
        $defaults = array( 'content' => '', );
        $instance = wp_parse_args( (array) $instance, $defaults );
        ?>
        <p class="frm_input">
            <label for="<?php echo $this->get_field_id( 'font_icon' ); ?>"><?php esc_html_e('Font icon class:', 'worldmart'); ?></label>
            <input  type="text" class="widefat " id="<?php echo $this->get_field_id( 'font_icon' ); ?>" name="font_icon" value=""  />

            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php esc_html_e('Title:', 'worldmart'); ?></label>
            <input  type="text" class="widefat " id="<?php echo $this->get_field_id( 'title' ); ?>" name="title" value=""  />

            <label for="<?php echo $this->get_field_id( 'sub_title' ); ?>"><?php esc_html_e('Subtitle:', 'worldmart'); ?></label>
            <input  type="text" class="widefat " id="<?php echo $this->get_field_id( 'sub_title' ); ?>" name="sub_title" value=""  />

            <label for="<?php echo esc_attr($this->get_field_id( 'desc' )); ?>"><?php esc_html_e('Describer:', 'worldmart'); ?></label>
            <textarea class="widefat" rows="3" id="<?php echo esc_attr($this->get_field_id( 'desc' )); ?>" name="desc"></textarea>

            <label for="<?php echo $this->get_field_id( 'url' ); ?>"><?php esc_html_e('Link to page:', 'worldmart'); ?></label>
            <input  type="text" class="widefat " id="<?php echo $this->get_field_id( 'url' ); ?>" name="url" value=""  />

            <input type="hidden" class="input_content" id="<?php echo $this->get_field_id( 'content' ); ?>" name="<?php echo $this->get_field_name('content')?>" value="<?php echo esc_attr($instance['content']) ; ?>" >

            <a  class="button btn_save">Add to list</a>
            <a  class="button btn_clear" style="margin-left: 10px">Clear</a>
        </p>
        <div class="result">
            <?php
            $dft = $instance['content'];
            $this->render_html($dft); ?>
        </div>
        <?php

    }

    public function widget( $args, $instance ) {
        if($instance['content'] && $this->is_json($instance['content'])){
            $data = json_decode($instance['content'], true);
            if(!empty($data) && is_array($data)){
                echo $args['before_widget'];?>
                <ul >
                <?php foreach ($data as $item){
                    $html = '';
                    if($item['title']) $html .='<h3 class="big-title">'.esc_html($item['title']).'</h3>';
                    if($item['sub_title']) $html .='<span class="small-title">'.esc_html($item['sub_title']).'</span>';
                    if($item['desc']) $html .= '<p class="desc-text">' . esc_html($item['desc']) . '</p>';
                    $star_link = $end_link = '';
                    if ($item['url']) {
                        $star_link = '<a href="'.esc_attr($item['url']).'">';
                        $end_link ='</a>';
                    }
                    ?>
                    <li>
                        <?php echo worldmart_output($star_link);?>
                        <?php if($item['font_icon']): ?>
                            <div class="wrap-banner"><i class="<?php echo esc_attr($item['font_icon']); ?>"></i></div>
                        <?php endif;?>
                        <?php if($html):?>
                            <div class="wrap-text-content"><?php echo worldmart_output($html); ?></div>
                        <?php endif;?>
                        <?php echo worldmart_output($end_link);?>
                    </li>
                <?php } ?>
                </ul>
                <?php
                echo $args['after_widget'];
            }
        }
    }
    public function is_json($str) {
        json_decode($str);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    public function render_html($data){
        if($this->is_json($data)){
            $new_data = json_decode($data, true);
            if(!empty($new_data) && is_array($new_data)){
                foreach ($new_data as $item){ ?>
                    <p class="item-box">
                        <label ><?php esc_html_e('Font icon class:', 'worldmart'); ?></label>
                        <span class="txt_info" name="_font_icon" ><?php echo esc_html($item['font_icon']); ?></span>

                        <label ><?php esc_html_e('Title:', 'worldmart'); ?></label>
                        <span class="txt_info "  name="_title"><?php echo esc_html($item['title']); ?></span>

                        <label ><?php esc_html_e('Subtitle:', 'worldmart'); ?></label>
                        <span class="txt_info "  name="_sub_title"><?php echo esc_html($item['sub_title']); ?></span>

                        <label ><?php esc_html_e('Describer:', 'worldmart'); ?></label>
                        <span class="txt_info" rows="3"  name="_desc"><?php echo esc_html($item['desc']); ?></span>

                        <label ><?php esc_html_e('Link to page:', 'worldmart'); ?></label>
                        <input class="txt_info "  name="_url" value="<?php echo esc_html($item['url']); ?>" disabled />
                        <a  class="button btn_delete" data-id="<?php echo esc_attr($item['id']); ?>" style="display: inline-block; float: left; text-align: center; font-size: 14px;">Delete</a>
                    </p>
                    <?php
                }
            }

        }else {
            return '';
        }
        return true;
    }
}
add_action( 'widgets_init', 'Register_Worldmart_Icon_Box_Widget' );
function Register_Worldmart_Icon_Box_Widget() {
    register_widget( 'Worldmart_Icon_Box_Widget' );
}