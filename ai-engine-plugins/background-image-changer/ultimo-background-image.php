<?php
/*
Plugin Name: Último Background Image
Description: Take the last image generated in the #image-gen div and place it as the background-image of the .mwai-content div that is the child of the #chat-code id div.
Version: 1.2
Author: Lucas Moura
*/

function ultimo_background_image_enqueue_scripts() {
    wp_enqueue_script('ultimo-background-image', plugin_dir_url(__FILE__) . 'ultimo-background-image.js', array('jquery'), '1.0', true);
}
add_action('wp_enqueue_scripts', 'ultimo_background_image_enqueue_scripts');