<?php

if ( ! current_user_can( 'manage_options' ) ) {
    wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
}

$language = isset( $_GET['language']) ? sanitize_text_field($_GET['language']) : '';

$languages = get_option( 'added_languages', [] );

if ( array_key_exists($language, $languages) ) {
    $json_file_url = $languages[$language];
    $json_data = json_decode(file_get_contents($json_file_url), true);
} else {
    wp_die(__('Language not found.'));
}

// Handle Add or Edit
if ( isset($_POST['save_language']) && check_admin_referer('edit_language_action', 'edit_language_nonce') ) {
    $keyword = sanitize_text_field($_POST['keyword']);
    $label = sanitize_text_field($_POST['label']);
    $description = sanitize_textarea_field($_POST['description']);

    if ( !empty($keyword) ) {
        $json_data[$keyword] = [
            'label' => $label,
            'description' => $description
        ];

        $upload_dir = wp_upload_dir();
        $json_file_path = str_replace($upload_dir['baseurl'], $upload_dir['basedir'], $json_file_url);

        // Save the updated data
        if ( file_put_contents($json_file_path, json_encode($json_data, JSON_PRETTY_PRINT)) ) {
            wp_safe_redirect(admin_url('admin.php?page=edit-language&language=' . urlencode($language)));
            exit;
        } else {
            wp_die(__('Failed to save language data.'));
        }
    } else {
        wp_die(__('Invalid keyword.'));
    }
}
// echo "<pre>";
// print_r($_GET); // This will show all GET parameters
// die;

// print_r( isset($_GET['delete']) && !empty($_GET['delete']) && check_admin_referer('delete_language_action', 'delete_language_nonce') );
// die;
// Handle Delete
// Handle Delete
if ( isset($_GET['delete']) && !empty($_GET['delete']) && check_admin_referer('delete_language_action', 'delete_language_nonce') ) {
    $keyword_to_delete = sanitize_text_field($_GET['delete']);


    if (isset($json_data[$keyword_to_delete])) {
        unset($json_data[$keyword_to_delete]);

        // Get the upload directory
        $upload_dir = wp_upload_dir();
        $json_file_path = str_replace($upload_dir['baseurl'], $upload_dir['basedir'], $json_file_url);

        // Debugging: Check if the file path is correct
        if (!file_exists($json_file_path)) {
            wp_die(__('JSON file not found at: ') . esc_html($json_file_path));
        }

        // Save updated JSON data
        $updated = file_put_contents($json_file_path, json_encode($json_data, JSON_PRETTY_PRINT));

        // Debugging: Check if the file_put_contents() is successful
        if ($updated === false) {
            wp_die(__('Failed to update the JSON file. Please check file permissions.'));
        }

        // If successful, redirect back
        wp_safe_redirect(admin_url('admin.php?page=edit-language&language=' . urlencode($language)));
        exit;
    } else {
        wp_die(__('Keyword not found.'));
    }
}


?>


