<?php

/**
* # WOOCOMMERCE FUNCTIONS #
* Included hooks:
*
*	add_filter( 'woocommerce_add_to_cart_fragments', 'dms_ajax_update_custom_cart' )
*	add_filter( 'woocommerce_cart_shipping_packages', 'dms_set_cart_packages_with_custom_vars' )
*	add_filter( 'woocommerce_checkout_fields' , 'dms_custom_latlng_field' )
*	add_filter( 'woocommerce_customer_meta_fields', 'show_extra_profile_fields' )
*	add_filter( 'woocommerce_admin_shipping_fields', 'show_extra_order_customer_shipping_fields' )
*	add_filter( 'woocommerce_admin_billing_fields', 'show_extra_order_customer_billing_fields' )
*	add_filter( 'woocommerce_checkout_get_value', 'set_custom_value_to_latlng_field', 10, 2 )
*	add_filter( 'woocommerce_states', 'wc_sell_only_states' );
*	add_filter( 'default_checkout_country', 'change_default_checkout_country' );
*	add_filter( 'default_checkout_state', 'change_default_checkout_state' );
*	add_filter( 'woocommerce_json_search_found_products', 'filter_found_products_by_author' )
*	add_filter( 'woocommerce_json_search_customers_query', 'dms_find_customer_by_phone' )
*	add_filter( 'woocommerce_found_customer_details', 'add_custom_fields_to_woocommerce_customer_details', 10, 3 )
*	add_filter( 'woocommerce_billing_fields', 'make_woocommerce_fields_required', 10, 1 )
*	add_filter( 'woocommerce_email_recipient_cancelled_order', 'wc_cancelled_order_add_customer_email', 10, 2 )
*	add_filter( 'woocommerce_get_shop_page_permalink', 'edit_wc_shop_link_to_restaurants' )
*	add_filter( 'wc_order_is_editable', 'filter_wc_order_is_editable', 10, 2 )
*	add_filter( 'wc_product_dropdown_categories_get_terms_args', 'limit_plates_categories_by_restaurant' )
* 	add_filter( 'woocommerce_package_rates', 'limit_local_pickup_to_restaurants', 10, 2 )
*
*	add_action( 'woocommerce_checkout_update_order_meta', 'dms_checkout_update_order_meta' )
*	add_action( 'woocommerce_checkout_update_user_meta', 'dms_checkout_update_user_meta' );
*	add_action( 'woocommerce_created_customer', 'dms_checkout_update_user_meta' )
*	add_action( 'personal_options_update', 'dms_checkout_update_user_meta' )
*	add_action( 'edit_user_profile_update', 'dms_checkout_update_user_meta' )
*	add_action( 'woocommerce_before_order_notes', 'display_shipping_address' )
*	add_action( 'woocommerce_order_status_pending_to_on-hold_notification', 'mensatek_sms_set_message_parameters', 10 )
*	add_action( 'woocommerce_order_status_processing', 'mensatek_sms_set_message_parameters', 10 )
*	add_action( 'woocommerce_order_status_completed', 'mensatek_sms_set_message_parameters', 10 )
*	add_action( 'woocommerce_payment_complete', 'dms_autocomplete_paid_orders' )
*	add_action( 'woocommerce_before_checkout_process', 'check_restaurant_before_processing_order' )
*	add_action( 'woocommerce_before_checkout_form', 'check_restaurant_pre_checkout' )
*	add_action( 'restrict_manage_posts', 'show_custom_filter_input' )
*	add_action( 'woocommerce_cancelled_order', 'change_custom_status_if_customer_cancel' )
*
*/

// add_action( 'woocommerce_order_status_pending_to_on-hold_notification', 'mensatek_sms_set_message_parameters', 10 );
// add_action( 'woocommerce_order_status_processing', 'mensatek_sms_set_message_parameters', 10 );
// add_action( 'woocommerce_order_status_completed', 'mensatek_sms_set_message_parameters', 10 );

