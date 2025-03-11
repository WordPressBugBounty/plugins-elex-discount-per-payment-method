
jQuery(document).ready(function(){
   
	/*Scripts for form-settings-temp */
    jQuery("#elex_raq_table_body").on('click', '.remove_icon_discount', function () {
		
		var tbody = jQuery('.fields_adjustment').find('tbody');
		var size = tbody.find('tr').size() - 2;
		var id = jQuery(this).closest("tr").attr("id");
		var value = jQuery(this).closest("tr").attr("class");

		if(value==="nodiscount"){

			jQuery("#nodiscount").remove();
			
		}else if(size===1){

			
			jQuery('#elex_discount_per_payment_method_options_select_value').append('<option id="'+id+'" value="'+value+'">'+transalted_pmd['payment_id'][id]+'</option>');
			jQuery(this).closest("tr").remove();

			var default_code='<tr id="nodiscount" class="nodiscount"><td class="table_col_1"></td><td class="table_col_2"><span class="wc-payment-gateway-method-name" id="no_discount_available">'+transalted_pmd['tooltip']['nodiscount']+'</span></td><td class="table_col_3"></td><td class="table_col_4"></td> <td class="table_col_4"></td></tr>';

			jQuery('#elex_raq_table_body').append( default_code );

		}else{

		jQuery('#elex_discount_per_payment_method_options_select_value').append('<option id="'+id+'" value="'+value+'">'+transalted_pmd['payment_id'][id]+'</option>');
		jQuery(this).closest("tr").remove();
		
		}
	});

	jQuery("#elex_raq_table_body").on('click','.checkbox_click', function () {

		var v = jQuery(this).closest("td").find('input').val();
		
		
		if(v=="yes")
		{
			jQuery(this).closest("td").find('input').val("no");
			jQuery(this).closest("td").find('label').attr('title',transalted_pmd['tooltip']['toggleon']);
		}
		

		if(v=="no")
		{
			jQuery(this).closest("td").find('input').val("yes");
			jQuery(this).closest("td").find('label').attr('title', transalted_pmd['tooltip']['toggleoff']);
			
		}


	});

	jQuery('#elex_raq_add_field_discount').click( function() {

		if(closest_tr_class="nodiscount"){

			jQuery("#nodiscount").remove();

		}


		var tbody = jQuery('.fields_adjustment').find('tbody');
		var size = tbody.find('tr').size();
		var closest_tr_class = jQuery('#nodiscount').attr("class");
		var checkbox_enabled= jQuery('#elex_discount_per_payment_method_options_enabled').is(':checked');

		var payment = jQuery('#elex_discount_per_payment_method_options_select_value').find(":selected").text();

		
		var id = jQuery('#elex_discount_per_payment_method_options_select_value').find(":selected").attr("id");
		var input_value = jQuery("#elex_discount_per_payment_method_options_input_value").val();
		jQuery("#elex_discount_per_payment_method_options_input_value").val(0); 
		
		if (input_value<=100 && input_value>=0 && input_value.match(/^[0-9]+$/)&&input_value!="" && payment!="")
		{
			jQuery('#elex_discount_per_payment_method_options_select_value').find(":selected").remove();	
			var code='<tr id="'+id+'" class="'+payment+'">\
					<td class="table_col_1"  > </td>\
					<td class="table_col_2"><input type="hidden" class="order" name="elex_discount_per_payment_method_options['+size+'][id]" value="'+id+'"/><input type="hidden" class="order" name="elex_discount_per_payment_method_options['+size+'][type]" value="'+payment+'"/> <span class="wc-payment-gateway-method-name" >'+transalted_pmd['payment_id'][id]+'</span></td>\
					<td class="table_col_3" title="'+transalted_pmd['tooltip']['inputtooltip']+'" > <input type="number" min="0" max="100" class="order" name="elex_discount_per_payment_method_options['+size+'][value]" value="'+input_value+'" required></td>';

			if(checkbox_enabled)
			{
				code +='<td  class="table_col_4" > <label title="'+transalted_pmd['tooltip']['toggleoff']+'" class="switch"><input type="hidden" class="order" name="elex_discount_per_payment_method_options['+size+'][checkbox_value]" value="yes"/><input    type="checkbox" class="checkbox_click" name="elex_discount_per_payment_method_options['+size+'][enabled]" value="1" checked><span class="slider round"></span></label></td>';
			
			}else{

				code +='<td class="table_col_4"> <label title="'+transalted_pmd['tooltip']['toggleon']+'" class="switch"><input type="hidden" class="order" name="elex_discount_per_payment_method_options['+size+'][checkbox_value]" value="no"/><input  type="checkbox" class="checkbox_click" name="elex_discount_per_payment_method_options['+size+'][enabled]" value="0"  ><span class="slider round"></span></label></td>';

			}

			code +='<td class="remove_icon_discount" title="'+transalted_pmd['tooltip']['remove']+'" ></td>\
					</tr>';

			jQuery('#elex_raq_table_body').append( code );
		}else{

			if(payment==""){

				alert(transalted_pmd['tooltip']['allpaymentselected']);

			}else{
				alert(transalted_pmd['tooltip']['inputalert']);
			}

		}
		return false;
	});


    /* script for refreshing cart on selecting different payment option */

    (function($){
                    
        $( 'form.checkout' ).on( 'change', 'input[name^="payment_method"]', function() {

        $('body').trigger('update_checkout');

        });

    })(jQuery);


});

