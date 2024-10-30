<?php
define( 'ALLOW_UNFILTERED_UPLOADS', true );

if ( ! current_user_can( 'manage_options' ) ) {
    wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
}

global $wpdb;
$table_name = $wpdb->prefix . 'languages'; // assuming your table name is 'wp_languages'

// Fetch all languages from the languages table for the dropdown
// Fetch all languages from the languages table for the dropdown
$all_languages = $wpdb->get_results( "SELECT * FROM $table_name ORDER BY language_name ASC" );

if ( isset( $_POST['submit_language'] ) && check_admin_referer( 'add_language_action', 'add_language_nonce' ) ) {
    $new_language = sanitize_text_field( $_POST['new_language'] );
    
    // Handle file upload
    if ( ! empty( $_FILES['language_file']['name'] ) ) {
        $uploadedfile = $_FILES['language_file'];
        $file_ext = pathinfo($uploadedfile['name'], PATHINFO_EXTENSION);
        
        if ( strtolower($file_ext) !== 'json' ) {
            wp_die( __( 'Please upload a valid JSON file.' ) );
        }
        $upload = wp_handle_upload( $uploadedfile, array( 'test_form' => false ) );
        if ( isset( $upload['error'] ) ) {
            wp_die( $upload['error'] );
        }
        $json_file_url = $upload['url']; 
    } else {
        $json_file_url = '';
    }

    // Fetch and validate languages
    $languages = get_option( 'added_languages', [] );
    if ( ! is_array( $languages ) ) {
        delete_option('added_languages'); // Reset if it was corrupted
        $languages = [];
    }

    // Check if the language abbreviation is already added
    if ( ! empty( $new_language ) && ! array_key_exists( $new_language, $languages ) ) {
        $languages[ $new_language ] = $json_file_url; 
        update_option( 'added_languages', $languages );
    } else {
        $language_already_added = true; // Set a flag for validation
    }

    wp_safe_redirect( admin_url( 'admin.php?page=manage-languages' ) );
    exit;
}

if ( isset( $_POST['delete_language'] ) && check_admin_referer( 'delete_language_action_' . $_POST['language_to_delete'], 'delete_language_nonce' ) ) {
    $language_to_delete = sanitize_text_field( $_POST['language_to_delete'] );

    $languages = get_option( 'added_languages', [] );
    if ( ! is_array( $languages ) ) {
        delete_option('added_languages'); // Reset if it was corrupted
        $languages = [];
    }

    if ( array_key_exists( $language_to_delete, $languages ) ) {
        unset( $languages[ $language_to_delete ] );
        update_option( 'added_languages', $languages );
    }

    wp_safe_redirect( admin_url( 'admin.php?page=manage-languages' ) );
    exit;
}

$languages = get_option( 'added_languages', [] );
if ( ! is_array( $languages ) ) {
    $languages = [];
}
?>

<div class="wrap">
    <h1><?php echo esc_html(isset($translations['manage_languages']['label']) ? $translations['manage_languages']['label'] : 'Manage Languages'); ?></h1>
    <p><?php echo esc_html(isset($translations['manage_languages']['description']) ? $translations['manage_languages']['description'] : 'Manage and add new languages for your store.'); ?></p>

    <!-- Add New Language Form -->
    <form method="post" action="" enctype="multipart/form-data" onsubmit="return validateForm()">
        <table class="form-table">
            <tr valign="top">
                <th scope="row">
                    <label for="new_language"><?php echo esc_html(isset($translations['manage_languages']['add_language']['label']) ? $translations['manage_languages']['add_language']['label'] : 'Add New Language'); ?></label>
                </th>
                <td>
                    <!-- Dropdown for selecting a language -->
                    <select id="new_language" name="new_language" required>
                        <option value=""><?php echo esc_html(isset($translations['manage_languages']['add_language']['placeholder']) ? $translations['manage_languages']['add_language']['placeholder'] : 'Select Language'); ?></option>
                        <?php foreach ( $all_languages as $lang ) : ?>
                            <option value="<?php echo esc_attr( $lang->abbreviation ); ?>"> <!-- Use abbreviation as the value -->
                                <?php echo esc_html( $lang->language_name . ' (' . $lang->abbreviation . ')' ); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if ( isset( $language_already_added ) && $language_already_added ) : ?>
                        <p style="color:red;"><?php echo esc_html(isset($translations['manage_languages']['language_exists']) ? $translations['manage_languages']['language_exists'] : 'This language is already added to the list.'); ?></p>
                    <?php endif; ?>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <label for="language_file"><?php echo esc_html(isset($translations['manage_languages']['language_file']['label']) ? $translations['manage_languages']['language_file']['label'] : 'Language File'); ?></label>
                </th>
                <td>
                    <input type="file" id="language_file" name="language_file" accept=".json" required />
                    <br>
                    <a href="<?php echo plugin_dir_url(__FILE__) . 'default_template.json'; ?>" class="button" download><?php echo esc_html(isset($translations['manage_languages']['language_file']['description']) ? $translations['manage_languages']['language_file']['description'] : 'Download Default JSON Template'); ?></a>
                </td>
            </tr>
        </table>
        
        <!-- Nonce for security -->
        <?php wp_nonce_field( 'add_language_action', 'add_language_nonce' ); ?>

        <!-- Submit button -->
        <p class="submit">
            <input type="submit" name="submit_language" class="button-primary" value="<?php echo esc_html(isset($translations['manage_languages']['add_language']['label']) ? $translations['manage_languages']['add_language']['label'] : 'Add Language'); ?>" />
        </p>
    </form>

    <hr />

    <!-- Display the list of added languages -->