add_filter( 'woocommerce_gateway_title' , 'dms_qtranslate_items_name' );
add_filter( 'woocommerce_order_get_items' , 'dms_qtranslate_items_name' );

/* Update cart on new product ajax  */
// add_filter( 'woocommerce_add_to_cart_fragments', 'dms_ajax_update_custom_cart' );
function dms_ajax_update_custom_cart( $fragments ) {
	global $woocommerce;

	if ( ! defined('WOOCOMMERCE_CHECKOUT') ) {
		define( 'WOOCOMMERCE_CHECKOUT', true );
	}

	ob_start();

	dms_cart_part_updatable();

	$fragments['.cart-updatable'] = ob_get_clean();

	return $fragments;
}
/* END update cart */

/* Set custom vars during WC()->cart->get_shipping_packages() */
add_filter( 'woocommerce_cart_shipping_packages', 'dms_set_cart_packages_with_custom_vars' );
function dms_set_cart_packages_with_custom_vars( $packages ) {

	$packages[0]['destination']['latlng'] = WC()->customer->__get("shipping_latlng");
	$packages[0]['destination']['indications'] = WC()->customer->__get("shipping_indications");

	if( isset( WC()->session->preorder ) ) $packages[0]["preorder"] = WC()->session->preorder;

	return $packages;
}
/* END set packages */

/* Add custom field latlng to woocommerce checkout */
add_filter( 'woocommerce_checkout_fields' , 'dms_custom_latlng_field' );
function dms_custom_latlng_field( $fields ) {
    $fields['shipping']['shipping_latlng'] = array(
        'label'     => __('Latlng', 'woocommerce'),
	    'placeholder'   => _x('Latlng', 'placeholder', 'woocommerce'),
	    'required'  => false,
	    'class'     => array('form-row-wide hidden-custom-field'),
	    'clear'     => true
    );

    $fields['shipping']['shipping_indications'] = array(
        'label'     => __('Address indications', THEME_NAME),
	    'placeholder'   => _x('Landmarks...', 'placeholder', THEME_NAME),
	    'required'  => false,
	    'class'     => array('form-row-wide'),
	    'clear'     => true
    );

    unset($fields['billing']['billing_address_1']);
    unset($fields['billing']['billing_address_2']);
    unset($fields['billing']['billing_city']);
    unset($fields['billing']['billing_postcode']);
    unset($fields['billing']['billing_country']);
    unset($fields['billing']['billing_state']);
    unset($fields['billing']['billing_company']);
    unset($fields['billing']['billing_alt']);

    unset($fields['shipping']['shipping_first_name']);
    unset($fields['shipping']['shipping_last_name']);
    unset($fields['shipping']['shipping_company']);

    return $fields;
}

/* Save order meta on checkout */
add_action( 'woocommerce_checkout_update_order_meta', 'dms_checkout_update_order_meta' );
function dms_checkout_update_order_meta( $order_id ) {
	update_post_meta( $order_id, 'dms_order_status', "order_on_hold" );

    if ( ! empty( $_POST['shipping_latlng'] ) ) {
        update_post_meta( $order_id, 'shipping_latlng', sanitize_text_field( $_POST['shipping_latlng'] ) );
    }

    if ( ! empty( $_POST['shipping_indications'] ) ) {
        update_post_meta( $order_id, 'shipping_indications', sanitize_text_field( $_POST['shipping_indications'] ) );
    }

    if ( ! empty( $_POST['previous_order_time'] ) ) {
        update_post_meta( $order_id, 'dms_order_time_preorder', sanitize_text_field( $_POST['previous_order_time'] ) );
        update_post_meta( $order_id, 'dms_order_status', 'preorder' );
    }
}
/* END save meta */

