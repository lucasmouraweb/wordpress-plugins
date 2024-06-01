<?php
/*
Plugin Name: Último Background Image
Plugin URI: https://example.com/
Description: Pega a última imagem gerada na div #image-gen e coloca como background-image da div .mwai-content filha da div de id #chat-code.
Version: 1.0
Author: Seu Nome
Author URI: https://example.com/
*/

function ultimo_background_image_enqueue_scripts() {
    wp_enqueue_script('ultimo-background-image', plugin_dir_url(__FILE__) . 'ultimo-background-image.js', array('jquery'), '1.0', true);
}
add_action('wp_enqueue_scripts', 'ultimo_background_image_enqueue_scripts');