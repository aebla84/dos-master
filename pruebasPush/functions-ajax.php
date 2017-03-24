<?php

/** # AJAX FUNCTIONS #
*  Included functions:
*
*       function create_restaurant_comment_page_callback()
*       function update_total_price_callback()
*       function select_shipping_method_callback()
*       function dms_add_to_cart_simple_callback()
*       function dms_add_to_cart_variable_callback()
*       function dms_remove_item_from_cart_callback()
*       function dms_set_customer_address_n_get_shipping_method_callback()
*       function filter_restaurants_callback()
*       function edit_user_profile_callback()
*       function dms_login_action_callback()
*       function dms_register_action_callback()
*       function dms_get_product_addons_inside_order_callback()
*       function dms_get_shipping_inside_order_callback()
*       function dms_address_validation_callback()
*       function dms_get_restaurant_products_in_order_callback()
*       function dms_create_new_customer_from_order_callback()
*       function dms_edit_customer_address_from_order_callback()
*       function dms_delete_customer_address_from_order_callback()
*       function dms_add_new_address_to_customer_from_order_callback()
*       function dms_add_multiple_address_selector_to_order_callback()
*       function dms_change_selected_address_callback()
*       function dms_change_order_driver_from_list_callback()
*       function dms_confirm_refund_from_list_callback()
*       function dms_confirm_correction_from_list_callback()
*       function dms_ajax_get_preorder_time_select_callback()
*		function download_order_receipts_callback()
*
*/

/* Select chipping method in minicart */
add_action('wp_ajax_create_restaurant_comment_page', 'create_restaurant_comment_page_callback');
add_action('wp_ajax_nopriv_create_restaurant_comment_page', 'create_restaurant_comment_page_callback');
function create_restaurant_comment_page_callback() {

	$restaurant_id = $_POST['restaurant_id'];

	if( $restaurant_id ){

		$restaurant_info = get_userdata( $restaurant_id );
		$restaurant_first_name = $restaurant_info->first_name;
		$restaurant_last_name = $restaurant_info->last_name;
		$restaurant_full_name = $restaurant_first_name . " " . $restaurant_last_name;
		$restaurant_page_title = sprintf( __( 'Comments about %s', THEME_NAME ), $restaurant_full_name );

		$comment_page  = array(
			'post_title'  		=> $restaurant_page_title,
			'post_type'      	=> 'page',
			'post_status'    	=> 'publish',
			'comment_status' 	=> 'open',
			'ping_status'    	=> 'closed',
			'post_author'    	=> $restaurant_id,
       	);

		$comments_page_id = wp_insert_post( $comment_page, FALSE );
	}

    $data = array(
		'page_id' => $comments_page_id,
	);

	echo json_encode( $data );

    exit;
}
/* END select method */

/* Update quantity of minicart */
add_action('wp_ajax_update_total_price', 'update_total_price_callback');
add_action('wp_ajax_nopriv_update_total_price', 'update_total_price_callback');
function update_total_price_callback() {
	global $woocommerce;

	if ( ! defined('WOOCOMMERCE_CART') ) {
		define( 'WOOCOMMERCE_CART', true );
	}

    if( !isset( $_POST['hash'] ) || !isset( $_POST['quantity'] ) ){
        exit;
    }

    $cart_item_key = $_POST['hash'];

    if( !isset( WC()->cart->get_cart()[ $cart_item_key ] ) ){
        exit;
    }

    $values = WC()->cart->get_cart()[ $cart_item_key ];

    $_product = $values['data'];

    $quantity = apply_filters( 'woocommerce_stock_amount_cart_item', apply_filters( 'woocommerce_stock_amount', preg_replace( "/[^0-9\.]/", '', filter_var($_POST['quantity'], FILTER_SANITIZE_NUMBER_INT)) ), $cart_item_key );

    if ( '' === $quantity || $quantity == $values['quantity'] )
        exit;

    $passed_validation  = apply_filters( 'woocommerce_update_cart_validation', true, $cart_item_key, $values, $quantity );

    if ( $_product->is_sold_individually() && $quantity > 1 ) {
        wc_add_notice( sprintf( __( 'You can only have 1 %s in your cart.', 'woocommerce' ), $_product->get_title() ), 'error' );
        $passed_validation = false;
    }

    if ( $passed_validation ) {
        WC()->cart->set_quantity( $cart_item_key, $quantity, false );
    }

    WC()->cart->calculate_totals();

    $packages = WC()->cart->get_shipping_packages();
    // $chosen_method = isset( WC()->session->chosen_shipping_methods[ 0 ] ) ? WC()->session->chosen_shipping_methods[ 0 ] : '';
    $chosen_method = 'distance_rate';
    $product_names = array();

    if ( sizeof( $packages ) > 1 ) {
        foreach ( $package['contents'] as $item_id => $values ) {
            $product_names[] = $values['data']->get_title() . ' &times;' . $values['quantity'];
        }
    }

    $package = WC()->shipping->calculate_shipping_for_package( $packages[0] );
    WC()->customer->calculated_shipping();

    ob_start();
	wc_get_template( 'cart/cart-shipping.php', array(
        'package'              => $package,
        'available_methods'    => $package['rates'],
        'show_package_details' => sizeof( $packages ) > 1,
        'package_details'      => implode( ', ', $product_names ),
        'package_name'         => apply_filters( 'woocommerce_shipping_package_name', sprintf( _n( 'Shipping', 'Shipping %d', ( 1 ), 'woocommerce' ), ( 1 ) ), 0, $package ),
        'index'                => 1,
        'chosen_method'        => $chosen_method
    ) );

    $shipping_methods = ob_get_clean();
    $shipping_messages = print_messages_for_calculated_shipping( $package );

    $data = array(
		'subtotal' 	=> WC()->cart->get_cart_subtotal(),
		'total' 	=> WC()->cart->get_total(),
		"html" 		=> $shipping_methods,
		'messages' 	=> $shipping_messages
	);

	echo json_encode( $data );

    exit;
}
/* END update quantity */


/* Select chipping method in minicart */
add_action('wp_ajax_select_shipping_method', 'select_shipping_method_callback');
add_action('wp_ajax_nopriv_select_shipping_method', 'select_shipping_method_callback');
function select_shipping_method_callback() {
	global $woocommerce;

	if ( ! defined('WOOCOMMERCE_CART') ) {
		define( 'WOOCOMMERCE_CART', true );
	}

	if( !isset( $_POST['method'] ) ){
        exit;
    }

    $selected_method = $_POST['method'];

    WC()->session->set( 'chosen_shipping_methods', array( $selected_method ) );

    WC()->cart->calculate_totals();

    $data = array(
		'total' => WC()->cart->get_total(),
		'method' => WC()->session->chosen_shipping_methods,
		'shipping' => WC()->cart->get_cart_shipping_total(),
		'cart' => json_encode( WC()->cart ),
	);

	echo json_encode( $data );

    exit;
}
/* END select method */

/* Simple ajax add to cart */
add_action( 'wp_ajax_dms_add_to_cart_simple', 'dms_add_to_cart_simple_callback' );
add_action( 'wp_ajax_nopriv_dms_add_to_cart_simple', 'dms_add_to_cart_simple_callback' );
function dms_add_to_cart_simple_callback() {
    // ob_start();

    $product_id = apply_filters( 'woocommerce_add_to_cart_product_id', absint( $_POST['product_id'] ) );
    $quantity = empty( $_POST['quantity'] ) ? 1 : apply_filters( 'woocommerce_stock_amount', $_POST['quantity'] );
    $passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity );
    $data = $_POST['addons'];

    dms_check_product_restaurant( $product_id );

    if ( $passed_validation && WC()->cart->add_to_cart( $product_id, $quantity, 0, array(), $data ) ) {
        do_action( 'woocommerce_ajax_added_to_cart', $product_id );

        // WC_AJAX::get_refreshed_fragments();
    }

    dms_cart_part_updatable();
    exit;
}
/* END simple ajax */

