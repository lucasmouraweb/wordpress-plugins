<?php
/*
Plugin Name: Approval Request Manager
Description: Sends a notification email after submitting a Contact Form 7 form and manages approval requests in the admin panel.
Version: 1.8
Author: Lucas Moura
*/

// Include plugin settings file
require_once plugin_dir_path(__FILE__) . 'includes/settings.php';

// Send notification email after Contact Form 7 submission
add_action('wpcf7_mail_sent', 'arm_send_notification_email', 10, 1);
function arm_send_notification_email($contact_form) {
    $form_id = $contact_form->id();
    $approved_form_id = get_option('arm_approved_form_id');

    if ($form_id == $approved_form_id) {
        $submission = WPCF7_Submission::get_instance();
        $posted_data = $submission->get_posted_data();

        $user_email = sanitize_email($posted_data['email']);
        $user_name = sanitize_text_field($posted_data['name1']);

        if (!empty($user_email) && !empty($user_name)) {
            // Check if a request with the same email already exists
            global $wpdb;
            $table_name = $wpdb->prefix . 'approval_requests';
            $existing_request = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE user_email = %s", $user_email));

            if ($existing_request) {
                // User is already registered, do not save the request or send the email
                return;
            }

            $subject = 'Registration Pending - Tech Xchange 2024';
            $message = file_get_contents(plugin_dir_path(__FILE__) . 'pending_email.html');
            $message = str_replace('[Name]', $user_name, $message);

            $headers = array(
                'Content-Type: text/html; charset=UTF-8',
                'From: Fulcrum Digital <wordpress@stgfulcrum.wpengine.com>'
            );

            $request_id = arm_save_request($user_email, $user_name);

            $mail_sent = wp_mail($user_email, $subject, $message, $headers);
            if (!$mail_sent) {
                error_log('Failed to send email to: ' . $user_email);
            }
        }
    }
}

function arm_save_request($user_email, $user_name)
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'approval_requests';
    
    // Verifique se já existe um mixed_code para o usuário
    $existing_request = $wpdb->get_row($wpdb->prepare("SELECT mixed_code FROM $table_name WHERE user_email = %s", $user_email));
    
    if ($existing_request && !empty($existing_request->mixed_code)) {
        $mixed_code = $existing_request->mixed_code;
    } else {
        $mixed_code = generateMixedCode();
    }

    $data = array(
        'user_email' => $user_email,
        'user_name' => $user_name,
        'mixed_code' => $mixed_code,
        'status' => 'pending',
        'created_at' => current_time('mysql')
    );

    $wpdb->insert($table_name, $data);

    return $wpdb->insert_id;
}

// Create admin menu page for requests management
add_action('admin_menu', 'arm_admin_menu');
function arm_admin_menu() {
    $parent_slug = add_menu_page(
        'Approval Requests',
        'Approval Requests',
        'manage_options',
        'approval-requests',
        'arm_requests_page',
        'dashicons-email',
        26
    );

    add_submenu_page(
        'approval-requests',
        'Approval Request Manager Settings',
        'Settings',
        'manage_options',
        'arm-settings',
        'arm_settings_page_content'
    );
}

