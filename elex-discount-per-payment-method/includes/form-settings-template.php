<?php
/**
 *
 * Form Settings.
 *
 * @package Elex Discount Per Payment Mehtod
 */

// To check whether accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$nonce = wp_create_nonce( 'form_data' ); 
?>
<tr valign="top" >
	<td class="forminp" colspan="2" style="padding-left:0px">
		<table class="fields_adjustment widefat" id="elex_discount_per_payment_method_options">
		<input type="hidden" name="nounce_verify" value="<?php echo esc_attr($nonce); ?>">
			<thead>
				<th class="table_header" >&nbsp;</th>
				<th class="table_header" ><?php esc_html_e( 'Payment Method', 'elex_wfp_discount_per_payment_method' ); ?></th>
				<th class="table_header" ><?php esc_html_e( 'Discount(%)', 'elex_wfp_discount_per_payment_method' ); ?></th>
				<th class="table_header" ><?php esc_html_e( 'Status', 'elex_wfp_discount_per_payment_method' ); ?></th>
				<th class="table_header_remove" ><?php esc_html_e( 'Actions', 'elex_wfp_discount_per_payment_method' ); ?></th>
			</thead>
			<tbody id='elex_raq_table_body'>
				<?php

				$default_form_fields = array ();
			
				$form_adjustment_fields  = !empty(get_option( 'elex_discount_per_payment_method_options' ))?get_option( 'elex_discount_per_payment_method_options' ):$default_form_fields;
				
				if ( ! empty( $form_adjustment_fields ) ) {
					
					foreach ( $form_adjustment_fields as $key => $value ) {

						?>
						<tr id='<?php echo esc_attr($value['id']); ?>' class='<?php echo esc_attr( $value['type'] ); ?>'>
						<td  class='table_col_1' ></td>
						<td  class='table_col_2'> 
							<input type='hidden'  name='elex_discount_per_payment_method_options[<?php echo esc_attr( $key ); ?>][id]' value='<?php echo esc_attr( $value['id'] ); ?>' />
							<input type='hidden' class='order' name='elex_discount_per_payment_method_options[<?php echo esc_attr( $key ); ?>][type]' value='<?php echo esc_attr( $value['type'] ); ?>' />
							<span class="bold_span" ><?php esc_attr_e( $value['type'], 'elex_wfp_discount_per_payment_method'); ?></span>
						</td>
						<td class='table_col_3' title='<?php esc_html_e( 'Enter the discount percentage value from 0 to 100.', 'elex_wfp_discount_per_payment_method' ); ?>' > 
							<input type='number' min='0' max='100' class='order' name='elex_discount_per_payment_method_options[<?php echo esc_attr( $key ); ?>][value]' value='<?php echo esc_attr( $value['value'] ); ?>' required>
						</td>
						<?php
						if ('yes' == $value['checkbox_value']) {
							?>
						
							<td class='table_col_4' >
								<label title='<?php esc_html_e( 'Click here to disable the discount.', 'elex_wfp_discount_per_payment_method' ); ?>' class='switch'>
									<input type='hidden' class='order' name='elex_discount_per_payment_method_options[<?php echo esc_attr( $key ); ?>][checkbox_value]' value='<?php echo esc_attr( $value['checkbox_value'] ); ?>' />
									<input type='checkbox' class='checkbox_click' name='elex_discount_per_payment_method_options[<?php echo esc_attr( $key ); ?>][enabled]'  checked>
									<span class='slider round'></span>
								</label>
							</td>
						<?php
						} else {

							?>
								
								
								<td class='table_col_4' >
									<label title='<?php esc_html_e( 'Click here to enable the discount.', 'elex_wfp_discount_per_payment_method' ); ?>' class='switch'>
										<input type='hidden' class='order' name='elex_discount_per_payment_method_options[<?php echo esc_attr( $key ); ?>][checkbox_value]' value='<?php echo esc_attr( $value['checkbox_value'] ); ?>' />
										<input type='checkbox' class='checkbox_click' name='elex_discount_per_payment_method_options[<?php echo esc_attr( $key ); ?>][enabled]'  >
										<span class='slider round'></span>
									</label>
								</td>
						
						<?php
						}
						?>
							<td class='remove_icon_discount' title='<?php esc_html_e( 'Click here to remove the discount.', 'elex_wfp_discount_per_payment_method' ); ?>'></td>
						</tr>
						<?php
						
					}

				} else {

					?>

					<tr id='nodiscount' class='nodiscount'>
					<td class='table_col_1'></td>
					<td class='table_col_2'>
						<span class='wc-payment-gateway-method-name' id='no_discount_available'><?php esc_html_e( 'No payment method discounts have been specified. Select a payment method from the drop-down to set up a discount.', 'elex_wfp_discount_per_payment_method' ); ?></span>
					</td>
					<td class='table_col_3'></td>
					<td class='table_col_4'></td>
					<td class='table_col_4'></td>
					</tr>

					<?php
				}

				?>

			</tbody>
			<br><br>
			<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
			<tr class="table_input_raw">
			
				<td></td>
				<td>
					
					<select  title='<?php esc_html_e( 'Select a payment methods.', 'elex_wfp_discount_per_payment_method' ); ?>' id='elex_discount_per_payment_method_options_select_value' >

						<?php
						foreach ( $PMD_available as $k => $v ) {
							$flag=1;
							foreach ($form_adjustment_fields as $key => $value) {

								if ($k==$value['id']) {
									$flag=0;
									break;
									
								}
							}

							if (1 == $flag) {
								?>
									<option id='<?php echo esc_attr( $k ); ?>' value='<?php echo esc_attr( $v ); ?>' selected><?php esc_attr_e( $v, 'elex_wfp_discount_per_payment_method'); ?></option>
								<?php
							}
						}
						?>
						</select>
						
				
				</td>
				
				<td>
					
					<input type='number' class='eraq_input_label' title="<?php esc_html_e( 'Enter the discount percentage value from 0 to 100.', 'elex_wfp_discount_per_payment_method' ); ?>" id="elex_discount_per_payment_method_options_input_value" placeholder=" discount value(%)" value="0" min="0" max="100" >
				</td>	
				<td>
					<label class="switch" title='<?php esc_html_e( 'Click to enable or disable the discount', 'elex_wfp_discount_per_payment_method' ); ?>'>
					  <input id="elex_discount_per_payment_method_options_enabled" type="checkbox" checked>
					  <span class="slider round"></span>
					</label>
				</td>
				
				<td  class="col_btn">
					<button class="btn_raw" title="<?php esc_html_e( 'Click here to add a new discount.', 'elex_wfp_discount_per_payment_method' ); ?>"  id="elex_raq_add_field_discount"  ><i class="dashicons dashicons-plus-alt"></i></button>
				</td>
			</tr>
			
		</table>
	</td>
</tr>

