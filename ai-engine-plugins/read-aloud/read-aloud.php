<?php
/**
 * Plugin Name: Read Aloud
 * Description: A simple WordPress plugin that adds a button to read aloud the text in divs with class "mwai-reply mwai-ai".
 * Version: 1.8
 * Author: Lucas Moura
 */

function read_aloud_enqueue_scripts() {
    wp_enqueue_script('read-aloud-js', plugin_dir_url(__FILE__) . 'read-aloud.js', array('jquery'), '1.0', true);
    wp_enqueue_script('save-form-js', plugin_dir_url(__FILE__) . 'save-form.js', array('jquery'), '1.0', true);

    if (is_user_logged_in()) {
        $user_id = get_current_user_id();
        $api_key = get_user_meta($user_id, 'elevenlabs_api_key', true);
        $voice_id = get_user_meta($user_id, 'elevenlabs_voice_id', true);
        $tts_provider = get_user_meta($user_id, 'tts_provider', true);
        $openai_api_key = get_custom_api_key('openai_api_key');
        $auto_read = get_user_meta($user_id, 'auto_read', true);

        wp_localize_script('read-aloud-js', 'read_aloud', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'api_key' => $api_key,
            'voice_id' => $voice_id,
            'tts_provider' => $tts_provider,
            'openai_api_key' => $openai_api_key,
            'auto_read' => $auto_read,
        ));
    }
}

add_action('wp_enqueue_scripts', 'read_aloud_enqueue_scripts');

// Shortcode to display the form
function read_aloud_form_shortcode() {
    if (is_user_logged_in()) {
        $user_id = get_current_user_id();
        $api_key = get_user_meta($user_id, 'elevenlabs_api_key', true);
        $voice_id = get_user_meta($user_id, 'elevenlabs_voice_id', true);
        $tts_provider = get_user_meta($user_id, 'tts_provider', true);
        $auto_read = get_user_meta($user_id, 'auto_read', true);

        return '
        <form id="read-aloud-form">
            <label for="api-key">API Key:</label>
            <input type="text" id="api-key" name="api-key" value="' . esc_attr($api_key) . '">
            
            <label for="voice-id">Voice ID:</label>
            <input type="text" id="voice-id" name="voice-id" value="' . esc_attr($voice_id) . '">
            
            <label for="tts-provider">Escolha o provedor do TTS, Elevenlabs ou Openai.</label>
            <select id="tts-provider" name="tts-provider">
                <option value="elevenlabs" ' . selected($tts_provider, 'elevenlabs', false) . '>ElevenLabs</option>
                <option value="openai" ' . selected($tts_provider, 'openai', false) . '>OpenAI</option>
            </select>
           
            <div style="margin-bottom:15px;min-height: 54px;height: 54px;display: flex; align-content: center;align-items: center;">
            <label for="auto-read" style="margin-right:10px;">Ler automaticamente:</label>
            <input type="checkbox" id="auto-read" name="auto-read" ' . checked($auto_read, '1', false) . '>
            </div>
            <input type="submit" value="Save">
        </form>
        ';
    } else {
        return 'You need to be logged in to see this form.';
    }
}
add_shortcode('read_aloud_form', 'read_aloud_form_shortcode');

// Function to save the data
function save_read_aloud_settings() {
    if (is_user_logged_in()) {
        $user_id = get_current_user_id();
        update_user_meta($user_id, 'elevenlabs_api_key', sanitize_text_field($_POST['api_key']));
        update_user_meta($user_id, 'elevenlabs_voice_id', sanitize_text_field($_POST['voice_id']));
        update_user_meta($user_id, 'tts_provider', sanitize_text_field($_POST['tts-provider']));
        update_user_meta($user_id, 'auto_read', isset($_POST['auto-read']) && $_POST['auto-read'] === '1' ? '1' : '0');
        echo 'Settings saved.';
    }
    wp_die();
}
add_action('wp_ajax_save_read_aloud_settings', 'save_read_aloud_settings');

function disable_auto_read() {
    if (is_user_logged_in()) {
        $user_id = get_current_user_id();
        update_user_meta($user_id, 'auto_read', '0');
        echo 'Auto Read disabled.';
    }
    wp_die();
}
add_action('wp_ajax_disable_auto_read', 'disable_auto_read');
add_action('wp_ajax_nopriv_disable_auto_read', 'disable_auto_read');