/* Variable ajax add to cart */
add_action( 'wp_ajax_dms_add_to_cart_variable', 'dms_add_to_cart_variable_callback' );
add_action( 'wp_ajax_nopriv_dms_add_to_cart_variable', 'dms_add_to_cart_variable_callback' );
function dms_add_to_cart_variable_callback() {
    // ob_start();

    $product_id = apply_filters( 'woocommerce_add_to_cart_product_id', absint( $_POST['product_id'] ) );
    $quantity = empty( $_POST['quantity'] ) ? 1 : apply_filters( 'woocommerce_stock_amount', $_POST['quantity'] );
    $variation_id = $_POST['variation_id'];
    $variation  = $_POST['variation'];
    $data = $_POST['addons'];
    $passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity );

    dms_check_product_restaurant( $product_id );

    if ( $passed_validation && WC()->cart->add_to_cart( $product_id, $quantity, $variation_id, $variation, $data  ) ) {
        do_action( 'woocommerce_ajax_added_to_cart', $product_id );

        // WC_AJAX::get_refreshed_fragments();
    }

    dms_cart_part_updatable();
    exit;
}
/* END variable ajax */

/* Ajax remove product from cart */
add_action( 'wp_ajax_dms_remove_item_from_cart', 'dms_remove_item_from_cart_callback' );
add_action( 'wp_ajax_nopriv_dms_remove_item_from_cart', 'dms_remove_item_from_cart_callback' );
function dms_remove_item_from_cart_callback() {
    // ob_start();

    $cart_item_key = $_POST['product_id'];

    if( $cart_item_key ){
        WC()->cart->remove_cart_item( $cart_item_key );

        // WC_AJAX::get_refreshed_fragments();
    }

    dms_cart_part_updatable();
    exit;
}
/* END ajax remove */

/* Calculate customer shipping */
add_action( 'wp_ajax_dms_set_customer_address_n_get_shipping_method', 'dms_set_customer_address_n_get_shipping_method_callback' );
add_action( 'wp_ajax_nopriv_dms_set_customer_address_n_get_shipping_method', 'dms_set_customer_address_n_get_shipping_method_callback' );
function dms_set_customer_address_n_get_shipping_method_callback() {
    global $woocommerce;

    if ( ! defined('WOOCOMMERCE_CHECKOUT') ) {
        define( 'WOOCOMMERCE_CHECKOUT', true );
    }

    $shipping_address_1         = ( isset( $_POST['shipping_address_1'] ) ) ? $_POST['shipping_address_1'] : "";
    $shipping_address_2         = ( isset( $_POST['shipping_address_2'] ) ) ? $_POST['shipping_address_2'] : "";
    $shipping_city              = ( isset( $_POST['shipping_city'] ) ) ? $_POST['shipping_city'] : "";
    $shipping_state             = ( isset( $_POST['shipping_state'] ) ) ? $_POST['shipping_state'] : "";
    $shipping_postcode          = ( isset( $_POST['shipping_postcode'] ) ) ? $_POST['shipping_postcode'] : "";
    $shipping_country           = ( isset( $_POST['shipping_country'] ) ) ? $_POST['shipping_country'] : "";
    $shipping_latlng            = ( isset( $_POST['shipping_latlng'] ) ) ? $_POST['shipping_latlng'] : "";
    $shipping_indications       = ( isset( $_POST['shipping_indications'] ) ) ? $_POST['shipping_indications'] : "";
    $preorder_time              = ( isset( $_POST['preorder_time'] ) ) ? $_POST['preorder_time'] : false;

    if( get_current_user_id() === 0 ){
        $woocommerce->customer->set_address( $shipping_address_1 );
        $woocommerce->customer->set_address_2( $shipping_address_2 );
        $woocommerce->customer->set_location( $shipping_country, $shipping_state, $shipping_postcode, $shipping_city );
    }

    $woocommerce->customer->set_shipping_address( $shipping_address_1 );
    $woocommerce->customer->set_shipping_address_2( $shipping_address_2 );
    $woocommerce->customer->set_shipping_location( $shipping_country, $shipping_state, $shipping_postcode, $shipping_city );
    $woocommerce->customer->__set( "shipping_latlng", $shipping_latlng );
    $woocommerce->customer->__set( "shipping_indications", $shipping_indications );

    if( $preorder_time ){
        WC()->session->set( 'preorder', $preorder_time );
    }else{
        unset( WC()->session->preorder );
    }

    $packages = WC()->cart->get_shipping_packages();

    foreach ( $packages as $i => $package ) {

        // $chosen_method = isset( WC()->session->chosen_shipping_methods[ $i ] ) ? WC()->session->chosen_shipping_methods[ $i ] : '';
        $chosen_method = 'distance_rate';
        $product_names = array();

        if ( sizeof( $packages ) > 1 ) {
            foreach ( $package['contents'] as $item_id => $values ) {
                $product_names[] = $values['data']->get_title() . ' &times;' . $values['quantity'];
            }
        }

        $package = WC()->shipping->calculate_shipping_for_package( $package );
        WC()->customer->calculated_shipping();

        ob_start();
        wc_get_template( 'cart/cart-shipping.php', array(
            'package'              => $package,
            'available_methods'    => $package['rates'],
            'show_package_details' => sizeof( $packages ) > 1,
            'package_details'      => implode( ', ', $product_names ),
            'package_name'         => apply_filters( 'woocommerce_shipping_package_name', sprintf( _n( 'Shipping', 'Shipping %d', ( $i + 1 ), 'woocommerce' ), ( $i + 1 ) ), $i, $package ),
            'index'                => $i,
            'chosen_method'        => $chosen_method
        ) );

        $shipping_methods = ob_get_clean();

        $shipping_messages = print_messages_for_calculated_shipping( $package );

        $data = array(
            "html"      => $shipping_methods,
            "messages"  => $shipping_messages,
            "package"   => $package
        );

        echo json_encode( $data );
    }

    exit;
}
/* END calculate shipping */