<!-- Display the list of added languages -->
<h2><?php echo esc_html(isset($translations['manage_languages']['added_languages']['label']) ? $translations['manage_languages']['added_languages']['label'] : 'Added Languages'); ?></h2>
<table class="wp-list-table widefat fixed striped">
    <thead>
        <tr>
            <th><?php echo esc_html(isset($translations['manage_languages']['added_languages']['headers']['language']) ? $translations['manage_languages']['added_languages']['headers']['language'] : 'Language'); ?></th>
            <th><?php echo esc_html(isset($translations['manage_languages']['added_languages']['headers']['json_file']) ? $translations['manage_languages']['added_languages']['headers']['json_file'] : 'JSON File'); ?></th>
            <th><?php echo esc_html(isset($translations['manage_languages']['added_languages']['headers']['actions']) ? $translations['manage_languages']['added_languages']['headers']['actions'] : 'Actions'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php if ( ! empty( $languages ) ) : ?>
            <?php foreach ( $languages as $abbreviation => $file_url ) : ?>
                <?php
                // Find the full language name using the abbreviation
                $full_language_name = '';
                foreach ( $all_languages as $lang ) {
                    if ( $lang->abbreviation === $abbreviation ) {
                        $full_language_name = $lang->language_name;
                        break;
                    }
                }
                ?>
                <tr>
                    <td><?php echo esc_html( $full_language_name ); ?></td>
                    <td>
                        <?php if ( ! empty( $file_url ) ) : ?>
                            <a href="<?php echo esc_url( $file_url ); ?>" target="_blank"><?php echo esc_html(isset($translations['manage_languages']['download_json']['label']) ? $translations['manage_languages']['download_json']['label'] : 'Download JSON'); ?></a>
                        <?php else : ?>
                            <?php _e( 'No file uploaded', 'one2five' ); ?>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="<?php echo admin_url( 'admin.php?page=edit-language&language=' . urlencode( $abbreviation ) ); ?>" class="button"><?php echo esc_html(isset($translations['manage_languages']['edit']['label']) ? $translations['manage_languages']['edit']['label'] : 'Edit'); ?></a>
                        <form method="post" action="" style="display:inline;">
                            <?php wp_nonce_field( 'delete_language_action_' . esc_attr( $abbreviation ), 'delete_language_nonce' ); ?>
                            <input type="hidden" name="language_to_delete" value="<?php echo esc_attr( $abbreviation ); ?>" />
                            <input type="submit" name="delete_language" value="<?php echo esc_html(isset($translations['manage_languages']['delete']['label']) ? $translations['manage_languages']['delete']['label'] : 'Delete'); ?>" class="button-secondary" />
                        </form> 
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else : ?>
            <tr>
                <td colspan="3"><?php echo esc_html(isset($translations['manage_languages']['no_languages']['label']) ? $translations['manage_languages']['no_languages']['label'] : 'No languages have been added yet.'); ?></td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

</div>
