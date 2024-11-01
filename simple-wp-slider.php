<?php
    
/*
Plugin Name: Simple Responsive WP Slider
Plugin URI: 
Description: The simplest, most lightweight responsive slider plugin for WP. Create and customize unlimited sliders. Place your sliders with a shortcode, with optional title and alt text. Blocks and CSV import and export to use again on new sites coming soon!
Version: 1.0
Author: Niki Sebastino @ Access Advertising and PR
Author URI: https://visitaccess.com/
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: simple-wp-slider
*/


/* !0. TABLE OF CONTENTS */

/*
    
    1. HOOKS
        1.1 - registers all our custom shortcodes
        1.2 - register custom admin column headers
        1.3 - register custom admin column data
        1.4 - add ACF as dependency

        1.5 - load external files to public website

    2. SHORTCODES
        2.1 - sws_register_shortcodes()
        2.2 - sws_slider_shortcode()
        
    3. FILTERS
        3.1 - sws_slider_column_headers()
        3.2 - sws_slider_column_data()
        3.3 - registers custom plugin admin menus
        
    4. EXTERNAL STYLES & SCRIPTS
        4.1 - sws_public_scripts()
        
    5. ACTIONS
        5.1 - sws_check_wp_version
        
    6. HELPERS
        
    7. CUSTOM POST TYPES
        7.1 - sws_slider fields
    
    8. ADMIN PAGES
        8.1 - sws_dashboard_admin_page()
        8.2 - sws_options_admin_page()

    9. SETTINGS

*/




/* !1. HOOKS */

// 1.1
// hint: registers all our custom shortcodes on init
add_action('init', 'sws_register_shortcodes');

// 1.2
// hint: register custom admin column headers
add_filter('manage_edit-sws_slider_columns','sws_slider_column_headers');

// 1.3
// hint: register custom admin column data
add_filter('manage_sws_slider_posts_custom_column','sws_slider_column_data',1,2);
add_action(
    'admin_head-edit.php',
    'sws_register_custom_admin_titles'
);

// 1.4
/**
 * Checks if the Advanced Custom Fields plugin is activated
 *
 * If the Advanced Custom Fields plugin is not active, then don't allow the
 * activation of this plugin.
 *
 * @since 1.0.0
 */
function simple_wp_slider_activate() {
  if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
    include_once( ABSPATH . '/wp-admin/includes/plugin.php' );
  }
  if ( current_user_can( 'activate_plugins' ) && ! class_exists( 'ACF_Data' ) ) {
    // Deactivate the plugin.
    deactivate_plugins( plugin_basename( __FILE__ ) );
    // Throw an error in the WP admin console.
    $error_message = '<p>' . esc_html__( 'This plugin requires ', 'simple-wp-slider' ) . '<a href="' . esc_url( 'https://www.advancedcustomfields.com/' ) . '">Advanced Custom Fields Pro</a>' . esc_html__( ' plugin to be active.', 'simple-wp-slider' ) . '</p>';
    die( $error_message ); // WPCS: XSS ok.
  }
}
register_activation_hook( __FILE__, 'simple_wp_slider_activate' );

// 1.5
// load external files to public website
add_action('wp_enqueue_scripts', 'sws_public_scripts');

// hint: register our custom menus
add_action('admin_menu', 'sws_admin_menus');


/* !2. SHORTCODES */

// 2.1
// hint: registers all our custom shortcodes
function sws_register_shortcodes() {
    
    add_shortcode('sws_slider', 'sws_slider_shortcode');
    
}

// 2.2
// hint: returns a html string for a slick slider
function sws_slider_shortcode( $args ) {

    // get the list id
    $slider_id = 0;
    if( isset($args['id']) ) $slider_id = (int)$args['id'];

    // title
    $title = '';
    if( isset($args['title']) ) $title = (string)$args['title'];
    
    // setup our output variable 
    $output= '';

    $args = array( 'post_type' => 'sws_slider', 'posts_per_page' => 1, 'post_status' => 'publish' );
    $loop = new wp_Query( $args );
    $i=0; while ( $loop->have_posts() ) : $loop->the_post(); $i++;

    $post_id = $slider_id;

        if( strlen($title) ):
            $output .= '<h3 class="sws-title">'. $title .'</h3>';         
        endif;

        $show_arrows = get_field('sws_show_arrows', $post_id);
        $show_dots = get_field('sws_show_dots', $post_id);

        if( have_rows('sws_slide_repeater', $post_id) ): 
            $output .= '<div id="slider' . $slider_id . '" class="slider slick-slider'. $slider_id .'">';
                while ( have_rows('sws_slide_repeater', $post_id) ) : the_row();

                    //$output .= 'row index ' . get_row_index();
                    //$output .= $i;

                    $imageID = get_sub_field('sws_image'); 
                    $image = wp_get_attachment_image_src( $imageID, 'large' );
                    $alt_text = get_post_meta($imageID , '_wp_attachment_image_alt', true);
                    //$caption = get_sub_field('sws_caption'); 


                    //$output .= 'slider id '.$post_id; // this can go away

                    $output .= '<img src="';
                    $output .= $image[0];
                    $output .= '" alt="';
                    $output .= $alt_text;
                    $output .= '" />';
                    //$output .= '<h5 class="text-center pt-5 sws_caption">'.$caption.'</h5>';
                endwhile; // end ACF while
            $output .= '</div>';

            $output .= '<script>jQuery(document).ready(function($){';
            $output .=     '$("#slider'.$slider_id.'").slick({';
            //$output .=         if ($show_arrows): 'arrows: "true"'. endif;
            
            if ($show_arrows) { 
                $output .= 'arrows: true,';
            } else {
                $output .= 'arrows: false,';                
            }

            if ($show_dots) { 
                $output .= 'dots: true,';
            } else {
                $output .= 'dots: false,';                
            }
            
            //$output .=         'dots:' . $show_dots;
            $output .=     '});';
            $output .= '});</script>'; 

        endif; // end ACF if
    endwhile; // end query loop
    wp_reset_query();

    // return our results
    return $output;
    
}

