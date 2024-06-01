<?php
/*
Plugin Name: Custom API Keys
Plugin URI: https://example.com/
Description: Adds custom API key fields for OpenAI and Claude AI to the user profile and provides a shortcode to display and update the values using AJAX.
Version: 1.0
Author: Your Name
Author URI: https://example.com/
*/

// Add custom user fields
function custom_api_keys_user_fields($user) {
    ?>
    <table class="form-table">
        <tr>
            <th><label for="openai_api_key">OpenAI API Key</label></th>
            <td>
                <input type="text" name="openai_api_key" id="openai_api_key" value="<?php echo esc_attr(get_user_meta($user->ID, 'openai_api_key', true)); ?>" class="regular-text" />
            </td>
        </tr>
        <tr>
            <th><label for="claude_ai_api_key">Claude AI API Key</label></th>
            <td>
                <input type="text" name="claude_ai_api_key" id="claude_ai_api_key" value="<?php echo esc_attr(get_user_meta($user->ID, 'claude_ai_api_key', true)); ?>" class="regular-text" />
            </td>
        </tr>
    </table>
    <?php
}
add_action('show_user_profile', 'custom_api_keys_user_fields');
add_action('edit_user_profile', 'custom_api_keys_user_fields');

// Save custom user fields
function custom_api_keys_save_user_fields($user_id) {
    if (!current_user_can('edit_user', $user_id)) {
        return;
    }
    update_user_meta($user_id, 'openai_api_key', sanitize_text_field($_POST['openai_api_key']));
    update_user_meta($user_id, 'claude_ai_api_key', sanitize_text_field($_POST['claude_ai_api_key']));
}
add_action('personal_options_update', 'custom_api_keys_save_user_fields');
add_action('edit_user_profile_update', 'custom_api_keys_save_user_fields');

// Shortcode to display and update API keys
function custom_api_keys_shortcode() {
    ob_start();
    ?>
    <div class="custom-api-keys">
        <label for="openai_api_key_input">OpenAI API Key:</label>
        <input type="text" id="openai_api_key_input" value="<?php echo esc_attr(get_user_meta(get_current_user_id(), 'openai_api_key', true)); ?>" />
        <br>
        <label for="claude_ai_api_key_input">Claude AI API Key:</label>
        <input type="text" id="claude_ai_api_key_input" value="<?php echo esc_attr(get_user_meta(get_current_user_id(), 'claude_ai_api_key', true)); ?>" />
        <br>
        <button id="update_api_keys_btn">Update</button>
        <span id="loading_icon" style="display: none;">Loading...</span>
        <span id="success_message" style="display: none;">Saved!</span>
    </div>
    <script>
        jQuery(document).ready(function($) {
            $('#update_api_keys_btn').on('click', function() {
                var openai_api_key = $('#openai_api_key_input').val();
                var claude_ai_api_key = $('#claude_ai_api_key_input').val();
                
                $('#update_api_keys_btn').hide();
                $('#loading_icon').show();
                
                $.ajax({
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    type: 'POST',
                    data: {
                        action: 'update_api_keys',
                        openai_api_key: openai_api_key,
                        claude_ai_api_key: claude_ai_api_key
                    },
                    success: function(response) {
                        $('#loading_icon').hide();
                        $('#success_message').show();
                        setTimeout(function() {
                            $('#success_message').hide();
                            $('#update_api_keys_btn').show();
                        }, 1000);
                    }
                });
            });
        });
    </script>
    <?php
    return ob_get_clean();
}
add_shortcode('custom_api_keys', 'custom_api_keys_shortcode');

// AJAX handler to update API keys
function custom_api_keys_update_ajax_handler() {
    $user_id = get_current_user_id();
    update_user_meta($user_id, 'openai_api_key', sanitize_text_field($_POST['openai_api_key']));
    update_user_meta($user_id, 'claude_ai_api_key', sanitize_text_field($_POST['claude_ai_api_key']));
    wp_die();
}
add_action('wp_ajax_update_api_keys', 'custom_api_keys_update_ajax_handler');

// Function to retrieve API keys
function get_custom_api_key($key_name) {
    $user_id = get_current_user_id();
    return get_user_meta($user_id, $key_name, true);
}