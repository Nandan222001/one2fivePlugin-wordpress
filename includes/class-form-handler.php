<?php
/**
 * Handles the submission of a product review via AJAX.
 *
 * @package one2five
 */

declare(strict_types = 1);

namespace One2Five;

/**
 * Form Handler class.
 */
final class FormHandler {

	/** API URL.
	 *
	 * @var string
	 */
	private string $_submit_api_url = 'https://test.one2five.digital/app/data/submitreview.json';

	/**
	 * Initializes the class.
	 */
	public function __construct() {

		add_action( 'wp_ajax_submit_review', [ $this, 'handle_review_submission' ] );
		add_action( 'wp_ajax_nopriv_submit_review', [ $this, 'handle_review_submission' ] );
	}

	/**
	 * Handles the submission of a product review via AJAX.
	 */
	public function handle_review_submission(): void {

		check_ajax_referer( 'wp_rest', 'nonce' );
		
		// Retrieve form data.
		$data = [
			'productId'                                => sanitize_text_field( $_POST['productId'] ?? '' ),
			'rating'                                   => intval( $_POST['rating'] ?? 0 ),
			'title'                                    => sanitize_text_field( $_POST['title'] ?? '' ),
			'reviewText'                               => sanitize_text_field( $_POST['opinion'] ?? '' ),
			'userNickname'                             => sanitize_text_field( $_POST['name'] ?? '' ),
			'isRecommended'                            => ( filter_var( isset( $_POST['isRecommended'] ), FILTER_SANITIZE_NUMBER_INT ) ?? 'No' ) === 'Yes',
			'agreedToTermsAndConditions'               => isset( $_POST['termsAgreement'] ),
			'rating_Design'                            => intval( $_POST['designRating'] ?? 0 ),
			'rating_Features'                          => intval( $_POST['functionalityRating'] ?? 0 ),
			'rating_Quality'                           => intval( $_POST['qualityRating'] ?? 0 ),
			'isAuthenticationRequired'                 => get_option( 'is_authentication_required' ) ? true : false,
			'passKey'                                  => get_option( 'pass_key' ),
			'displayCode'                              => get_option( 'display_code' ),
			'apiversion'                               => '5.4',
			'format'                                   => 'json',
			'action'                                   => 'submit',
			'FP'                                       => '0400CIfeAe15Cx8Nf94lis1ztukLyuFCkWWWr0T1wSz524Qb96BM10a',
			'sendEmailAlertWhenPublished'              => true,
			'hostedAuthentication_AuthenticationEmail' => sanitize_email( $_POST['email'] ?? '' ),
			'hostedAuthentication_CallbackURL'         => 'https://one2five-test.s3.eu-central-1.amazonaws.com/content/owlet/verification.html',
			'isPIEsubmission'                          => false,
		];


		$json_data = wp_json_encode( $data );

// echo "Nandan";
// 		echo "<pre>";
// 		print_r($json_data);die;

		// Send data to the API endpoint.
		$response  = wp_remote_post(
			$this->_submit_api_url,
			[
				'method'  => 'POST',
				'body'    => $json_data,
				'headers' => [ 'Content-Type' => 'application/json' ],
				'timeout' => 60,
			]
		);

		if ( is_wp_error( $response ) ) {
			wp_send_json_error( 'Failed to post review: ' . $response->get_error_message() );
		} else {
			$decoded_body = json_decode( wp_remote_retrieve_body( $response ), true );
			if ( ! empty( $decoded_body['Review']['reviewId'] ) ) {
				wp_send_json_success( 'Review submitted successfully. Review ID: ' . $decoded_body['Review']['reviewId'] );
			} else {
				$error_message = $decoded_body['Errors'][0]['Message'] ?? 'Unknown error';
				wp_send_json_error( 'Review submission failed: ' . $error_message );
			}
		}
	}
}