// Render requests management page
function arm_requests_page()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'approval_requests';

    // Process approval action
    if (isset($_POST['action']) && $_POST['action'] == 'approve') {
        $request_id = intval($_POST['request_id']);
        $send_email = isset($_POST['send_email']) && $_POST['send_email'] === 'true';
        arm_approve_request($request_id, $send_email);
    }

    // Process rejection action
    if (isset($_POST['action']) && $_POST['action'] == 'reject') {
        $request_id = intval($_POST['request_id']);
        $send_email = isset($_POST['send_email']) && $_POST['send_email'] === 'true';
        arm_reject_request($request_id, $send_email);
    }

    // Process revert action
    if (isset($_POST['action']) && $_POST['action'] == 'revert') {
        $request_id = intval($_POST['request_id']);
        arm_revert_request($request_id);
    }

    // Process delete action
    if (isset($_POST['action']) && $_POST['action'] == 'delete') {
        $request_id = intval($_POST['request_id']);
        arm_delete_request($request_id);
    }

    // Process bulk actions
    if (isset($_POST['bulk_action'])) {
        $bulk_action = $_POST['bulk_action'];
        $request_ids = isset($_POST['request_ids']) ? $_POST['request_ids'] : array();
        $send_email = isset($_POST['send_email']) && $_POST['send_email'] === 'true';
        
        if (!empty($bulk_action) && !empty($request_ids)) {
            foreach ($request_ids as $request_id) {
                switch ($bulk_action) {
                    case 'approve':
                        arm_approve_request($request_id, $send_email);
                        break;
                    case 'reject':
                        arm_reject_request($request_id, $send_email);
                        break;
                    case 'revert':
                        arm_revert_request($request_id);
                        break;
                    case 'delete':
                        arm_delete_request($request_id);
                        break;
                }
            }
        }
    }

    // Get selected approval status from filter
    $selected_status = isset($_GET['approval_status']) ? $_GET['approval_status'] : '';

    // Modify SQL query based on selected approval status
    $sql = "SELECT * FROM $table_name";
    if (!empty($selected_status)) {
        $sql .= " WHERE status = '$selected_status'";
    }
    $sql .= " ORDER BY created_at DESC";
    $requests = $wpdb->get_results($sql);
    ?>
    <div class="wrap">
        <h1>Approval Requests</h1>
        <form method="get">
            <input type="hidden" name="page" value="<?php echo $_REQUEST['page']; ?>">
            <select name="approval_status">
                <option value="">All Approval Statuses</option>
                <option value="pending" <?php selected($selected_status, 'pending'); ?>>Pending</option>
                <option value="approved" <?php selected($selected_status, 'approved'); ?>>Approved</option>
                <option value="rejected" <?php selected($selected_status, 'rejected'); ?>>Rejected</option>
            </select>
            <button type="submit" class="button">Filter</button>
        </form>
        <button class="button button-primary export-csv-btn">Export all to CSV</button>

        <form id="bulk-action-form" method="post">
            <input type="hidden" name="request_ids" value="">
            <select name="bulk_action">
                <option value="">Bulk Actions</option>
                <option value="approve">Approve</option>
                <option value="reject">Reject</option>
                <option value="revert">Revert</option>
                <option value="delete">Delete</option>
            </select>
            <button type="submit" name="apply_bulk_action" class="button action">Apply</button>
        </form>

        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th><input type="checkbox" id="select-all"></th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Mixed Code</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($requests as $request) : ?>
                    <tr>
                        <td><input type="checkbox" name="request_id[]" value="<?php echo $request->id; ?>"></td>
                        <td><?php echo $request->user_name; ?></td>
                        <td><?php echo $request->user_email; ?></td>
                        <td><?php echo $request->status; ?></td>
                        <td><?php echo date('Y-m-d H:i:s', strtotime($request->created_at)); ?></td>
                        <td><?php echo $request->mixed_code; ?></td>
                        <td>
                            <?php if ($request->status == 'pending') : ?>
                                <button class="button button-primary approve-btn" data-request-id="<?php echo $request->id; ?>">Approve</button>
                                <button class="button button-secondary reject-btn" data-request-id="<?php echo $request->id; ?>">Reject</button>
                            <?php else : ?>
                                <button class="button button-secondary revert-btn" data-request-id="<?php echo $request->id; ?>">Revert</button>
                            <?php endif; ?>
                            <button class="button button-danger delete-btn" data-request-id="<?php echo $request->id; ?>">Delete</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <script>
    jQuery(document).ready(function($) {
        $('.approve-btn, .reject-btn').click(function() {
            var requestId = $(this).data('request-id');
            var action = $(this).hasClass('approve-btn') ? 'approve' : 'reject';
            var confirmMessage = action === 'approve' ? 'Are you sure you want to approve this request?' : 'Are you sure you want to reject this request?';
            var confirmed = confirm(confirmMessage);

            if (confirmed) {
                var sendEmail = confirm('Do you want to send an email notification?');
                var form = $('<form>', {
                    'method': 'POST',
                    'action': ''
                });
                form.append($('<input>', {
                    'type': 'hidden',
                    'name': 'action',
                    'value': action
                }));
                form.append($('<input>', {
                    'type': 'hidden',
                    'name': 'request_id',
                    'value': requestId
                }));
                form.append($('<input>', {
                    'type': 'hidden',
                    'name': 'send_email',
                    'value': sendEmail
                }));
                $('body').append(form);
                form.submit();
            }
        });

        $('.revert-btn').click(function() {
            var requestId = $(this).data('request-id');
            var confirmMessage = 'Are you sure you want to revert this request?';
            var confirmed = confirm(confirmMessage);

            if (confirmed) {
                var form = $('<form>', {
                    'method': 'POST',
                    'action': ''
                });
                form.append($('<input>', {
                    'type': 'hidden',
                    'name': 'action',
                    'value': 'revert'
                }));
                form.append($('<input>', {
                    'type': 'hidden',
                    'name': 'request_id',
                    'value': requestId
                }));
                $('body').append(form);
                form.submit();
            }
        });

        $('.export-csv-btn').click(function() {
            var form = $('<form>', {
                'method': 'POST',
                'action': '<?php echo admin_url('admin-post.php'); ?>'
            });
            form.append($('<input>', {
                'type': 'hidden',
                'name': 'action',
                'value': 'arm_export_csv'
            }));
            $('body').append(form);
            form.submit();
        });

        $('.delete-btn').click(function() {
            var requestId = $(this).data('request-id');
            var confirmMessage = 'Are you sure you want to delete this request?';
            var confirmed = confirm(confirmMessage);

            if (confirmed) {
                var form = $('<form>', {
                    'method': 'POST',
                    'action': ''
                });
                form.append($('<input>', {
                    'type': 'hidden',
                    'name': 'action',
                    'value': 'delete'
                }));
                form.append($('<input>', {
                    'type': 'hidden',
                    'name': 'request_id',
                    'value': requestId
                }));
                $('body').append(form);
                form.submit();
            }
        });

        $('#bulk-action-form').on('submit', function(e) {
            e.preventDefault();
            var selectedIds = [];
            $('input[name="request_id[]"]:checked').each(function() {
                selectedIds.push($(this).val());
            });
            
            if (selectedIds.length === 0) {
                alert('Please select at least one request.');
                return;
            }
            
            var bulkAction = $('select[name="bulk_action"]').val();
            var confirmMessage = '';
            
            if (bulkAction === 'approve') {
                confirmMessage = 'Are you sure you want to approve the selected requests?';
            } else if (bulkAction === 'reject') {
                confirmMessage = 'Are you sure you want to reject the selected requests?';
            }
            
            if (confirmMessage !== '') {
                var confirmed = confirm(confirmMessage);
                
                if (confirmed) {
                    var sendEmail = confirm('Do you want to send email notifications to the selected users?');
                    
                    if (sendEmail) {
                        $('<input>').attr({
                            type: 'hidden',
                            name: 'send_email',
                            value: 'true'
                        }).appendTo('#bulk-action-form');
                    }
                } else {
                    return;
                }
            }
            
            // Remove the existing <input> field
            $(this).find('input[name="request_ids"]').remove();
            
            // Add hidden <input> fields for each selected ID
            selectedIds.forEach(function(id) {
                $('<input>').attr({
                    type: 'hidden',
                    name: 'request_ids[]',
                    value: id
                }).appendTo('#bulk-action-form');
            });
            
            this.submit();
        });
        
        // Select/deselect all items when the "Select All" checkbox is clicked
        $('#select-all').on('click', function() {
            var isChecked = $(this).prop('checked');
            $('input[name="request_id[]"]').prop('checked', isChecked);
        });

        // Update the state of the "Select All" checkbox when an individual checkbox is clicked
        $('input[name="request_id[]"]').on('click', function() {
            var totalCheckboxes = $('input[name="request_id[]"]').length;
            var checkedCheckboxes = $('input[name="request_id[]"]:checked').length;
            $('#select-all').prop('checked', totalCheckboxes === checkedCheckboxes);
        });
    });
    </script>

    <style>
        .revet-btn {}

        .reject-btn {
            background-color: #FF5733 !important;
            color: #ffffff !important;
            border-color: #FF5733 !important;
        }

        .approve-btn {
            background-color: #4CAF50 !important;
            color: #ffffff !important;
            border-color: #4CAF50 !important;
        }

        .widefat thead td, .widefat thead th {
            border-bottom: 1px solid #c3c4c7;
            padding: 20px;
            font-size: 14px;
        }

        .widefat td, .widefat th {
            padding: 20px!important;
            font-size: 14px!important;
        }

        #wpbody-content h1 {
            margin-bottom:20px;
