<h4>
    <?php 
    if (!isset($translations)) {
        $translations = [];
    }

    echo ( get_option('selected_language') === 'fr' ) 
        ? __('French Paramètres de Configuration Globale de l’Application d’Avis one2Five', 'one2five') 
        : esc_html($translations['global_settings_title']['label'] ?? __('one2Five Review App Global Config Settings', 'one2five'));
    ?>
</h4>

<div class="one2five-bootstrap">
    <div class="wrap">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <h4><?php echo esc_html($translations['global_settings_title']['label'] ?? __('one2Five Review App Global Config Settings', 'one2five')); ?></h4>
                    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                        <input type="hidden" name="action" value="save_product_review_settings">
                        <?php wp_nonce_field('product_review_settings_nonce', 'product_review_nonce'); ?>
                        <table class="form-table">
                            <tr>
                                <th scope="row"><label for="pass_key"><?php echo esc_html($translations['pass_key']['label'] ?? __('Pass Key', 'one2five')); ?></label></th>
                                <td>
                                    <input type="text" name="pass_key" id="pass_key" value="<?php echo esc_attr($settings['pass_key']); ?>" style="width: 300px;" required>
                                    <p><?php echo esc_html($translations['pass_key']['description'] ?? __('Enter your pass key.', 'one2five')); ?></p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="display_code"><?php echo esc_html($translations['display_code']['label'] ?? __('Display Code', 'one2five')); ?></label></th>
                                <td>
                                    <input type="text" name="display_code" id="display_code" value="<?php echo esc_attr($settings['display_code']); ?>" style="width: 300px;" required>
                                    <p><?php echo esc_html($translations['display_code']['description'] ?? __('Enter your display code.', 'one2five')); ?></p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="authentication_required"><?php echo esc_html($translations['authentication_required']['label'] ?? __('Review Authentication Required', 'one2five')); ?></label></th>
                                <td>
                                    <input type="checkbox" name="authentication_required" id="authentication_required" <?php checked($settings['is_authentication_required'], 1); ?>>
                                    <p><?php echo esc_html($translations['authentication_required']['description'] ?? __('Enabling this will require reviews to be authenticated before being displayed on site.', 'one2five')); ?></p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="sync_products"><?php echo esc_html($translations['sync_products']['label'] ?? __('Sync Products', 'one2five')); ?></label></th>
                                <td>
                                    <input type="checkbox" name="sync_products" id="sync_products" <?php checked($settings['sync_products'], 1); ?>>
                                    <p><?php echo esc_html($translations['sync_products']['description'] ?? __('Enabling this allows products to sync to One2Five Servers.', 'one2five')); ?></p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="text_case"><?php echo esc_html($translations['text_case']['label'] ?? __('Text Case', 'one2five')); ?></label></th>
                                <td>
                                    <select name="button_text_case" id="button_text_case">
                                        <option value="uppercase" <?php selected(get_option('button_text_case', 'uppercase'), 'uppercase'); ?>><?php _e('Uppercase', 'one2five'); ?></option>
                                        <option value="lowercase" <?php selected(get_option('button_text_case', 'lowercase'), 'lowercase'); ?>><?php _e('Lowercase', 'one2five'); ?></option>
                                        <option value="capitalize" <?php selected(get_option('button_text_case', 'capitalize'), 'capitalize'); ?>><?php _e('Capitalize', 'one2five'); ?></option>
                                    </select>
                                    <p><?php echo esc_html($translations['text_case']['description'] ?? __('Select text case for Button Font.', 'one2five')); ?></p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="primary_color"><?php echo esc_html($translations['primary_color']['label'] ?? __('Primary Color', 'one2five')); ?></label></th>
                                <td>
                                    <input type="text" name="primary_color" id="primary_color" class="color-field" value="<?php echo esc_attr($settings['primary_color']); ?>">
                                    <p><?php echo esc_html($translations['primary_color']['description'] ?? __('This applies to all One2Five Plugin buttons.', 'one2five')); ?></p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="accent_color"><?php echo esc_html($translations['accent_color']['label'] ?? __('Accent Color', 'one2five')); ?></label></th>
                                <td>
                                    <input type="text" name="accent_color" id="accent_color" class="color-field" value="<?php echo esc_attr($settings['accent_color']); ?>">
                                    <p><?php echo esc_html($translations['accent_color']['description'] ?? __('This applies to all ratings including stars and bars.', 'one2five')); ?></p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="font_color"><?php echo esc_html($translations['font_color']['label'] ?? __('Font Color', 'one2five')); ?></label></th>
                                <td>
                                    <input type="text" name="font_color" id="font_color" class="color-field" value="<?php echo esc_attr($settings['font_color']); ?>">
                                    <p><?php echo esc_html($translations['font_color']['description'] ?? __('This applies to all One2Five Plugin buttons.', 'one2five')); ?></p>
                                </td>
                            </tr>
                        </table>
                        <?php submit_button(); ?>
                    </form>

                    <form method="post" id="language-form" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
    <input type="hidden" name="action" value="save_language_settings">
    <label for="language"><?php _e('Select Language:', 'one2five'); ?></label>
    <select name="language" id="language" onchange="document.getElementById('language-form').submit();">
        <option value="">Select Language</option>
        <?php
        global $wpdb; // Use the global WP database object
        $languages = get_option('added_languages', []);
        $selected_language = get_option('selected_language', get_option('WPLANG', 'en_US'));
        $default_locale = get_locale(); // Get the current locale

        // Fetch the language names based on abbreviations
        if (!empty($languages)) {
            foreach ($languages as $language_code => $language_url) {
                // Get the language name from the database
                $language_name = $wpdb->get_var($wpdb->prepare(
                    "SELECT language_name FROM wp_languages WHERE abbreviation = %s",
                    $language_code
                ));

                // Check if a language name was found, if not use the abbreviation
                if ($language_name === null) {
                    $language_name = ucfirst($language_code); // Default to the code if no name found
                }

                // Check if the language code matches the current locale or selected language
                $is_selected = ($language_code === $default_locale || $language_code === $selected_language) ? 'selected' : '';

                echo '<option value="' . esc_attr($language_code) . '" ' . $is_selected . '>';
                echo esc_html($language_name);
                echo '</option>';
            }
        }
        ?>
    </select>
    <?php wp_nonce_field('language_settings_nonce', 'language_nonce'); ?>
</form>


                </div>
                <?php // echo "<pre>"; print_r($languages); ?>

                <div class="col-lg-6 mt-lg-5">
                    <div class="row">
                        <div class="col-lg-6 one2five-bootstrap">
                            <div class="alert alert-success" role="alert">
                                <h4 class="alert-heading"><?php echo esc_html($translations['synced_products']['label'] ?? __('Synced Products', 'one2five')); ?></h4>
                                <p><?php echo esc_html($translations['synced_products']['description'] ?? __('Number of products synced:', 'one2five')); ?> <?php echo esc_html($synced_products); ?></p>
                            </div>
                        </div>
                        <div class="col-lg-6 one2five-bootstrap">
                            <div class="alert alert-danger" role="alert">
                                <h4 class="alert-heading"><?php echo esc_html($translations['unsynced_products']['label'] ?? __('Unsynced Products', 'one2five')); ?></h4>
                                <p><?php echo esc_html($translations['unsynced_products']['label'] ?? __('Number of products not synced:', 'one2five')); ?> <?php echo esc_html($not_synced_products); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
