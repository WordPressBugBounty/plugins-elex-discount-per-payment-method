<?php
/**
 * Plugin Name:          ELEX WooCommerce Discount Per Payment Method
 * Plugin URI:           https://elextensions.com/plugin/elex-woocommerce-discount-per-payment-method-free/
 * Description:          The plugin allows to set discounts according to the payment method selected on the Checkout page.
 * Version:              1.2.9
 * Author:               ELEXtensions
 * Author URI:           https://elextensions.com/
 * Developer:            ELEXtensions    
 * WC requires at least: 2.6.0
 * WC tested up to:      9.8
 */

 //Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

// for multisite compatibility
$active_plugins = (array) get_option( 'active_plugins' );
if ( is_multisite() ) {
	$active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins' ), array() );
} 


// for Required functions
if ( ! function_exists( 'elex_dpp_basic_is_woocommerce_active' ) ) {
	require_once  'elex-includes/elex-discount-per-payment-basic-functions.php' ;
}
// to check woocommerce is active
if ( ! ( elex_dpp_basic_is_woocommerce_active() ) ) {
	add_action( 'admin_notices', 'woocommerce_activation_notice_in_dpp_basic' );
	return;
}

function woocommerce_activation_notice_in_dpp_basic() {  ?>
	<div id="message" class="error">
		<p>
			<?php echo( esc_attr_e( 'WooCommerce plugin must be active for ELEX WooCommerce Discount Per Payment Method plugin to work.', 'elex_wfp_discount_per_payment_method' ) ); ?>
		</p>
	</div>
	<?php
}
// review component
if ( ! function_exists( 'get_plugin_data' ) ) {
	require_once  ABSPATH . 'wp-admin/includes/plugin.php';
}
include_once __DIR__ . '/review_and_troubleshoot_notify/review-and-troubleshoot-notify-class.php';
$data                      = get_plugin_data( __FILE__, false, false);
$data['name']              = $data['Name'];
$data['basename']          = plugin_basename( __FILE__ );
$data['documentation_url'] = 'https://elextensions.com/knowledge-base/set-up-elex-woocommerce-discount-per-payment-method-plugin/';
$data['rating_url']        = 'https://elextensions.com/plugin/elex-woocommerce-discount-per-payment-method-free/#reviews';
$data['support_url']       = 'https://wordpress.org/support/plugin/elex-discount-per-payment-method/';

new \Elex_Review_Components( $data );

require_once( WP_PLUGIN_DIR . '/woocommerce/includes/admin/settings/class-wc-settings-page.php' );
include_once( ABSPATH . 'wp-admin/includes/plugin.php');


class Elex_Woo_Discount_Per_Payment_Method extends WC_Settings_Page {


