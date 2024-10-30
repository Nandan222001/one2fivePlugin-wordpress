<?php
/**
 * Integrates Admin panel with the plugin.
 *
 * @package one2five
 */

declare(strict_types=1);

namespace One2Five;

/**
 * Admin Settings class.
 */
final class AdminSettings {
	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'admin_menu', [ $this, 'admin_page' ] );
		add_action( 'admin_head', [ $this, 'enqueue_sync_column_styles' ] );
		add_action( 'admin_post_save_product_review_settings', [ $this, 'handle_form_submission' ] );
		add_filter( 'bulk_actions-edit-product', [ $this, 'custom_bulk_action' ] );
		add_action( 'admin_footer', [ $this, 'one2five_add_nonce_to_bulk_action' ] );
		add_filter( 'manage_product_posts_columns', [ $this, 'custom_sync_status_column_header' ] );
		add_action( 'manage_product_posts_custom_column', [ $this, 'custom_sync_status_column_content' ], 10, 2 );
		add_action( 'admin_post_save_language_settings', [ $this, 'handle_language_settings_save' ] );
	
		// Add JSON MIME type support using an anonymous function
		add_filter('upload_mimes', function($mimes) {
			$mimes['json'] = 'application/json';
			return $mimes;
		});
	}
	
	
	// Function to allow JSON file uploa
	

	/**
	 * Register the admin page for the plugin settings.
	 */
	public function admin_page(): void {
		// Add main menu page
		add_menu_page(
			'Product Review Settings',
			'Product Review',
			'manage_options',
			'product-review-menu',
			[ $this, 'menu_page' ],
			'dashicons-star-filled',
			100
		);
		
		// Add submenu page for Manage Languages
		add_submenu_page(
			'product-review-menu',       // Parent slug (main menu)
			'Manage Languages',          // Page title
			'Manage Languages',          // Menu title
			'manage_options',            // Capability
			'manage-languages',          // Menu slug
			[ $this, 'manage_languages_page' ] // Callback function to display the page
		);

		add_submenu_page(
            'manage-languages', // Parent slug
            __('Edit Language', 'one2five'), // Page title
            __('Edit Language', 'one2five'), // Menu title
            'manage_options', // Capability
            'edit-language', // Menu slug
            [ $this, 'edit_language_callback' ] // Correct function reference
        );
	}

	public function edit_language_callback() {
		// Get the current locale
		$default_locale = get_locale();
		// Get the selected language option from the database
		$selected_language = get_option('selected_language', $default_locale);
		
		// Check if the abbreviation of the current locale matches the stored selected language
		if ($selected_language === $default_locale) {
			// If they match, retain the locale as the selected language
			$selected_language = $default_locale;
		} else {
			// If not, check if there's a language matching the current locale abbreviation
			$languages = get_option('added_languages', []);
			if (array_key_exists($default_locale, $languages)) {
				// If a language is found, set it as the selected language
				$selected_language = $default_locale;
			}
		}
	
		$translations = $this->one2five_get_translations($selected_language);
		include_once ONE2FIVE_ROOT . '/templates/edit-language.php';
	}
	

	/**
	 * Enqueue custom styles for the sync status column.
	 */
	public function enqueue_sync_column_styles(): void {
		echo '<style>.column-sync_status { width: 100px; }</style>';
	}

	/**
	 * Add custom bulk action to the WooCommerce Products page.
	 *
	 * @param array $actions Array of bulk actions.
	 * @return array Modified array of bulk actions.
	 */
	public function custom_bulk_action( array $actions ): array {
		$sync_products = get_option( 'sync_products' );
		if ( $sync_products ) {
			$actions['sync_to_external_api'] = 'Sync Product';
		}
		return $actions;
	}

	/**
	 * Add nonce field to the bulk actions form.
	 */
	public function one2five_add_nonce_to_bulk_action(): void {
		$screen = get_current_screen();
		if ( isset( $screen->id ) && 'edit-product' === $screen->id ) {
			?>
			<script type="text/javascript">
				jQuery(document).ready(function($) {
					var nonceField = '<?php echo esc_html( wp_nonce_field( 'bulk-products', '_wpnonce_bulk', true, false ) ); ?>';
					$('form#posts-filter').append(nonceField);
				});
			</script>
			<?php
		}
	}