/* Filter restaurants  */
add_action( 'wp_ajax_filter_restaurants', 'filter_restaurants_callback' );
add_action( 'wp_ajax_nopriv_filter_restaurants', 'filter_restaurants_callback' );
function filter_restaurants_callback() {

    $order      = ( isset( $_POST['order'] ) ) ? $_POST['order'] : "";
    $type       = ( isset( $_POST['type'] ) ) ? urldecode( $_POST['type'] ) : "";
    $search     = ( isset( $_POST['search'] ) ) ? $_POST['search'] : "";

    $args = array(
        'role'          => 'restaurante',
        'number'        => -1,
        'meta_key'      => 'dms_restaurant_order',
        'orderby'       => array(
                                'meta_value_num'    => 'DESC',
                                'display_name'      => 'ASC',
                            ),
        'meta_query'    => array(
                                array(
                                    'key'     => 'dms_restaurant_active',
                                    'value'   => 1,
                                ),
                                array(
                                    'key'     => 'dms_restaurant_busy',
                                    'value'   => 0,
                                ),
                            ),
    );

    if( $order ){
        $args["orderby"] = 'meta_value';
        $args["order"] = $order;
        $args["meta_key"] = 'dms_restaurant_name';
    }

    if( $search ){
        $search_array = array(
            array(
                'key'     => 'dms_restaurant_name',
                'value'   => $search,
                'compare' => 'LIKE',
            ),
        );

        array_push($args["meta_query"], $search_array);
    }

    if( $type ){

        $type_array = array(
            'relation'  => 'OR',
            array(
                'key'     => 'dms_restaurant_types_0_dms_restaurant_type',
                'value'   => $type,
                'compare' => 'LIKE'
            ),
            array(
                'key'     => 'dms_restaurant_types_1_dms_restaurant_type',
                'value'   => $type,
                'compare' => 'LIKE'
            ),
            array(
                'key'     => 'dms_restaurant_types_2_dms_restaurant_type',
                'value'   => $type,
                'compare' => 'LIKE'
            ),
            array(
                'key'     => 'dms_restaurant_types_3_dms_restaurant_type',
                'value'   => $type,
                'compare' => 'LIKE'
            ),
            array(
                'key'     => 'dms_restaurant_types_4_dms_restaurant_type',
                'value'   => $type,
                'compare' => 'LIKE'
            ),
            array(
                'key'     => 'dms_restaurant_types_5_dms_restaurant_type',
                'value'   => $type,
                'compare' => 'LIKE'
            ),
        );

        array_push($args["meta_query"], $type_array);
    }

    $html = '';

    $restaurants = new WP_User_Query( $args );
    $restaurants_count = $restaurants->total_users;

    $rest_array = get_restaurant_data_to_print_list( $restaurants );
    ksort( $rest_array, SORT_NATURAL | SORT_FLAG_CASE );

    if ( ! empty( $rest_array ) ) {
        foreach ( $rest_array as $restaurant ) {

            $restaurant_redirect        = $restaurant["restaurant_redirect"];
            $restaurant_logo            = $restaurant["restaurant_logo"];
            $restaurant_name            = $restaurant["restaurant_name"];
            $restaurant_type_string     = $restaurant["restaurant_type_string"];
            $restaurant_address         = $restaurant["restaurant_address"];
            $restaurant_closed          = $restaurant["restaurant_closed"];

            $html .= '  <div class="single-restaurant">
                            <div class="column-image">
                                <a href="' . $restaurant_redirect . '"><img class="restaurant-logo" src="' . $restaurant_logo . '"></a>
                            </div>
                            <div class="column-text">
                                <a href="' . $restaurant_redirect . '"><h2 class="restaurant-name">' . $restaurant_name . '</h2></a>
                                <div class="restaurant-types"><i class="fa fa-cutlery" aria-hidden="true"></i><span>' . $restaurant_type_string . '</span></div>
                                <div class="restaurant-address"><i class="fa fa-map-o" aria-hidden="true"></i><span>' . $restaurant_address . '</span></div>';

            if( $restaurant_closed ){
                $html .= '      <div class="restaurant-schedule"><i class="fa fa-clock-o" aria-hidden="true"></i><span>' . $restaurant_closed . '</span></div>';
            }

            $html .= '          </div>
                            <div class="column-action">
                                <a class="button-default restaurant-redirect" href="' . $restaurant_redirect . '">' . __("See menu", THEME_NAME) . '</a>
                            </div>
                        </div>';

            $restaurant_closed = false;
        }
    } else {
        $html .= __( 'No restaurants found.', THEME_NAME );
    }

    $data = array(
        "html" => $html,
        "qty" => sprintf( esc_html__( 'Watching %s restaurants', THEME_NAME ), $restaurants_count )
    );

    echo json_encode( $data );

    exit;
}
/* END filter */

/* Edit user profile */
add_action( 'wp_ajax_edit_user_profile', 'edit_user_profile_callback' );
add_action( 'wp_ajax_nopriv_edit_user_profile', 'edit_user_profile_callback' );
function edit_user_profile_callback() {

    $user = wp_get_current_user();
    $data = array();

    $data["all_ok"] = true;

    if( $user != 0 ){

        $user_id = $user->ID;

        $current_account_password   = ( isset( $_POST['current_account_password'] ) ) ? sanitize_text_field( $_POST['current_account_password'] ) : "";
        $billing_first_name         = ( isset( $_POST['billing_first_name'] ) ) ? sanitize_text_field( $_POST['billing_first_name'] ) : "";
        $billing_last_name          = ( isset( $_POST['billing_last_name'] ) ) ? sanitize_text_field( $_POST['billing_last_name'] ) : "";
        $billing_email              = ( isset( $_POST['billing_email'] ) ) ? sanitize_text_field( $_POST['billing_email'] ) : "";
        $billing_phone              = ( isset( $_POST['billing_phone'] ) ) ? sanitize_text_field( $_POST['billing_phone'] ) : "";
        $account_password           = ( isset( $_POST['account_password'] ) ) ? sanitize_text_field( $_POST['account_password'] ) : "";
        $repeate_account_password   = ( isset( $_POST['repeate_account_password'] ) ) ? sanitize_text_field( $_POST['repeate_account_password'] ) : "";

        if( $current_account_password ){

            if( wp_check_password( $current_account_password, $user->data->user_pass, $user_id ) ){

                if( $billing_first_name ){
                    $billing_first_name_old = get_user_meta( $user_id, "billing_first_name", true );

                    update_user_meta( $user_id, "billing_first_name", $billing_first_name, $billing_first_name_old );
                }

                if( $billing_last_name ){
                    $billing_last_name_old = get_user_meta( $user_id, "billing_last_name", true );

                    update_user_meta( $user_id, "billing_last_name", $billing_last_name, $billing_last_name_old );
                }

                if( $billing_email ){
                    $billing_email_old = get_user_meta( $user_id, "billing_email", true );

                    update_user_meta( $user_id, "billing_email", $billing_email, $billing_email_old );
                }

                if( $billing_phone ){
                    $billing_phone_old = get_user_meta( $user_id, "billing_phone", true );

                    update_user_meta( $user_id, "billing_phone", $billing_phone, $billing_phone_old );
                }

                if( $account_password ){

                    if( $repeate_account_password ){

                        if( $repeate_account_password == $account_password ){

                            wp_set_password( $account_password, $user_id );
                        }else{
                            // echo "ERROR: DIFFERENT PASSWORDS";
                            $data["error_diff"] = __("Passwords don't match.", THEME_NAME);
                            $data["all_ok"] = false;
                        }
                    }else{
                        // echo "ERROR: MISSING REPEAT PASSWORD";
                        $data["error_miss_rep"] = __("Repeat new password is a required field.", THEME_NAME);
                        $data["all_ok"] = false;
                    }
                }
            }else{
                // echo "ERROR: WRONG PASSWORD";
                $data["error_wrong_pass"] = __("Incorrect password. Please, try again.", THEME_NAME);
                $data["all_ok"] = false;
            }
        }else{
            // echo "ERROR: MISSING CURRENT PASSWORD";
            $data["error_miss_pass"] = __("Current password is a required field. To update your personal information you need to input your account password.", THEME_NAME);
            $data["all_ok"] = false;
        }
    }else{
        // echo "ERROR: USER NOT LOGGED";
        $data["error_user"] = __("User is not logged in.", THEME_NAME);
        $data["all_ok"] = false;
    }

    if( $data["all_ok"] ) $data["ok_message"] = __("Your data has been succesfully updated.", THEME_NAME);

    echo json_encode( $data );

    exit;
}
/* END edit profile */

/* Custom login action */
add_action( 'wp_ajax_dms_login_action', 'dms_login_action_callback' );
add_action( 'wp_ajax_nopriv_dms_login_action', 'dms_login_action_callback' );
function dms_login_action_callback() {
    $username           = (!isset($_POST['username'])) ? '' : $_POST['username'];
    $account_password   = (!isset($_POST['account_password'])) ? '' : $_POST['account_password'];

    $data = array();

    $data["all_ok"] = true;

    if( !$username ){
        // echo "ERROR: MISSING USERNAME";
        $data["error_miss_user"] = __("Username is a required field.", THEME_NAME);
        $data["all_ok"] = false;
    }

    if( !$account_password ){
        // echo "ERROR: MISSING PASSWORD";
        $data["error_miss_pass"] = __("Password is a required field.", THEME_NAME);
        $data["all_ok"] = false;
    }

    if( $data["all_ok"] ){

        $user = get_user_by( 'login', $username );

        if( !empty( $user ) ){

            $check = wp_check_password( $account_password, $user->data->user_pass, $user->ID );

            if( $check ){
                $access = array();
                $access['user_login'] = $username;
                $access['user_password'] = $account_password;
                wp_signon( $access );
            }else{
                $data["error_diff"] = __("Invalid login or password.", THEME_NAME);
                $data["all_ok"] = false;
            }
        }else{
            $data["error_diff"] = __("Invalid login or password.", THEME_NAME);
            $data["all_ok"] = false;
        }
    }

    if( $data["all_ok"] ) $data["ok_message"] = __("Login successfull", THEME_NAME);

    echo json_encode( $data );

    exit;
}
/* END login */

