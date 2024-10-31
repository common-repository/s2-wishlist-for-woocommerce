<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) || ! defined( 'S2_WL_VERSION' ) ) {
	exit;
}

/**
 * General settings
 *
 * @package S2 Wishlist
 * @since   1.0.0
 * @author  Shuban Studio <shuban.studio@gmail.com>
 */

$settings = [

	'general_settings' 		=> [
								'title' 		=> __( 'General settings', 's2-wishlist' ),
								'type'  		=> 'title',
							],
	'show_button'   		=> [
								'title'     	=> __( 'Show fixed wishlist button in footer', 's2-wishlist' ),
								'description'  	=> '',
								'type'      	=> 'checkbox',
								'default'   	=> 'yes',
							],
	'page_link'     		=> [
								'title'       	=> __( 'My wishlist page link', 's2-wishlist' ),
								'type'        	=> 'title',
								'description' 	=> 'If you don\'t want to show button, you can add the following page link ' . get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) . 'my-wishlist/ to your menu(custom link).',
							],
	'social_sharing' 		=> [
								'title' 		=> __( 'Social Sharing', 's2-wishlist' ),
								'description'  	=> '',
								'type'  		=> 'title',
							],
	'social_sharing_enable' => [
								'title'     	=> __( 'Enable / Disable', 's2-wishlist' ),
								'description'  	=> '',
								'type'      	=> 'checkbox',
								'default'   	=> 'yes',
							],

];

return $settings;
