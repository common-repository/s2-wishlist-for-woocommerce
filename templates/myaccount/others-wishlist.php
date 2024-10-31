<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) || ! defined( 'S2_WL_VERSION' ) ) {
	exit;
}

/**
 * Others wishlist
 *
 * @package S2 Wishlist\Templates
 * @version 1.0.0
 * @author  Shuban Studio <shuban.studio@gmail.com>
 */
?>

<input type="hidden" id="start-wishlist-products" value="<?php echo esc_attr( $start ); ?>"/>
<input type="hidden" id="end-wishlist-products" value="<?php echo esc_attr( $end ); ?>"/>
<input type="hidden" id="wishlist-template" value="myaccount/others-wishlist-content-product.php"/>
<input type="hidden" id="load-more-wishlist-products" value="<?php echo esc_attr( $load_more ); ?>"/>
<input type="hidden" id="user-id" value="<?php echo esc_attr( $_REQUEST['user_id'] ); ?>"/>

<ul class="wishlist-products products columns-3">

	<?php
	if( ! empty( $wishlist ) ):
		$count = 0;
		foreach ( $wishlist as $product_id ) :

			$count++;

			$product = wc_get_product( $product_id );

			include( 'my-wishlist-content-product.php' );

		endforeach;
	else:
		echo '<div>' . esc_html__( 'Wishlist is Empty', 's2-wishlist' ) . '</div>';
	endif;
	?>

</ul>