/* Custom login action */
add_action( 'wp_ajax_dms_register_action', 'dms_register_action_callback' );
add_action( 'wp_ajax_nopriv_dms_register_action', 'dms_register_action_callback' );
function dms_register_action_callback() {
    $username                   = (!isset($_POST['username'])) ? '' : $_POST['username'];
    $billing_email              = ( isset( $_POST['billing_email'] ) ) ? sanitize_text_field( $_POST['billing_email'] ) : "";
    $account_password           = ( isset( $_POST['account_password'] ) ) ? sanitize_text_field( $_POST['account_password'] ) : "";
    $repeate_account_password   = ( isset( $_POST['repeate_account_password'] ) ) ? sanitize_text_field( $_POST['repeate_account_password'] ) : "";

    $data = array();

    $data["all_ok"] = true;

    if( !$username ){
        // echo "ERROR: MISSING USERNAME";
        $data["error_miss_user"] = __("Username is a required field.", THEME_NAME);
        $data["all_ok"] = false;
    }

    if( !$billing_email ){
        // echo "ERROR: MISSING EMAIL";
        $data["error_miss_email"] = __("Email is a required field.", THEME_NAME);
        $data["all_ok"] = false;
    }else{

        if( !is_email( $billing_email ) ){
            // echo "ERROR: NOT EMAIL";
            $data["error_not_email"] = __("Email is invalid.", THEME_NAME);
            $data["all_ok"] = false;
        }
    }

    if( !$account_password ){
        // echo "ERROR: MISSING PASSWORD";
        $data["error_miss_pass"] = __("Password is a required field.", THEME_NAME);
        $data["all_ok"] = false;
    }

    if( !$repeate_account_password ){
        // echo "ERROR: MISSING REPEAT PASSWORD";
        $data["error_miss_rep"] = __("Repeat password is a required field.", THEME_NAME);
        $data["all_ok"] = false;
    }

    if( $data["all_ok"] ){

        $user_exists = get_user_by( 'login', $username );

        if( !$user_exists ){

            $email_exists = get_user_by( 'email', $billing_email );

            if( !$email_exists ){
                if( $repeate_account_password == $account_password ){

                    $userdata = array(
                        'user_login'    => $username,
                        'user_pass'     => $account_password,
                        'user_email'    => $billing_email,
                        'role'          => 'customer',
                    );

                    wp_insert_user( $userdata );

                    $new_user = get_user_by( 'login', $username );
                    if( $new_user ){
                        $user_id = $new_user->ID;

                        update_user_meta( $user_id, 'billing_email', $billing_email );
                    }else{
                        // echo "ERROR: REGISTER";
                        $data["error_register"] = __("An error ocurred during registration. Please try again later.", THEME_NAME);
                        $data["all_ok"] = false;
                    }
                }else{
                    // echo "ERROR: DIFFERENT PASSWORDS";
                    $data["error_diff"] = __("Passwords don't match. Please try again.", THEME_NAME);
                    $data["all_ok"] = false;
                }
            }else{
                // echo "ERROR: EMAIL EXISTS";
                $data["error_email"] = __("Selected email already exists. Please choose another email.", THEME_NAME);
                $data["all_ok"] = false;
            }
        }else{
            // echo "ERROR: USERNAME EXISTS";
            $data["error_user"] = __("Selected username already exists. Please choose another username.", THEME_NAME);
            $data["all_ok"] = false;
        }
    }

    if( $data["all_ok"] ) $data["ok_message"] = __("Registration successfull", THEME_NAME);

    echo json_encode( $data );

    exit;
}
/* END login */

/* Get product addons for new order */
add_action( 'wp_ajax_dms_get_product_addons_inside_order', 'dms_get_product_addons_inside_order_callback' );
add_action( 'wp_ajax_nopriv_dms_get_product_addons_inside_order', 'dms_get_product_addons_inside_order_callback' );
function dms_get_product_addons_inside_order_callback() {
    $product_id = ( isset( $_POST["product_id"] ) ) ? $_POST["product_id"] : false;

    if( $product_id ){
        $product_data = get_product_addons( $product_id, false );

        if( empty( $product_data ) ){
            return;
        }

        echo  json_encode( $product_data );
    }

    exit;
}
/* END get addons */