<div class="wrap">
    <h1><?php echo esc_html(isset($translations['edit_language']['page_title']) ? $translations['edit_language']['page_title'] : 'Edit Language'); ?></h1>

    <!-- Show Existing Data in Table -->
    <h2><?php echo esc_html(isset($translations['edit_language']['existing_entries']) ? $translations['edit_language']['existing_entries'] : 'Existing Entries'); ?></h2>
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th><?php echo esc_html(isset($translations['edit_language']['label']) ? $translations['edit_language']['label'] : 'Label'); ?></th>
                <th><?php echo esc_html(isset($translations['edit_language']['description']) ? $translations['edit_language']['description'] : 'Description'); ?></th>
                <th><?php echo esc_html(isset($translations['manage_languages']['added_languages']['headers']['actions']) ? $translations['manage_languages']['added_languages']['headers']['actions'] : 'Actions'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($json_data as $keyword => $data) : ?>
                <tr>
                    <td><?php echo esc_html($data['label']); ?></td>
                    <td><?php echo esc_html($data['description']); ?></td>
                    <td>
                        <a href="#" class="edit-entry button" data-keyword="<?php echo esc_attr($keyword); ?>" data-label="<?php echo esc_attr($data['label']); ?>" data-description="<?php echo esc_attr($data['description']); ?>">
                            <?php echo esc_html(isset($translations['manage_languages']['edit']['label']) ? $translations['manage_languages']['edit']['label'] : 'Edit'); ?>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<div id="editModal" style="display:none;">
    <!-- Edit Modal (hidden by default) -->
    <div class="modal-content">
        <h2><?php echo esc_html(isset($translations['edit_language']['edit_entry']) ? $translations['edit_language']['edit_entry'] : 'Edit Entry'); ?></h2>
        <form method="post" action="">
            <input type="hidden" id="edit_keyword" name="keyword" />
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">
                        <label for="edit_label"><?php echo esc_html(isset($translations['edit_language']['label']) ? $translations['edit_language']['label'] : 'Label'); ?></label>
                    </th>
                    <td>
                        <input type="text" id="edit_label" name="label" required />
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <label for="edit_description"><?php echo esc_html(isset($translations['edit_language']['description']) ? $translations['edit_language']['description'] : 'Description'); ?></label>
                    </th>
                    <td>
                        <textarea id="edit_description" name="description" required></textarea>
                    </td>
                </tr>
            </table>
            <?php wp_nonce_field('edit_language_action', 'edit_language_nonce'); ?>
            <p class="submit">
                <input type="submit" name="save_language" class="button-primary" value="<?php echo esc_html(isset($translations['edit_language']['update_entry_button']) ? $translations['edit_language']['update_entry_button'] : 'Update Entry'); ?>" />
            </p>
        </form>
    </div>
</div>

<!-- Add Modal for Terms and Conditions -->
<div id="termsModal" style="display:none;">
    <div class="modal-content">
        <h2><?php _e('Edit Terms and Conditions URL', 'one2five'); ?></h2>
        <form method="post" action="">
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">
                        <label for="terms_url"><?php _e('Terms and Conditions URL', 'one2five'); ?></label>
                    </th>
                    <td>
                        <input type="url" id="terms_url" name="terms_url" value="<?php echo esc_url($terms_url); ?>" required />
                    </td>
                </tr>
            </table>
            <?php wp_nonce_field('save_terms_url_action', 'save_terms_url_nonce'); ?>
            <p class="submit">
                <input type="submit" name="save_terms_url" class="button-primary" value="<?php esc_attr_e('Save URL', 'one2five'); ?>" />
            </p>
        </form>
    </div>
</div>
<!-- Add Modal (hidden by default) -->
<div id="addModal" style="display:none;">
    <div class="modal-content">
        <h2><?php _e('Add New Entry', 'one2five'); ?></h2>
        <form method="post" action="">
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">
                        <label for="add_keyword"><?php _e('Keyword', 'one2five'); ?></label>
                    </th>
                    <td>
                        <input type="text" id="add_keyword" name="keyword" required />
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <label for="add_label"><?php _e('Label', 'one2five'); ?></label>
                    </th>
                    <td>
                        <input type="text" id="add_label" name="label" required />
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <label for="add_description"><?php _e('Description', 'one2five'); ?></label>
                    </th>
                    <td>
                        <textarea id="add_description" name="description" required></textarea>
                    </td>
                </tr>
            </table>
            <?php wp_nonce_field('edit_language_action', 'edit_language_nonce'); ?>
            <p class="submit">
                <input type="submit" name="save_language" class="button-primary" value="<?php esc_attr_e('Save Entry', 'one2five'); ?>" />
            </p>
        </form>
    </div>
</div>

<!-- Styles for the modals -->
<style>
    .modal-content {
        background: #fff;
        padding: 20px;
        border-radius: 5px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 9999;
    }

    #addModal, #editModal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 9998;
    }
  .add-entry-container {
        display: flex;
        justify-content: flex-end;
        margin-bottom: 20px; /* Adjust spacing as needed */
    }

      #termsModal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 9998;
    }
</style>

<!-- JavaScript for Modal Handling -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const addModal = document.getElementById('addModal');
    const editModal = document.getElementById('editModal');
    // const addNewEntryButton = document.getElementById('addNewEntryButton');
    const editLinks = document.querySelectorAll('.edit-entry');

    // Open Add Modal
    // addNewEntryButton.addEventListener('click', function() {
    //     addModal.style.display = 'block';
    // });

    // Open Edit Modal with pre-filled data
    editLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('edit_keyword').value = this.getAttribute('data-keyword');
            document.getElementById('edit_label').value = this.getAttribute('data-label');
            document.getElementById('edit_description').value = this.getAttribute('data-description');
            editModal.style.display = 'block';
        });
    });

    // Close the modals when clicked outside
    window.addEventListener('click', function(e) {
        // if (e.target === addModal) {
        //     addModal.style.display = 'none';
        // }
        if (e.target === editModal) {
            editModal.style.display = 'none';
        }
    });
});
</script>

