<?php
/**
* # API FUNCTIONS #
* Included functions:
*
* 	function api_register_routes()
* 	function login_from_app()
* 	function logout_from_app()
* 	function save_devices_into_ddbb()
* 	function update_devices_in_ddbb()
* 	function get_devices()
* 	function get_areadelivery()
* 	function get_maxnumber_orders_accepted()
* 	function get_maxnumber_orders_visible_inlist()
* 	function get_driver_max_time()
* 	function get_webconfigurator_byapp()
*
*/

add_action( 'rest_api_init', 'api_register_routes' );
/**
 * Register the /wp-json/wp/v2/login route
 */
function api_register_routes() {
    register_rest_route( 'wp/v2', '/login', array(
        'methods'  => 'GET',
        'callback' => 'login_from_app',
        'args'     => array(
						'username'          => string,
						'password'      => string,
					),
	));
	 register_rest_route( 'wp/v2', '/logout', array(
        'methods'  => 'GET',
        'callback' => 'logout_from_app',
        'args'     => array(
						'username'          => string,
						'password'      => string,
					),
	));
   register_rest_route( 'wp/v2', 'save_devices', array(
        'methods'  => 'POST',
        'callback' => 'save_devices_into_ddbb',
         'args'     => array(
						 'user_id'          => int,
						 'registration_id'      => string,
         			),
	));
     register_rest_route( 'wp/v2', 'update_devices', array(
        'methods'  => 'POST',
        'callback' => 'update_devices_in_ddbb',
         'args'     => array(
						 'user_id'          => int,
						 'registration_id'      => string,
         			),
	));
    register_rest_route( 'wp/v2', 'get_devices', array(
        'methods'  => 'GET',
        'callback' => 'get_devices',
         'args'     => array(
						 'user_id'          => int
         			),
	));
	register_rest_route( 'wp/v2', 'get_areadelivery', array(
        'methods'  => 'GET',
        'callback' => 'get_areadelivery',
         'args'     => array(),
	));
	register_rest_route( 'wp/v2', 'get_maxnumber_orders_accepted', array(
        'methods'  => 'GET',
        'callback' => 'get_maxnumber_orders_accepted',
         'args'     => array(),
	));
	register_rest_route( 'wp/v2', 'get_maxnumber_orders_visible_inlist', array(
        'methods'  => 'GET',
        'callback' => 'get_maxnumber_orders_visible_inlist',
         'args'     => array(),
	));
	register_rest_route( 'wp/v2', 'get_driver_max_time', array(
        'methods'  => 'GET',
        'callback' => 'get_driver_max_time',
         'args'     => array(),
	));
	register_rest_route( 'wp/v2', 'get_webconfigurator_byapp', array(
        'methods'  => 'GET',
        'callback' => 'get_webconfigurator_byapp',
         'args'     => array(),
	));

	register_rest_route( 'wp/v2', '/get_usermeta_from_app', array(
        'methods'  => 'GET',
        'callback' => 'get_usermeta_from_app',
        'args'     => array(
						'username'          => string,
						'password'      => string,
					),
	));
 }



function login_from_app( WP_REST_Request $request ) {

   $user_login = $request['username'];
   $pass = $request['password'];
 	global $wpdb;
	//COMPROBAR EXISTENCIA USER
	$user_info = get_user_by( 'login', $user_login );

        //COMPROBAR CONTRASEÑA

        $comprobar = wp_check_password( $pass, $user_info->data->user_pass, $user_info->ID);
        $driver_can_access = get_user_meta( $user_info->ID,'dms_driver_access', true);
        if($comprobar && $driver_can_access==1){
            $user = $user_info;

            //update status user - user active
			update_user_meta( $user_info->ID, 'dms_driver_active', 1 );	
			$wpdb->insert( 
						'042lvF4_drivers_logs', 
						array( 
							'user_id' => $user_info->ID, 
							'time' => current_time( 'mysql' ), 
							'type' => 1, 
						) 
					);
        }

    return  $user;
}



