<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) || ! defined( 'S2_WL_VERSION' ) ) {
	exit;
}

/**
 * Wishlist popbox content product
 *
 * @package S2 Wishlist\Templates
 * @version 1.0.0
 * @author  Shuban Studio <shuban.studio@gmail.com>
 */

$product_id = $product->get_id();

$product_class = implode( ' ', wc_get_product_class( '', $product ) );
if ( $count == 5 ) $product_class .= " last-wishlist-product";

$link = get_the_permalink( $product_id );

$sale = $product->is_on_sale();

$selected_class = 's2-wishlist s2-wishlist-selected';
?>

<div>
	<div class="<?php echo esc_attr( $product_class ); ?>">

		<a href="<?php echo esc_url( $link ); ?>" class="">

			<figure class="woocommerce-popbox-product-gallery__wrapper">
				<!-- Sale -->
				<?php
				if ( $sale ) :
					echo '<span class="onsale">' . esc_html__( 'Sale!', 's2-wishlist' ) . '</span>';
				endif;
				?>

				<!-- Image -->
				<?php echo $product->get_image( 'thumbnail', [ 'style' => 'width: 150px;' ] ); ?>

				<!-- Wishlist -->
				<span class="<?php echo esc_attr( $selected_class ); ?>" data-wishlist="true" data-product_id="<?php echo esc_attr( $product_id ); ?>" href="javascript:void(0);"></span>
			</figure>

			<!-- Title -->
			<h6 class="woocommerce-loop-product__title"><?php echo esc_html( $product->get_title() ); ?></h6>

			<!-- Price -->
			<?php if ( $price_html = $product->get_price_html() ) : ?>
				<p><span class="price"><?php echo $price_html; ?></span></p>
			<?php endif; ?>

			<!-- Rating -->
			<?php
			if ( wc_review_ratings_enabled() ) :
				echo wc_get_rating_html( $product->get_average_rating() );
			endif;
			?>

		</a>

		<!-- Add to cart -->
		<?php
		$args = [
			'quantity'   => 1,
			'class'      => implode(
				' ',
				array_filter(
					[
						'button',
						'product_type_' . $product->get_type(),
						$product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '',
						$product->supports( 'ajax_add_to_cart' ) && $product->is_purchasable() && $product->is_in_stock() ? 'ajax_add_to_cart' : '',
					]
				)
			),
			'attributes' => [
				'data-product_id'  => $product->get_id(),
				'data-product_sku' => $product->get_sku(),
				'aria-label'       => $product->add_to_cart_description(),
				'rel'              => 'nofollow',
			],
		];

		if ( isset( $args['attributes']['aria-label'] ) ) {
			$args['attributes']['aria-label'] = wp_strip_all_tags( $args['attributes']['aria-label'] );
		}

		echo sprintf(
				'<a href="%s" data-quantity="%s" class="%s" %s>%s</a>',
				esc_url( $product->add_to_cart_url() ),
				esc_attr( isset( $args['quantity'] ) ? $args['quantity'] : 1 ),
				esc_attr( isset( $args['class'] ) ? $args['class'] : 'button' ),
				isset( $args['attributes'] ) ? wc_implode_html_attributes( $args['attributes'] ) : '',
				esc_html( $product->add_to_cart_text() )
			);
		?>

	</div>
</div>
