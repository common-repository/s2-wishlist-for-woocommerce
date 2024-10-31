<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) || ! defined( 'S2_WL_VERSION' ) ) {
	exit;
}

/**
 * Wishlist social sharing
 *
 * @package S2 Wishlist\Templates
 * @version 1.0.5
 * @author  Shuban Studio <shuban.studio@gmail.com>
 */

// if social_sharing_enable is no then do not social sharing html
if( ( ! empty( $s2wl_settings['social_sharing_enable'] ) && $s2wl_settings['social_sharing_enable'] == 'no' ) || empty( $wishlist ) ) return;

$message = 'I loved this collection on ' . get_bloginfo( 'name' ) . ' and hope you will too. Check it out!';
$url = get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) . 'others-wishlist/?user_id=' . wp_hash( get_current_user_id(), 'nonce' );
?>

<div class="s2-wishlist-social-sharing">
    <ul>
    	<li>
			<span class="dashicons dashicons-share"></span>
		</li>
		<li>
			<a href="https://www.facebook.com/sharer/sharer.php?display=page&quote=<?php echo $message . ' ' . $url; ?>&u=<?php echo get_home_url(); ?>" target="_blank">
				<span class="dashicons dashicons-facebook"></span>
			</a>
		</li>
		<li>
			<a href="http://twitter.com/share?text=<?php echo $message; ?>&url=<?php echo $url; ?>" target="_blank">
				<span class="dashicons dashicons-twitter"></span>
			</a>
		</li>
		<li>
			<a href="https://api.whatsapp.com/send?phone=&text=<?php echo $message . ' ' . $url; ?>" target="_blank">
				<span class="dashicons dashicons-whatsapp"></span>
			</a>
		</li>
    </ul>
</div>
