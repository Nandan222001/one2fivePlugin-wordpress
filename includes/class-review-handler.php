<?php
/**
 * Handles Product review display on the front end.
 *
 * @package one2five
 */

declare(strict_types = 1);

namespace One2Five;

/**
 * Review Handler class.
 */
final class ReviewHandler {

	/** API URL.
	 *
	 * @var string
	 */
	private string $_api_url = 'https://test.one2five.digital/displayreview/data/displayreviews.json';
	/** API Pass Key.
	 *
	 * @var string
	 */
	private string $_pass_key = '';
	/** Display code for API
	 *
	 * @var string
	 */
	private string $_display_code = '';

	/**
	 * Initializes the class.
	 */
	public function __construct() {
		$this->_pass_key     = get_option( 'pass_key' ) ?: '';
		$this->_display_code = get_option( 'display_code' ) ?: '';
		add_action( 'woocommerce_shop_loop_item_title', [ $this, 'display_custom_product_ratings' ] );
		add_action( 'woocommerce_single_product_summary', [ $this, 'display_custom_product_ratings' ], 15 );
		add_action( 'woocommerce_after_add_to_cart_button', [ $this,  'add_rating_button' ], 100 );
	}

	/**
	 * Display the rating button.
	 *
	 * Outputs a button for writing a review.
	 */
	public function add_rating_button(): void {
		$product_id        = get_the_ID();
		$primary_color     = get_option( 'primary_color' );
		$font_color        = get_option( 'font_color' );
		$text_case         = get_option( 'button_text_case' );
		$sync_product_meta = get_post_meta( $product_id, 'sync_product', true );
		if ( ! empty( $sync_product_meta ) ) {
			?>
			<div class="one2five-bootstrap">
			<button type="button" id="add-rating-btn" class="btn add-rating-btn mt-5 d-block text-white write offcanvasRight w-50 border-0 rounded-0 text-left"
				data-product-id="<?php echo esc_attr( $product_id ); ?>"
				style="background-color: <?php echo esc_attr( $primary_color ); ?> !important; color: <?php echo esc_attr( $font_color ); ?> !important; text-transform: <?php echo esc_attr( $text_case ); ?> !important;">Leave a Review</button>
			</div>
			<?php
		}
	}

	/**
	 * Display custom product ratings on the front end.
	 */
	public function display_custom_product_ratings(): void {
		global $product;

		// Get the product ID.
		$product_id = $product->get_id();

		// Prepare and make the API request.
		$api_response = $this->_make_api_request( $product_id );
		if ( $api_response ) {
			$this->_render_ratings( $api_response );
		}
	}

	/**
	 * Make an API request to fetch product ratings.
	 *
	 * @param int $product_id The product ID.
	 * @return array|bool The API response data or false on failure.
	 */
	private function _make_api_request( int $product_id ): array|bool {
		$data = array(
			'passKey'     => $this->_pass_key,
			'apiversion'  => '5.4',
			'format'      => 'json',
			'displayCode' => $this->_display_code,
			'productId'   => $product_id,
		);

		$data_json = wp_json_encode( $data );

		$response = wp_remote_post(
			$this->_api_url,
			array(
				'body'    => $data_json,
				'headers' => array( 'Content-Type' => 'application/json' ),
			)
		);

		if ( ! is_wp_error( $response ) && wp_remote_retrieve_response_code( $response ) === 200 ) {
			$api_data = json_decode( wp_remote_retrieve_body( $response ), true );

			if ( ! $api_data['hasErrors'] ) {
				return $api_data;
			}
		}

		return false;
	}

	/**
	 * Render the product ratings on the front end.
	 *
	 * @param array $api_data The API response data.
	 */
	private function _render_ratings( array $api_data ): void {
		$average_feedback_rating = $api_data['averageFeedbackRating'];
		$total_reviews           = $api_data['totalReviews'];

		// Decide div based on whether it's the shop page or not.
		if ( is_shop() ) {
			echo '<div class="star shop-star col-auto ms-0" style="justify-content: center;">';
		} else {
			echo '<div class="star col-auto ms-0" style="padding-left: 0 !important;">';
		}

		// Star wrapper for whole stars.
		echo '<div style="position: relative; display: inline-block;">';
		echo '<div class="stars-background" style="display: inline-block; white-space: nowrap; color: #acacac;">';
		for ( $i = 1; $i <= 5; $i++ ) {
			echo '<i class="bi bi-star-fill" style="color: inherit;"></i>';
		}
		echo '</div>';

		// Overlay for filled stars.
		$width = ( $average_feedback_rating / 5 ) * 100; // Calculate width percentage.
		echo '<div class="stars-foreground" style="display: inline-block; white-space: nowrap; color: ' . esc_attr( get_option( 'accent_color' ) ) . '; position: absolute; top: 0; left: 0; overflow: hidden; width: ' . esc_attr( $width ) . '%;">';
		for ( $i = 1; $i <= 5; $i++ ) {
			echo '<i class="bi bi-star-fill" style="color: inherit;"></i>';
		}
		echo '</div>';
		echo '</div>';

		// Display the total number of reviews differently on shop pages and product pages.
		if ( is_shop() ) {
			echo '<span style="font-size:12px !important;">(' . esc_attr( $total_reviews ) . ' customer reviews)</span>';
		} else {
			echo '<span class="review-text" style="font-size:12px !important;">(' . esc_attr( $total_reviews ) . ' customer reviews)</span>';
		}
		echo '</div>';
	}
}