/* Get shipping cost for new order */
add_action( 'wp_ajax_dms_get_shipping_inside_order', 'dms_get_shipping_inside_order_callback' );
add_action( 'wp_ajax_nopriv_dms_get_shipping_inside_order', 'dms_get_shipping_inside_order_callback' );
function dms_get_shipping_inside_order_callback() {

    $admin = current_user_can( "manage_options" );

    $data = array();

    $restaurant_id  = ( isset( $_POST["restaurant_id"] ) ) ? $_POST["restaurant_id"] : false;
    $shipping_id    = get_field( "dms_restaurant_shipping", "user_" . $restaurant_id );
    $problem        = get_field( "dms_shipping_problem", $shipping_id );

    if( $problem ){
        $problem_text       = get_field( "dms_shipping_problem_text", $shipping_id );
        $data["problem"]    = $problem_text;

        echo json_encode( $data );
        exit;
    }

    $preorder_time = ( isset( $_POST["preorder_time"] ) ) ? array( "preorder" => $_POST["preorder_time"] ) : array();

    if( !$admin ){
        $is_closed = check_restaurant_closed( $restaurant_id, $preorder_time );
        if( $is_closed ){
            $data["closed"] = $is_closed["label"];

            echo json_encode( $data );
            exit;
        }
    }

    $order_cost                 = ( isset( $_POST["order_cost"] ) ) ? floatval( str_replace(",", ".", $_POST["order_cost"] ) ) : false;
    $address                    = ( isset( $_POST["address"] ) ) ? $_POST["address"] : false;

    if( in_array("latlng", array_keys( $address ) ) && $address["latlng"] != "" ){
        $client_address         = $address["latlng"];
        $has_latlng             = true;
    }else{
        $address["province"]    = "Illes Balears";
        $address["country"]     = "Spain";
        $client_address         = join( ", ", array_filter( array_values( $address ) ) );
        $has_latlng             = false;
    }

    $center                     = get_field( "dms_shipping_center", $shipping_id );
    $radius                     = floatval( get_field( "dms_shipping_radius", $shipping_id ) );
    $sections                   = get_field( "dms_shipping_section", $shipping_id );

    require_once WP_PLUGIN_DIR . "/woocommerce-distance-rate-shipping/includes/class-wc-shipping-distance-rate.php";
    $distance_rate_class        = new WC_Shipping_Distance_Rate();

    global $post;

    // CHECK IF CUSTOMER ADDRESS IS FOUND
    $google_address             = $distance_rate_class->get_api()->get_address( $client_address, $has_latlng );
    $data["google_address"]     = $google_address;

    if( $google_address->status == 'ZERO_RESULTS'){
        $data["location_type"]  = $google_address->status;

        // $data["id"]          = $distance_rate_class->id;
        // $data["label"]           = $distance_rate_class->title . " (No se encuentra la direccion introducida)";
        // $data["cost"]            = 1;

        if( $admin ){
            $data["not_found"]  = true;
        }

        echo json_encode( $data );
        exit;
    }

    $data["location_type"]  = $google_address->results[0]->geometry->location_type;

    if( $data["location_type"] == "APPROXIMATE" ){

        if( $admin ){
            $data["not_found"]  = $google_address->results[0]->formatted_address;
        }
    }

    // CHECK IF CLIENT IN ZONE AND SAVE DISTANCE BETWEEN CLIENT AND CENTRAL
    $in_limit               = false;

    $limit_distance         = $distance_rate_class->get_api()->get_distance( $client_address, $center, false, 'driving', 'none', 'metric' );

    if ( ! isset( $limit_distance->rows[0] ) || 'OK' !== $limit_distance->rows[0]->elements[0]->status ) {
        // $data["error"] = "No se encuentra la direccion introducida.";
        // $data["id"]      = $distance_rate_class->id;
        // $data["label"]       = $distance_rate_class->title . " (No se encuentra la direccion introducida)";
        // $data["cost"]        = 1;

        if( $admin ){
            $data["not_found"]  = true;
        }

        echo json_encode( $data );
        exit;
    }

    $limit_rounding_precision   = apply_filters( 'woocommerce_distance_rate_shipping_distance_rounding_precision', 1 );
    $limit_distance_value       = $limit_distance->rows[0]->elements[0]->distance->value;
    $limit_distance             = round( $limit_distance_value / 1000, $limit_rounding_precision );

    if( $limit_distance <= $radius ){
        $in_limit = true;
    }

    if( !$in_limit ){
        $data["in_limit"] = $in_limit;

        echo json_encode( $data );
        exit;
    }

    $restaurant_map     = get_field( "dms_restaurant_map", "user_" . $restaurant_id );
    $restaurant_latlng  = $restaurant_map["lat"] . "," . $restaurant_map["lng"];

    // SAVE DISTANCE BETWEEN CLIENT AND RESTAURANT
    $distance           = $distance_rate_class->get_api()->get_distance( $restaurant_latlng, $client_address, false, 'driving', 'none', 'metric' );

    if ( ! isset( $distance->rows[0] ) || 'OK' !== $distance->rows[0]->elements[0]->status ) {
        // $data["error"] = "No se encuentra la direccion introducida.";
        // $data["id"]      = $distance_rate_class->id;
        // $data["label"]       = $distance_rate_class->title . " (No se encuentra la direccion introducida)";
        // $data["cost"]        = 1;

        if( $admin ){
            $data["not_found"]  = true;
        }

        echo json_encode( $data );
        exit;
    }

    $data["distance"]       = $distance;
    $distance_text          = ' (' . $distance->rows[0]->elements[0]->distance->text . ')';
    $rounding_precision     = apply_filters( 'woocommerce_distance_rate_shipping_distance_rounding_precision', 1 );
    $distance_value         = $distance->rows[0]->elements[0]->distance->value;
    $distance               = round( $distance_value / 1000, $rounding_precision );

    // SAVE DISTANCE BETWEEN CENTRAL AND RESTAURANT
    $restaurant_distance    = $distance_rate_class->get_api()->get_distance( $center, $restaurant_latlng, false, 'driving', 'none', 'metric' );

    if ( ! isset( $restaurant_distance->rows[0] ) || 'OK' !== $restaurant_distance->rows[0]->elements[0]->status ) {
        // $data["error"] = "No se encuentra la direccion introducida.";
        // $data["id"]      = $distance_rate_class->id;
        // $data["label"]       = $distance_rate_class->title . " (No se encuentra la direccion introducida)";
        // $data["cost"]        = 1;

        if( $admin ){
            $data["not_found"]  = true;
        }

        echo json_encode( $data );
        exit;
    }

    $restaurant_rounding_precision  = apply_filters( 'woocommerce_distance_rate_shipping_distance_rounding_precision', 1 );
    $restaurant_distance_value      = $restaurant_distance->rows[0]->elements[0]->distance->value;
    $restaurant_distance            = round( $restaurant_distance_value / 1000, $restaurant_rounding_precision );

    $distance                       += $restaurant_distance + $limit_distance;

    // CHECK DISTANCE FOR PRICE
    $shipping_total = 0;
    foreach ( $sections as $key => $rule ) {

        if( $rule_found ){
            continue;
        }

        $min_match      = false;
        $max_match      = false;
        $shipping_cost  = null;

        $rule_distance_min  =  ( isset( $rule['dms_section_distance_minimum'] ) ) ? floatval( $rule['dms_section_distance_minimum'] ) : false;
        $rule_distance_max  =  ( isset( $rule['dms_section_distance_maximum'] ) ) ? floatval( $rule['dms_section_distance_maximum'] ) : false;

        if ( !$rule_distance_min || $distance >= $rule_distance_min ) {
            $min_match = true;
        }

        if ( !$rule_distance_max || $distance <= $rule_distance_max ) {
            $max_match = true;
        }

        if ( $min_match && $max_match ) {

            $rule_price_free    =  ( isset( $rule['dms_section_free'] ) ) ? floatval( $rule['dms_section_free'] ) : false;
            $rule_price_min     =  ( isset( $rule['dms_section_order'] ) ) ? floatval( $rule['dms_section_order'] ) : false;

            if( $rule_price_free && $order_cost < $rule_price_free ){
                $data["price_free"] = number_format( $rule_price_free - $order_cost, 2 );
            }

            if ( !$rule_price_min || $order_cost >= $rule_price_min ) {
                $rule_price_cost =  ( isset( $rule['dms_section_price'] ) ) ? floatval( $rule['dms_section_price'] ) : false;

				// ##############################################################################
				// THIS SHIPPING COST CALCULATION METHOD IS JUST WRONG BUT THE CLIENT IS A MORRON
				// ##############################################################################
				if( $rule_price_free && $order_cost < $rule_price_free && $rule_price_cost ){

					if( $order_cost + $rule_price_cost < $rule_price_free ){
						$shipping_cost = $rule_price_cost;
					}elseif( $order_cost + $rule_price_cost >= $rule_price_free ){
						$shipping_cost = $rule_price_free - $order_cost;
					}
                }
				// ##############################################################################
				// ##########     END OF STUPID SHIPPING COST CALCULATION METHOD      ###########
				// ##############################################################################

                if( $rule_price_free && $order_cost >= $rule_price_free ){
                    $shipping_cost = 0;
                }

            }else{
                $data["price_min"] = $rule_price_min;
            }


            $data["price_current"]  = number_format( $order_cost, 2 );
            $data["order_min"]      = number_format( $rule_price_min, 2 );
            $data["order_free"]     = number_format( $rule_price_free, 2 );
        }

        if ( ! is_null( $shipping_cost ) ) {
            $rule_found         = true;
            $shipping_total     += $shipping_cost;
        }
    }

    if ( $rule_found ) {
        $data["id"]     = $distance_rate_class->id;
        $data["label"]  = $distance_rate_class->title . $distance_text;
        $data["cost"]   = number_format( $shipping_total, 2 );
    }elseif( !$data["price_min"] ){

        if( $distance >= $rule_distance_max ){
            $data["distance_max"] = $rule_distance_max;
        }

        if( $distance <= $rule_distance_min ){
            $data["distance_min"] = $rule_distance_min;
        }

        $data["distance_current"] = $distance;
    }

    echo json_encode( $data );

    exit;
}
/* END get shipping */

/* Check if valid address before calculating shipping cost */
add_action( 'wp_ajax_dms_address_validation', 'dms_address_validation_callback' );
add_action( 'wp_ajax_nopriv_dms_address_validation', 'dms_address_validation_callback' );
function dms_address_validation_callback() {

    $admin          = current_user_can( "manage_options" );

    $data           = array();

    $restaurant_id  = ( isset( $_POST["restaurant_id"] ) ) ? $_POST["restaurant_id"] : false;
    $shipping_id    = get_field( "dms_restaurant_shipping", "user_" . $restaurant_id );

    $address        = ( isset( $_POST["address"] ) ) ? $_POST["address"] : false;

    if( in_array("latlng", array_keys( $address ) ) && $address["latlng"] != "" ){
        $client_address         = $address["latlng"];
        $has_latlng             = true;
    }else{
        $address["province"]    = "Illes Balears";
        $address["country"]     = "Spain";
        $client_address         = join( ", ", array_filter( array_values( $address ) ) );
        $has_latlng             = false;
    }

    $center                     = get_field( "dms_shipping_center", $shipping_id );
    $radius                     = floatval( get_field( "dms_shipping_radius", $shipping_id ) );
    $sections                   = get_field( "dms_shipping_section", $shipping_id );

    require_once WP_PLUGIN_DIR . "/woocommerce-distance-rate-shipping/includes/class-wc-shipping-distance-rate.php";
    $distance_rate_class        = new WC_Shipping_Distance_Rate();

    global $post;

    // CHECK IF CUSTOMER ADDRESS IS FOUND
    $google_address             = $distance_rate_class->get_api()->get_address( $client_address, $has_latlng );
    $data["google_address"]     = $google_address;

    if( $google_address->status == 'ZERO_RESULTS'){

        $data["error"]  = "Dirección no encontrada, solo se permite recogida en restaurante";
    }

    if( $google_address->results[0]->geometry->location_type == "APPROXIMATE" ){

        $data["error"]  = "Dirección imprecisa, puedes seguir con el pedido";
    }

    // CHECK IF CLIENT IN ZONE AND SAVE DISTANCE BETWEEN CLIENT AND CENTRAL
    $limit_distance         = $distance_rate_class->get_api()->get_distance( $client_address, $center, false, 'driving', 'none', 'metric' );

    if ( ! isset( $limit_distance->rows[0] ) || 'OK' !== $limit_distance->rows[0]->elements[0]->status ) {

        if( $admin ){
            $data["error"]  = "No se ha calculado la distancia porque dirección no encontrada";
        }
    }

    $limit_rounding_precision   = apply_filters( 'woocommerce_distance_rate_shipping_distance_rounding_precision', 1 );
    $limit_distance_value       = $limit_distance->rows[0]->elements[0]->distance->value;
    $limit_distance             = round( $limit_distance_value / 1000, $limit_rounding_precision );

    if( $limit_distance > $radius ){
        $data["error"] = "No repartimos a esa zona";

        echo json_encode( $data );
        exit;
    }

    if( !$data["error"] ){
        $data["ok"] = "Dirección válida";
    }

    echo json_encode( $data );
    exit;
}
/* END check address */

