<?php
/**
 * Plugin Name: S2 Wishlist for WooCommerce
 * Plugin URI: 
 * Description: <code><strong>S2 Wishlist for WooCommerce</strong></code> allows enabling wishlist on your products. Perfect for any kind of products like simple, variable and so on.
 * Version: 2.0.0
 * Author: Shuban Studio <shuban.studio@gmail.com>
 * Author URI: https://shubanstudio.github.io/
 * Text Domain: s2-wishlist
 * Domain Path: /languages/
 * WC requires at least: 4.7.0
 * WC tested up to: 5.6.0
 */

/**
 * @package S2 Wishlist
 * @since   1.0.0
 * @author  Shuban Studio <shuban.studio@gmail.com>
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'is_plugin_active' ) ) {
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
}

// Define constants __
! defined( 'S2_WL_DIR' ) 			&& define( 'S2_WL_DIR', plugin_dir_path( __FILE__ ) );
! defined( 'S2_WL_VERSION' ) 		&& define( 'S2_WL_VERSION', '2.0.0' );
! defined( 'S2_WL_FILE' ) 			&& define( 'S2_WL_FILE', __FILE__ );
! defined( 'S2_WL_URL' ) 			&& define( 'S2_WL_URL', plugins_url( '/', __FILE__ ) );
! defined( 'S2_WL_ASSETS_URL' ) 	&& define( 'S2_WL_ASSETS_URL', S2_WL_URL . 'assets' );
! defined( 'S2_WL_TEMPLATE_PATH' ) 	&& define( 'S2_WL_TEMPLATE_PATH', S2_WL_DIR . 'templates' );
! defined( 'S2_WL_INC' ) 			&& define( 'S2_WL_INC', S2_WL_DIR . '/includes/' );
! defined( 'S2_WL_TEST_ON' ) 		&& define( 'S2_WL_TEST_ON', ( defined( 'WP_DEBUG' ) && WP_DEBUG ) );
if ( ! defined( 'S2_WL_SUFFIX' ) ) {
	$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
	define( 'S2_WL_SUFFIX', $suffix );
}

/**
 * Print a notice if WooCommerce is not installed.
 *
 * @since  1.0.0
 */
function s2_wishlist_install_woocommerce_admin_notice() {
	?>
	<div class="error">
		<p><?php esc_html_e( 'S2 Wishlist for WooCommerce is enabled but not effective. It requires WooCommerce in order to work.', 's2-wishlist' ); ?></p>
	</div>
	<?php
}

/**
 * Check WC installation, user logged in and update the database if necessary.
 *
 * @since  1.0.0
 */
function s2_wishlist_install() {
	if ( ! function_exists( 'WC' ) ) {
		add_action( 'admin_notices', 's2_wishlist_install_woocommerce_admin_notice' );
	} else {
		if( is_user_logged_in() ) {
			load_plugin_textdomain( 's2-wishlist', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
			
			require_once S2_WL_INC . 'class.s2-wishlist.php';
			S2_Wishlist();

			do_action( 's2_wishlist_flush_rewrite_rules' );
		}
	}
}
add_action( 'plugins_loaded', 's2_wishlist_install', 11 );

/**
 * Remove flush rewrite rule option.
 *
 * @since  1.0.0
 */
function s2_wishlist_remove_flush_rewrite_rule_option() {
	delete_option( 's2_wishlist_queue_flush_rewrite_rules' );
}
register_deactivation_hook( __FILE__, 's2_wishlist_remove_flush_rewrite_rule_option' );
