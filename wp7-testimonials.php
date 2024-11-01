<?php

/*
 * Plugin Name:       WP7 Testimonials
 * Plugin URI:        https://wordpress.org/plugins/wp7-testimonials/
 * Description:       Add Testimonials section in your website using [testimonial] shortcode.
 * Version:           1.1
 * Requires at least: 4.0
 * Requires PHP:      7.0
 * Author:            Ranzuni
 * Author URI:        https://profiles.wordpress.org/ranzuni/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       wp7-testimonials
 * Domain Path:       /languages
 */

/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

// do not call this file directly
if( !defined( 'ABSPATH' ) ) {
    exit();
}

// version
define( 'WP7_TESTIMONIALS_VERSION', '1.1' );

// translation
add_action( 'init', 'wp7_testimonials_load_textdomain' );
function wp7_testimonials_load_textdomain() {
    load_plugin_textdomain( 'wp7-testimonials', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' ); 
}

// WP7 Testimonials Initialization

function wp7_testimonials_init() {

    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    register_post_type('testimonials', 
        array( 
            'labels' => array(
            'name' => __( 'Testimonials', 'wp7-testimonials' ),
            'singular_name' => __( 'Testimonial', 'wp7-testimonials' ),
        ),
            'public' => true,
            'supports' => array('title','thumbnail','editor'),
            'menu_icon'=>'dashicons-admin-plugins'
    ));
}

add_action( 'init', 'wp7_testimonials_init' );

// WP7 Testimonials Enqueue Styles

function wp7_testimonials_enqueue_style() {

    wp_enqueue_style('bootstrap-min', plugins_url('css/bootstrap-min.css', __FILE__ ), false, '5.3.0' );
    wp_enqueue_style('wp7-testimonials', plugins_url('css/wp7-testimonials.css', __FILE__ ), false, '1.0.0' );

}

add_action( 'wp_enqueue_scripts', 'wp7_testimonials_enqueue_style' );

// WP7 Testimonials Enqueue Scripts

function wp7_testimonials_enqueue_script() {

    wp_enqueue_script('jquery');
    wp_enqueue_script('bootstrap-bundle', plugins_url('js/bootstrap-bundle.js', __FILE__), array(), '5.3.0', 'true');

}

add_action( 'wp_enqueue_scripts', 'wp7_testimonials_enqueue_script' );

// WP7 Testimonials Main

function wp7_testimonials_main($attr,$content) {

    ob_start();
    ?>

        <div class="text-center">
            <h2><?php esc_attr_e( 'TESTIMONIALS', 'wp7-testimonials' ); ?></h2>
            <h4><?php esc_attr_e( 'what our clients say', 'wp7-testimonials' ); ?></h4>
            <br>
            <div class="row">
                <?php
                $wp7testimonial = new wp_Query(array(
                    'post_type' => 'testimonials'
                ));
                while( $wp7testimonial->have_posts() ) : $wp7testimonial->the_post();
                ?>
                <div class="col-xxl-4 col-xl-4 col-lg-6 col-md-6 col-sm-12 col-12">
                    <div class="m-1 py-3 bg-success rounded">
                        <div class="wp7-testimonials-thumb">
                            <?php the_post_thumbnail(); ?>
                        </div>
                        <br>
                        <div class="text-white text-center">
                            <h4><?php the_title(); ?></h4>
                            <?php the_content(); ?>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div> <!-- row -->
        </div> <!-- text-center -->
    
    <?php
    return ob_get_clean();
}

add_shortcode( 'testimonial', 'wp7_testimonials_main' );

register_activation_hook( __FILE__, 'wp7_testimonials_activation_hook' );

function wp7_testimonials_activation_hook() {
    set_transient( 'wp7-testimonials-notification', true, 5 );
}
add_action( 'admin_notices', 'wp7_testimonials_activation_notification' );
 
function wp7_testimonials_activation_notification(){
    if( get_transient( 'wp7-testimonials-notification' ) ) {
        ?>
        <div class="updated notice is-dismissible">
            <p><?php esc_attr_e( 'Thank you for installing WP7 Testimonials!', 'wp7-testimonials' ); ?></p>
        </div>
        <?php
        delete_transient( 'wp7-testimonials-notification' );
    }
}