function wpa_wcpb_add_to_cart( obj ) {
	var parent = obj.parent(),
		list_product_id = ''
		loader = jQuery('.wpa-wcpb-list .loader');
	loader.css('display', 'inline-block');	
	
	jQuery( '.list-select .item', parent ).each(function(){
		var checked = jQuery( 'input[type="checkbox"]:checked', jQuery(this) ).length;
		if ( checked ) {
			list_product_id += jQuery(this).attr('data-product-id') + ',';
		}
	});

	if ( list_product_id ) {
		jQuery.ajax( {
			type : "POST",
			url  : wpa_wcpb.ajaxurl,
			data : {
				action : 'wpa_wcpb_add_to_cart',
				list_product_id : list_product_id,
				_nonce : wpa_wcpb._nonce,
			},
			success : function( response ) {
				wpa_wcpb_toggleClass_loading('done', loader[0]);
				jQuery('.wpa-message').css('opacity', '1');
				// Update mini cart
				if ( jQuery('.widget_shopping_cart_content').length ){
					jQuery.post(
						wpa_wcpb.ajaxurl,
						{'action': 'wpa_wcpb_update_mini_cart'},
						function(response) {
							jQuery('.widget_shopping_cart_content').html(response);
						}
					);
				}
			}
		} );
	}
}

function wpa_wcpb_toggleClass_loading(toggleClassName, target) {
	var currentClassName = ' '+target.className+' ';
	if(~currentClassName.indexOf(' '+toggleClassName+' ')) {
		target.className = currentClassName.replace(' '+toggleClassName+' ', ' ').trim();
	} else {
		target.className = (currentClassName+' done').trim();
	}
}

function wpa_wcpb_onchange_input_check_total_discount(){
	var total_price = 0,
		wpa_wcpb_list = jQuery('.wpa-wcpb-list'),
		product_bundles = jQuery('.px-product-bundles'),
		input_checked_lenght = jQuery('.px-product-bundles input[type=checkbox]:checked').length,
		product_bundle_data = product_bundles.attr('data-total-discount')
		product_bundle_data_arr = product_bundle_data.split(','),
		bundle_percent = product_bundle_data_arr[input_checked_lenght - 1],
		currencySymbol = '<span class="woocommerce-Price-currencySymbol">' + jQuery('.total.price .current-price span.woocommerce-Price-amount .woocommerce-Price-currencySymbol', wpa_wcpb_list).html() + '</span>';
	if ( ! bundle_percent ) {
		for ( var i = product_bundle_data_arr.length - 1; i >= 0; i-- ) {
			if ( product_bundle_data_arr[i] ) {
				bundle_percent = product_bundle_data_arr[i];
				break;
			}
		};
	}
	
	jQuery('.px-product-bundles input[type=checkbox]:checked').each(function(){
		var parent = jQuery(this).parent().parent(),
			price = parent.attr('data-item-price'),
			new_price = parseFloat( price ) - parseFloat( price ) * parseFloat( bundle_percent ) / 100;
		jQuery('.price > span.woocommerce-Price-amount', parent).html(
			'<span class="woocommerce-Price-currencySymbol">' + jQuery('.price > span.woocommerce-Price-amount .woocommerce-Price-currencySymbol', parent).html() + '</span>' + new_price.toFixed(2)
		);
		total_price += parseFloat( price );
	});
	
	jQuery('.total.price .current-price span.woocommerce-Price-amount', wpa_wcpb_list).html(
		currencySymbol + (parseFloat( total_price ) - parseFloat( total_price ) * parseFloat( bundle_percent ) / 100 ).toFixed(2)
	);
	jQuery('.total.price .old-price span.woocommerce-Price-amount', wpa_wcpb_list).html(
		currencySymbol + total_price.toFixed(2)
	);
	jQuery('.total.price .save-price span.woocommerce-Price-amount', wpa_wcpb_list).html(
		currencySymbol + Math.round( parseFloat( total_price ) - ( parseFloat( total_price ) - parseFloat( total_price ) * parseFloat( bundle_percent ) / 100 ) ).toFixed(2)
	);
	jQuery('.total.price .save-percent', wpa_wcpb_list).html(
		bundle_percent
	);
}

function wpa_wcpb_onchange_input_check_discount_per_item(){
	var total_price = 0,
		bundles_price = 0,
		wpa_wcpb_list = jQuery('.wpa-wcpb-list'),
		product_bundles = jQuery('.px-product-bundles'),
		currencySymbol = '<span class="woocommerce-Price-currencySymbol">' + jQuery('.total.price .current-price span.woocommerce-Price-amount .woocommerce-Price-currencySymbol', wpa_wcpb_list).html() + '</span>';
	
	jQuery('.px-product-bundles input[type=checkbox]:checked').each(function(){
		var parent = jQuery(this).parent().parent(),
			item_price = parent.attr('data-item-price');
			item_percent = ( parent.attr('data-item-percent') ) ? parent.attr('data-item-percent') : 0,
			new_item_price = parseFloat( item_price ) - parseFloat( item_price ) * parseFloat( item_percent ) / 100;
		bundles_price += parseFloat( new_item_price ); 
		total_price += parseFloat( item_price );
	});

	jQuery('.total.price .current-price span.woocommerce-Price-amount', wpa_wcpb_list).html(
		currencySymbol + bundles_price
	);
	jQuery('.total.price .old-price span.woocommerce-Price-amount', wpa_wcpb_list).html(
		currencySymbol + total_price
	);
	jQuery('.total.price .save-price span.woocommerce-Price-amount', wpa_wcpb_list).html(
		currencySymbol + ( total_price - bundles_price )
	);
	jQuery('.total.price .save-percent', wpa_wcpb_list).html(
		parseFloat( 100 - ( bundles_price / total_price * 100 ) )
	);
}