margin-top: 20px;
padding-left: 0px;
}
		button.button.button-primary.export-csv-btn {
        margin-bottom: 30px;
    }

    #wpbody-content form {
        margin-bottom: 20px;
    }
</style>

<?php

}

// Approve request and send confirmation email
function arm_approve_request($request_id, $send_email = false) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'approval_requests';
    $request = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $request_id));

    if ($request->status == 'pending') {
        $wpdb->update(
            $table_name,
            array('status' => 'approved'),
            array('id' => $request_id),
            array('%s'),
            array('%d')
        );

        if ($send_email) {
            $subject = 'Registration Approved - Tech Xchange 2024';
            $message = file_get_contents(plugin_dir_path(__FILE__) . 'approved_email.html');
            $message = str_replace('[Name]', $request->user_name, $message);
            $message = str_replace('[RANDOM ID NUMBER]', $request->mixed_code, $message);
            $headers = array(
                'Content-Type: text/html; charset=UTF-8',
                'From: Fulcrum Digital <wordpress@stgfulcrum.wpengine.com>'
            );

            $mail_sent = wp_mail($request->user_email, $subject, $message, $headers);
            if (!$mail_sent) {
                error_log('Failed to send email to: ' . $request->user_email);
            }
        }
    }
}

// Reject request and send notification email
function arm_reject_request($request_id, $send_email = false) {
global $wpdb;
$table_name = $wpdb->prefix . 'approval_requests';
	$request = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $request_id));