/* Save user meta on checkout */
add_action( 'woocommerce_checkout_update_user_meta', 'dms_checkout_update_user_meta' );
add_action( 'woocommerce_created_customer', 'dms_checkout_update_user_meta' );
add_action( 'personal_options_update', 'dms_checkout_update_user_meta' );
add_action( 'edit_user_profile_update', 'dms_checkout_update_user_meta' );
function dms_checkout_update_user_meta( $user_id ) {

	if ( $user_id && $_POST['shipping_latlng'] ) update_user_meta( $user_id, 'shipping_latlng', esc_attr( $_POST['shipping_latlng'] ) );
	if ( $user_id && $_POST['shipping_indications'] ) update_user_meta( $user_id, 'shipping_indications', esc_attr( $_POST['shipping_indications'] ) );
}
/* END save meta */

add_filter( 'woocommerce_customer_meta_fields', 'show_extra_profile_fields' );
function show_extra_profile_fields( $fields ) {

	unset( $fields["billing"]["fields"]["billing_company"] );
	unset( $fields["billing"]["fields"]["billing_address_1"] );
	unset( $fields["billing"]["fields"]["billing_address_2"] );
	unset( $fields["billing"]["fields"]["billing_city"] );
	unset( $fields["billing"]["fields"]["billing_postcode"] );
	unset( $fields["billing"]["fields"]["billing_country"] );
	unset( $fields["billing"]["fields"]["billing_state"] );
	unset( $fields["shipping"]["fields"]["shipping_first_name"] );
	unset( $fields["shipping"]["fields"]["shipping_last_name"] );
	unset( $fields["shipping"]["fields"]["shipping_company"] );

	$fields["billing"]["fields"]["billing_gender"] = array(
		'label' 		=> __( "Sexo", THEME_NAME ),
		'description' 	=> '',
		'type'        => 'select',
		'options'     => array( '' => __( 'Selecciona sexo', 'woocommerce' ), 'Hombre' => __( 'Hombre', THEME_NAME ), 'Mujer' => __( "Mujer", THEME_NAME ) ) ,
	);

	$fields["billing"]["fields"]["billing_age"] = array(
		'label' 		=> __( "Fecha nacimiento", THEME_NAME ),
		'description' 	=> '',
	);

	$fields["shipping"]["fields"]["shipping_latlng"] = array(
		'label' 		=> __( "Latitud/Longitud", THEME_NAME ),
		'description' 	=> '',
	);

	$fields["shipping"]["fields"]["shipping_indications"] = array(
		'label' 		=> __( "Indicaciones para llegar", THEME_NAME ),
		'description' 	=> '',
	);

	$customer_id 	= $_GET["user_id"];
	$otherAddrs 	= get_user_meta( $customer_id, 'wc_multiple_shipping_addresses', true );
	$addresses    	= array();

	if( sizeof( $otherAddrs ) <= 1 ){
		$addresses[0] 	= __( 'Edita la dirección o añade una nueva', THEME_NAME );
	}else{
		$addresses[0] 	= __( 'Selecciona otra dirección o añade una nueva', THEME_NAME );
	}

	if( !empty( $otherAddrs ) ){
		for ( $i = 1; $i <= count( $otherAddrs ); ++$i ) {

			if (!empty($otherAddrs[$i - 1]['label'])) {
	            $addresses[ $i ] = $otherAddrs[$i - 1]['label'] . ' ' . $otherAddrs[ $i - 1 ]['shipping_postcode'];
	        } else {
	            $addresses[ $i ] = $otherAddrs[ $i - 1 ]['shipping_address_1'] . ' ' . $otherAddrs[ $i - 1 ]['shipping_address_2'] . ', ' . $otherAddrs[ $i - 1 ]['shipping_postcode'] . ' ' . $otherAddrs[ $i - 1 ]['shipping_city'];
	        }
		}
	}

	$addresses[ "new" ] = __( 'Añadir nueva dirección', THEME_NAME );
	$alt_fields["shipping_alt"] = array(
		'label'    => __( 'Direcciones guardadas', THEME_NAME ),
		'description' 	=> 'Para editar una dirección concreta, debes seleccionarla en el desplegable y presionar el botón "Guardar dirección seleccionada".<br> Para añadir una nueva dirección, debes seleccionar la opción "Añadir nueva dirección" y presionar el botón "Guardar nueva dirección".<br> Para guardar los datos de facturación, debes presionar el botón "Guardar datos" o "Actualizar usuario".',
		'type'     => 'select',
		'options'  => $addresses,
	);

	$fields["shipping"]["fields"] = $alt_fields + $fields["shipping"]["fields"];

	return $fields;
}

