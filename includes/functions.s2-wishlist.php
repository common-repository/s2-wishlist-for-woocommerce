<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) || ! defined( 'S2_WL_VERSION' ) ) {
	exit;
}

/**
 * Implements helper functions for S2 Wishlist
 *
 * @package S2 Wishlist
 * @since   1.0.0
 * @author  Shuban Studio <shuban.studio@gmail.com>
 */

if ( ! function_exists( 's2wl_get_user_wishlist' ) ) {
	/**
	 * Get wishlist of a user
	 *
	 * @param int $user_id
	 *
	 * @return array
	 *
	 * @since   1.0.0
	 */

	function s2wl_get_user_wishlist( $user_id = 0 ) {

		if ( 0 === $user_id || empty( $user_id ) ) {
			$user_id = get_current_user_id();
		}

		$wishlist = get_user_meta( $user_id, 's2-wishlist', true );

		return $wishlist ? $wishlist : [];
	}
}
