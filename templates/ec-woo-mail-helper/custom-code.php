<?php
/**
 * Custom code shortcode
 *
 * @var $order WooCommerce order
 * @var $email_id WooCommerce email id (new_order, completed_order,etc)
 * @var $attr array custom code attributes
 *
 * IMPORTANT NOTE:
 * After adding custom shortcode, you will not see the result during customizing,
 * If you want to test it, just click 'Preview' button (the first button in the top-right menu in the builder interface)
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Example for the short code [ec_woo_custom_code type="demo-purchase"]
// if(isset($attr['type']) && $attr['type'] == 'demo-purchase'){
//    printf( __( "This text added from custom shortcode"));
// }