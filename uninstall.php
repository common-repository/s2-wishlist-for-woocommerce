<?php
/**
 * @package S2 Wishlist
 * @since   1.0.0
 * @author  Shuban Studio <shuban.studio@gmail.com>
 */

// Exit if accessed directly
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

$option_name = 's2wl_settings';

delete_option( $option_name );