add_filter( 'woocommerce_admin_shipping_fields', 'show_extra_order_customer_shipping_fields' );
function show_extra_order_customer_shipping_fields( $fields ) {

	unset( $fields["first_name"] );
	unset( $fields["last_name"] );
	unset( $fields["company"] );

	$fields["indications"] = array(
		'label' 	=> __( "Indicaciones para llegar", THEME_NAME ),
		'show' 		=> true,
	);

	$fields["latlng"] = array(
		'label' 	=> __( "Latitud/Longitud", THEME_NAME ),
		'show' 		=> true,
	);

	if( isset( $_GET["cloned_order"] ) ){
		$order_id = ( isset( $_GET["cloned_order"] ) ) ? $_GET["cloned_order"] : false;

		foreach ( $fields as $key => $array ) {
			$field_value = get_post_meta( $order_id, '_shipping_' . $key, true );

			$fields[$key]["value"] = $field_value;
		}
	}

	global $post;

	$order_id 		= $post->ID;
	$order 			= wc_get_order();
	$customer_id 	= $order->customer_user;
	$otherAddrs 	= get_user_meta( $customer_id, 'wc_multiple_shipping_addresses', true );

	$addresses    	= array();

	if( sizeof( $otherAddrs ) <= 1 ){
		$addresses[0] 	= __( 'Edita la dirección o añade una nueva', THEME_NAME );
	}else{
		$addresses[0] 	= __( 'Selecciona otra dirección o añade una nueva', THEME_NAME );
	}

	if( !empty( $otherAddrs ) ){
		for ( $i = 1; $i <= count( $otherAddrs ); ++$i ) {

			if (!empty($otherAddrs[$i - 1]['label'])) {
	            $addresses[ $i ] = $otherAddrs[$i - 1]['label'] . ' ' . $otherAddrs[ $i - 1 ]['shipping_postcode'];
	        } else {
	            $addresses[ $i ] = $otherAddrs[ $i - 1 ]['shipping_address_1'] . ' ' . $otherAddrs[ $i - 1 ]['shipping_address_2'] . ', ' . $otherAddrs[ $i - 1 ]['shipping_postcode'] . ' ' . $otherAddrs[ $i - 1 ]['shipping_city'];
	        }
		}
	}

	$addresses[ "new" ] = __( 'Añadir nueva dirección', THEME_NAME );

	$alt_fields = array();
	$alt_fields["alt"] = array(
		'label'    => __( 'Direcciones guardadas', THEME_NAME ),
		'required' => false,
		'class'    => "select",
		'id' 		=> "_shipping_alt",
		'name' 		=> "_shipping_alt",
		'clear'    => true,
		'type'     => 'select',
		'options'  => $addresses,
		'wrapper_class' => "form-field form-field-wide"
	);

	$fields = $alt_fields + $fields;

	return $fields;
}

