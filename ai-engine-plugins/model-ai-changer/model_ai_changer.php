<?php
/*
Plugin Name: Model AI Changer
Plugin URI: 
Description: Permite que os usuários escolham um modelo de IA e salva a escolha em um campo personalizado.
Version: 1.0
Author: 
Author URI: 
*/

function model_ai_changer_shortcode() {
    $current_model = get_user_meta( get_current_user_id(), 'model_ai', true );
    if ( ! $current_model ) {
        $current_model = 'gpt-3.5-turbo-0125'; // Modelo padrão
    }

    $html = '<select id="model-ai-select">'; // Abertura da tag <select>
    $html .= '<option value="">Selecione um modelo de IA</option>';
    
    // Opções do menu
    $models = array(
        'gpt-3.5-turbo' => 'gpt-3.5-turbo',
        'gpt-3.5-turbo-16k' => 'gpt-3.5-turbo-16k',
        'gpt-4-turbo' => 'gpt-4-turbo',
        'gpt-4o' => 'gpt-4o',
        'claude-3-opus-20240229' => 'claude-3-opus-20240229',
        'claude-3-sonnet-20240229' => 'claude-3-sonnet-20240229',
        'claude-3-haiku-20240307' => 'claude-3-haiku-20240307', 
    );

    foreach ( $models as $value => $label ) {
        $selected = ( $current_model === $value ) ? ' selected' : '';
        $html .= '<option value="' . $value . '"' . $selected . '>' . $label . '</option>';
    }

    $html .= '</select>'; // Fechamento da tag </select>

    return $html;
}
add_shortcode( 'model_ai_changer', 'model_ai_changer_shortcode' );

// Função para processar a solicitação Ajax
function model_ai_changer_ajax_update() {
    if ( isset( $_POST['model_ai'] ) ) {
        update_user_meta( get_current_user_id(), 'model_ai', sanitize_text_field( $_POST['model_ai'] ) );
    }
    wp_die();
}
add_action( 'wp_ajax_model_ai_changer_update', 'model_ai_changer_ajax_update' );

// Enfileirar script e passar a URL do Ajax
function model_ai_changer_scripts() {
    wp_enqueue_script( 'model-ai-changer-script', plugin_dir_url( __FILE__ ) . 'model-ai-changer.js', array( 'jquery' ), '1.0', true );
    wp_localize_script( 'model-ai-changer-script', 'model_ai_changer_ajax', array(
        'url' => admin_url( 'admin-ajax.php' )
    ));
}
add_action( 'wp_enqueue_scripts', 'model_ai_changer_scripts' );