function get_usermeta_from_app( WP_REST_Request $request ) {

   $user_login = $request['username'];
   $pass = $request['password'];
 	global $wpdb;
 	$result = array('dms_driver_new'=>"",'dms_driver_new_distance'=>"");
	//COMPROBAR EXISTENCIA USER
	$user_info = get_user_by( 'login', $user_login );

    //COMPROBAR CONTRASEÑA
    $comprobar = wp_check_password( $pass, $user_info->data->user_pass, $user_info->ID);
    $driver_can_access = get_user_meta( $user_info->ID,'dms_driver_access', true);
    if($comprobar && $driver_can_access==1){
        $result = array('is_driver_new'=>get_user_meta( $user_info->ID,'dms_driver_new', true),
        				'maxdistance'=>get_user_meta( $user_info->ID,'dms_driver_new_distance', true)
        				);
    }
    return  $result;
}


function logout_from_app( WP_REST_Request $request ) {

   $user_login = $request['username'];
   $pass = $request['password'];
   global $wpdb;

	//COMPROBAR EXISTENCIA USER
	$user_info = get_user_by( 'login', $user_login );

        //COMPROBAR CONTRASEÑA

        $comprobar = wp_check_password( $pass, $user_info->data->user_pass, $user_info->ID);
        if($comprobar){
            $user = $user_info;

            //update status user - user deactivated
			update_user_meta( $user_info->ID, 'dms_driver_active', 0 );	
			$wpdb->insert( 
						'042lvF4_drivers_logs', 
						array( 
							'user_id' => $user_info->ID, 
							'time' => current_time( 'mysql' ), 
							'type' => 0, 
						) 
					);
        }


    return  $user;
}

function save_devices_into_ddbb( WP_REST_Request $request ) {

     $user_id          		= $request['user_id'];
	 $registration_id      	= $request['registration_id'];
	 global $wpdb;

	  $result = $wpdb->insert( 
	 	'042lvG4_devices', 
	 	array( 
		 	'registration_id' => $registration_id, 
	  		'user_id' => $user_id
	  	), 
	 	array( 
	  		'%s', 
	  		'%d'
	 	) 
	 );
	if (!$result) {
		return new WP_Error( 'save_devices_into_ddbb - functions.php', 'Error - not save device in ddbb ', array( 'status' => 500 ) );
	}
	else
	{
		$result = true;
	}
	 return $result;
}
function update_devices_in_ddbb( WP_REST_Request $request ) {

     $user_id          		= $request['user_id'];
	 $registration_id      	= $request['registration_id'];
	 global $wpdb;

	$result =  $wpdb->update( 
		'042lvG4_devices', 
		array( 'registration_id' => $registration_id), 
		array( 'user_id' => $user_id ), 
		array( '%s'), 
		array( '%d') 
	);
	if (!$result) {
		return new WP_Error( 'update_devices_in_ddbb - functions.php', 'Error - not update device in ddbb ', array( 'status' => 500 ) );
	}
	else if ($result  == 0)
	{
		return new WP_Error( 'update_devices_in_ddbb - functions.php', 'Not update device in ddbb - User_Id:'.$user_id, array( 'status' => 500 ) );
	}
	else
		{	$result = true;}
	 return $result;
}
function get_devices( WP_REST_Request $request ) {

    $user_id          		= $request['user_id'];
	global $wpdb;
 	$result = $wpdb->get_var( 
                    $wpdb->prepare("SELECT count(ID) as total FROM 042lvG4_devices WHERE user_id=%d", $user_id) 
                 );
	
	if ($result == null) {
		return new WP_Error( 'get_devices - functions.php', 'Error - not find device in ddbb ', array( 'status' => 500 ) );
	}
	 return $result;
}