add_filter( 'woocommerce_admin_billing_fields', 'show_extra_order_customer_billing_fields' );
function show_extra_order_customer_billing_fields( $fields ) {

	unset( $fields["company"] );
	unset( $fields["address_1"] );
	unset( $fields["address_2"] );
	unset( $fields["city"] );
	unset( $fields["postcode"] );
	unset( $fields["state"] );
	unset( $fields["country"] );

	$fields["gender"] = array(
		'label' 	=> __( "Sexo", THEME_NAME ),
		'show' 		=> true,
		'class'   => 'select short',
		'type'      => 'select',
		'options'   => array( '' => __( 'Selecciona sexo', 'woocommerce' ), 'Hombre' => __( 'Hombre', THEME_NAME ), 'Mujer' => __( "Mujer", THEME_NAME ) ) ,
	);

	$fields["age"] = array(
		'label' 	=> __( "Fecha nacimiento", THEME_NAME ),
		'show' 		=> true,
	);

	if( isset( $_GET["cloned_order"] ) ){
		$order_id = ( isset( $_GET["cloned_order"] ) ) ? $_GET["cloned_order"] : false;

		foreach ( $fields as $key => $array ) {
			$field_value = get_post_meta( $order_id, '_billing_' . $key, true );

			$fields[$key]["value"] = $field_value;
		}
	}

	return $fields;
}

/* Set value of latlng custom field */
add_filter( 'woocommerce_checkout_get_value', 'set_custom_value_to_latlng_field', 10, 2 );
function set_custom_value_to_latlng_field( $value, $input ) {

    $shipping_fields = array(
        'shipping_latlng',
        'shipping_indications'
    );

    if ( in_array( $input, $shipping_fields ) ) {

		if ( ! empty( $_POST[ $input ] ) ) {

	        return wc_clean( $_POST[ $input ] );

	    } else {

	        // Get the billing_ and shipping_ address fields
	        if ( isset( WC()->checkout->checkout_fields['shipping'] ) && isset( WC()->checkout->checkout_fields['billing'] ) ) {

	            $address_fields = array_merge( WC()->checkout->checkout_fields['billing'], WC()->checkout->checkout_fields['shipping'] );

	            if ( is_user_logged_in() && is_array( $address_fields ) && array_key_exists( $input, $address_fields ) ) {
	                $current_user = wp_get_current_user();

	                if ( $meta = get_user_meta( $current_user->ID, $input, true ) ) {
	                    return $meta;
	                }

	                if ( $input == 'billing_email' ) {
	                    return $current_user->user_email;
	                }
	            }

	        }
	    }
    }
}
/* END set value */

/* Add custom block to checkout page */
add_action( 'woocommerce_before_order_notes', 'display_shipping_address' );
function display_shipping_address( $checkout ) {

	$shipping_address_1 		= ( isset( $_POST['shipping_address_1'] ) ) ? $_POST['shipping_address_1'] : WC()->customer->get_shipping_address();
	$shipping_address_2 		= ( isset( $_POST['shipping_address_2'] ) ) ? $_POST['shipping_address_2'] : WC()->customer->get_shipping_address_2();
	$shipping_city 				= ( isset( $_POST['shipping_city'] ) ) ? $_POST['shipping_city'] : WC()->customer->get_shipping_city();
	$shipping_state 			= ( isset( $_POST['shipping_state'] ) ) ? $_POST['shipping_state'] : WC()->customer->get_default_state();
	$shipping_postcode 			= ( isset( $_POST['shipping_postcode'] ) ) ? $_POST['shipping_postcode'] : WC()->customer->get_shipping_postcode();
	$shipping_country 			= ( isset( $_POST['shipping_country'] ) ) ? $_POST['shipping_country'] : WC()->customer->get_default_country();
	$shipping_latlng 			= ( isset( $_POST['shipping_latlng'] ) ) ? $_POST['shipping_latlng'] : WC()->customer->__get("shipping_latlng");
	$shipping_indications 		= ( isset( $_POST['shipping_indications'] ) ) ? $_POST['shipping_indications'] : WC()->customer->__get("shipping_indications");
	$set_preorder 				= ( isset( $_POST['make_previous_order'] ) ) ? $_POST['make_previous_order'] : 0;
	$preorder_time 				= ( isset( $_POST['previous_order_time'] ) ) ? $_POST['previous_order_time'] : WC()->session->preorder;

	?>
	<h3 id="order_review_heading"><?php _e( 'Shipping address', THEME_NAME ); ?></h3>
	<ul>
		<li><?php echo __("Address", 'woocommerce'). ": " . $shipping_address_1 . " " . $shipping_address_2; ?></li>
	    <li><?php echo __("City", 'woocommerce'). ": " . $shipping_city; ?></li>
	    <li><?php echo __("Postcode", 'woocommerce'). ": " . $shipping_postcode; ?></li>
	    <li><?php echo __("Address indications", THEME_NAME). ": " . $shipping_indications; ?></li>
	</ul>
    <?php

    if( $set_preorder != 0 ){
    	$formated_time = date( "H:i", strtotime( $preorder_time ) );
	    ?>
		<h3 id="order_review_heading"><?php _e( 'Pre-order', THEME_NAME ); ?></h3>
		<p><?php echo sprintf( __( "Preorder time %s", THEME_NAME ), $formated_time ); ?></p>
		<input type="hidden" class="input-text" name="previous_order_time" id="previous_order_time" value="<?php echo $preorder_time; ?>" />
		<?php
	}
}
/* END custom checkout */

