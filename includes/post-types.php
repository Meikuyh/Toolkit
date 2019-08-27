<?php
if( !class_exists('Worldmart_Toolkit_Posttype')){
    class Worldmart_Toolkit_Posttype {

        public function __construct() {
            add_action( 'init', array( &$this, 'init' ),9999 );
        }

        public static function init() {
            /*Mega menu */
            $args  = array(
                'labels'              => array(
                    'name'               => __( 'Mega Builder', 'worldmart-toolkit' ),
                    'singular_name'      => __( 'Mega menu item', 'worldmart-toolkit' ),
                    'add_new'            => __( 'Add new', 'worldmart-toolkit' ),
                    'add_new_item'       => __( 'Add new menu item', 'worldmart-toolkit' ),
                    'edit_item'          => __( 'Edit menu item', 'worldmart-toolkit' ),
                    'new_item'           => __( 'New menu item', 'worldmart-toolkit' ),
                    'view_item'          => __( 'View menu item', 'worldmart-toolkit' ),
                    'search_items'       => __( 'Search menu items', 'worldmart-toolkit' ),
                    'not_found'          => __( 'No menu items found', 'worldmart-toolkit' ),
                    'not_found_in_trash' => __( 'No menu items found in trash', 'worldmart-toolkit' ),
                    'parent_item_colon'  => __( 'Parent menu item:', 'worldmart-toolkit' ),
                    'menu_name'          => __( 'Menu Builder', 'worldmart-toolkit' ),
                ),
                'hierarchical'        => false,
                'description'         => __('Mega Menus.', 'worldmart-toolkit'),
                'supports'            => array('title', 'editor'),
                'public'              => true,
                'show_ui'             => true,
                'show_in_menu'        => 'worldmart',
                'menu_position'       => 40,
                'show_in_nav_menus'   => true,
                'publicly_queryable'  => false,
                'exclude_from_search' => true,
                'has_archive'         => false,
                'query_var'           => true,
                'can_export'          => true,
                'rewrite'             => false,
                'capability_type'     => 'page',
                'menu_icon'           => 'dashicons-welcome-widgets-menus',
            );
            register_post_type( 'megamenu', $args);

            /*Popup builder */
            $args  = array(
                'labels'              => array(
                    'name'               => __( 'Popup Builder', 'worldmart-toolkit' ),
                    'singular_name'      => __( 'Popup item', 'worldmart-toolkit' ),
                    'add_new'            => __( 'Add new', 'worldmart-toolkit' ),
                    'add_new_item'       => __( 'Add new menu item', 'worldmart-toolkit' ),
                    'edit_item'          => __( 'Edit popup item', 'worldmart-toolkit' ),
                    'new_item'           => __( 'New popup item', 'worldmart-toolkit' ),
                    'view_item'          => __( 'View popup item', 'worldmart-toolkit' ),
                    'search_items'       => __( 'Search popup items', 'worldmart-toolkit' ),
                    'not_found'          => __( 'No popup items found', 'worldmart-toolkit' ),
                    'not_found_in_trash' => __( 'No popup items found in trash', 'worldmart-toolkit' ),
                    'parent_item_colon'  => __( 'Parent popup item:', 'worldmart-toolkit' ),
                    'menu_name'          => __( 'Popup Builder', 'worldmart-toolkit' ),
                ),
                'hierarchical'        => false,
                'description'         => __('Popup.', 'worldmart-toolkit'),
                'supports'            => array('title', 'editor'),
                'public'              => true,
                'show_ui'             => true,
                'show_in_menu'        => 'worldmart',
                'menu_position'       => 40,
                'show_in_nav_menus'   => false,
                'publicly_queryable'  => false,
                'exclude_from_search' => true,
                'has_archive'         => false,
                'query_var'           => true,
                'can_export'          => true,
                'rewrite'             => false,
                'capability_type'     => 'page',
                'menu_icon'           => 'dashicons-welcome-widgets-menus',
            );
            register_post_type( 'megapopup', $args);

            /* Footer */
            $args =  array(
                'labels'              => array(
                    'name'               => __( 'Footers', 'worldmart-toolkit' ),
                    'singular_name'      => __( 'Footers', 'worldmart-toolkit' ),
                    'add_new'            => __( 'Add New', 'worldmart-toolkit' ),
                    'add_new_item'       => __( 'Add new footer', 'worldmart-toolkit' ),
                    'edit_item'          => __( 'Edit footer', 'worldmart-toolkit' ),
                    'new_item'           => __( 'New footer', 'worldmart-toolkit' ),
                    'view_item'          => __( 'View footer', 'worldmart-toolkit' ),
                    'search_items'       => __( 'Search template footer', 'worldmart-toolkit' ),
                    'not_found'          => __( 'No template items found', 'worldmart-toolkit' ),
                    'not_found_in_trash' => __( 'No template items found in trash', 'worldmart-toolkit' ),
                    'parent_item_colon'  => __( 'Parent template item:', 'worldmart-toolkit' ),
                    'menu_name'          => __( 'Footer Builder', 'worldmart-toolkit' ),
                ),
                'hierarchical'        => false,
                'description'         => __('To Build Template Footer.', 'worldmart-toolkit'),
                'supports'            => array( 'title', 'editor','page-attributes' ),
                'public'              => true,
                'show_ui'             => true,
                'show_in_menu'        => 'worldmart',
                'menu_position'       => 40,
                'show_in_nav_menus'   => true,
                'publicly_queryable'  => false,
                'exclude_from_search' => true,
                'has_archive'         => false,
                'query_var'           => true,
                'can_export'          => true,
                'rewrite'             => false,
                'capability_type'     => 'page',
            );
            register_post_type( 'footer', $args);
        }
    }

    new Worldmart_Toolkit_Posttype();
}
