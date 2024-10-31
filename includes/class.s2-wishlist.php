<?php

if ( ! defined( 'ABSPATH' ) || ! defined( 'S2_WL_VERSION' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Implements features of S2 Wishlist
 *
 * @class   S2_Wishlist
 * @package S2 Wishlist
 * @since   1.0.0
 * @author  Shuban Studio <shuban.studio@gmail.com>
 */
if ( ! class_exists( 'S2_Wishlist' ) ) {

	class S2_Wishlist {

		/**
		 * Single instance of the class
		 *
		 * @var \S2_Wishlist
		 */
		protected static $instance;

		/**
		 * @var bool
		 */
		public $debug_active = false;

		/**
		 * @var WC_Logger
		 */
		public $debug;

		/**
		 * Returns single instance of the class
		 *
		 * @return \S2_Wishlist
		 * @since 1.0.0
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
		 * Initialize plugin and registers actions and filters to be used
		 *
		 * @since  1.0.0
		 */
		public function __construct() {

			require_once S2_WL_INC . 'functions.s2-wishlist.php';
			require_once S2_WL_INC . 'class.s2-wishlist-admin.php';
			require_once S2_WL_INC . 'class.s2-wishlist-frontend.php';

			add_action( 's2_wishlist_flush_rewrite_rules', [ __CLASS__, 'maybe_flush_rewrite_rules' ] );

		}

		/**
		 * Register the message on plugin log
		 *
		 * @param string $message
		 *
		 * @since  1.0.0
		 */
		public function log( $message ) {
			if ( S2_Wishlist()->debug_active ) {
				S2_Wishlist()->debug->add( 's2wl', $message );
			}
		}

		/**
		 * Flush rules if the event is queued.
		 *
		 * @since  1.0.0
		 */
		public static function maybe_flush_rewrite_rules() {
			if ( ! get_option( 's2_wishlist_queue_flush_rewrite_rules' ) ) {
				update_option( 's2_wishlist_queue_flush_rewrite_rules', 'yes' );
				flush_rewrite_rules();
			}
		}

	}

}

/**
 * Unique access to instance of S2_Wishlist class
 *
 * @return \S2_Wishlist
 */
function S2_Wishlist() {
	return S2_Wishlist::get_instance();
}