/* Limit shipping to Baleares */
add_filter( 'woocommerce_states', 'wc_sell_only_states' );
add_filter( 'default_checkout_country', 'change_default_checkout_country' );
add_filter( 'default_checkout_state', 'change_default_checkout_state' );
function wc_sell_only_states( $states ) {
	$states['ES'] = array(
		'PM' => __( 'Baleares', 'woocommerce' ),
	);
	return $states;
}
function change_default_checkout_country() {
  return 'ES';
}
function change_default_checkout_state() {
  return 'PM';
}
/* END Limit shipping */

/* Show products in order: filter by author */
add_filter( 'woocommerce_json_search_found_products', 'filter_found_products_by_author' );
function filter_found_products_by_author( $products ) {

	$user = wp_get_current_user();

	foreach ( $products as $post_id => $product ) {
		$post = get_post( $post_id );
     	$post_author_id = $post->post_author;
     	$post_parent_id = $post->post_parent;

     	if( $post_parent_id > 0 ){
     		$parent_post = get_post( $post_parent_id );
     		$parent_post_author_id = $parent_post->post_author;
     		$post_author_id = $parent_post_author_id;
     	}

     	if( in_array( "restaurante", $user->roles ) ){

     		if( $user->id == $post_author_id ){
     			$new_products[ $post_id ] = $product;
     		}
     	}else{
     		$new_product = substr_replace( $product, "%_" . $post_author_id . "_% &ndash; ", 0, 0 );
     		$new_products[ $post_id ] = $new_product;
     	}
	}

	return $new_products;
}
/* END filter products */


/* Search customer by phone number */
add_filter( 'woocommerce_json_search_customers_query', 'dms_find_customer_by_phone' );
function dms_find_customer_by_phone( $found_customers ) {

	$search_term = str_replace( "*", "", $found_customers["search"] );

	if( is_numeric( $search_term ) ){

		$found_customers['meta_query'] = array(
	        array(
	            'key' 		=> 'billing_phone',
	            'value' 	=> $search_term,
	            'compare' 	=> 'LIKE'
	        ),
	    );

	    unset( $found_customers["search"] );
	    unset( $found_customers["search_columns"] );
	}

    return $found_customers;
}
/* END search */

/* Add custom field to woocommerce ajax request */
add_filter( 'woocommerce_found_customer_details', 'add_custom_fields_to_woocommerce_customer_details', 10, 3 );
function add_custom_fields_to_woocommerce_customer_details( $customer_data, $user_id, $type_to_load ) {

	if( $type_to_load == "shipping" ){
		$customer_data[ $type_to_load . '_latlng' ] 		= get_user_meta( $user_id, $type_to_load . '_latlng', true );
		$customer_data[ $type_to_load . '_indications' ] 	= get_user_meta( $user_id, $type_to_load . '_indications', true );
	}

	if( $type_to_load == "billing" ){
		$customer_data[ $type_to_load . '_gender' ] = get_user_meta( $user_id, $type_to_load . '_gender', true );
		$customer_data[ $type_to_load . '_age' ] 	= get_user_meta( $user_id, $type_to_load . '_age', true );
	}

	return $customer_data;
}
/* END add to ajax */

