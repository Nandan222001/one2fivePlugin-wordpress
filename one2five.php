<?php
/**
 * Plugin Name: One2Five Customer Ratings
 * Description: Review Management & UGC for Sales Growth WooCommerce.
 * Version: 1.0.0
 * Author: One2Five
 * Website: https://one2five-reviews.com/
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package One2Five
 */

declare(strict_types = 1);

namespace One2Five;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Define plugin constants.
define( 'ONE2FIVE_VERSION', '1.0.0' );
define( 'ONE2FIVE_URI', plugin_dir_url( __FILE__ ) );
define( 'ONE2FIVE_ROOT', plugin_dir_path( __FILE__ ) );

require_once __DIR__ . '/includes/class-admin-settings.php';
require_once __DIR__ . '/includes/class-assets-manager.php';
require_once __DIR__ . '/includes/class-form-handler.php';
require_once __DIR__ . '/includes/class-front-end.php';
require_once __DIR__ . '/includes/class-review-handler.php';
require_once __DIR__ . '/includes/class-sync-handler.php';

/**
 * Main plugin class.
 */
final class ReviewManager {
	/**
	 * Initialize the plugin.
	 */
	public function __construct() {
		register_activation_hook( __FILE__, [ $this, 'on_activation' ] );
		add_action( 'plugins_loaded', [ $this, 'consistently_disable_woocommerce_reviews' ] );

		$this->admin_settings   = new AdminSettings();
		$this->assets_manager   = new AssetsManager();
		$this->form_handler     = new FormHandler();
		$this->sync_handler     = new SyncHandler();
		$this->review_handler   = new ReviewHandler();
		$this->frontend_handler = new FrontEnd();
	}

	/**
	 * Check plugin requirements on activation.
	 */
	public function on_activation() {
		if ( ! class_exists( 'WooCommerce', false ) ) {
			// Deactivate the plugin.
			deactivate_plugins( plugin_basename( __FILE__ ) );

			// Throw an Error.
			wp_die( 'This plugin requires WooCommerce to be installed and active.', 'Plugin dependency check', array( 'back_link' => true ) );
		}
		
		$this->disable_woocommerce_reviews();
	}

	/**
	 * Disable WooCommerce reviews.
	 */
	public function disable_woocommerce_reviews(): void {
		update_option( 'woocommerce_enable_reviews', 'no' ); // Turn off reviews.
		update_option( 'woocommerce_enable_review_rating', 'no' ); // Turn off review ratings as well.
	}

	/**
	 * Consistently disable WooCommerce reviews.
	 */
	public function consistently_disable_woocommerce_reviews(): void {
		if ( class_exists( 'WooCommerce', false ) ) {
			if ( 'yes' === get_option( 'woocommerce_enable_reviews' ) ) {
				update_option( 'woocommerce_enable_reviews', 'no' );
			}
			if ( 'yes' === get_option( 'woocommerce_enable_review_rating' ) ) {
				update_option( 'woocommerce_enable_review_rating', 'no' );
			}
		}
	}
}

$one2five_plugin = new ReviewManager(); // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
