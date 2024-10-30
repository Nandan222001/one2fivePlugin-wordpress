<?php

declare(strict_types=1);

namespace One2Five;

/**
 * Assets Manager class.
 */
final class AssetsManager {

	/**
	 * Constructor.
	 */
	public function __construct() {
		// Enqueue scripts and styles.
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts_and_styles' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts_and_styles' ] );
	}

	/**
	 * Enqueue scripts and styles.
	 */
	public function enqueue_scripts_and_styles(): void {
		// Enqueue jQuery.
		wp_enqueue_script('jquery', ONE2FIVE_URI . '/src/js/jquery.min.js', array(), '3.6.0', false);
	
		wp_enqueue_style('wp-color-picker');
		wp_enqueue_script('wp-color-picker');
	
		// Enqueue Bootstrap JavaScript.
		wp_enqueue_script('bootstrap-js', ONE2FIVE_URI . '/src/js/bootstrap.min.js', array('jquery'), '5.0.0', true);
	
		wp_enqueue_style('font-awesome-cdn', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css', array(), '5.15.4', 'all');
	
		// Enqueue Bootstrap Icons CSS.
		wp_enqueue_style('bootstrap-icons-css', ONE2FIVE_URI . '/src/assets/bootstrap-icons/font/bootstrap-icons.css', array(), '1.7.0', 'all');
	
		// Enqueue Bootstrap CSS.
		wp_enqueue_style('one2five-bootstrap-css', ONE2FIVE_URI . '/src/css/one2five-bootstrap.min.css', array(), '5.0.0', 'all');
	
		// Enqueue your custom script.
		wp_enqueue_script('product-review-script', ONE2FIVE_URI . '/build/index.js', array('jquery'), '1.0', true);
	
		// Enqueue your chart script.
		wp_enqueue_script('product-review-display', ONE2FIVE_URI . '/build/chart.js', array('jquery'), '1.0', true);
	
		// Enqueue your custom styles.
		wp_enqueue_style('product-review-style', ONE2FIVE_URI . '/src/css/style.css', array(), '1.0', 'all');
		wp_enqueue_style('product-display-style', ONE2FIVE_URI . '/src/css/custom.css', array(), '1.0', 'all');
	
		// Enqueue custom color picker script.
		wp_enqueue_script('custom-color-picker', ONE2FIVE_URI . '/build/color-picker.js', array('wp-color-picker'), '1.0.0', true);
	
		// Localize script variables.
		$root_url        = get_site_url();
		$primary_color   = get_option('primary_color');
		$accent_color    = get_option('accent_color');
		$font_color      = get_option('font_color');
		$text_case       = get_option('button_text_case');
	
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
	
		// Fetch the translations for the selected language
		$admin_settings = new AdminSettings();
		$translations = $admin_settings->one2five_get_translations($selected_language);
	
		wp_localize_script(
			'product-review-display',
			'one2five_chart_vars',
			array(
				'ajax_url'         => admin_url('admin-ajax.php'),
				'root_url'         => $root_url,
				'nonce'            => wp_create_nonce('wp_rest'),
				'primary_color'    => $primary_color,
				'accent_color'     => $accent_color,
				'font_color'       => $font_color,
				'button_text_case' => $text_case,
				'is_product_page'  => is_product(),
			)
		);
	
		wp_localize_script(
			'product-review-script',
			'one2five_vars',
			array(
				'ajax_url'         => admin_url('admin-ajax.php'),
				'root_url'         => $root_url,
				'nonce'            => wp_create_nonce('wp_rest'),
				'primary_color'    => $primary_color,
				'accent_color'     => $accent_color,
				'font_color'       => $font_color,
				'button_text_case' => $text_case,
				'is_product_page'  => is_product(),
				'translations'     => $translations, 
			)
		);
	}
	
}