/* Auto complete order after succefull payment */
add_action( 'woocommerce_payment_complete', 'dms_autocomplete_paid_orders' );
function dms_autocomplete_paid_orders( $order_id )
{
	$order = new WC_Order( $order_id );

	if ( $order_status == 'processing' && ( $order->status == 'on-hold' || $order->status == 'pending' || $order->status == 'failed' ) ) {
		$order->update_status('completed');
	}
}
/* END auto complete */

/* Edit woocommerce billing fields  */
add_filter( 'woocommerce_billing_fields', 'make_woocommerce_fields_required', 10, 1 );
function make_woocommerce_fields_required( $address_fields ) {
	$address_fields['billing_phone']['required'] = true;

	return $address_fields;
}
/* END edit fields */

/* Send cancelation email to customer */
add_filter( 'woocommerce_email_recipient_cancelled_order', 'wc_cancelled_order_add_customer_email', 10, 2 );
function wc_cancelled_order_add_customer_email( $recipient, $order ){
   return $recipient . ',' . $order->billing_email;
}
/* END send cancelation */

/* Check if cart products restaurant is active and not busy before processing checkout */
add_action( 'woocommerce_before_checkout_process', 'check_restaurant_before_processing_order' );
function check_restaurant_before_processing_order() {
	$cart 			= WC()->cart->get_cart();
	$cart_product 	= array_values( $cart )[0]["data"];
	$restaurant_id 	= $cart_product->post->post_author;

	$current_restaurant_active = get_field( "dms_restaurant_active", "user_" . $restaurant_id );
	if( !$current_restaurant_active ){
		WC()->cart->empty_cart();
		throw new Exception( sprintf( __( 'This restaurant is not available. Please, choose another restaurant. <a href="%s" class="wc-backward">Return to restaurants</a>', THEME_NAME ), esc_url( wc_get_page_permalink( 'shop' ) ) ) );
	}

	$current_restaurant_busy = get_field( "dms_restaurant_busy", "user_" . $restaurant_id );
	if( $current_restaurant_busy ){
		WC()->cart->empty_cart();
		throw new Exception( sprintf( __( "At the moment this restaurant if too busy and can't accept more orders. Please, choose another restaurant or try again later. <a href='%s' class='wc-backward'>Return to restaurants</a>", THEME_NAME ), esc_url( wc_get_page_permalink( 'shop' ) ) ) );
	}
}
add_action( 'woocommerce_before_checkout_form', 'check_restaurant_pre_checkout' );
function check_restaurant_pre_checkout() {
	$cart 			= WC()->cart->get_cart();
	$cart_product 	= array_values( $cart )[0]["data"];
	$restaurant_id 	= $cart_product->post->post_author;

	$current_restaurant_active = get_field( "dms_restaurant_active", "user_" . $restaurant_id );
	if( !$current_restaurant_active ){
		WC()->cart->empty_cart();
		// wc_add_notice( sprintf( __( 'This restaurant is not available. Please, choose another restaurant. <a href="%s" class="wc-backward">Return to restaurants</a>', THEME_NAME ), esc_url( wc_get_page_permalink( 'shop' ) ) ) );
		return;
	}

	$current_restaurant_busy = get_field( "dms_restaurant_busy", "user_" . $restaurant_id );
	if( $current_restaurant_busy ){
		WC()->cart->empty_cart();
		// wc_add_notice( sprintf( __( "At the moment this restaurant if too busy and can't accept more orders. Please, choose another restaurant or try again later. <a href='%s' class='wc-backward'>Return to restaurants</a>", THEME_NAME ), esc_url( wc_get_page_permalink( 'shop' ) ) ) );
		return;
	}
}
/* END check restaurant in checkout */

