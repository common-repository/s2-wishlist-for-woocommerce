<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) || ! defined( 'S2_WL_VERSION' ) ) {
	exit;
}

/**
 * Product loop wishlist flash
 *
 * @package S2 Wishlist\Templates
 * @version 1.0.0
 * @author  Shuban Studio <shuban.studio@gmail.com>
 */

$product_id    = $product->get_id();
$product_class = 's2-wishlist';

$data_wishlist = in_array( $product_id, $wishlist );
if ( $data_wishlist ) {
	$product_class .= ' s2-wishlist-selected';
}

echo apply_filters( 's2_wishlist_flash', '<span class="' . esc_attr( $product_class ) . '" data-wishlist="' . esc_attr( $data_wishlist ? 'true' : 'false' ) . '" data-product_id="' . esc_attr( $product_id ) . '" href="javascript:void(0);"></span>' );
