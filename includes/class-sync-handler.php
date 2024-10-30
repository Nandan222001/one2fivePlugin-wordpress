<?php
/**
 * Handles Bulk synchronization to an external API and Single Product Sync.
 *
 * @package one2five
 */

declare(strict_types = 1);

namespace One2Five;

/**
 * SyncHandler class.
 */
final class SyncHandler {
	/** API URL.
	 *
	 * @var string
	 */
	private string $_api_url = 'https://test.one2five.digital/app/data/submitproduct';

	/**
	 * Initializes the class.
	 */
	public function __construct() {
		add_action( 'admin_action_sync_to_external_api', [ $this, 'handle_custom_bulk_action' ] );
		add_action( 'save_post', [ $this, 'sync_product_on_update_create' ], 10, 3 );
		add_action( 'wp_ajax_sync_product_ajax', [ $this, 'handle_sync_submission_ajax' ] );
		add_action( 'wp_ajax_nopriv_sync_product_ajax', [ $this, 'handle_sync_submission_ajax' ] );
	}

	/**
	 * Syncs product data to an external API.
	 *
	 * @param int $product_id The ID of the product.
	 */
	public function sync_product_to_api( int $product_id ): void {
		$product       = wc_get_product( $product_id );
		$image_url     = $this->_get_product_image_url( $product );
		$category_data = $this->_get_product_category_data( $product_id );

		$data = [
			'format'      => 'json',
			'apiversion'  => '5.4',
			'passKey'     => get_option( 'pass_key' ),
			'displayCode' => get_option( 'display_code' ),
			'productData' => [
				[
					'productExternalId'  => $product_id,
					'productName'        => $product->get_name(),
					'productImageUrl'    => $image_url,
					'productPageUrl'     => get_permalink( $product_id ),
					'categoryExternalId' => $category_data['id'],
					'categoryName'       => $category_data['name'],
				],
			],
		];

		$this->_send_payload_to_api( $data );
	}

	/**
	 * Retrieves the image URL of a product.
	 *
	 * @param WC_Product $product The product object.
	 * @return string Image URL.
	 */
	private function _get_product_image_url( ?\WC_Product $product ): string { // phpcs:ignore 
		if ( $product->get_image_id() ) {
			$image = wp_get_attachment_image_src( $product->get_image_id(), 'full' );
			return $image ? $image[0] : '';
		}

		return wc_placeholder_img_src( 'full' ); // Fallback to placeholder image.
	}

	/**
	 * Retrieves the category data of a product.
	 *
	 * @param int $product_id The ID of the product.
	 * @return array Contains category ID and name.
	 */
	private function _get_product_category_data( int $product_id ): array {
		$terms = get_the_terms( $product_id, 'product_cat' );
		if ( $terms && ! is_wp_error( $terms ) ) {
			$term = reset( $terms );
			return [
				'id'   => $term->term_id,
				'name' => $term->name,
			];
		}

		return [
			'id'   => '',
			'name' => '',
		];
	}

	/**
	 * Sends the payload to the API endpoint.
	 *
	 * @param array $payload The payload data to be sent to the API.
	 */
	private function _send_payload_to_api( array $payload ): void {
		$response = wp_remote_post(
			$this->_api_url,
			[
				'body'    => wp_json_encode( $payload ),
				'headers' => [ 'Content-Type' => 'application/json' ],
			]
		);

		if ( is_wp_error( $response ) ) {
			wp_send_json_error( $response->get_error_message() );
			return; // Exit function without sending JSON response.
		}

		$response_body = wp_remote_retrieve_body( $response );
		$response_data = json_decode( $response_body, true );

		if ( isset( $response_data['HasErrors'] ) && ! $response_data['HasErrors'] ) {
			if ( isset( $response_data['productResponse'] ) && is_array( $response_data['productResponse'] ) && count( $response_data['productResponse'] ) > 0 ) {
				$product_id = $response_data['productResponse'][0]['productId'];
				update_post_meta( $payload['productData'][0]['productExternalId'], 'sync_product', $product_id );
			}
			if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
				wp_send_json_success( 'Product synced successfully!' );
			}
		} elseif ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			wp_send_json_error( 'Failed to sync product.' );
		}
	}

	/**
	 * Bulk syncs products to an external API.
	 *
	 * @param array $post_ids The IDs of the products to sync.
	 */
	public function bulk_sync_to_api( array $post_ids ): void {
		foreach ( $post_ids as $post_id ) {
			$this->sync_product_to_api( $post_id );
		}
	}

	/**
	 * Handles custom bulk action for syncing products.
	 */
	public function handle_custom_bulk_action(): void {
		if ( ! isset( $_REQUEST['_wpnonce_bulk'] ) || ! wp_verify_nonce( $_REQUEST['_wpnonce_bulk'], 'bulk-products' ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			wp_die( 'Security check failed' );
		}

		$action   = isset( $_REQUEST['action'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['action'] ) ) : '';
		$post_ids = isset( $_REQUEST['post'] ) ? array_map( 'absint', (array) wp_unslash( $_REQUEST['post'] ) ) : array();

		if ( 'sync_to_external_api' === $action && ! empty( $post_ids ) ) {
			$this->bulk_sync_to_api( $post_ids );
		}
	}

	/**
	 * Syncs a product to an external API on update or create.
	 *
	 * @param int     $post_id The ID of the post.
	 * @param WP_Post $post The post object.
	 * @param bool    $update Whether this is an existing post being updated.
	 */
	public function sync_product_on_update_create( int $post_id, ?\WP_Post $post, bool $update ): void {
		if ( 'product' !== $post->post_type || ! $update ) {
			return;
		}

		$sync_products = get_option( 'sync_products' );
		if ( $sync_products && !is_product() ) {
			$this->sync_product_to_api( $post_id );
		}
	}

	/**
	 * Handles the submission of a product sync via AJAX.
	 */
	public function handle_sync_submission_ajax(): void {
		$nonce = isset( $_POST['nonce'] ) ? sanitize_key( $_POST['nonce'] ) : '';

		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $nonce, 'wp_rest' ) ) {
			$error_message = sanitize_text_field( 'Invalid nonce' );
			wp_send_json_error( $error_message );
		}

		$sync_products = get_option( 'sync_products' );
		if ( ! $sync_products ) {
			return; // If not enabled, do nothing.
		}

		$product_id = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : '';
		if ( $product_id ) {
			$this->sync_product_to_api( $product_id );
		}
	}
}