/* Change woocommerce default shop url to restaurants list */
add_filter( 'woocommerce_get_shop_page_permalink', 'edit_wc_shop_link_to_restaurants' );
function edit_wc_shop_link_to_restaurants( $permalink ){
   return get_permalink( 109 );
}
/* END change url */

/* Make order editable on cloning */
add_filter( 'wc_order_is_editable', 'filter_wc_order_is_editable', 10, 2 );
function filter_wc_order_is_editable( $in_array, $order ){
    $in_array = ( isset( $_GET["cloned_order"] ) ) ? true : $in_array;

    if( $order->payment_method == 'cod' ) $in_array = true;

    return $in_array;
};
/* END editable */

/* Filter products list by restaurants */
add_action( 'restrict_manage_posts', 'show_custom_filter_input' );
function show_custom_filter_input() {
	global $typenow;

	if (  current_user_can( 'manage_options' ) && 'product' == $typenow ) {
		plate_by_restaurant_filter_field();
	}
}

function plate_by_restaurant_filter_field() {
	global $wp_query;

	$users   = get_users( array( 'role'=> 'restaurante' ) );
	$output  = '<select name="restaurant_id" id="dropdown_plate_restaurant">';
	$output .= '<option value="">' . __( 'Todos Restaurantes', THEME_NAME ) . '</option>';
	foreach ( $users as $user ) {
		$output .= '<option value="' . sanitize_title( $user->ID ) . '" ';
		if ( isset( $wp_query->query_vars['author'] ) ) {
			$output .= selected( $user->ID, $wp_query->query_vars['author'], false );
		}
		$output .= '>';
		$output .= ucfirst( $user->display_name );
		$output .= '</option>';
	}
	$output .= '</select>';
	echo  $output;
}

add_filter( 'parse_query', 'filter_plates_by_restaurant' );
function filter_plates_by_restaurant( $query ) {
	global $typenow, $wp_query;

	if ( 'product' == $typenow ) {

		if ( isset( $_GET['restaurant_id'] ) ) {
			$query->query_vars['author'] = $_GET['restaurant_id'];
		}
	}
}

add_filter( 'wc_product_dropdown_categories_get_terms_args', 'limit_plates_categories_by_restaurant' );
function limit_plates_categories_by_restaurant( $args ) {
	global $typenow;

	if ( 'product' == $typenow ) {

		if ( current_user_can('manage_options') ){
			if ( isset( $_GET['restaurant_id'] ) ) {
				$author_id = $_GET['restaurant_id'];
			}
		}else{
			$author_id = get_current_user_id();
		}

		if( $author_id ){
			$args['meta_query'] = array(
		         array(
		            'key'       => 'term_author',
		            'value'     => $author_id,
		         )
		    );
		}
	}

	return $args;
}
/* END filter products */

/* Change custom order status if customer cancels order from his profile page */
add_action( 'woocommerce_cancelled_order', 'change_custom_status_if_customer_cancel' );
function change_custom_status_if_customer_cancel( $order_id ){

	$order = wc_get_order( $order_id );
	$payment_method = $order->payment_method;
    $new_status = "order_cancelled";

    if( $payment_method == "redsys" ){
    	$new_status = "cancelled_return_money";
    }

    update_post_meta( $order_id, "dms_order_status", $new_status );
    update_field( "field_580a0ea69b9cb", current_time( 'Y-m-d H:i:s' ), $order_id ); // cancelled_time
}
/* END change status */

/* Hide local_pickup shipping for certain restaurants */
add_filter( 'woocommerce_package_rates', 'limit_local_pickup_to_restaurants', 10, 2 );
function limit_local_pickup_to_restaurants(  $rates, $package ) {
	$restaurant_id 	= array_values( $package["contents"])[0]["data"]->post->post_author;
	$local 			= get_user_meta( $restaurant_id, "dms_restaurant_local", true );

	if( $local == 1 || $local == true ){
		unset( $rates["local_pickup"] );
	}

	return $rates;
}
/* END hide localpickup */