if ($request->status == 'pending') {
    $wpdb->update(
        $table_name,
        array('status' => 'rejected'),
        array('id' => $request_id),
        array('%s'),
        array('%d')
    );

    if ($send_email) {
        $subject = 'Registration Rejected - Tech Xchange 2024';
        $message = file_get_contents(plugin_dir_path(__FILE__) . 'rejected_email.html');
        $message = str_replace('[Name]', $request->user_name, $message);

        $headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'From: Fulcrum Digital <wordpress@stgfulcrum.wpengine.com>'
        );

        $mail_sent = wp_mail($request->user_email, $subject, $message, $headers);
        if (!$mail_sent) {
            error_log('Failed to send email to: ' . $request->user_email);
        }
    }
}
	}
// Revert request status to pending
function arm_revert_request($request_id) {
global $wpdb;
$table_name = $wpdb->prefix . 'approval_requests';
	$request = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $request_id));

if ($request->status != 'pending') {
    $wpdb->update(
        $table_name,
        array('status' => 'pending'),
        array('id' => $request_id),
        array('%s'),
        array('%d')
    );
}
	}
// Create approval requests table in database on plugin activation
register_activation_hook(FILE, 'arm_create_table');
function arm_create_table()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'approval_requests';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        user_email varchar(255) NOT NULL,
        user_name varchar(255) NOT NULL,
        status varchar(20) NOT NULL,
        created_at datetime NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

    