/* Print list of selected restaurant products */
add_action( 'wp_ajax_dms_get_restaurant_products_in_order', 'dms_get_restaurant_products_in_order_callback' );
add_action( 'wp_ajax_nopriv_dms_get_restaurant_products_in_order', 'dms_get_restaurant_products_in_order_callback' );
function dms_get_restaurant_products_in_order_callback() {
    $restaurant_id      = ( isset( $_POST["restaurant_id"] ) ) ? $_POST["restaurant_id"] : false;

    print_in_order_products( $restaurant_id );

    exit;
}
/* END print products */

/* Register new customer from new order */
add_action('wp_ajax_dms_create_new_customer_from_order', 'dms_create_new_customer_from_order_callback');
add_action('wp_ajax_nopriv_dms_create_new_customer_from_order', 'dms_create_new_customer_from_order_callback');
function dms_create_new_customer_from_order_callback() {
    global $woocommerce;

    $data = array();

    $email          = ( isset( $_POST["email"] ) ) ? $_POST["email"] : "";
    $first_name     = ( isset( $_POST["first_name"] ) ) ? $_POST["first_name"] : "";
    $last_name      = ( isset( $_POST["last_name"] ) ) ? $_POST["last_name"] : "";
    $phone          = ( isset( $_POST["phone"] ) ) ? $_POST["phone"] : "";
    $gender         = ( isset( $_POST["gender"] ) ) ? $_POST["gender"] : "";
    $age            = ( isset( $_POST["age"] ) ) ? $_POST["age"] : "";
    $address_1      = ( isset( $_POST["address_1"] ) ) ? $_POST["address_1"] : "";
    $address_2      = ( isset( $_POST["address_2"] ) ) ? $_POST["address_2"] : "";
    $city           = ( isset( $_POST["city"] ) ) ? $_POST["city"] : "";
    $postcode       = ( isset( $_POST["postcode"] ) ) ? $_POST["postcode"] : "";
    $latlng         = ( isset( $_POST["latlng"] ) ) ? $_POST["latlng"] : "";
    $indications    = ( isset( $_POST["indications"] ) ) ? $_POST["indications"] : "";

    $customer_id = wc_create_new_customer( $email );

    if ( !is_wp_error( $customer_id ) ) {

        $userdata = array(
          'ID'           => $customer_id,
          'first_name'   => $first_name,
          'last_name'    => $last_name,
          'display_name' => $first_name
        );

        wp_update_user( $userdata );

        $default_address = array();

        $default_address[0]['shipping_country']             = "ES";
        $default_address[0]['shipping_address_1']           = $address_1;
        $default_address[0]['shipping_address_2']           = $address_2;
        $default_address[0]['shipping_city']                = $city;
        $default_address[0]['shipping_state']               = "PM";
        $default_address[0]['shipping_postcode']            = $postcode;
        $default_address[0]['shipping_latlng']             = $latlng;
        $default_address[0]['shipping_indications']         = $indications;
        $default_address[0]['shipping_address_is_default']  = 'true';

        update_user_meta( $customer_id, 'wc_multiple_shipping_addresses', $default_address );
        update_user_meta( $customer_id, 'billing_first_name', $first_name );
        update_user_meta( $customer_id, 'billing_last_name', $last_name );
        update_user_meta( $customer_id, 'billing_phone', $phone );
        update_user_meta( $customer_id, 'billing_email', $email );
        update_user_meta( $customer_id, 'billing_gender', $gender );
        update_user_meta( $customer_id, 'billing_age', $age );

        update_user_meta( $customer_id, 'shipping_country', "ES" );
        update_user_meta( $customer_id, 'shipping_address_1', $address_1 );
        update_user_meta( $customer_id, 'shipping_address_2', $address_2 );
        update_user_meta( $customer_id, 'shipping_city', $city );
        update_user_meta( $customer_id, 'shipping_state', "PM" );
        update_user_meta( $customer_id, 'shipping_postcode', $postcode );
        update_user_meta( $customer_id, 'shipping_latlng', $latlng );
        update_user_meta( $customer_id, 'shipping_indications', $indications );

        $data["customer"] = $customer_id;
    }else{
        $data = $customer_id;
    }

    echo json_encode( $data );

    exit;
}
/* END register customer */