	public function __construct () {

					
		//registering and loading scripts
		include('includes/scripts.php');  		
				
		$this->id = 'elex-discount-per-payment-method';
		$this->label = __( 'ELEX Discount Per Payment Method', 'elex_wfp_discount_per_payment_method' );
						
		add_filter( 'woocommerce_settings_tabs_array', array( $this, 'add_settings_page' ), 99 );
		add_action( 'woocommerce_sections_' . $this->id, array( $this, 'Elex_Woo_Discount_Per_Payment_Method_Output_Sections' ) );
		add_action( 'woocommerce_settings_' . $this->id, array( $this, 'Elex_Woo_Discount_Per_Payment_Method_Output' ) );
		add_action( 'woocommerce_settings_save_' . $this->id, array( $this, 'Elex_Woo_Discount_Per_Payment_Method_Save' ) );
		add_filter('plugin_action_links_' . plugin_basename(__FILE__), array($this, 'Elex_Woo_Discount_Per_Payment_Method_Add_Menu') );

		//for adding discount on checkout page
		add_action( 'woocommerce_before_calculate_totals', array($this,'Elex_Woo_Discount_Per_Payment_Method_Add_Discount'), 99999, 3);
		//for adding html ( label and discounted price ) on checkout page.
		add_filter('woocommerce_checkout_cart_item_quantity', array($this,'Elex_Woo_Discount_Per_Payment_Method_Add_html'), 10, 3);

		// High performance order tables compatibility.
		add_action( 'before_woocommerce_init', function() {
			if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
				\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
			}
		} );
	}
	public function Elex_Woo_Discount_Per_Payment_Method_Add_html( $product_quantity, $cart_item, $cart_item_key) {
		if ( is_admin() && ! defined ( 'DOING_AJAX' ) ) {
			return 0;
		}   
		if (is_cart()) {
			return 0;
		} 
		$PWD_Selected_Payment_id = WC()->session->get('chosen_payment_method');
						
		if ( empty( $PWD_Selected_Payment_id ) ) {

		return 0;

		}	
		$PWD_stored_values = get_option( 'elex_discount_per_payment_method_options' );
		$PWD_label_default='Payment method discount';
		$PWD_label=!empty(get_option( 'elex_wfp_discount_per_payment_method_label' ))?get_option( 'elex_wfp_discount_per_payment_method_label' ):$PWD_label_default;

		if ( !WC()->session->__isset( 'reload_checkout' )) {
			if (!empty($PWD_stored_values)) {
				foreach ($PWD_stored_values as $key=>$PWD_stored_value) {
					if ($PWD_stored_value['id']==$PWD_Selected_Payment_id && 'yes' == $PWD_stored_value	['checkbox_value']) {
					$regular_price = $cart_item['data']->get_regular_price();
					$sale_price = $cart_item['data']->get_sale_price();
						if (empty( $sale_price )) {
							$product_price = $regular_price;
						} else {
							$product_price = $sale_price;
						}
					$discounted_price = $cart_item['data']->get_price();
					$regular_price_formatted = wc_price($product_price);
				   return '<br><span class="custom-product-text" style="color: #666; font-weight: normal;font-size: 13px;"><strike>' . $regular_price_formatted . '</strike> ' . wc_price( $discounted_price ) . ' ' . $PWD_label . '</span>';
					}
				}
			}
		}
	}
	
	public function Elex_Woo_Discount_Per_Payment_Method_Get_Sections() {
						
		$sections = array(
			'' => __( 'General Settings', 'elex_wfp_discount_per_payment_method' ),
			'interested_products' => __( 'Related Products!', 'elex_wfp_discount_per_payment_method' )
			
		);
		/**
		 * Filter the sections of the WooCommerce settings page for a specific tab.
		 *
		 * @since 1.0.0
		 *
		 * @param array  $sections The sections available on the WooCommerce settings page.
		 */
		return apply_filters( 'woocommerce_get_sections_' . $this->id, $sections );
					
	}

	public function Elex_Woo_Discount_Per_Payment_Method_Output_Sections() {
		global $current_section;
		$sections = $this->Elex_Woo_Discount_Per_Payment_Method_Get_Sections();
		if ( empty( $sections ) || 1 === count( $sections ) ) {
			return;
		}
		echo '<ul class="subsubsub">';
		$array_keys = array_keys( $sections );
		foreach ( $sections as $id => $label ) {

			echo '<li><a href="' . esc_attr( admin_url( 'admin.php?page=wc-settings&tab=' . $this->id . '&section=' . sanitize_title( $id ) )) . '" class="' . esc_attr( ( $current_section == $id ? '':'interested_products' )) . '">' . esc_html( $label ) . '</a> ' . ( end( $array_keys ) == $id ? '' : '|' ) . ' </li>';
		}
		echo '</ul><br class="clear" />';
	}

	public function Elex_Woo_Discount_Per_Payment_Method_Get_Settings() {
						
		global $current_section;
		$settings = array();

						
		if ( 'interested_products' == $current_section ) {
						   
			$settings = array();
						
		} else {

				$wc_gateways    = new WC_Payment_Gateways();
				$PMD_available  = array();
							
			foreach ( $wc_gateways->get_available_payment_gateways() as $gateway ) {

				$PMD_available[$gateway->id] = $gateway->get_method_title();
				add_option('elex_wfp_discount_per_payment_method_available_' . $gateway->id, 0);
							
			}

			$settings = array(

				'section_title' => array(
					'name'     => __( 'ELEX Discount Per Payment Method', 'elex_wfp_discount_per_payment_method' ),
					'type'     => 'title',
					'desc'     => __('<p>Helps you configure and apply a discount on the Checkout page based on the payment method selected by the customer.</p>'),
					'id'       => 'elex_wfp_discount_per_payment_method_title'
				),
				'section' => array(
					'name' => __( 'Checkout discount label', 'elex_wfp_discount_per_payment_method' ),
					'type' => 'text',
					'placeholder' => __( 'Enter custom discount label', 'elex_wfp_discount_per_payment_method'),
					'desc_tip' => true,
					'css' => 'width:200px',
					'desc' => __( 'Enter the text to be displayed on checkout. The default text is <strong>Payment method discount</strong>.', 'elex_wfp_discount_per_payment_method' ),
					'id'   => 'elex_wfp_discount_per_payment_method_label'
				),

				'section_end' => array(
					'type' => 'sectionend',
					'id' => 'elex_wfp_discount_per_payment_method_end'
				),
								
				);    
					
		}

		/**
		 * Filter the settings array for a specific WooCommerce settings tab.
		 *
		 * @since 1.0.0
		 *
		 * @param array $settings The settings array for the current WooCommerce tab.
		 * @param string $this->id The ID of the current settings tab.
		 */
		return apply_filters( 'woocommerce_get_settings_' . $this->id, $settings );
			
	}

	public function Elex_Woo_Discount_Per_Payment_Method_Output() {

		global $current_section;

		if ( 'interested_products' == $current_section ) {
			wp_enqueue_style( 'elex-bootstrap', plugins_url( '/assets/css/elex-market-styles.css', __FILE__ ), false, true );	
			include_once 'includes/market.php';
					
		} else {


		$wc_gateways      =  new WC_Payment_Gateways();
		$settings = $this->Elex_Woo_Discount_Per_Payment_Method_Get_Settings();
		WC_Admin_Settings::output_fields( $settings );
					   
		$PMD_available  = array();
							
			foreach ( $wc_gateways->get_available_payment_gateways() as $gateway ) {

						$PMD_available[$gateway->id] = $gateway->get_method_title();
							
			}
						include_once 'includes/form-settings-template.php';
		}
	}

	public function Elex_Woo_Discount_Per_Payment_Method_Save() {

		global $current_section;
		$settings = $this->Elex_Woo_Discount_Per_Payment_Method_Get_Settings();
		WC_Admin_Settings::save_fields( $settings );
		
		if (isset($_POST['nounce_verify'])) {
		$verify = wp_verify_nonce( sanitize_text_field( $_POST['nounce_verify']), 'form_data');
		}

		if ( isset( $_POST['elex_discount_per_payment_method_options'] ) && $verify ) {

			$options = array_values(
				$_POST['elex_discount_per_payment_method_options']
			);
			
			array_walk_recursive(
				$options,
				'sanitize_text_field'
			);
			update_option( 'elex_discount_per_payment_method_options', $options);
		} else {
			update_option( 'elex_discount_per_payment_method_options', array() );
		}
		if ( $current_section ) {
			/**
			 * Triggered after updating the options for a specific section in a WooCommerce settings tab.
			 *
			 * @since 1.0.0
			 *
			 * @param string $current_section The current section being updated.
			 */
			do_action( 'woocommerce_update_options_' . $this->id . '_' . $current_section );
		}
	}

	public function Elex_Woo_Discount_Per_Payment_Method_Add_Discount( $cart_object ) {

		if ( is_admin() && ! defined ( 'DOING_AJAX' ) ) {

			return 0;
		}   
		if (is_cart()) {

			return 0;
		} 
		$PWD_Selected_Payment_id = WC()->session->get('chosen_payment_method');
						
		if ( empty( $PWD_Selected_Payment_id ) ) {

		return 0;

		}	
		$PWD_stored_values = get_option( 'elex_discount_per_payment_method_options' );
		if ( !WC()->session->__isset( 'reload_checkout' )) {
			if (!empty($PWD_stored_values)) {
						
				foreach ($PWD_stored_values as $key=>$PWD_stored_value) {
								
					if ($PWD_stored_value['id']==$PWD_Selected_Payment_id && 'yes' == $PWD_stored_value	['checkbox_value']) {
						foreach ( $cart_object->cart_contents as $key => $value ) {
							// Turn $value['data']->price in to $value['data']->get_price()
							$orgPrice = floatval( $value['data']->get_price() );
							$PWD_total=$PWD_stored_value['value'] * $orgPrice;
							$PWD_discount = $PWD_total / 100;
							$discPrice = $orgPrice - $PWD_discount;
							$value['data']->set_price($discPrice);
						}
					}
				}
			}
			
		}
	}
	public function Elex_Woo_Discount_Per_Payment_Method_Add_Menu ( $links ) {

		$plugin_links = array(
			'<a href="' . admin_url('/admin.php?page=wc-settings&tab=elex-discount-per-payment-method') . '">' . __('Settings', 'elex_wfp_discount_per_payment_method') . '</a>',
			'<a href=https://elextensions.com/product-category/plugins/> ' . __('Related Products!', 'elex_wfp_discount_per_payment_method') . '</a>',
		);
		return array_merge($plugin_links, $links);


	}



}

new Elex_Woo_Discount_Per_Payment_Method();

function elex_discount_per_payment_basic_load_plugin_textdomain() {
	load_plugin_textdomain( 'elex_wfp_discount_per_payment_method', false, basename( dirname( __FILE__ ) ) . '/language/' );
}
add_action( 'plugins_loaded', 'elex_discount_per_payment_basic_load_plugin_textdomain' );