function get_areadelivery( WP_REST_Request $request ) {

	// WP_Query arguments
	$deliveries =  array();
	$args = array (
		'post_type'              => array( 'reparto' ),
	);

	// The Query
	$query = new WP_Query( $args );

	// The Loop
	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();
			$product_id = get_the_ID();
			$product = get_product( $product_id );
			$product_title = get_the_title();
		 	array_push($deliveries, array(
        		"id" => get_the_ID(),
        		"areadelivery" => get_the_title(), 
        		"state" =>get_post_meta( get_the_ID(), 'dms_delivery_state', true ),
    			));
		}
	} else {
	// no posts found
	}

	// Restore original Post Data
	wp_reset_postdata();
  	
  	ksort($deliveries, SORT_DESC);

	if ($deliveries == null) {
		return new WP_Error( 'get_areaderivery - functions.php', 'Error - not find areas in ddbb ', array( 'status' => 500 ) );
	}
	 return $deliveries;
}

function get_maxnumber_orders_accepted( WP_REST_Request $request ) {

	global $dms;
 	$driver_max_accept =  $dms["driver_max_accept"]; /* Devuelve una string  (indica el número máximo de ordenes aceptadas por motorista) del apartado de APP Settings de Opt  */
	
	if ($driver_max_accept == null) {
		return new WP_Error( 'get_maxnumber_orders_accepted - functions.php', 'Error - not find max number of orders accepted in ddbb ', array( 'status' => 500 ) );
	}
	 return $driver_max_accept;
}

function get_maxnumber_orders_visible_inlist( WP_REST_Request $request ) {

	global $dms;
 	$driver_max_show =  $dms["driver_max_show"]; /*Devuelve una string  (indica el número máximo de ordenes visibles) del apartado de APP Settings de Opt */
	
	if ($driver_max_show == null) {
		return new WP_Error( 'get_maxnumber_orders_visible_inlist - functions.php', 'Error - not find max number of orders visible in list ', array( 'status' => 500 ) );
	}
	return $driver_max_show;
}
function get_driver_max_time( WP_REST_Request $request ) {

	global $dms;
 	$driver_max_time =  $dms["driver_max_time"]; /*Devuelve una string  (indica el tiempo, minutos, máximos de una orden con su prioridad normal antes de cambiar a prioridad 1) del apartado de APP Settings de Opt */
	
	if ($driver_max_time == null) {
		return new WP_Error( 'driver_max_time - functions.php', 'Error - not find max time', array( 'status' => 500 ) );
	}
	return $driver_max_time;
}

function get_webconfigurator_byapp( WP_REST_Request $request ) {

	global $dms;
	$webconfigurator =  array();
	$webconfigurator['maxOrdersAccepted'] =   $dms["driver_max_accept"];/* Devuelve una string  (indica el número máximo de ordenes aceptadas por motorista) del apartado de APP Settings de Opt  */
	$webconfigurator['maxOrdersVisible'] =   $dms["driver_max_show"];/*Devuelve una string  (indica el número máximo de ordenes visibles) del apartado de APP Settings de Opt */
	$webconfigurator['maxTime'] =   $dms["driver_max_time"];/*Devuelve una string  (indica el tiempo, minutos, máximos de una orden con su prioridad normal antes de cambiar a prioridad 1) del apartado de APP Settings de Opt */
	
	if ($webconfigurator['maxOrdersAccepted'] == null) {
		return new WP_Error( 'get_webconfigurator_byapp - functions.php', 'Error - not find max number of orders accepted in ddbb ', array( 'status' => 501 ) );
	}
	if ($webconfigurator['maxOrdersVisible'] == null) {
		return new WP_Error( 'get_webconfigurator_byapp - functions.php', 'Error - not find max number of orders visible in list ', array( 'status' => 502 ) );
	}
	if ($webconfigurator['maxTime'] == null) {
		return new WP_Error( 'get_webconfigurator_byapp - functions.php', 'Error - not find max time', array( 'status' => 500 ) );
	}
	return $webconfigurator;
}