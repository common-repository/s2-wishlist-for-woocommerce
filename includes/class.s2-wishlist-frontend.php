<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) || ! defined( 'S2_WL_VERSION' ) ) {
	exit;
}

/**
 * Implements features of S2 Wishlist Frontend
 *
 * @class   S2_Wishlist_Frontend
 * @package S2 Wishlist
 * @since   1.0.0
 * @author  Shuban Studio <shuban.studio@gmail.com>
 */
if ( ! class_exists( 'S2_Wishlist_Frontend' ) ) {

	class S2_Wishlist_Frontend {

		/**
		 * Plugin settings
		 */
		public $s2wl_settings;

		/**
		 * Single instance of the class
		 *
		 * @var \S2_Wishlist_Frontend
		 */
		protected static $instance;

		/**
		 * Returns single instance of the class
		 *
		 * @return \S2_Wishlist_Frontend
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

			// Plugin settings
			$this->s2wl_settings = get_option('s2wl_settings');

			require_once S2_WL_INC . 'class.s2-wishlist-product.php';
			require_once S2_WL_INC . 'class.s2-wishlist-my-account.php';

			// custom styles and javascripts
			add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_styles_scripts' ], 11 );

			/* Ajax save_wishlist on user */
			add_action( 'wp_ajax_add_product_in_wishlist', [ $this, 'add_product_in_wishlist' ] );
			add_action( 'wp_ajax_nopriv_add_product_in_wishlist', [ $this, 'add_product_in_wishlist' ] );

			add_action( 'wp_ajax_remove_product_from_wishlist', [ $this, 'remove_product_from_wishlist' ] );
			add_action( 'wp_ajax_nopriv_remove_product_from_wishlist', [ $this, 'remove_product_from_wishlist' ] );

			add_action( 'wp_ajax_load_wishlist', [ $this, 'load_wishlist' ] );
			add_action( 'wp_ajax_nopriv_load_wishlist', [ $this, 'load_wishlist' ] );

			// add wishlist popbox in footer
			add_action( 'wp_footer', [ $this, 'load_wishlist_popbox' ] );

		}

		/**
		 * Enqueue styles and scripts
		 *
		 * @since  1.0.0
		 */
		public function enqueue_styles_scripts() {

			wp_enqueue_style( 's2_wishlist_frontend', S2_WL_ASSETS_URL . '/css/frontend' . S2_WL_SUFFIX . '.css', [], S2_WL_VERSION );

			wp_enqueue_script(
				's2_wishlist_frontend',
				S2_WL_ASSETS_URL . '/js/frontend' . S2_WL_SUFFIX . '.js',
				[ 'jquery' ],
				S2_WL_VERSION,
				true
			);

			wp_localize_script(
				's2_wishlist_frontend',
				's2_wishlist_frontend',
				[
					'ajaxurl'        => admin_url( 'admin-ajax.php' ),
					'wishlist_nonce' => wp_create_nonce( 'wishlist-nonce' ),
				]
			);

			/* slick slider css */
			wp_enqueue_style( 's2_wishlist_slick', S2_WL_ASSETS_URL . '/css/slick' . S2_WL_SUFFIX . '.css', [], '1.8.1' );

			/* slick slider js */
			wp_enqueue_script(
				's2_wishlist_slick',
				S2_WL_ASSETS_URL . '/js/slick' . S2_WL_SUFFIX . '.js',
				[ 'jquery' ],
				'1.8.1',
				true
			);

			/* dashicons css */
			wp_enqueue_style( 'dashicons' );

		}

		/**
		 * Save a product in wishlist in user meta.
		 *
		 * @since  1.0.0
		 */
		public function add_product_in_wishlist() {

			check_ajax_referer( 'wishlist-nonce', 'security' );

			$user_id = get_current_user_id();

			$wishlist = get_user_meta( $user_id, 's2-wishlist', true );
			$wishlist = ! is_array( $wishlist ) ? [] : $wishlist;

			if( ! empty( $_REQUEST['product_id'] ) ) {

				$product_id = sanitize_text_field( $_REQUEST['product_id'] );
				$product_id = intval( $product_id );

				$product = wc_get_product( $product_id );

				if( ! empty( $product ) ) {
					$wishlist[ $product_id ] = $product_id;

					update_user_meta( $user_id, 's2-wishlist', $wishlist );
				}

			}

			exit();

		}

		/**
		 * Remove a product from wishlist from user meta.
		 *
		 * @since  1.0.0
		 */
		public function remove_product_from_wishlist() {

			check_ajax_referer( 'wishlist-nonce', 'security' );

			$user_id = get_current_user_id();

			$wishlist = get_user_meta( $user_id, 's2-wishlist', true );

			if( ! empty( $wishlist ) && ! empty( $_REQUEST['product_id'] ) ) {

				$product_id = sanitize_text_field( $_REQUEST['product_id'] );
				$product_id = intval( $product_id );

				unset( $wishlist[ $product_id ] );

				update_user_meta( $user_id, 's2-wishlist', $wishlist );

			}

			exit();

		}

		/**
		 * Load a product from wishlist from user meta.
		 *
		 * @since  1.0.0
		 */
		public function load_wishlist() {

			check_ajax_referer( 'wishlist-nonce', 'security' );

			$load_products = 5;

			// if user_id is present then get wishlist by user_id
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
			
			} else {

				$wishlist = s2wl_get_user_wishlist();

			}

			$start = $end = 0;
			if( ! empty( $_REQUEST['start'] ) ) {

				$start = sanitize_text_field( $_REQUEST['start'] );
				$start = intval( $start );
				if( empty( $start ) ) {
					$start = -5;
				}

			}

			if( ! empty( $_REQUEST['end'] ) ) {

				$end = sanitize_text_field( $_REQUEST['end'] );
				$end = intval( $end );
				if( empty( $end ) ) {
					$end = 0;
				}

			}

			$start += $load_products;
			$end   += $load_products;

			$load_more = 'true';
			if( $end >= count( $wishlist ) ) $load_more = 'false';

			$wishlist = array_slice( $wishlist, $start, $end );

			$response = [
							'start' 	=> $start,
							'end'		=> $end,
							'load_more'	=> $load_more
						];

			$template = '';
			if( ! empty( $_REQUEST['template'] ) ) {
				$template = sanitize_text_field( $_REQUEST['template'] );
			}

			if( count( $wishlist ) == 0 || empty( $template ) ) {

				$response['html'][] = '<div>' . esc_html__( 'Wishlist is Empty', 's2-wishlist' ) . '</div>';

				echo wp_json_encode( $response );

			} else {

				$count = 0;
				foreach ( $wishlist as $product_id ) :

					$count++;

					$product = wc_get_product( $product_id );

					ob_start();
					wc_get_template(
						$template,
						[ 'product' => $product, 'count' => $count ],
						'',
						S2_WL_TEMPLATE_PATH . '/'
					);
					$html = ob_get_clean();

					$response['html'][] = $html;

				endforeach;

				echo wp_json_encode( $response );

			}

			exit();

		}

		/**
		 * Load wishlist popbox
		 *
		 * @since  1.0.0
		 */
		public function load_wishlist_popbox() {

			// if show_setting is no then do not load popbox html
			if( ! empty( $this->s2wl_settings['show_button'] ) && $this->s2wl_settings['show_button'] == 'no' ) return; 

			$wishlist = s2wl_get_user_wishlist();

			$start = 0;
			$end   = 5;

			$load_more = 'true';
			if( $end >= count( $wishlist ) ) $load_more = 'false';

			$wishlist = array_slice( $wishlist, $start, $end );

			wc_get_template(
				'frontend/wishlist-popbox.php',
				[ 'start' => $start, 'end' => $end, 'load_more' => $load_more, 'wishlist' => $wishlist, 's2wl_settings' => $this->s2wl_settings ],
				'',
				S2_WL_TEMPLATE_PATH . '/'
			);

		}

	}

}

/**
 * Unique access to instance of S2_Wishlist_Frontend class
 */
S2_Wishlist_Frontend::get_instance();