// Drop approval requests table on plugin deactivation
register_deactivation_hook(FILE, 'arm_drop_table');
function arm_drop_table()
{
global $wpdb;
$table_name = $wpdb->prefix . 'approval_requests';
$sql = "DROP TABLE IF EXISTS $table_name";
$wpdb->query($sql);
}
// AJAX action to handle database reset
add_action('wp_ajax_arm_reset_database', 'arm_reset_database');
function arm_reset_database() {
global $wpdb;
$table_name = $wpdb->prefix . 'approval_requests';
	$wpdb->query("TRUNCATE TABLE $table_name");

wp_send_json_success('Database has been reset successfully.');
	}
// Export Approval Requests data to CSV
add_action('admin_post_arm_export_csv', 'arm_export_csv');
function arm_export_csv() {
global $wpdb;
$table_name = $wpdb->prefix . 'approval_requests';
	$requests = $wpdb->get_results("SELECT * FROM $table_name ORDER BY created_at DESC");

    $csv_data = "Name,Email,Mixed Code,Status,Date\n";
    foreach ($requests as $request) {
        $csv_data .= $request->user_name . "," . $request->user_email . "," . $request->mixed_code . "," . $request->status . "," . $request->created_at . "\n";
    }

$filename = 'approval_requests_' . date('Y-m-d_H-i-s') . '.csv';

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="' . $filename . '"');
echo $csv_data;
exit;
	}
// Add approval status filter to user list table
add_action('restrict_manage_users', 'arm_add_user_table_filters');
function arm_add_user_table_filters($which) {
if ($which === 'top') {
$selected_status = isset($_GET['approval_status']) ? $_GET['approval_status'] : '';
?>
<select name="approval_status">
<option value="">All Approval Statuses</option>
<option value="pending" <?php selected($selected_status, 'pending'); ?>>Pending</option>
<option value="approved" <?php selected($selected_status, 'approved'); ?>>Approved</option>
<option value="rejected" <?php selected($selected_status, 'rejected'); ?>>Rejected</option>
</select>
<?php
}
}
// Filter users based on approval status
add_action('pre_get_users', 'arm_filter_users_by_approval_status');
function arm_filter_users_by_approval_status($query) {
if (is_admin() && $query->is_main_query() && $query->query_vars['screen'] === 'users') {
$approval_status = isset($_GET['approval_status']) ? $_GET['approval_status'] : '';
if (!empty($approval_status)) {
global $wpdb;
$table_name = $wpdb->prefix . 'approval_requests';
$user_ids = $wpdb->get_col("SELECT DISTINCT user_id FROM $table_name WHERE status = '$approval_status'");
if (!empty($user_ids)) {
$query->set('include', $user_ids);
} else {
$query->set('include', array(0)); // Empty result set if no matching user IDs
}
}
}
}
// Delete request
function arm_delete_request($request_id) {
global $wpdb;
$table_name = $wpdb->prefix . 'approval_requests';
	$wpdb->delete(
    $table_name,
    array('id' => $request_id),
    array('%d')
);
	}


    function generateMixedCode() {
        $characters = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $codeLength = 7;
        $code = "TX-";
    
        for ($i = 0; $i < $codeLength; $i++) {
            $randomIndex = mt_rand(0, strlen($characters) - 1);
            $code .= $characters[$randomIndex];
        }
    
        return $code;
    }

    function arm_add_mixed_code_column() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'approval_requests';
        $column_name = 'mixed_code';
        $column_exists = $wpdb->get_results($wpdb->prepare(
            "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = %s AND TABLE_NAME = %s AND COLUMN_NAME = %s",
            DB_NAME, $table_name, $column_name
        ));
    
        if (empty($column_exists)) {
            $wpdb->query("ALTER TABLE $table_name ADD $column_name VARCHAR(10) DEFAULT NULL");
        }
    }

add_action('plugins_loaded', 'arm_add_mixed_code_column');

?>