/* Edit customer address from new order */
add_action('wp_ajax_dms_edit_customer_address_from_order', 'dms_edit_customer_address_from_order_callback');
add_action('wp_ajax_nopriv_dms_edit_customer_address_from_order', 'dms_edit_customer_address_from_order_callback');
function dms_edit_customer_address_from_order_callback() {
	$current_user 	= wp_get_current_user();
	$is_restaurant 	= in_array( "restaurante", $current_user->roles );

	if( $is_restaurant ){
		echo json_encode( array( "restaurant" => "not allowed edit customer ") );

	    exit;
	}

    $data = array();

    $customer_id    = ( isset( $_POST["customer_id"] ) ) ? $_POST["customer_id"] : false;
    $address_id     = ( isset( $_POST["address_id"] ) ) ? intval( $_POST["address_id"] ) - 1 : false;
    $email          = ( isset( $_POST["email"] ) ) ? $_POST["email"] : "";
    $first_name     = ( isset( $_POST["first_name"] ) ) ? $_POST["first_name"] : "";
    $last_name      = ( isset( $_POST["last_name"] ) ) ? $_POST["last_name"] : "";
    $phone          = ( isset( $_POST["phone"] ) ) ? $_POST["phone"] : "";
    $gender         = ( isset( $_POST["gender"] ) ) ? $_POST["gender"] : "";
    $age            = ( isset( $_POST["age"] ) ) ? $_POST["age"] : "";
    $address_1      = ( isset( $_POST["address_1"] ) ) ? $_POST["address_1"] : "";
    $address_2      = ( isset( $_POST["address_2"] ) ) ? $_POST["address_2"] : "";
    $city           = ( isset( $_POST["city"] ) ) ? $_POST["city"] : "";
    $postcode       = ( isset( $_POST["postcode"] ) ) ? $_POST["postcode"] : "";
    $latlng         = ( isset( $_POST["latlng"] ) ) ? $_POST["latlng"] : "";
    $indications    = ( isset( $_POST["indications"] ) ) ? $_POST["indications"] : "";

    $data["customer_id"] = $customer_id;
    $data["address_id"] = $address_id;

    if ( $customer_id ) {

        $userdata = array(
          'ID'           => $customer_id,
          'first_name'   => $first_name,
          'last_name'    => $last_name,
          'display_name' => $first_name
        );

        wp_update_user( $userdata );

        if( $address_id >= 0 ){
			global $woocommerce;

            $default_address = get_user_meta( $customer_id, 'wc_multiple_shipping_addresses', true );

            $default_address[ $address_id ]['shipping_address_1']       = $address_1;
            $default_address[ $address_id ]['shipping_address_2']       = $address_2;
            $default_address[ $address_id ]['shipping_city']            = $city;
            $default_address[ $address_id ]['shipping_postcode']        = $postcode;
            $default_address[ $address_id ]['shipping_latlng']         = $latlng;
            $default_address[ $address_id ]['shipping_indications']     = $indications;

            update_user_meta( $customer_id, 'wc_multiple_shipping_addresses', $default_address );

            if( $default_address[ $address_id ]['shipping_address_is_default'] == 'true' ){
                update_user_meta( $customer_id, 'shipping_country', "ES" );
                update_user_meta( $customer_id, 'shipping_address_1', $address_1 );
                update_user_meta( $customer_id, 'shipping_address_2', $address_2 );
                update_user_meta( $customer_id, 'shipping_city', $city );
                update_user_meta( $customer_id, 'shipping_state', "PM" );
                update_user_meta( $customer_id, 'shipping_postcode', $postcode );
                update_user_meta( $customer_id, 'shipping_latlng', $latlng );
                update_user_meta( $customer_id, 'shipping_indications', $indications );
            }
        }else{
            update_user_meta( $customer_id, 'shipping_country', "ES" );
            update_user_meta( $customer_id, 'shipping_address_1', $address_1 );
            update_user_meta( $customer_id, 'shipping_address_2', $address_2 );
            update_user_meta( $customer_id, 'shipping_city', $city );
            update_user_meta( $customer_id, 'shipping_state', "PM" );
            update_user_meta( $customer_id, 'shipping_postcode', $postcode );
            update_user_meta( $customer_id, 'shipping_latlng', $latlng );
            update_user_meta( $customer_id, 'shipping_indications', $indications );
        }

        if( $first_name ) update_user_meta( $customer_id, 'billing_first_name', $first_name );
        if( $last_name ) update_user_meta( $customer_id, 'billing_last_name', $last_name );
        if( $phone ) update_user_meta( $customer_id, 'billing_phone', $phone );
        if( $email ) update_user_meta( $customer_id, 'billing_email', $email );
        if( $gender ) update_user_meta( $customer_id, 'billing_gender', $gender );
        if( $age ) update_user_meta( $customer_id, 'billing_age', $age );

        $data[""] = $customer_id;
    }else{
        $data = $customer_id;
    }

    echo json_encode( $data );

    exit;
}
/* END edit customer */

/* Edit customer address from new order */
add_action('wp_ajax_dms_delete_customer_address_from_order', 'dms_delete_customer_address_from_order_callback');
add_action('wp_ajax_nopriv_dms_delete_customer_address_from_order', 'dms_delete_customer_address_from_order_callback');
function dms_delete_customer_address_from_order_callback() {
	$current_user 	= wp_get_current_user();
	$is_restaurant 	= in_array( "restaurante", $current_user->roles );

	if( $is_restaurant ){
		echo json_encode( array( "restaurant" => "not allowed edit customer ") );

	    exit;
	}

    $data = array();

    $customer_id    = ( isset( $_POST["customer_id"] ) ) ? $_POST["customer_id"] : false;
    $address_id     = ( isset( $_POST["address_id"] ) ) ? intval( $_POST["address_id"] ) - 1 : false;

    $data["customer_id"] = $customer_id;
    $data["address_id"] = $address_id;

    if ( $customer_id ) {

        if( $address_id >= 0 ){
			global $woocommerce;

            $was_default = false;
            $default_address = get_user_meta( $customer_id, 'wc_multiple_shipping_addresses', true );

            if( $default_address[ $address_id ]['shipping_address_is_default'] == 'true' ) $was_default = true;

            unset( $default_address[ $address_id ] );
            $default_address = array_merge( $default_address );

            if( $was_default ){
                $default_address[ 0 ]['shipping_address_is_default'] == 'true';

                update_user_meta( $customer_id, 'shipping_country', "ES" );
                update_user_meta( $customer_id, 'shipping_address_1', $default_address[ 0 ]['shipping_address_1'] );
                update_user_meta( $customer_id, 'shipping_address_2', $default_address[ 0 ]['shipping_address_2'] );
                update_user_meta( $customer_id, 'shipping_city', $default_address[ 0 ]['shipping_city'] );
                update_user_meta( $customer_id, 'shipping_state', "PM" );
                update_user_meta( $customer_id, 'shipping_postcode', $default_address[ 0 ]['shipping_postcode'] );
                update_user_meta( $customer_id, 'shipping_latlng', $default_address[ 0 ]['shipping_latlng'] );
                update_user_meta( $customer_id, 'shipping_indications', $default_address[ 0 ]['shipping_indications'] );
            }

            update_user_meta( $customer_id, 'wc_multiple_shipping_addresses', $default_address );
        }
    }

    echo json_encode( $data );

    exit;
}
/* END edit customer */

/* Register new customer from new order */
add_action('wp_ajax_dms_add_new_address_to_customer_from_order', 'dms_add_new_address_to_customer_from_order_callback');
add_action('wp_ajax_nopriv_dms_add_new_address_to_customer_from_order', 'dms_add_new_address_to_customer_from_order_callback');
function dms_add_new_address_to_customer_from_order_callback() {
    global $woocommerce;

    $data = array();

    $customer_id    = ( isset( $_POST["customer_id"] ) ) ? $_POST["customer_id"] : false;
    $address_1      = ( isset( $_POST["address_1"] ) ) ? $_POST["address_1"] : "";
    $address_2      = ( isset( $_POST["address_2"] ) ) ? $_POST["address_2"] : "";
    $city           = ( isset( $_POST["city"] ) ) ? $_POST["city"] : "";
    $postcode       = ( isset( $_POST["postcode"] ) ) ? $_POST["postcode"] : "";
    $latlng         = ( isset( $_POST["latlng"] ) ) ? $_POST["latlng"] : "";
    $indications    = ( isset( $_POST["indications"] ) ) ? $_POST["indications"] : "";

    if ( $customer_id ) {

        $default_address = get_user_meta( $customer_id, 'wc_multiple_shipping_addresses', true );
        $last_address = count( $default_address );

        $default_address[ $last_address ]['shipping_country']               = "ES";
        $default_address[ $last_address ]['shipping_address_1']             = $address_1;
        $default_address[ $last_address ]['shipping_address_2']             = $address_2;
        $default_address[ $last_address ]['shipping_city']                  = $city;
        $default_address[ $last_address ]['shipping_state']                 = "PM";
        $default_address[ $last_address ]['shipping_postcode']              = $postcode;
        $default_address[ $last_address ]['shipping_latlng']               	= $latlng;
        $default_address[ $last_address ]['shipping_indications']           = $indications;
        $default_address[ $last_address ]['shipping_address_is_default']    = 'false';

        update_user_meta( $customer_id, 'wc_multiple_shipping_addresses', $default_address );

        $data["customer"] = $customer_id;
    }else{
        $data = $customer_id;
    }

    echo json_encode( $data );

    exit;
}
/* END register customer */


