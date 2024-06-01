<?php
/*
Plugin Name: Contact Form 7 Loading Overlay
Description: Add a loading overlay on submit a Contact Form 7.
Version: 1.0
Author: Lucas Moura
*/

function cf7_loading_overlay_scripts() {
    wp_enqueue_style('cf7-loading-overlay-style', plugin_dir_url(__FILE__) . 'css/style.css');
    wp_enqueue_script('cf7-loading-overlay-script', plugin_dir_url(__FILE__) . 'js/script.js', array('jquery'), '1.0', true);
}
add_action('wp_enqueue_scripts', 'cf7_loading_overlay_scripts');

function cf7_loading_overlay_display() {
    echo '<div class="cf7-loading-overlay"><div class="cf7-loading-spinner"></div></div>';
}
add_action('wp_footer', 'cf7_loading_overlay_display');