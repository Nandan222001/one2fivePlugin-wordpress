<?php
/**
 * Integrates with WooCommerce Frontend.
 *
 * @package one2five
 */

declare(strict_types = 1);
namespace One2Five;

/**
 * FrontEnd class.
 */
final class FrontEnd {
	
	/** API URL.
	 *
	 * @var string
	 */
	private string $_api_url = 'https://test.one2five.digital/displayreview/data/displayreviews.json';
	/** Primary Color for buttons.
	 *
	 * @var string
	 */
	private string $_primary_color = '';
	/** Accent Color for Stars and ratings.
	 *
	 * @var string
	 */
	private string $_accent_color = '';
	/** Text Case for buttons.
	 *
	 * @var string
	 */
	private string $_text_case = '';
	/** Font Color for buttons.
	 *
	 * @var string
	 */
	private string $_font_color = '';

	/**
	 * Initializes the class.
	 */
	public function __construct() {
		$this->_primary_color = get_option( 'primary_color' ) ?: '';
		$this->_accent_color  = get_option( 'accent_color' ) ?: '';
		$this->_text_case     = get_option( 'button_text_case' ) ?: '';
		$this->_font_color    = get_option( 'font_color' ) ?: '';
		add_filter( 'woocommerce_product_tabs', [ $this, 'modify_product_tabs' ], 98 );
	}


	public function one2five_get_translations(string $language = 'en'): array {
		$languages = get_option('added_languages', []);
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
			// echo "<pre>";
			// print_r($translations);die;
			return $translations ?: []; 
		} else {
			error_log("Language not found: $language");
			return []; 
		}
	}
	/**
	 * Modifies WooCommerce product tabs by adding or removing tabs.
	 *
	 * @param array $tabs Existing WooCommerce product tabs.
	 * @return array Modified product tabs.
	 */
	public function modify_product_tabs( array $tabs ): array {
		// Remove the default WooCommerce reviews tab.
		unset( $tabs['reviews'] );

		// Add a custom reviews tab.
		$tabs['reviews_tab'] = [
			'title'    => __( 'Reviews', 'woocommerce' ),
			'priority' => 100,
			'callback' => [ $this, 'custom_tab_content' ],
		];

		return $tabs;
	}

	/**
	 * Outputs content for the custom tab added in WooCommerce product tabs.
	 */
	public function custom_tab_content(): void {
		// API request.
		$api_data = $this->_fetch_reviews();
			$selected_language = get_option('selected_language',get_locale());
		$translations = $this->one2five_get_translations($selected_language);
		if ( ! empty( $api_data['totalReviews'] ) ) {
			$total_reviews                = $api_data['totalReviews'];
			$average_feedback_rating      = $api_data['averageFeedbackRating'];
			$average_quality_rating       = $api_data['averageQualityRating'];
			$average_design_rating        = $api_data['averageDesignRating'];
			$average_functionality_rating = $api_data['averageFunctionalityRating'];
			include_once ONE2FIVE_ROOT . '/templates/product-review-widget-header.php'; // phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.UsingCustomConstant
			$this->_render_reviews( $api_data );
			include_once ONE2FIVE_ROOT . '/templates/product-review-widget-footer.php'; // phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.UsingCustomConstant
		} else {
			include_once ONE2FIVE_ROOT . '/templates/product-no-review-widget.php'; // phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.UsingCustomConstant
		}
	}

	/**
	 * Fetch reviews from an API.
	 * 
	 * @return array|false Array of reviews or false if there was an error.
	 */
	private function _fetch_reviews(): array {
		$pass_key     = get_option( 'pass_key' );
		$display_code = get_option( 'display_code' );
		$product_id   = get_the_ID();

		$data = [
			'passKey'     => $pass_key,
			'apiversion'  => '5.4',
			'format'      => 'json',
			'displayCode' => $display_code,
			'productId'   => $product_id,
		];

		$data_json = wp_json_encode( $data );
		$response  = wp_remote_post(
			$this->_api_url,
			[
				'body'    => $data_json,
				'headers' => [ 'Content-Type' => 'application/json' ],
			]
		);

		if ( ! is_wp_error( $response ) && wp_remote_retrieve_response_code( $response ) === 200 ) {
			return json_decode( wp_remote_retrieve_body( $response ), true );
		}
		return false;
	}

	/**
	 * Render reviews dynamically fetched from API.
	 *
	 * @param array $api_data Data retrieved from the API.
	 */
	private function _render_reviews( array $api_data ): void {
		// Rendering logic.
		if ( isset( $api_data['reviews'] ) ) {
			foreach ( $api_data['reviews'] as $review ) {
				include ONE2FIVE_ROOT . '/templates/product-review-widget-main.php'; // phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.UsingCustomConstant
			}
		}
	}
}
