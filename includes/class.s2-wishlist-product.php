<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) || ! defined( 'S2_WL_VERSION' ) ) {
	exit;
}

/**
 * Implements product features of S2 Wishlist Product
 *
 * @class   S2_Wishlist_Product
 * @package S2 Wishlist
 * @since   1.0.0
 * @author  Shuban Studio <shuban.studio@gmail.com>
 */
if ( ! class_exists( 'S2_Wishlist_Product' ) ) {

	class S2_Wishlist_Product {

		/**
		 * Single instance of the class
		 *
		 * @var \S2_Wishlist_Product
		 */
		protected static $instance;

		/**
		 * Returns single instance of the class
		 *
		 * @return \S2_Wishlist_Product
		 *
		 * @since  1.0.0
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Constructor
		 *
		 * @param array $args
		 *
		 * @since  1.0.0
		 */
		public function __construct() {

			// Add wishlist button / link / image before shop loop item
			add_action( 'woocommerce_before_shop_loop_item_title', [ $this, 'show_wishlist_flash' ], 10 );
			add_action( 'woocommerce_product_thumbnails', [ $this, 'show_wishlist_flash' ], 10 );

		}

		/**
		 * Add wishlist button / link / image before shop loop item
		 *
		 * @since  1.0.0
		 */
		public function show_wishlist_flash() {

			global $product;

			$user_id  = get_current_user_id();
			$wishlist = get_user_meta( $user_id, 's2-wishlist', true );
			$wishlist = $wishlist ? $wishlist : [];

			wc_get_template( 
				'frontend/wishlist-flash.php', 
				[ 'product' => $product, 'wishlist' => $wishlist ], 
				'', 
				S2_WL_TEMPLATE_PATH . '/' 
			);

		}

	}

}

/**
 * Unique access to instance of S2_Wishlist_Product class
*/
S2_Wishlist_Product::get_instance();