/* !3. FILTERS */

// 3.1
function sws_slider_column_headers( $columns ) {
    
    // creating custom column header data
    $columns = array(
        'cb'=>'<input type="checkbox" />',
        'title'=>__('Slider Name'),
        'shortcode'=>__('Shortcode'),   
    );
    
    // returning new columns
    return $columns;
    
}

// 3.2
function sws_slider_column_data( $column, $post_id ) {
    
    // setup our return text
    $output = '';
    
    switch( $column ) {
        
        case 'shortcode':
            $output .= '[sws_slider id="'. $post_id .'"]';
            break;
        
    }
    
    // echo the output
    echo $output;
    
}

// 3.3
// hint: registers custom plugin admin menus
function sws_admin_menus() {
    
    /* main menu */
    
        $top_menu_item = 'sws_dashboard_admin_page';
        
        add_menu_page( '', 'Slider Builder', 'manage_options', 'sws_dashboard_admin_page', 'sws_dashboard_admin_page', 'dashicons-admin-post' );
    
    /* submenu items */
    
        // dashboard
        add_submenu_page( $top_menu_item, '', 'Slider Dashboard', 'manage_options', $top_menu_item, $top_menu_item );
        
        // email lists
        add_submenu_page( $top_menu_item, '', 'Sliders', 'manage_options', 'edit.php?post_type=sws_slider' );
                
        // plugin options

        // lets comment out for now, options are per slider
        //add_submenu_page( $top_menu_item, '', 'Plugin Options', 'manage_options', 'sws_options_admin_page', 'sws_options_admin_page' );

}


/* !4. EXTERNAL SCRIPTS & STYLES */

// 4.1
// hint: loads external files into PUBLIC website
function sws_public_scripts() {
    
    // register scripts with WordPress's internal library
    wp_register_script('slick-js', plugins_url('/js/vendor/slick.min.js',__FILE__), array('jquery'),'',true);
    wp_register_style('slick-css', plugins_url('/css/vendor/slick.css',__FILE__));
    wp_register_style('slick-theme-css', plugins_url('/css/vendor/slick-theme.css',__FILE__));
    wp_register_script('public-js', plugins_url('/js/public/simple-wordpress-slider.js',__FILE__), array('jquery'),'',true);

    // add to queue of scripts that get loaded into every page
    wp_enqueue_script('slick-js');
    wp_enqueue_script('public-js');
    wp_enqueue_style('slick-css');
    wp_enqueue_style('slick-theme-css');
    
}

/* !5. ACTIONS */
// 5.1
// hint: checks the current version of wordpress against supported versions and displays a admin notice if it is not supported
function sws_check_wp_version() {
    
    global $pagenow;
    
    
    if ( $pagenow == 'plugins.php' && is_plugin_active('simple-wp-slider/simple-wp-slider.php') ):
    
        // get the wp version
        $wp_version = get_bloginfo('version');
        
        // tested vesions
        // these are the versions we've tested our plugin in
        $tested_versions = array(
            '5.0',
        );
        
        $tested_range = array(5.0,5.6);
        
        // IF the current wp version is  in our tested versions...
        // remove: if( !in_array( $wp_version, $tested_versions ) ):
        if( (float)$wp_version >= (float)$tested_range[0] && (float)$wp_version <= (float)$tested_range[1] ):
        
            // we're good!
        
        else:
            
            // get notice html
            $notice = slb_get_admin_notice('Responsive WP Slider has not been tested in your version of WordPress. It still may work though...','error');
            
            // echo the notice html
            echo( $notice );
            
        endif;
    
    endif;
    
}


/* !6. HELPERS */


/* !7. CUSTOM POST TYPES */

// 7.1
// sliders
include_once( plugin_dir_path( __FILE__ ) . 'cpt/sws_slider.php');



/* !8. ADMIN PAGES */

// 8.1
// hint: dashboard admin page
function sws_dashboard_admin_page() {
    
    
    $output = '
        <div class="wrap">
            
            <h2>Simple Responsive WP Slider Options</h2>
            
            <p>The simplest, most lightweight responsive slider plugin for WordPress. Create and customize unlimited sliders. Place your sliders with a shortcode. Gutenberg blocks coming soon!</p>
        
        </div>
    ';
    
    echo $output;
    
}


// 8.2
// hint: plugin options admin page
function sws_options_admin_page() {
    
    $output .= '
        <div class="wrap">
            
            <h2>Simple Responsive WP Slider Options</h2>
            
            <p>Page description...</p>
                    
        </div>
    ';

    echo $output;
    
}