public function menu_page(): void {
    $settings = $this->_get_plugin_settings();
    $total_products = wp_count_posts('product')->publish;
    $synced_products = $this->_count_synced_products();
    $not_synced_products = $total_products - $synced_products;
    
    $default_locale = get_locale(); // Get the current locale
    $selected_language = get_option('selected_language', 'en'); // Get the stored selected language option

    // Check if the abbreviation of the current locale matches the stored selected language
    if ($selected_language === $default_locale) {
        // If they match, set selected_language to the current locale
        $selected_language = $default_locale;
    } else {
        // If not, check if there's a language matching the current locale abbreviation
        $languages = get_option('added_languages', []);
        if (array_key_exists($default_locale, $languages)) {
            // If a language is found, set it as the selected language
            $selected_language = $default_locale;
        }
    }

    // Retrieve translations based on the selected language
    $translations = $this->one2five_get_translations($selected_language);
    $full_language_name = '';

    if (function_exists('wp_get_available_translations')) {
        $available_translations = wp_get_available_translations();
        if (isset($available_translations[$default_locale])) {
            $full_language_name = $available_translations[$default_locale]['native_name'] . ' (' . $available_translations[$default_locale]['english_name'] . ')';
        } else {
            $full_language_name = __('Default Language', 'one2five');
        }
    } else {
        $full_language_name = __('Default Language', 'one2five');
    }

    // Ensure the default locale translation is included if not already in translations
    if (!array_key_exists($default_locale, $translations)) {
        $translations[$default_locale] = $full_language_name;
    }

    include_once ONE2FIVE_ROOT . '/templates/admin-settings-page.php';
}



	/**
	 * Display the "Manage Languages" page content.
	 */
	public function manage_languages_page(): void {
		// Get the current locale
		$default_locale = get_locale();
		// Get the selected language option from the database
		$selected_language = get_option('selected_language', $default_locale);
		
		// Check if the abbreviation of the current locale matches the stored selected language
		if ($selected_language === $default_locale) {
			// If they match, retain the locale as the selected language
			$selected_language = $default_locale;
		} else {
			// If not, check if there's a language matching the current locale abbreviation
			$languages = get_option('added_languages', []);
			if (array_key_exists($default_locale, $languages)) {
				// If a language is found, set it as the selected language
				$selected_language = $default_locale;
			}
		}
	
		// Retrieve translations based on the selected language
		$translations = $this->one2five_get_translations($selected_language);
		
		// Include the template for managing languages
		include_once ONE2FIVE_ROOT . '/templates/manage-languages-page.php';
	}
	

	/**
	 * Retrieve plugin settings from the database.
	 *
	 * @return array Plugin settings.
	 */
	private function _get_plugin_settings(): array {
		return [
			'pass_key'                   => get_option( 'pass_key', '' ),
			'display_code'               => get_option( 'display_code', '' ),
			'is_authentication_required' => get_option( 'is_authentication_required', false ),
			'sync_products'              => get_option( 'sync_products', false ),
			'primary_color'              => get_option( 'primary_color', '#000000' ),
			'accent_color'               => get_option( 'accent_color', '#ffffff' ),
			'font_color'                 => get_option( 'font_color', '#000000' ),
		];
	}

	/**
	 * Count synced products.
	 */
	private function _count_synced_products(): int {
		if ( ! get_option( 'sync_products' ) ) {
			return 0;
		}
		$args = [
			'post_type' => 'product',
			'meta_query' => [
				[
					'key' => 'sync_product',
					'compare' => 'EXISTS',
				],
			],
			'posts_per_page' => -1,
			'fields' => 'ids',
		];
		$synced_products = get_posts( $args );
		return count( $synced_products );
	}

	/**
	 * Handle form submission for plugin settings.
	 */
	public function handle_form_submission(): void {
		if ( ! check_admin_referer( 'product_review_settings_nonce', 'product_review_nonce' ) ) {
			wp_die( 'Nonce check failed' );
		}
	
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( 'You do not have sufficient permissions to access this page.' );
		}

		$settings = [
			'pass_key' => sanitize_text_field( $_POST['pass_key'] ?? '' ),
			'display_code' => sanitize_text_field( $_POST['display_code'] ?? '' ),
			'is_authentication_required' => isset( $_POST['authentication_required'] ),
			'sync_products' => isset( $_POST['sync_products'] ),
			'primary_color' => sanitize_text_field( $_POST['primary_color'] ?? '#000000' ),
			'accent_color' => sanitize_text_field( $_POST['accent_color'] ?? '#ffffff' ),
			'font_color' => sanitize_text_field( $_POST['font_color'] ?? '#000000' ),
			'button_text_case' => sanitize_text_field( $_POST['button_text_case'] ?? 'uppercase' ),
		];

		foreach ( $settings as $key => $value ) {
			update_option( $key, $value );
		}

		wp_safe_redirect( admin_url( 'admin.php?page=product-review-menu' ) );
		exit;
	}

	/**
	 * Add a custom column header to the Products page.
	 *
	 * @param array $columns Array of columns.
	 * @return array Modified array of columns.
	 */
	public function custom_sync_status_column_header( array $columns ): array {
		$columns['sync_status'] = __( 'Sync Status', 'one2five' );
		return $columns;
	}

	/**
	 * Add custom content for the sync status column on the Products page.
	 *
	 * @param string $column Column name.
	 * @param int    $post_id Post ID.
	 */
	public function custom_sync_status_column_content( string $column, int $post_id ): void {
		if ( 'sync_status' === $column ) {
			$sync_product = get_post_meta( $post_id, 'sync_product', true );
			if ( ! empty( $sync_product ) ) {
				echo '<input type="checkbox" checked disabled>';
			} else {
				echo '<button class="sync-product-button sync-product-btn btn btn-primary" data-product-id="' . esc_attr( $post_id ) . '"><i class="fas fa-sync"></i></button>';
			}
		}
	}

	/**
	 * Handle language settings form submission.
	 */
	public function handle_language_settings_save(): void {
		if ( ! check_admin_referer( 'language_settings_nonce', 'language_nonce' ) ) {
			wp_die( 'Nonce check failed' );
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( 'You do not have sufficient permissions to perform this action.' );
		}

		$language = sanitize_text_field( $_POST['language'] ?? 'en' );
		update_option( 'selected_language', $language );

		wp_safe_redirect( wp_get_referer() );
		// wp_safe_redirect( admin_url( 'admin.php?page=manage-languages' ) );
		exit;
	}

	/**
	 * Fetch translations for the selected language.
	 *
	 * @param string $language Selected language.
	 * @return array Array of translations.
	 */

	public function one2five_get_translations(string $language = 'en'): array {
    $languages = get_option('added_languages', []);
    
    // Ensure $languages is an array
    if (!is_array($languages)) {
        $languages = [];
        error_log("Warning: 'added_languages' option is not an array. Defaulting to an empty array.");
    }

    if (array_key_exists($language, $languages)) {
        $json_file_url = str_replace('http://localhost:10003', ABSPATH, $languages[$language]);
        $json_data = file_get_contents($json_file_url);
        if ($json_data === false) {
            error_log("Failed to read file: $json_file_url");
            return []; 
        }
        $translations = json_decode($json_data, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log("JSON decode error: " . json_last_error_msg());
            return [];
        }
        return $translations ?: []; 
    } else {
        error_log("Language not found: $language");
        return []; 
    }
}

	
	
	
}
