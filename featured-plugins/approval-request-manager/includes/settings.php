<?php
// Render settings page content
function arm_settings_page_content() {
    ?>
    <div class="wrap">
        <h1>Approval Request Manager Settings</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('arm_settings_group');
            do_settings_sections('arm-settings');
            submit_button();
            ?>
        </form>
    </div>
    <script>
        jQuery(document).ready(function($) {
            $('#arm_reset_database').click(function() {
                if (confirm('Are you sure you want to reset the database? All data will be permanently deleted.')) {
                    $.post(ajaxurl, {
                        action: 'arm_reset_database'
                    }, function(response) {
                        alert(response.data);
                    });
                }
            });
        });
    </script>
    <?php
}

// Register settings section and field
add_action('admin_init', 'arm_register_settings');
function arm_register_settings() {
    register_setting(
        'arm_settings_group',
        'arm_approved_form_id',
        'sanitize_text_field'
    );

    add_settings_section(
        'arm_settings_section',
        'Settings',
        'arm_settings_section_callback',
        'arm-settings'
    );

    add_settings_field(
        'arm_approved_form_id',
        'Approved Form ID',
        'arm_approved_form_id_callback',
        'arm-settings',
        'arm_settings_section'
    );

    add_settings_field(
        'arm_reset_database',
        'Reset Database',
        'arm_reset_database_callback',
        'arm-settings',
        'arm_settings_section'
    );
}

// Settings section callback
function arm_settings_section_callback() {
    echo '<p>Configure the Contact Form 7 form ID for which the notification email will be sent.</p>';
}

// Approved Form ID field callback
function arm_approved_form_id_callback() {
    $form_id = get_option('arm_approved_form_id');
    echo '<input type="text" name="arm_approved_form_id" value="' . esc_attr($form_id) . '" placeholder="Enter Form ID" />';
}

// Reset Database button callback
function arm_reset_database_callback() {
    ?>
    <button type="button" id="arm_reset_database" class="button button-secondary">Reset Database</button>
    <?php
}
?>