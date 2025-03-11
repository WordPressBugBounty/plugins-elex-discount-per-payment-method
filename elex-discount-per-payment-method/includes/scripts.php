<?php

/*****************
 * Script control
 *****************/


add_action('wp_enqueue_scripts', 'Elex_Woo_Discount_Per_Payment_Method_Load_Assets');
add_action('admin_enqueue_scripts', 'Elex_Woo_Discount_Per_Payment_Method_Load_Assets');

function Elex_Woo_Discount_Per_Payment_Method_Load_Assets() {
	wp_nonce_field('elex_cpp_ajax_nonce', '_elex_cpp_ajax_nonce');
	if (is_admin()) {
		global $woocommerce;
		$woocommerce_version = function_exists('WC') ? WC()->version : $woocommerce->version;
		wp_enqueue_style('woocommerce_admin_styles', $woocommerce->plugin_url() . '/assets/css/admin.css', array(), $woocommerce_version, false);
	}

	$page = isset( $_GET['page'] ) ? $_GET['page'] : '';

	$tab = isset( $_GET['tab'] ) ? $_GET['tab'] : '';

	$section = isset( $_GET['section'] ) ? $_GET['section'] : '';

	if ( 'wc-settings' == $page && 'elex-discount-per-payment-method' == $tab && '' == $section ) {
			wp_register_style('elex-cpp-plugin-styles', plugins_url('/assets/css/plugin-styles.css', dirname(__FILE__)), array(), $woocommerce_version, false);
			wp_enqueue_style('elex-cpp-plugin-styles');
		
	}

	wp_enqueue_script( 'jquery' );
	wp_register_script('elex-cpp-custom-jquery', plugins_url('/assets/js/plugin-scripts.js', dirname(__FILE__)), array('jquery'), '1.0', false);
	wp_enqueue_script( 'elex-cpp-custom-jquery' );

	$wc_gateways    = new WC_Payment_Gateways();
	$PMD_available  = array();

	foreach ( $wc_gateways->get_available_payment_gateways() as $gateway ) {
		
		$PMD_available['payment_id'][$gateway->id] = __( $gateway->get_method_title(), 'elex_wfp_discount_per_payment_method');
	}
	$PMD_available['tooltip']['nodiscount']=__('No payment method discounts have been specified. Select a payment method from the drop-down to set up a discount.', 'elex_wfp_discount_per_payment_method');
	$PMD_available['tooltip']['allpaymentselected']=__('All available payment methods have been selected', 'elex_wfp_discount_per_payment_method');
	$PMD_available['tooltip']['inputalert']=__('Please enter value between 0 to 100', 'elex_wfp_discount_per_payment_method');
	$PMD_available['tooltip']['toggleon']=__('Click here to enable the discount', 'elex_wfp_discount_per_payment_method');
	$PMD_available['tooltip']['toggleoff']=__('Click here to disable the discount', 'elex_wfp_discount_per_payment_method');
	$PMD_available['tooltip']['inputtooltip']=__('Enter the discount percentage value from 0 to 100.', 'elex_wfp_discount_per_payment_method');
	$PMD_available['tooltip']['remove']=__('Click here to remove the discount.', 'elex_wfp_discount_per_payment_method');

	wp_localize_script( 'elex-cpp-custom-jquery', 'transalted_pmd', $PMD_available);

}