/* Print alt field select options */
add_action('wp_ajax_dms_add_multiple_address_selector_to_order', 'dms_add_multiple_address_selector_to_order_callback');
add_action('wp_ajax_nopriv_dms_add_multiple_address_selector_to_order', 'dms_add_multiple_address_selector_to_order_callback');
function dms_add_multiple_address_selector_to_order_callback() {
    global $woocommerce;

    $customer_id    = ( isset( $_POST["customer_id"] ) ) ? $_POST["customer_id"] : "";

    if( ! $customer_id ){
        exit;
    }

    $otherAddrs = get_user_meta( $customer_id, 'wc_multiple_shipping_addresses', true );
    if ( ! $otherAddrs ) {
        exit;
    }

    $addresses    = array();
    $addresses[0] = __( 'Selecciona una dirección guardada o añade una nueva', THEME_NAME );
    for ( $i = 1; $i <= count( $otherAddrs ); ++$i ) {

        if (!empty($otherAddrs[$i - 1]['label'])) {
            $addresses[ $i ] = $otherAddrs[$i - 1]['label'] . ' ' . $otherAddrs[ $i - 1 ]['shipping_postcode'];
        } else {
            $addresses[ $i ] = $otherAddrs[ $i - 1 ]['shipping_address_1'] . ' ' . $otherAddrs[ $i - 1 ]['shipping_address_2'] . ', ' . $otherAddrs[ $i - 1 ]['shipping_postcode'] . ' ' . $otherAddrs[ $i - 1 ]['shipping_city'];
        }
    }

    $addresses[ "new" ] = __( 'Añadir nueva dirección', THEME_NAME );

    $field = array(
        'label'    => __( 'Direcciones guardadas', THEME_NAME ),
        'required' => false,
        'class'    => "select",
        'id'        => "_shipping_alt",
        'name'      => "_shipping_alt",
        'clear'    => true,
        'type'     => 'select',
        'options'  => $addresses,
        'wrapper_class' => "form-field form-field-wide"
    );

    // woocommerce_wp_select( $alt_field );
    foreach ( $addresses as $key => $value ) {
        echo '<option value="' . esc_attr( $key ) . '" ' . selected( esc_attr( $field['value'] ), esc_attr( $key ), false ) . '>' . esc_html( $value ) . '</option>';
    }

    exit;
}
/* END print alt */

/* Load selected customer address */
add_action('wp_ajax_dms_change_selected_address', 'dms_change_selected_address_callback');
add_action('wp_ajax_nopriv_dms_change_selected_address', 'dms_change_selected_address_callback');
function dms_change_selected_address_callback() {
    global $woocommerce;

    $customer_id    = ( isset( $_POST["customer_id"] ) ) ? $_POST["customer_id"] : "";
    if( ! $customer_id ){
        exit;
    }

    $address_id     = ( isset( $_POST["address_id"] ) ) ? ( intval( $_POST["address_id"] ) - 1 ) : -1;
    if ( $address_id < 0 ) {
        exit;
    }

    $otherAddr = get_user_meta( $customer_id, 'wc_multiple_shipping_addresses', true );

    global $woocommerce;
    $addr                          = $otherAddr[ $address_id ];
    $addr['shipping_country_text'] = $woocommerce->countries->countries[ $addr['shipping_country'] ];

    echo json_encode( $addr );

    exit;
}
/* END load address */

/* Change order asigned driver from custom orders list */
add_action('wp_ajax_dms_change_order_driver_from_list', 'dms_change_order_driver_from_list_callback');
add_action('wp_ajax_nopriv_dms_change_order_driver_from_list', 'dms_change_order_driver_from_list_callback');
function dms_change_order_driver_from_list_callback() {

    $order  = ( $_POST["order"] ) ? intval( $_POST["order"] ) : false;
    if( ! $order ){
        exit;
    }

    $driver = ( $_POST["driver"] ) ? ( intval( $_POST["driver"] ) ) : "";

    $updated = "";
    $data = array();

    $current_driver_id  = get_post_meta( $order, "dms_order_driver", true );
    $current_driver     = get_user_by( "id", $current_driver_id );

    $is_driver          = in_array( "repartidora", $current_driver->roles );

    $data["driver"]     = $current_driver_id;

    if( !$current_driver || !$is_driver ){
        $full_date  = current_time('Y-m-d H:i:s');

        $updated    =   update_post_meta( $order, "dms_order_driver", $driver );
                        update_post_meta( $order, "dms_order_status", "driver_has_accepted" );
                        update_post_meta( $order, "dms_order_time_driver_has_accepted", $full_date );

        $data["case"]       = "no_driver";
        $data["updated"]    = $updated;
        $data["new_status"] = "driver_has_accepted";
        $data["date"]       = $full_date;
    }else{
        $status             = get_field( "dms_order_status", $order );

        switch ( $status ) {
            case 'rest_has_accepted':
            case 'driver_has_accepted':
			case 'driver_in_rest':
            case 'driver_on_road':
                $updated            = update_post_meta( $order, "dms_order_driver", $driver );
                $data["updated"]    = $updated;
                break;
            case 'problem':
                $updated = update_post_meta( $order, "dms_order_driver_problem", $driver );
                $data["case"]       = "problem";
                $data["updated"]    = $updated;
                break;
        }
    }

	if( $driver == "" ){
		update_post_meta( $order, "dms_order_status", "rest_has_accepted" );
		update_post_meta( $order, "dms_order_time_driver_has_accepted", "" );

		$title 		= "Nuevo pedido";
		$message 	= "Nuevo pedido: #" . $order;

	    $push = push_notification_to_all_drivers( $order, $message, $title );
	}else{
		$title 		= "Te han asignado nuevo pedido";
		$message 	= "Nuevo pedido: #" . $order;

	    $push = push_notification_to_single_driver( $driver, $message, $title );
	}

    $data["push"] = $push;
    echo json_encode( $data );

    exit;
}
/* END change driver */

/* Confirm that a refund has been made */
add_action('wp_ajax_dms_confirm_refund_from_list', 'dms_confirm_refund_from_list_callback');
add_action('wp_ajax_nopriv_dms_confirm_refund_from_list', 'dms_confirm_refund_from_list_callback');
function dms_confirm_refund_from_list_callback() {

    $order_id   = ( isset( $_POST["order_id"] ) ) ? intval( $_POST["order_id"] ) : false;
    if( ! $order_id ){
        exit;
    }

    $data = array();

    $current_status = get_post_meta( $order_id, "dms_order_status", true );
    $new_status = "";

    if( $current_status == "cancelled_return_money" ){
        $new_status = "order_cancelled";
    }

    if( $current_status == "return_money" ){
        $new_status = "order_refunded";
    }

    $data["refunded"] = update_post_meta( $order_id, "dms_order_status", $new_status );

    echo json_encode( $data );

    exit;
}
/* END confirm refund */

/* Confirm that a order revision has been made */
add_action('wp_ajax_dms_confirm_correction_from_list', 'dms_confirm_correction_from_list_callback');
add_action('wp_ajax_nopriv_dms_ccorrection_refund_from_list', 'dms_confirm_correction_from_list_callback');
function dms_confirm_correction_from_list_callback() {

    $order_id   = ( isset( $_POST["order_id"] ) ) ? intval( $_POST["order_id"] ) : false;
    if( ! $order_id ){
        exit;
    }

    $data["wrong"]      = delete_post_meta( $order_id, "_wrong_shipping_address" );
    $data["latlng"]     = delete_post_meta( $order_id, "_isnew_shipping_latlng" );
    $data["comment"]    = delete_post_meta( $order_id, "dms_comment_created_byapp" );

    echo json_encode( $data );

    exit;
}
/* END confirm revision */

/* Load restaurant preorder hours select */
add_action('wp_ajax_dms_ajax_get_preorder_time_select', 'dms_ajax_get_preorder_time_select_callback');
add_action('wp_ajax_nopriv_dms_ajax_get_preorder_time_select', 'dms_ajax_get_preorder_time_select_callback');
function dms_ajax_get_preorder_time_select_callback() {

    $restaurant_id  = ( isset( $_POST["restaurant_id"] ) ) ? intval( $_POST["restaurant_id"] ) : false;

    if( !$restaurant_id ) exit;

    $select = print_restaurant_previous_order_time_select( $restaurant_id );

    echo $select;

    exit;
}
/* END preorder select */

/* Download order receipts from order button click */
add_action('wp_ajax_download_order_receipts', 'download_order_receipts_callback');
add_action('wp_ajax_nopriv_download_order_receipts', 'download_order_receipts_callback');
function download_order_receipts_callback(){
	$order_id = ( isset( $_POST["order_id"] ) ) ? $_POST["order_id"] : false;

	generate_receipt_pdf( $order_id );

	exit;
}
/* END download receipt */
