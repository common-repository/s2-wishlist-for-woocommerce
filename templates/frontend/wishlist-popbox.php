<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) || ! defined( 'S2_WL_VERSION' ) ) {
	exit;
}

/**
 * Wishlist popbox
 *
 * @package S2 Wishlist\Templates
 * @version 1.0.0
 * @author  Shuban Studio <shuban.studio@gmail.com>
 */
?>

<input type="hidden" id="start-wishlist-popbox-products" value="<?php echo esc_attr( $start ); ?>"/>
<input type="hidden" id="end-wishlist-popbox-products" value="<?php echo esc_attr( $end ); ?>"/>
<input type="hidden" id="load-more-wishlist-popbox-products" value="<?php echo esc_attr( $load_more ); ?>"/>

<div id="wishlist-popbox-overlay"></div> 
<div class="sticky-s2-wishlist">
	<div class="contents">
		<span class="label"><?php esc_html_e( 'My wishlist', 's2-wishlist' ); ?></span>
		<div class="footerbox">
			<div class="popbox" style="display: none;">
				<div class="popbox-inner">
					<?php
					// load wishlist social sharing template
					wc_get_template(
						'frontend/wishlist-social-sharing.php',
						[ 's2wl_settings' => $s2wl_settings, 'wishlist' => $wishlist ],
						'',
						S2_WL_TEMPLATE_PATH . '/'
					);
					?>

					<div class="wishlist-popbox-products">

						<?php
						if( ! empty( $wishlist ) ):
							$count = 0;
							foreach ( $wishlist as $product_id ) :

								$count++;

								$product = wc_get_product( $product_id );

								include( 'wishlist-popbox-content-product.php' );

							endforeach;
						else:
							echo '<div>' . esc_html__( 'Wishlist is Empty', 's2-wishlist' ) . '</div>';
						endif;
						?>

					</div>
				</div>
			</div>
		</div>
	</div><!-- contents -->
</div>
