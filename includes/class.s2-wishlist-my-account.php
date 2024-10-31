<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) || ! defined( 'S2_WL_VERSION' ) ) {
	exit;
}

/**
 * Implements admin features of Wishlist in My Account page
 *
 * @class   S2_Wishlist_Product
 * @package S2 Wishlist
 * @since   1.0.0
 * @author  Shuban Studio <shuban.studio@gmail.com>
 */
if ( ! class_exists( 'S2_Wishlist_My_Account' ) ) {

	class S2_Wishlist_My_Account {

		/**
		 * Plugin settings
		 */
		public $s2wl_settings;

		/**
		 * Single instance of the class
		 *
		 * @var \S2_Wishlist_My_Account
		 */
		protected static $instance;

		/**
		 * Returns single instance of the class
		 *
		 * @return \S2_Wishlist_My_Account
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

			// Plugin settings
			$this->s2wl_settings = get_option('s2wl_settings');

			// Actions used to insert a new endpoint in the WordPress.
			add_action( 'init', [ $this, 'add_endpoints' ] );
			add_filter( 'query_vars', [ $this, 'add_query_vars' ], 0 );

			// Change the My Accout page title.
			add_filter( 'the_title', [ $this, 'endpoint_title' ] );

			// Insering your new tab/page into the My Account page.
			add_filter( 'woocommerce_account_menu_items', [ $this, 'add_menu_items' ] );

			add_action( 'woocommerce_account_my-wishlist_endpoint', [ $this, 'my_wishlist' ] );
			add_action( 'woocommerce_account_others-wishlist_endpoint', [ $this, 'others_wishlist' ] );

		}

		/**
		 * Register new endpoint to use inside My Account page.
		 *
		 * @see https://developer.wordpress.org/reference/functions/add_rewrite_endpoint/
		 *
		 * @since  1.0.0
		 */
		public function add_endpoints() {
			add_rewrite_endpoint( 'my-wishlist', EP_ROOT | EP_PAGES );
			add_rewrite_endpoint( 'others-wishlist', EP_ROOT | EP_PAGES );
		}

		/**
		 * Add new query var.
		 *
		 * @param array $vars
		 * @return array
		 *
		 * @since  1.0.0
		 */
		public function add_query_vars( $vars ) {
			$vars[] = 'my-wishlist';
			$vars[] = 'others-wishlist';

			return $vars;
		}

		/**
		 * Set endpoint title.
		 *
		 * @param string $title
		 * @return string
		 *
		 * @since  1.0.0
		 */
		public function endpoint_title( $title ) {
			global $wp_query;

			if ( ! is_admin() && is_main_query() && in_the_loop() && is_account_page() ) {

				if ( isset( $wp_query->query_vars[ 'my-wishlist' ] ) ) {

					// New page title.
					$title = __( 'My wishlist', 's2-wishlist' );

					remove_filter( 'the_title', [ $this, 'endpoint_title' ] );

				} elseif ( isset( $wp_query->query_vars[ 'others-wishlist' ] ) ) {

					// New page title.
					$title = __( 'Others wishlist', 's2-wishlist' );

					remove_filter( 'the_title', [ $this, 'endpoint_title' ] );

				}

			}

			return $title;
		}

		/**
		 * Insert the new endpoint into the My Account menu.
		 *
		 * @param array $items
		 * @return array
		 *
		 * @since  1.0.0
		 */
		public function add_menu_items( $menu_items ) {

			// Add our menu item after the Orders tab if it exists, otherwise just add it to the end
			$orders_key_exist = array_key_exists( 'orders', $menu_items );
			if ( $orders_key_exist ) {

				$new_menu_items = [];

				foreach ( $menu_items as $key => $value ) {

					$new_menu_items[ $key ] = $value;

					if ( $key == 'orders' ) {
						$new_menu_items['my-wishlist'] = "My wishlist";

						if( ! empty( $_REQUEST['user_id'] ) ) $new_menu_items['others-wishlist'] = "Others wishlist";
					}

				}

				$menu_items = $new_menu_items;

			} else {

				$menu_items['my-wishlist'] = "My Wishlist";

				if( ! empty( $_REQUEST['user_id'] ) ) $menu_items['others-wishlist'] = "Others Wishlist";

			}

			return $menu_items;

		}

		/**
		 * Wishlist Endpoint HTML content.
		 *
		 * @since  1.0.0
		 */
		public function my_wishlist() {
			$wishlist = s2wl_get_user_wishlist();

			$start = 0;
			$end   = 5;

			$load_more = 'true';
			if( $end >= count( $wishlist ) ) $load_more = 'false';

			$wishlist = array_slice( $wishlist, $start, $end );

			wc_get_template(
				'myaccount/my-wishlist.php',
				[ 'start' => $start, 'end' => $end, 'load_more' => $load_more, 'wishlist' => $wishlist, 's2wl_settings' => $this->s2wl_settings ],
				'',
				S2_WL_TEMPLATE_PATH . '/'
			);
		}

		/**
		 * Others Wishlist Endpoint HTML content.
		 *
		 * @since  1.0.5
		 */
		public function others_wishlist() {

			if( ! empty( $_REQUEST['user_id'] ) ) {

            	$wishlist_user_id = sanitize_text_field( $_REQUEST['user_id'] );

				$users   	   = get_users( [ 'fields' => 'ID' ] );
				$other_user_id = 0;
				foreach ( $users as $user_id ) {

					if( $wishlist_user_id == wp_hash( $user_id, 'nonce' ) ) {
						$other_user_id = $user_id;
						break;
					}

				}

				if( ! empty( $other_user_id ) ) $wishlist = s2wl_get_user_wishlist( $other_user_id );
			
			}

			$start = 0;
			$end   = 5;

			$load_more = 'true';
			if( $end >= count( $wishlist ) ) $load_more = 'false';

			$wishlist = array_slice( $wishlist, $start, $end );

			wc_get_template(
				'myaccount/others-wishlist.php',
				[ 'start' => $start, 'end' => $end, 'load_more' => $load_more, 'wishlist' => $wishlist, 's2wl_settings' => $this->s2wl_settings ],
				'',
				S2_WL_TEMPLATE_PATH . '/'
			);

		}

	}

}

/**
 * Unique access to instance of S2_Wishlist_My_Account class
 */
S2_Wishlist_My_Account::get_instance();
