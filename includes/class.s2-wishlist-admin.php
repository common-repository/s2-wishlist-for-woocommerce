<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) || ! defined( 'S2_WL_VERSION' ) ) {
	exit;
}

/**
 * Implements admin features of S2 Wishlist
 *
 * @class   S2_Wishlist_Admin
 * @package S2 Wishlist
 * @since   1.0.0
 * @author  Shuban Studio <shuban.studio@gmail.com>
 */
if ( ! class_exists( 'S2_Wishlist_Admin' ) ) {

	class S2_Wishlist_Admin {

		/**
		 * Single instance of the class
		 *
		 * @var \S2_Wishlist_Admin
		 */

		protected static $instance;

		/**
		 * @var $_panel Panel Object
		 */
		protected $_panel;

		/**
		 * Returns single instance of the class
		 *
		 * @return \S2_Wishlist_Admin
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

			require_once S2_WL_INC . 'admin/class.s2-wishlist-plugin-panel.php';
			require_once S2_WL_INC . 'admin/class.s2-wishlist-plugin-setting.php';
			require_once S2_WL_INC . 'admin/class.s2-wishlist-report-list.php';

		}

	}

}

/**
 * Unique access to instance of S2_Wishlist_Admin class
*/
if ( is_admin() ) {
	S2_Wishlist_Admin::get_instance();
}
