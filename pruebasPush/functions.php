<?php
global $theme_version;

/**
 * *****************************************************************************
 * Define issue fields
 * *****************************************************************************
 */
// FROM :: WPML Coding API -- https://wpml.org/documentation/support/wpml-coding-api/
define ( 'ICL_DONT_LOAD_LANGUAGE_SELECTOR_CSS', true );
// FROM :: CONTROLLING BEHAVIOR BY SETTING CONSTANTS --- http://contactform7.com/controlling-behavior-by-setting-constants/
define ( 'WPCF7_LOAD_CSS', false );
define ( 'WPCF7_AUTOP', false );
// FROM :: Using contact Form 7 3.9 or higher
add_filter ( 'wpcf7_load_css', '__return_false' );
add_filter ( 'wpcf7_autop', '__return_false' );
// FROM: Using woocommerce
if(class_exists('woocommerce')) {
	if ( version_compare( WOOCOMMERCE_VERSION, "2.1" ) >= 0 ) {
		add_filter( 'woocommerce_enqueue_styles', '__return_false' );
	} else {
		define( 'WOOCOMMERCE_USE_CSS', false );
	}
}

/**
 * *****************************************************************************
 * Define custom fields
 * *****************************************************************************
 */
// define ( "DEVELOPER_NAME", "" );
// define ( "DEVELOPER_WEB", "" );

// DEVELOPMENT MODE
define ( "DEVELOPMENT_MODE", false);

// DEBUG fields
define ( "DEBUG_ADMIN", false );

// THEME fields
$theme = wp_get_theme();
define ( "THEME_NAME", $theme->get( 'Name' ) );
define ( "THEME_TITLE", ucfirst($theme->get( 'Name' )) );

// DIRECTORY fields
define ( "ASSETS_DIRECTORY", get_template_directory_uri() . "/assets" );
define ( "IMAGES_DIRECTORY", ASSETS_DIRECTORY . "/images" );
define ( "CSS_DIRECTORY", ASSETS_DIRECTORY . "/css" );
define ( "JS_DIRECTORY", ASSETS_DIRECTORY . "/js" );

/**
 * *****************************************************************************
 * Require files
 * *****************************************************************************
 */

// TGM config
require_once get_template_directory() . '/config/wp-require-plugins.php';
require_once get_template_directory() . '/emails/DMS_Email.php';
require_once get_template_directory() . '/emails/smtpvalidateclass.php';

// Redux config
if (class_exists( 'Redux' )) {
	require_once get_template_directory() . '/config/Redux-config.php';
}

if(function_exists("is_woocommerce")){
	//include_once('widgets/woocommerce-dropdown-cart.php');
}

/**
 * *****************************************************************************
 * Favicon setup
 * *****************************************************************************
 */
function favicon() {
	global $dms;
	if($dms['favicon']['url']){
		echo '<link rel="shortcut icon" href="' . $dms['favicon']['url']  . '" />';
	}else{
		echo '<link rel="shortcut icon" href="' . get_template_directory_uri() . '/favicon.ico" />';
	}
}

/**
 * *****************************************************************************
 * Login setup
 * *****************************************************************************
 */
function my_login_logo() {
	global $dms;

	favicon();

	$logo = ($dms['logo']['url']) ? $dms['logo']['url'] : IMAGES_DIRECTORY . '/logo.png';
	?>
    <style type="text/css">
    	/* Custom login */
        body.login div#login h1 a {
            width: 100%;
			background: url(<?php echo $logo; ?>) no-repeat;
        	margin-bottom: 10px;
        	background-position: center;
        	background-size: contain;
        }
    </style>
	<?php
}
add_action( 'login_enqueue_scripts', 'my_login_logo' );
function my_login_logo_url() {return home_url();}
function my_login_logo_url_title() {return THEME_TITLE;}
add_filter( 'login_headerurl', 'my_login_logo_url' );
add_filter( 'login_headertitle', 'my_login_logo_url_title' );

/**
 * *****************************************************************************
 * Setup
 * *****************************************************************************
 */
remove_action('wp_head', 'wp_generator');
add_action('admin_head','favicon');
if (! function_exists ( 'theme_setup' )) :
	function theme_setup() {
		global $dms;

		// Load text domain
		load_theme_textdomain ( THEME_NAME, get_template_directory () . '/languages' );
		// Add theme supports
		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'title-tag' );
		add_theme_support('post-thumbnails');

		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support( 'html5', array(
				'search-form',
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
		) );

		/*
		 * Enable support for Post Formats.
		 * See https://developer.wordpress.org/themes/functionality/post-formats/
		 */
		add_theme_support( 'post-formats', array(
				'aside',
				'image',
				'video',
				'quote',
				'link',
		) );

		add_theme_support('woocommerce');

		// instanciate email
		$GLOBALS['dms_emails'] = DMS_Email::instance($dms);
	}
endif;
add_action ( 'after_setup_theme', 'theme_setup' );

if (! function_exists ( 'theme_admin_setup' )) :
	function theme_admin_setup() {
		// Restrict admin page
		if ( ! current_user_can( 'manage_options' ) && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {
			wp_redirect( home_url() );
			exit;
		} else {
			// Add capabilities

		}
	}
endif;
// add_action( 'admin_init', 'theme_admin_setup', 1 );

/**
 * *****************************************************************************
 * Theme admin debug
 * *****************************************************************************
 */
if (DEBUG_ADMIN) {
	if (!function_exists('debug_admin_menus')):
		function debug_admin_menus() {
			if ( !is_admin())
				return;
			global $submenu, $menu, $pagenow;
			if ( current_user_can('manage_options') ) {
				if( $pagenow == 'index.php' ) {
					echo '<pre>'; print_r( $menu ); echo '</pre>';
					echo '<pre>'; print_r( $submenu ); echo '</pre>';
				}
			}
		}
		add_action( 'admin_notices', 'debug_admin_menus' );
	endif;
}

/**
 * *****************************************************************************
 * Enqueue scripts and styles
 * *****************************************************************************
 */
function custom_admin_enqueue_scripts() {

	wp_enqueue_style( 'dms-jquery-ui-css', "//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css" );
	wp_enqueue_style( 'font-awesome', "https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css" );

	global $post_type;
    global $post;

    if ( $post_type == 'shop_order' ){

    }

    $post_id = 0;
    if( $post ){
    	$post_id = $post->ID;
    }

    $included_scripts = array('jquery', 'jquery-ui-datepicker', 'accounting');

    if( isset( $_GET["user_id"] ) ){
		$editable_user = get_user_by( "ID", $_GET["user_id"] );

		if( in_array("customer", $editable_user->roles) ){
			wp_register_script( 'google-maps-api', 'https://maps.googleapis.com/maps/api/js?key=AIzaSyBIO8BKXqA894JAPH84-Fwb4znwjpekPMA', null, null ); /*IAS 201600711*/
			$included_scripts[] = 'google-maps-api';
		}
	}

    wp_enqueue_script( 'custom-admin-js', JS_DIRECTORY . "/custom-admin.js", $included_scripts, '', true );

    $custom_admin_data = array(
		'dms_ajaxurl'							=> admin_url(  'admin-ajax.php' ),
		'current_time'							=> current_time( 'mysql' ),
		'order_item_nonce' 						=> wp_create_nonce( 'order-item' ),
		'post_id' 								=> $post_id,
		'currency_format_num_decimals'  		=> wc_get_price_decimals(),
		'mon_decimal_point' 					=> wc_get_price_decimal_separator(),
		'rounding_precision'            		=> wc_get_price_decimals() + 2,
		'round_at_subtotal'             		=> esc_attr( get_option( 'woocommerce_tax_round_at_subtotal' ) ),
		'cloned_order_url'						=> admin_url() .  "post-new.php?post_type=shop_order&cloned_order=" . $post_id,
	);

	wp_localize_script( 'custom-admin-js', 'custom_admin_data', $custom_admin_data );
}
add_action ( 'admin_enqueue_scripts', 'custom_admin_enqueue_scripts' );

function custom_wp_enqueue_scripts() {

	// FONTS
	//wp_enqueue_style( 'fonts', "http://fonts.googleapis.com/css?family=Open+Sans:400,800italic,800,700italic,600italic,600,400italic,300italic,300|Oswald:200,300,400,600,800|Oswald:200,300,400,600,800|Oswald:200,300,400,600,800|Oswald:200,300,400,600,800|Oswald:200,300,400,600,800|Droid+Sans:200,300,400,600,800&amp;subset=latin,latin-ext" );
	wp_enqueue_style( 'wpb-google-fonts', 'http://fonts.googleapis.com/css?family=Roboto:400,300,700');

	// FONT AWESOME
	wp_enqueue_style( 'font-awesome', "https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css" );

	// SWIPER STYLE
	wp_enqueue_style( 'dms-swiper-css', JS_DIRECTORY . "/libs/swiper/swiper.min.css" );

	// JQUERY UI CSS
	wp_enqueue_style( 'dms-jquery-ui-css', "//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css" );

	// NICESCROLL JS
	wp_enqueue_script( 'nicescroll-js', JS_DIRECTORY . "/libs/nicescroll/jquery.nicescroll.min.js", '', '', true );

	// SWIPER JS
	wp_enqueue_script( 'dms-swiper',  JS_DIRECTORY . '/libs/swiper/swiper.jquery.min.js', array('jquery'),false,true);

	// CUSTOM CSS
	wp_enqueue_style( 'style-css', get_template_directory_uri() . "/style.css" );

	// GOOGLE MAPS
	wp_register_script( 'google-maps-api', 'https://maps.googleapis.com/maps/api/js?key=AIzaSyBIO8BKXqA894JAPH84-Fwb4znwjpekPMA', null, null ); /*IAS 201600711*/

	// CUSTOM JS
	wp_enqueue_script( 'custom-js', JS_DIRECTORY . "/custom.js", array( 'jquery','jquery-ui-tabs', 'google-maps-api' ), '', true );

	$custom_data = array(
		'path_file_customer_set_shipping' 		=> get_template_directory_uri() . "/inc/customer_set_shipping.php",
		'dms_ajaxurl'							=> admin_url(  'admin-ajax.php' ),
	);

	wp_localize_script( 'custom-js', 'custom_data', $custom_data );

}
add_action ( 'wp_enqueue_scripts', 'custom_wp_enqueue_scripts' );

/**
 * *****************************************************************************
 * Register menus
 * *****************************************************************************
 */
if (! function_exists ( 'custom_navigation_menus' )) :
	function custom_navigation_menus() {
		$locations = array(
				'header_menu' => __( 'Header Menu', THEME_NAME ),
				'footer_menu' => __( 'Footer Menu', THEME_NAME ),
		);
		register_nav_menus( $locations );
	}
	add_action ( 'init', 'custom_navigation_menus' );
endif;
/**
 * *****************************************************************************
 * Register sidebars
 * *****************************************************************************
 */
if (! function_exists ( 'custom_sidebars' )) {
	function custom_sidebars() {
		// TODO
	}
	// add_action ( 'widgets_init', 'custom_sidebars' );
}

/**
 * *****************************************************************************
 * Optimice title page
 * *****************************************************************************
 */
function custom_wp_title($title) {
	if (empty ( $title ) && is_home ()) {
		return get_bloginfo ( 'description' ) . ' | ' . get_bloginfo ( 'title' );
	}
	if (is_front_page ()) {
		return get_bloginfo ( 'description' ) . ' | ' . get_bloginfo ( 'title' );
	}
	$title = str_replace ( get_bloginfo ( 'title' ), '', $title);
	$title = str_replace ( '&raquo;', '', $title );
	return $title . ' | ' . get_bloginfo ( 'title' );
}
add_filter ( 'wp_title', 'custom_wp_title' );

/**
 * *****************************************************************************
 * Customize wp_head function; add google analytics, facebook, twitter api, etc
 * *****************************************************************************
 */
function custom_wp_head() {
	global $dms;

	if($dms['google-analytics']){
		?>
		<!-- [google_analytics] begin -->

		<?php echo $dms['google-analytics']; ?>

		<!-- [google_analytics] end -->
		<?php
	}

}
add_action ( 'wp_head', 'custom_wp_head' );

/**
 * *****************************************************************************
 * Excerpt Lenght
 * *****************************************************************************
 */
function custom_excerpt_length($length) {
	return 40;
}
// add_filter ( 'excerpt_length', 'custom_excerpt_length', 99 );

/**
 * *****************************************************************************
 * Contact form 7 hook
 * *****************************************************************************
 */
function wpcf7_before_send_mail($contact_form) {
	global $dms_emails;

	$mail = $contact_form->prop( 'mail' );

	$body = $mail['body'];
	$mail['body'] = $dms_emails->get_html_body($mail['body']);

	$contact_form->set_properties( array( 'mail' => $mail ) );
}
add_action('wpcf7_before_send_mail', 'wpcf7_before_send_mail');

/**
 * *****************************************************************************
 * Translation functions
 * *****************************************************************************
 */
function get_default_language() {
	return get_option( 'qtranslate_default_language' );
}
function get_current_language(){
	global $q_config;
	return qtrans_getLanguage();
}
// Function to get url for a determinate language.
function get_url_for_language( $original_url, $lang = null ) {
	global $wpdb, $qtranslate_slug;
	if (is_null($lang)) {
		$lang = qtrans_getLanguage();
	}

	// Search original url in post metadata
	$post_id = null;
	$default_lang = get_default_language();
	$wpdb->query("SELECT `post_id`, `meta_value` FROM $wpdb->postmeta WHERE `meta_key` = '_qts_slug_$default_lang'");
	foreach($wpdb->last_result as $row) {
		if ($row->meta_value == ltrim($original_url, '/') ) {
			$post_id = $row->post_id;
			break;
		}
	}

	if ($post_id == null) {
		return $qtranslate_slug->home_url();
	} else {
		$url = get_site_url();
		if ($lang != $default_lang) {
			$url .= "/" . $lang;
		}
		$url .= "/" . $qtranslate_slug->get_slug($post_id, $lang);
		return $url;
	}

	return $original_url;
}

/**
 * *****************************************************************************
 * Woocommerce Functions
 * *****************************************************************************
 */
if(class_exists('woocommerce')) :
	add_filter( 'product_type_selector', 'remove_product_types' );
	function remove_product_types( $types ){
		return $types;
	}

	function get_country_selector($select_name, $default = "") {
		$countries_obj   = new WC_Countries();
		$countries   = $countries_obj->__get('countries');
		woocommerce_form_field($select_name,
				array(
						'type'       => 'select',
						'options'    => $countries,
						'default'    => $default,
				)
		);
	}

	// Custom extension woocommerto to put a checkout wrap
	function woocommerce_checkout_wrap() {
		get_template_part( "templates-parts/woocommerce-checkout", "wrap" );
	}
	add_action( 'woocommerce_checkout_wrap', 'woocommerce_checkout_wrap' );

	// Separate order review from checkout payments.
	//remove_action('woocommerce_checkout_order_review', 'woocommerce_checkout_payment', 20);
	//add_action('woocommerce_checkout_payment', 'woocommerce_checkout_payment');

	// Function product is complete
	function dms_custom_payment_complete($order_id) {

	}
	add_action("woocommerce_payment_complete", 'dms_custom_payment_complete');
endif;

/**
 * *****************************************************************************
 * Custom Functions
 * *****************************************************************************
 */

// function to limit string
function dms_limit_string($limit, $content, $permalink, $ending = "[...]"){
    if($limit<strlen($content)){
        $content = preg_replace(" (\[.*?\])",'',$content);
        $content = strip_tags($content);
        $content = substr($content, 0, $limit);
        $content = substr($content, 0, strripos($content, " "));
        $content = trim(preg_replace( '/\s+/', ' ', $content));
        if($permalink=="none"){
            //NONE
            $content = $content . ' ' . $ending;
        }elseif($permalink){
            $link = '<a href="' . $permalink . '">' . $ending . '</a>';
            $content = $content . ' ' . $link;
        }else{
            $content = $content . ' ' . $ending;
        }
    }
    return $content;
}



// function to check if device is a mobile.
function is_mobile() {
	return (bool)preg_match('#\b(ip(hone|od|ad)|android|opera m(ob|in)i|windows (phone|ce)|blackberry|tablet'.
			'|s(ymbian|eries60|amsung)|p(laybook|alm|rofile/midp|laystation portable)|nokia|fennec|htc[\-_]'.
			'|mobile|up\.browser|[1-4][0-9]{2}x[1-4][0-9]{2})\b#i', $_SERVER['HTTP_USER_AGENT'] );
}

// function to add classes in body of browser and OS
function browser_body_class($classes) {
	global $is_lynx, $is_gecko, $is_IE, $is_opera, $is_NS4, $is_safari, $is_chrome, $is_iphone;

	if($is_lynx) $classes[] = 'lynx';
	elseif($is_gecko) $classes[] = 'gecko';
	elseif($is_opera) $classes[] = 'opera';
	elseif($is_NS4) $classes[] = 'ns4';
	elseif($is_safari) $classes[] = 'safari';
	elseif($is_chrome) $classes[] = 'chrome';
	elseif($is_IE) $classes[] = 'ie';
	else $classes[] = 'unknown';

	if($is_iphone) $classes[] = 'iphone';


	if ( stristr( $_SERVER['HTTP_USER_AGENT'],"mac") ) {
		$classes[] = 'osx';
	} elseif ( stristr( $_SERVER['HTTP_USER_AGENT'],"linux") ) {
		$classes[] = 'linux';
	} elseif ( stristr( $_SERVER['HTTP_USER_AGENT'],"windows") ) {
		$classes[] = 'windows';
	}

	if(is_mobile()) $classes[] = 'is_mobile';

	return $classes;
}
add_filter('body_class','browser_body_class');



// Function to send custom email
function dms_send_email($subject, $email_to, $html_content) {
	global $dms_emails;
	$dms_emails->send($subject, THEME_TITLE, FROM_EMAIL, BCC_EMAIL, $email_to, $html_content);
}


function my_remove_meta_boxes() {
	// if( !current_user_can('manage_options') ) {
		// remove_meta_box('qtranxs-meta-box-lsb', 'page', 'normal');
		// remove_meta_box('qtranxs-meta-box-lsb', 'page', 'advanced');
		// remove_meta_box('qtranxs-meta-box-lsb', 'page', 'side');
	// }
}
// add_action( 'admin_menu', 'my_remove_meta_boxes', 999 );


// EDIT COLUMNS BACKEND CPT

// add_filter('manage_edit-cpt_columns', 'add_new_cpt_columns');
function add_new_cpt_columns($columns) {
	$new_columns['cb'] = $columns['cb'];
	return $new_columns;
}

// add_action('manage_cpt_posts_custom_column', 'manage_cpt_columns', 10, 2);
function manage_cpt_columns($column_name, $id) {
	global $wpdb;
	switch ($column_name) {
		case 'custom_tax':
			// Get taxonomies asociated to this post
			$str = "";
			foreach(get_the_terms($id, "custom_tax") as $term) {
				$name = $term->slug;
				$name = qtranxf_use(qtrans_getLanguage(), $name, false);
				$str .= '<a href="' . site_url() . "/wp-admin/edit.php?post_type=trip&trip_type=" . $term->slug . '">' . $name . '</a>, ';
			}
			echo rtrim($str, ", ");
			break;
		default:
			break;
	}
}


// DISPLAY CORRECTLY TAXS IN BACKEND
add_filter( 'wp_terms_checklist_args', 'checked_not_ontop', 1, 2 );
function checked_not_ontop( $args, $post_id ) {
	// IF NEED SPECIFICATION
    if ( 'cpt' == get_post_type( $post_id ) && $args['taxonomy'] == 'custom_tax' ) $args['checked_ontop'] = false;

    // DEFAULT
	$args['checked_ontop'] = false;

    return $args;
}


// DETERMINE THE TOP MOST PARENT OF A TERM
function get_term_top_most_parent($term_id, $taxonomy){
    // start from the current term
    $parent  = get_term_by( 'id', $term_id, $taxonomy);
    // climb up the hierarchy until we reach a term with parent = '0'
    while ($parent->parent != '0'){
        $term_id = $parent->parent;

        $parent  = get_term_by( 'id', $term_id, $taxonomy);
    }
    return $parent;
}


// ADD CURRENT ITEMS IN NAV MENU IF CURRENT PAGE IS SINGLE-{CPT}
// add_action('nav_menu_css_class', 'add_current_nav_class_trip', 10, 2 );
	function add_current_nav_class_trip($classes, $item ) {

	// Necessary, otherwise we can't get current post ID
	global $post;

	// Grabs the terms from the current post
	$page_tax_terms = wp_get_object_terms($post->ID, 'custom_tax');

	// Grabs the term object that was returned by wp_get_object_terms()
	$page_tax = $page_tax_terms[0];

	// Grabs the post type of the current post
	$page_post_type = get_post_type();

	// Grabs the Description of the current menu item, trims whitespace
	$item_desc = trim($item->description);

	// Do the magic...
	if ($item_desc != '' && (($item_desc == $page_post_type) || ($item_desc == $page_tax->name))) {
	       $classes[] = 'current-menu-item';
	};

	// Return the corrected set of classes to be added to the menu item
	return $classes;
}


// DEBUG PHP - DATA TO BROWSER CONSOLE
function debug_to_console($data) {
    if(is_array($data) || is_object($data))
	{
		echo("<script>console.log('PHP: ".json_encode($data)."');</script>");
	} else {
		echo("<script>console.log('PHP: ".$data."');</script>");
	}
}


// RETURN FORMATTED DATE
function dms_format_date($date, $format){
	// extract Y,M,D
	$y = substr($date, 0, 4);
	$m = substr($date, 4, 2);
	$d = substr($date, 6, 2);

	// create UNIX
	$time = strtotime("{$d}-{$m}-{$y}");

	// return format date
	return date($format, $time);
}

/**
 * *****************************************************************************
 * Custom user meta fields
 * *****************************************************************************
 */


/**
 * *****************************************************************************
 * Custom post types
 * *****************************************************************************
 */

if ( ! function_exists('theme_post_type') ) {
	function theme_post_type() {
		// Create custom post types
		$labels_envio = array(
				'name'                => _x( 'Envios', 'Post Type General Name', THEME_NAME ),
				'singular_name'       => _x( 'Envio', 'Post Type Singular Name', THEME_NAME ),
				'menu_name'           => __( 'Envios', THEME_NAME ),
				'parent_item_colon'   => __( 'Parent Envio:', THEME_NAME ),
				'all_items'           => __( 'All Envios', THEME_NAME ),
				'view_item'           => __( 'View Envio', THEME_NAME ),
				'add_new_item'        => __( 'Add New Envio', THEME_NAME ),
				'add_new'             => __( 'Add New', THEME_NAME ),
				'edit_item'           => __( 'Edit Envio', THEME_NAME ),
				'update_item'         => __( 'Update Envio', THEME_NAME ),
				'search_items'        => __( 'Search Envio', THEME_NAME ),
				'not_found'           => __( 'Not found', THEME_NAME ),
				'not_found_in_trash'  => __( 'Not found in Trash', THEME_NAME ),
		);
		$args_envio = array(
				'label'               => __( 'envio', THEME_NAME ),
				'description'         => __( 'Tipos de gastos de envio', THEME_NAME ),
				'labels'              => $labels_envio,
				'supports'            => array( 'title'),
				'taxonomies'          => array( 'envio_type'),
				'hierarchical'        => false,
				'public'              => true,
				'show_ui'             => true,
				'show_in_menu'        => true,
				'show_in_nav_menus'   => true,
				'show_in_admin_bar'   => true,
				'menu_position'       => 57,
				'can_export'          => true,
				'has_archive'         => true,
				'exclude_from_search' => false,
				'publicly_queryable'  => true,
				'capability_type'     => 'page',
				'menu_icon'           => 'dashicons-location'
		);
		register_post_type( 'envio', $args_envio );


		$labels_reparto = array(
				'name'                => _x( 'Repartos', 'Post Type General Name', THEME_NAME ),
				'singular_name'       => _x( 'Reparto', 'Post Type Singular Name', THEME_NAME ),
				'menu_name'           => __( 'Repartos', THEME_NAME ),
				'parent_item_colon'   => __( 'Parent reparto:', THEME_NAME ),
				'all_items'           => __( 'All Repartos', THEME_NAME ),
				'view_item'           => __( 'View reparto', THEME_NAME ),
				'add_new_item'        => __( 'Add New reparto', THEME_NAME ),
				'add_new'             => __( 'Add New', THEME_NAME ),
				'edit_item'           => __( 'Edit reparto', THEME_NAME ),
				'update_item'         => __( 'Update reparto', THEME_NAME ),
				'search_items'        => __( 'Search reparto', THEME_NAME ),
				'not_found'           => __( 'Not found', THEME_NAME ),
				'not_found_in_trash'  => __( 'Not found in Trash', THEME_NAME ),
		);
		$args_reparto = array(
				'label'               => __( 'Reparto', THEME_NAME ),
				'description'         => __( 'Zonas de reparto', THEME_NAME ),
				'labels'              => $labels_reparto,
				'supports'            => array( 'title'),
				'taxonomies'          => array( 'reparto_type'),
				'hierarchical'        => false,
				'public'              => true,
				'show_ui'             => true,
				'show_in_menu'        => true,
				'show_in_nav_menus'   => true,
				'show_in_admin_bar'   => true,
				'menu_position'       => 57,
				'can_export'          => true,
				'has_archive'         => true,
				'exclude_from_search' => false,
				'publicly_queryable'  => true,
				'capability_type'     => 'page',
				'menu_icon'           => 'dashicons-admin-site'
		);
		register_post_type( 'reparto', $args_reparto );
	}

	add_action( 'init', 'theme_post_type', 0 );
}


/**
 * *****************************************************************************
 * Theme custom functions
 * *****************************************************************************
 */

/* RESTRICT RESTAURANT ACCESS TO OTHERS PRODUCTS AND MENUS */
require_once get_template_directory() . '/inc/sw_menu_restriction.php';
/* END restrict access */

/**
* # MENUS FUNCTIONS #
* HTML and functions of admin custom pages for reports and orders
* Included functions:
*
* 	function custom_menu_pages()
* 	function show_report_daily_cash()
* 	function show_report_restaurants()
* 	function show_report_drivers()
* 	function show_report_refunds()
* 	function show_report_cancelled()
* 	function show_report_wrong_address()
* 	function show_report_timing()
* 	function show_orders_by_importance_to_restaurant()
* 	function show_orders_by_importance()
*
*/
require_once get_template_directory() . '/inc/functions-menus.php';

/**
* # API FUNCTIONS #
* Custom function for ZAS app
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
require_once get_template_directory() . '/inc/functions-api.php';

/** # AJAX FUNCTIONS #
* WP_AJAX calls for different actions
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
require_once get_template_directory() . '/inc/functions-ajax.php';

/**
* # WOOCOMMERCE FUNCTIONS #
* Functions associated with woocommerce hooks
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
require_once get_template_directory() . '/inc/functions-woocommerce.php';

/**
* # STYLE FUNCTIONS #
* Main style code for backend differentiating between user types
* Included functions:
*
*	function custom_woocommerce_backend_style()
*
*/
require_once get_template_directory() . '/inc/functions-style.php';


add_action( 'init', 'change_post_object_label', 999 );
function change_post_object_label() {
    global $wp_post_types;
    $labels = &$wp_post_types['product']->labels;
    $labels->name = 'Plato';
    $labels->singular_name = 'Plato';
    $labels->add_new = 'Añadir Plato';
    $labels->add_new_item = 'Añadir Plato';
    $labels->edit_item = 'Edit Plato';
    $labels->new_item = 'Plato';
    $labels->all_items = 'Platos';
    $labels->view_item = 'Ver Plato';
    $labels->search_items = 'Buscar Plato';
    $labels->not_found = 'Plato no encontrado';
    $labels->not_found_in_trash = 'No Plato found in Trash';
}

add_action( 'admin_menu', 'change_post_menu_label' );
function change_post_menu_label() {
	global $menu;

	$menu[26][0] = 'Platos';

	add_menu_page( 'Restaurantes', 'Restaurantes', 'manage_options', 'users.php?role=restaurante', '', 'dashicons-store', "56.083" );
	add_menu_page( 'Repartidoras', 'Repartidoras', 'manage_options', 'users.php?role=repartidora', '', 'dashicons-universal-access', "56.084" );
}

add_action( 'save_post', 'add_owner_on_new_order', 10, 3 );
function add_owner_on_new_order( $post_id, $post, $update ) {

    $slug = 'shop_order';

    if ( $slug != $post->post_type ) return;

    $order = wc_get_order( $post_id );

    $items = $order->get_items();

    if( empty( $items ) ) return;

    $item 		= array_values($items)[0];
    $product_id = $item['item_meta']['_product_id'][0];

    $product 	= get_post( $product_id );
	$author_id 	= $product->post_author;

	update_field('field_57d15d82a6b9a', $author_id, $post_id ); // dms_order_restaurant
}

/* ADD READ ONLY FEATURE TO ACF */
add_action('acf/render_field_settings/type=text', 'add_disables_to_fields');
add_action('acf/render_field_settings/type=select', 'add_disables_to_fields');
function add_disables_to_fields( $field ) {

	acf_render_field_setting( $field, array(
		'label'      => __('Read Only?','acf'),
		'instructions'  => '',
		'type'      => 'radio',
		'name'      => 'readonly',
		'choices'    => array(
			1        => __("Yes",'acf'),
			0        => __("No",'acf'),
		),
		'layout'  =>  'horizontal',
	));
}

add_filter("acf/load_field/type=text", "remove_readonly_fields_for_admin");
add_filter("acf/load_field/type=select", "remove_readonly_fields_for_admin");
function remove_readonly_fields_for_admin($field) {

	if( current_user_can( 'manage_options' ) ) {
		$field['disabled'] = 0;
	}

	return $field;
}
/* END ADD RO */

/* Add custom fields to existing acf fields with custom functionality */
add_action( 'acf/render_field', 'custom_buttons_in_acf', 10, 1 );
function custom_buttons_in_acf( $field ) {
	global $post;

	$current_order_id = ( isset( $_GET["post"] ) ) ? $_GET["post"] : $post->ID;

	if( $field["key"] == "field_576a6374d9892" ){
		$order_restaurant = get_field( "dms_order_restaurant", $current_order_id );
		$order_restaurant_id = $order_restaurant["ID"];

		if( !$order_restaurant_id ){
			$order_restaurant_id = get_current_user_id();
		}

		$order_restaurant_time = get_field( "dms_restaurant_time", "user_" . $order_restaurant_id );

		if( $order_restaurant_time ){
			echo '<p>Tiempo estimado por defecto: <strong>'. $order_restaurant_time .' min.</strong></p>';
		}
	}

	if( $field["key"] == "field_5747129addb9f" ){
		$current_user = wp_get_current_user();
		$is_restaurant = in_array( "restaurante", $current_user->roles );

		$order_status = get_field( "dms_order_status", $current_order_id );

		if( $order_status === NULL ) $order_status = array();

		$is_test = false;
		if( $is_restaurant && true == get_field( "dms_restaurant_testing", "user_" . $current_user->ID ) ){
			$is_test = true;
		}

		if( ! is_array( $order_status )  ){
			$the_order = wc_get_order( $current_order_id );
			$order_shipping_method = array_values( $the_order->get_shipping_methods() )[0]['item_meta']['method_id'][0];
		}

		?>
		<input id="order_status_placeholder" class="" name="order_status_placeholder" value="" placeholder="" readonly="readonly" type="text">
		<div class="order-action-buttons-container">
			<?php
			if( ( $order_status == "preorder" || $order_status == "order_on_hold" || is_array( $order_status ) ) && !$is_test ){
				?>
				<button type="button" class="button-primary dms-action-button button-ok" id="button-rest-accept-order" data-action="rest_accept_order">ACEPTAR PEDIDO</button>
				<?php
			}

			if( $is_test ){
				?>
				<button type="button" class="button-primary dms-action-button button-ok" id="button-do-test" data-action="order_testing">HACER TEST</button>
				<?php
			}

			if( $order_shipping_method == "local_pickup" && $order_status == "rest_has_accepted" ){
				?>
				<button type="button" class="button-primary dms-action-button button-ok" id="button-order-delivered" data-action="order_delivered">ENTREGADO</button>
				<?php
			}

			if( $order_status == "order_delivered" || $order_status == "driver_on_road" ){
				?>
				<button type="button" class="button-primary dms-action-button button-error" id="button-problem" data-action="problem">DECLARAR INCIDENCIA</button>
				<?php
			}

			if( $order_status == "preorder" || $order_status == "order_on_hold" || $order_status == "rest_has_accepted" || $order_status == "driver_has_accepted" || $order_status == "driver_in_rest" ){
				?>
				<button type="button" class="button-primary dms-action-button button-error" id="button-cancel" data-action="cancel">CANCELAR PEDIDO</button>
				<?php
			}

			if( $order_status == "problem" ){
				?>
				<a id="button-goto-problem" class="button-primary dms-action-button button-error" href="#button-return-orders">VER INCIDENCIA</a>
				<?php
			}

			if( $post->post_status != "auto-draft" && !is_array( $order_status ) ){
				?>
				<button type="button" class="button-primary dms-action-button button-do" id="button-download-receipt"  data-order_id="<?php echo $current_order_id; ?>" data-action="download">DESCARGAR TICKET</button>
				<?php
			}
			?>

			<button type="button" class="button-primary dms-action-button button-do <?php if( $order_status == "preorder" || $order_status == "order_on_hold" || is_array( $order_status ) ) echo 'button-hidden'; ?> " id="button-save-order" data-action="cancel">ACTUALIZAR DATOS DEL PEDIDO</button>

			<a id="button-return-orders" class="button-primary dms-action-button button-do" href="<?php menu_page_url( 'admin_menu_orders' ); ?>">VOLVER A PEDIDOS    <i class="fa fa-undo" aria-hidden="true"></i></a>

		</div>
		<?php
	}

	if( $field["key"] == "field_57b2c8838ac94" && $field["type"] == "post_object" ){
		global $profileuser;
		$user_id = $profileuser->ID;

		$page_id = get_field( "dms_restaurant_comment", "user_". $user_id );

		if( !$page_id || $page_id == 0 ){
			?>
			<button type="button" class="button-primary" id="button-assign-comment-page" style="margin: 20px 0;">ASIGNAR PAGINA DE COMENTARIOS</button>
			<i class="buton-instructions">* Pulse el boton para generar una pagina de comentarios/opiniones para el Restaurante.</i>

			<script type="text/javascript">
				(function($) {
					$(function () {

						$(document).ready(function() {

							$("#button-assign-comment-page").on("click", function(){

								var data = {
							        action: 'create_restaurant_comment_page',
							        restaurant_id: <?php echo $user_id; ?>,
							    };

								$.post( "<?php echo admin_url( 'admin-ajax.php' ); ?>", data, function( response ) {
						        	var data = JSON.parse( response );

						        	var restaurant_comment_page_id = data.page_id;

						        	if( restaurant_comment_page_id != false && $.isNumeric( restaurant_comment_page_id ) ){
						        		$("#acf-field_57b2c8838ac94.select2-offscreen").val( restaurant_comment_page_id );
										$("#acf-field_57b2c8838ac94.select2-offscreen").change();

										$("#button-assign-comment-page").remove();
										$(".buton-instructions").html( "La pagina de comentarios se ha generado con exito. No olvide guardar los cambios." );
						        	}

						        });
							});
						});
					});
				})(jQuery);
			</script>
			<?php
		}
	}

	if( $field["key"] == "field_57cfcfb5e482c" && ( $post->post_status != "wc-completed" && $post->post_status != "wc-cancelled" && $post->post_status != "wc-refunded" ) ){
		$saved_global_post 		= $post;
		$current_user 			= wp_get_current_user();
		$is_restaurant 			= in_array( "restaurante", $current_user->roles );
		$has_restaurant 		= false;

		$saved_restaurant 		= get_field( "dms_order_restaurant", $current_order_id );
		$saved_restaurant_id 	= $saved_restaurant["ID"];

		if( $saved_restaurant ){
			$has_restaurant = true;
		}

		?>
		<div id="selectable-products">
		<?php

		if( $is_restaurant || $has_restaurant ){

			if( $has_restaurant ){
				$selected_restaurant_id = $saved_restaurant_id;
			}

			if( $is_restaurant ){
				$selected_restaurant_id = $current_user->ID;
			}

			$restaurant_delivery_id = get_field( "dms_restaurant_delivery", "user_" . $selected_restaurant_id );
			$restaurant_shipping_id = get_field( "dms_restaurant_shipping", "user_" . $selected_restaurant_id );

			if( true == get_field( "dms_shipping_problem", $restaurant_shipping_id ) ){
				?>
				<h3 class="no_restaurant_notification problem_notification"><?php echo get_field( "dms_shipping_problem_text", $restaurant_shipping_id ); ?></h3>
				<?php
			}

			if( true == get_field( "dms_delivery_delay", $restaurant_delivery_id ) ){
				?>
				<h3 class="no_restaurant_notification problem_notification"><?php echo get_field( "dms_delivery_delay_text", $restaurant_delivery_id ); ?></h3>
				<?php
			}

			print_in_order_products( $selected_restaurant_id );
		}else{
			?>
			<h3 class="no_restaurant_notification"><?php echo __( "Selecciona un restaurante para añadir productos al pedido.", THEME_NAME ); ?></h3>
			<?php
		}
		?>
		</div>
		<?php
	}

	if( $field["key"] == "field_57d15d82a6b9a" ){
		$saved_global_post = $post;
		$current_user = wp_get_current_user();
		$is_restaurant = in_array( "restaurante", $current_user->roles );

		if( $is_restaurant ){
			?>
			<script type="text/javascript">
				(function($) {
					$(function () {

						$(document).ready(function() {
							var selected_restaurant = $("#acf-field_57d15d82a6b9a").val();

							if( !selected_restaurant ){
								$("#acf-field_57d15d82a6b9a").val( "<?php echo $current_user->ID; ?>" );
							}

						});
					});
				})(jQuery);
			</script>
			<?php
		}
	}
}
/* END custom buttons  */

/* Add custom column to woocommerce orders */
// add_filter( 'manage_edit-shop_order_sortable_columns', 'add_custom_columns' );
add_filter( 'manage_edit-shop_order_columns', 'add_custom_columns' );
function add_custom_columns( $columns ){
    $new_columns = ( is_array( $columns ) ) ? $columns : array();

    unset( $new_columns['order_actions'] );
    unset( $new_columns['customer_message'] );
    unset( $new_columns['order_notes'] );

    $new_columns['problem_type'] = '<i class="fa fa-flag" aria-hidden="true"></i>';
    // $new_columns['order_priority'] = '<i class="fa fa-clock-o" aria-hidden="true"></i>';
    $new_columns['order_actions'] = $columns['order_actions'];

    return $new_columns;
}

add_action( 'manage_shop_order_posts_custom_column', 'check_value_of_custom_columns', 2 );
function check_value_of_custom_columns( $column ){
    global $post;
    $data = get_post_meta( $post->ID );
    switch( $column ){
    	case 'problem_type':

	        $problem_type = $data['dms_order_problem_type'][0];

			if( $problem_type == "drop_food" || $problem_type == "wrong_plate" || $problem_type == "wrong_order" ){
				echo '<i class="fa fa-flag" aria-hidden="true" style="color: #FF0000;"></i>';
			}

			if( $problem_type == "forget_plate" || $problem_type == "drop_drink" || $problem_type == "wrong_drink" || $problem_type == "forget_drink" ){
				echo '<i class="fa fa-flag" aria-hidden="true" style="color: #FF8A00;"></i>';
			}

			break;

		case 'order_priority':
			$restaurant_id = $data['dms_order_restaurant'][0];
			$date_start = $data['dms_order_time_rest_has_accepted'][0];

			if( !$date_start || !$restaurant_id ){
				break;
			}

			$order_status = $data['dms_order_status'][0];

			if( $order_status != "rest_has_accepted" && $order_status != "driver_has_accepted" && $order_status != "driver_in_rest" ){
				break;
			}

			$date_start = str_replace( "[:es]", "", $date_start );
			$date_start = str_replace( "[:]", "", $date_start );

			$time_default = ( get_field( 'dms_restaurant_time', "user_" . $restaurant_id ) ) ? get_field( 'dms_restaurant_time', "user_" . $restaurant_id ) : 0;
			$time_extra = ( $data['dms_order_time_extra'][0] ) ? $data['dms_order_time_extra'][0] : 0;
			$time_extra_full = intval( $time_extra ) + intval( $time_default );

			$date_current = strtotime( current_time( 'mysql' ) );
			$date_extra = strtotime( $date_start . "+" . $time_extra_full . " minutes" );

			$date_left = round( abs( $date_current - $date_extra ) / 60, 0 );

			if( $date_current >= $date_extra ){
				$date_left = $date_left * -1;
			}

			echo $date_left;

			break;
    }
}

/* Change query to product post type in author template */
add_action( 'pre_get_posts', 'query_products_in_author_template' );
function query_products_in_author_template( $query )
{
    if ( $query->is_author ){
        $query->set( 'post_type', 'product' );
        $query->set( 'posts_per_page', '-1' );
    }
}
/* END change query */

/* Print updatable cart part html */
function dms_cart_part_updatable(){
	if ( ! defined('WOOCOMMERCE_CHECKOUT') ) {
		define( 'WOOCOMMERCE_CHECKOUT', true );
	}
	?>
		<div class="cart-updatable">
			<div class="scroll-fixer">
			    <div class="cart-text cart-products dms-cart-scroll">
			    	<form action="<?php echo WC()->cart->get_cart_url(); ?>" method="post">
					<?php if ( sizeof( WC()->cart->get_cart() ) > 0 ) : ?>
					    <ul class="dms-minicart-top-products">
					        <?php foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) :
						        $_product = $cart_item['data'];
						        // Only display if allowed
						        if ( ! apply_filters('woocommerce_widget_cart_item_visible', true, $cart_item, $cart_item_key ) || ! $_product->exists() || $cart_item['quantity'] == 0 ) continue;
						        // Get price
						        $product_price = get_option( 'woocommerce_tax_display_cart' ) == 'excl' ? $_product->get_price_excluding_tax() : $_product->get_price_including_tax();
						        $product_price = apply_filters( 'woocommerce_cart_item_price_html', woocommerce_price( $product_price ), $cart_item, $cart_item_key );
						        ?>
						        <li class="dms-mini-cart-product clearfix">
						        	<div class="product-action">
						        		<span class="product-quantity">
						        			<?php echo apply_filters( 'woocommerce_widget_cart_item_quantity', '<span class="dms-mini-cart-quantity">' . $cart_item['quantity'] . 'x</span>', $cart_item, $cart_item_key ); ?>
						        			<input type="button" class="button-quantity quantity-less" value="-"><input type="button" class="button-quantity quantity-more" value="+">
						        			<?php
												if ( $_product->is_sold_individually() ) {
													$product_quantity = sprintf( '1 <input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key );
												} else {
													$product_quantity = woocommerce_quantity_input( array(
														'input_name'  => "cart[{$cart_item_key}][qty]",
														'input_value' => $cart_item['quantity'],
														'max_value'   => $_product->backorders_allowed() ? '' : $_product->get_stock_quantity(),
														'min_value'   => '0'
													), $_product, false );
												}

												echo "<div class='input-quantity-hidden'>" . apply_filters( 'woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item ) . "</div>";
											?>
						        		</span>
						        		<span class="product-remove">
											<?php
												echo apply_filters( 'woocommerce_cart_item_remove_link', sprintf(
													'<a href="%s" class="remove" title="%s" data-product_id="%s" data-product_sku="%s">&times;</a>',
													"javascript:void(0)",
													__( 'Remove this item', 'woocommerce' ),
													esc_attr( $cart_item_key ),
													esc_attr( $_product->get_sku() )
												), $cart_item_key );
											?>
										</span>
						        	</div>
						        	<div class="product-info">
						        		<span class="product-name">
						                    <?php
												if ( ! $_product->is_visible() ) {
													echo apply_filters( 'woocommerce_cart_item_name', $_product->get_title(), $cart_item, $cart_item_key ) . '&nbsp;';
												} else {
													echo apply_filters( 'woocommerce_cart_item_name', sprintf( '<strong>%s</strong>', $_product->get_title() ), $cart_item, $cart_item_key );
												}

												// Meta data
												echo WC()->cart->get_item_data( $cart_item );
											?>
						                </span>
						                <span class="product-price"><?php echo apply_filters( 'woocommerce_widget_cart_item_price', '<span class="woffice-mini-cart-price">' . $product_price . '</span>', $cart_item, $cart_item_key ); ?></span>
						        	</div>
						        </li>
					        <?php endforeach; ?>
					    </ul><!-- end .dms-mini-cart-products -->
					<?php else : ?>
				    	<p class="dms-mini-cart-product-empty"><?php _e( 'No products in the cart.', THEME_NAME ); ?></p>
					<?php endif; ?>
					</form>
				</div>
			</div>
			<div class="info-box calc-height cart-subtotal">
			    <h3 class="text-center dms-mini-cart-subtotal"><span><?php _e( 'Subtotal', THEME_NAME ); ?></span><span id="dms-cart-subtotal"><?php echo WC()->cart->get_cart_subtotal(); ?></span></h3>
			</div>

			<?php
			$check_cart 			= WC()->cart->get_cart();
			$check_product 			= array_values( $check_cart )[0]["data"];
			$check_restaurant_id 	= $check_product->post->post_author;
			$restaurant_busy 		= get_field( "dms_restaurant_busy", $check_restaurant_id );
			$restaurant_closed 		= check_restaurant_closed( $check_restaurant_id );
			$prevent_checkout 		= false;

			if( $restaurant_closed["type"] == "closed" ) 	$prevent_checkout = true;
			if( $restaurant_busy ) 							$prevent_checkout = true;

			if( WC()->customer->has_calculated_shipping() ) {
				$user_type = "user_registered";
			}else{
				$user_type = "user_anonymous";
			}
			?>

			<div class="cart-text shipping-address-container calc-height <?php echo $user_type; ?>">
				<strong><?php _e("Current address", THEME_NAME); ?></strong>
				<div class="shipping-address">
					<?php
					$destination_address 		= WC()->customer->get_shipping_address();
					$destination_address_2 		= WC()->customer->get_shipping_address_2();
					$destination_city 			= WC()->customer->get_shipping_city();
					$destination_postcode 		= WC()->customer->get_shipping_postcode();

					echo $destination_address . " " . $destination_address_2 . ", " . $destination_city . ", " . $destination_postcode;
					?>
				</div>
			</div>

			<?php
			if( WC()->customer->has_calculated_shipping() ) {
				?>
				<input type="button" class="cart-button calc-height" id="button_open_shipping_form" value="<?php _e("Change address", THEME_NAME); ?>">
				<?php
			}else{
				?>
				<input type="button" class="cart-button calc-height" id="button_open_shipping_form" value="<?php _e("Select address", THEME_NAME); ?>">
				<?php
			}
			?>

			<div class="shipping-method cart-text calc-height <?php echo $user_type; ?>">
				<?php

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

			            if( !$prevent_checkout ){

					        wc_get_template( 'cart/cart-shipping.php', array(
					            'package'              => $package,
					            'available_methods'    => $package['rates'],
					            'show_package_details' => sizeof( $packages ) > 1,
					            'package_details'      => implode( ', ', $product_names ),
					            'package_name'         => apply_filters( 'woocommerce_shipping_package_name', sprintf( _n( 'Shipping', 'Shipping %d', ( $i + 1 ), 'woocommerce' ), ( $i + 1 ) ), $i, $package ),
					            'index'                => $i,
					            'chosen_method'        => $chosen_method
					        ) );
					    }

				        $messages = print_messages_for_calculated_shipping( $package );

				        if( $messages["limit"] ){
				        	?> <p class="shipping-message"><?php echo $messages["limit"]; ?></p> <?php
				        }

				        if( $messages["price_min"] ){
				        	?> <p class="shipping-message"><?php echo $messages["price_min"]; ?></p> <?php
				        }

				        if( $messages["price_free"] ){
				        	?> <p class="shipping-message"><?php echo $messages["price_free"]; ?></p> <?php
				        }
				    }
				?>
			</div>

			<div class="info-box cart-total calc-height <?php echo $user_type; ?>">
			    <h3 class="text-center dms-mini-cart-total"><span><?php _e( 'Total', THEME_NAME ); ?></span><span id="dms-cart-total"><?php WC()->cart->calculate_totals(); echo WC()->cart->get_total(); ?></span></h3>
			</div>
			<?php

			if( WC()->customer->has_calculated_shipping() && !WC()->cart->is_empty() ) {
				$user_type = "user_registered";
			}else{
				$user_type = "user_anonymous";
			}
			?>

			<?php if( !$prevent_checkout ){ ?>
				<input type="submit" class="cart-button calc-height <?php echo $user_type; ?>" id="button_save_checkout" value="<?php _e("Checkout", THEME_NAME); ?>">
			<?php } ?>
		</div>
	<?php
}
/* END updatable part */

/* Floating cart */
function dms_wc_print_mini_cart() {
	global $woocommerce, $wp_query;

	if ( ! defined('WOOCOMMERCE_CHECKOUT') ) {
		define( 'WOOCOMMERCE_CHECKOUT', true );
	}

	$map_instruction = get_field("dms_options_map_instruction", 109);

    ?>
    <div id="dms-minicart" class="state-initial">
	    <h2 class="cart-title cart-text calc-height"><?php _e("Your order", THEME_NAME); ?></h2>

    	<?php
			$current_restaurant_id 	= ( $wp_query->query_vars["page_id"] ) ? $wp_query->query_vars["page_id"] : $wp_query->query_vars["author"];
			$previous 				= get_field( "dms_restaurant_previous", "user_" . $current_restaurant_id );
			$has_preorder 			= ( isset( WC()->session->preorder ) ) ? true : false;
			$restaurant_closed 		= check_restaurant_closed( $current_restaurant_id );
			$closed_type 			= false;

			if( $restaurant_closed ){
				switch ( $restaurant_closed["type"] ) {

					case 'closed_until':
						$closed_type = $restaurant_closed["type"];
						$has_preorder = true;
						break;

					case 'closed':
						$closed_type = $restaurant_closed["type"];
						break;
				}
			}

    		if( ( $previous || $previous == NULL ) && $closed_type != "closed" ){
    			?>
    			<div class="cart-previous cart-text calc-height">
    				<div class="order-type-selector">
    					<span class="mini-cart-radio">
    						<input type="radio" name="dms_radio_previous_order" id="radio_order_now" <?php if( !$has_preorder ){ echo 'class="active"'; checked( true ); } if( $closed_type == "closed_until" ){ disabled( true ); } ?> value="0">
    						<label for="radio_order_now"><?php _e( "Order now", THEME_NAME ); ?></label>
    					</span>
    					<span class="mini-cart-radio">
    						<input type="radio" name="dms_radio_previous_order" id="radio_order_previous" <?php if( $has_preorder ){ echo 'class="active"'; checked( true ); } ?> value="1">
    						<label for="radio_order_previous"><?php _e( "Pre-Order", THEME_NAME ); ?></label>
    					</span>
    				</div>
    				<div class="order-time-selector <?php if( !$has_preorder ){ echo 'hidden'; } ?>">
    					<label for="dms_order_previous_hour"><?php _e( "Delivery from", THEME_NAME ); ?></label>
    					<?php echo print_restaurant_previous_order_time_select( $current_restaurant_id ); ?>
    				</div>
    			</div>
    			<?php
    		}
    	?>

	    <?php dms_cart_part_updatable(); ?>

		<div class="shipping-container popup-background">
			<div class="user-shipping popup-box dms-container">
				<a href="javascript:void(0)" class="button_close_popup close-x">&times;</a>
				<h2 class="popup-title"><?php _e("Shipping address", THEME_NAME); ?></h2>
				<form action="<?php echo WC()->cart->get_checkout_url(); ?>" id="dms-goto-checkout-form" method="post">
					<input id="ship-to-different-address-checkbox" class="input-checkbox" name="ship_to_different_address" value="1" type="hidden">
					<input id="make-previous-order" class="input-checkbox" name="make_previous_order" value="0" type="hidden">
					<input id="previous-order-time" class="input-checkbox" name="previous_order_time" value="" type="hidden">
					<div id="addresses">
				    	<?php
			    		global $wma_current_address;
						$checkout = WC()->checkout();
						$shipFields = $checkout->checkout_fields['shipping'];

			    		if( is_user_logged_in() ){
							foreach ( $shipFields as $key => $field ) {
								$field['id'] = $key;
								woocommerce_form_field( $key, $field, $checkout->get_value( $field['id'] ) );
							}
			    		}else{
							foreach ( $shipFields as $key => $field ) {
								$field['id'] = $key;

								if( $key == "shipping_state" ){
									woocommerce_form_field( $key, $field, $checkout->get_value( $field['id'] ) );
								}elseif( $key == "shipping_country" ){
									woocommerce_form_field( $key, $field, $checkout->get_value( $field['id'] ) );
								}else{
									woocommerce_form_field( $key, $field, WC()->customer->__get( $field['id'] ) );
								}
							}
			    		}
						?>
					</div>
					<?php if( $map_instruction ){ ?><div class="shipping-instructions"><?php echo $map_instruction; ?></div><?php } ?>
					<div id="mapCanvas"></div>
					<div class="popup-buttons">
						<input type="button" id="button_set_shipping_address" class="button-simple" value="<?php _e('Confirm address', THEME_NAME); ?>">
						<input type="button" class="button_close_popup button-simple" value="<?php _e('Cancel', THEME_NAME); ?>">
					</div>
				</form>
		    </div>
		</div>
	</div>
    <?php
}
/* END cart*/

/* Check added product and cart product restaurant */
function dms_check_product_restaurant( $product_id ){
	$added_product = get_product( $product_id );
	$added_product_restaurant = $added_product->post->post_author;

	$cart = WC()->cart->get_cart();
	$cart_product = array_values( $cart )[0]["data"];
	$cart_product_restaurant = $cart_product->post->post_author;

	if( $cart_product_restaurant && ( $added_product_restaurant != $cart_product_restaurant ) ) {
		WC()->cart->empty_cart();
	}
}
/* END check restaurant */

/* Print shipping messages */
function print_messages_for_calculated_shipping( $package ){
	$data = array();

	$restaurant_id 					= array_values( $package["contents"])[0]["data"]->post->post_author;

	if( !$restaurant_id ){
		$data["limit"] = __("Add a product to your cart to get a delivery fee.", THEME_NAME);

		return $data;
	}

	$shipping_id 		= get_field( "dms_restaurant_shipping", "user_" . $restaurant_id );
	$problem 			= get_field( "dms_shipping_problem", $shipping_id );

	if( $problem ){
		$problem_text 	= get_field( "dms_shipping_problem_text", $shipping_id );
		$data["limit"] 	= $problem_text;

		return $data;
	}

	$is_closed = check_restaurant_closed( $restaurant_id, $package );
	if( $is_closed ){
		$data["limit"] = $is_closed["label"];

		return $data;
	}

	require_once WP_PLUGIN_DIR . "/woocommerce-distance-rate-shipping/includes/class-wc-shipping-distance-rate.php";
	$distance_rate_class 			= new WC_Shipping_Distance_Rate();

	$center 						= get_field( "dms_shipping_center", $shipping_id );
	$radius 						= floatval( get_field( "dms_shipping_radius", $shipping_id ) );
	$sections 						= get_field( "dms_shipping_section", $shipping_id );

	$address_origin 				= $distance_rate_class->get_shipping_address_string( $package );
	$address_destination 			= $distance_rate_class->get_customer_address_string( $package );

	$rounding_precision 			= apply_filters( 'woocommerce_distance_rate_shipping_distance_rounding_precision', 1 );

	// CHECK IF CLIENT IN ZONE AND SAVE DISTANCE BETWEEN CLIENT AND CENTRAL
	$in_limit 						= false;

	$limit_distance 				= $distance_rate_class->get_api()->get_distance( $address_destination, $center, false, 'driving', 'none', 'metric' );

	if ( ! isset( $limit_distance->rows[0] ) || 'OK' !== $limit_distance->rows[0]->elements[0]->status ) {
		$data["limit"] = __("We couldn't find your address. Please, try to write it more specifically.");

		return  $data;
	}

	$limit_distance_value 			= $limit_distance->rows[0]->elements[0]->distance->value;
	$limit_distance 				= round( $limit_distance_value / 1000, $rounding_precision );

	if( $limit_distance <= $radius ){
		$in_limit = true;
	}

	$sugest_area_link = get_permalink( 3788 );

	if( !$in_limit ){
		// $data["limit"] = __("We don't deliver to your address, but you can pick up your order at the restaurant. <a href='%s' target='_blank'>Tell us if you want a delivery to your area.</a>", THEME_NAME);
		$data["limit"] = sprintf( __( "We don't deliver to your address, but you can pick up your order at the restaurant. <a href='%s' target='_blank'>Tell us if you want a delivery to your area.</a>", THEME_NAME ), $sugest_area_link );
		return  $data;
	}

	$restaurant_map 				= get_field( "dms_restaurant_map", "user_" . $restaurant_id );
	$restaurant_latlng 				= $restaurant_map["lat"] . "," . $restaurant_map["lng"];

	// SAVE DISTANCE BETWEEN CLIENT AND RESTAURANT
	$distance 						= $distance_rate_class->get_api()->get_distance( $address_origin, $address_destination, false, 'driving', 'none', 'metric' );

	if ( ! isset( $distance->rows[0] ) || 'OK' !== $distance->rows[0]->elements[0]->status ) {
		$data["limit"] = __("We couldn't find your address. Please, try to write it more specifically.", THEME_NAME);

		return  $data;
	}

	$distance_text 					= ' (' . $distance->rows[0]->elements[0]->distance->text . ')';
	$distance_value 				= $distance->rows[0]->elements[0]->distance->value;
	$distance 						= round( $distance_value / 1000, $rounding_precision );

	// SAVE DISTANCE BETWEEN CENTRAL AND RESTAURANT
	$restaurant_distance 			= $distance_rate_class->get_api()->get_distance( $center, $address_origin, false, 'driving', 'none', 'metric' );

	if ( ! isset( $restaurant_distance->rows[0] ) || 'OK' !== $restaurant_distance->rows[0]->elements[0]->status ) {
		$data["limit"] = __("We couldn't find your address. Please, try to write it more specifically.", THEME_NAME);

		return  $data;
	}

	$restaurant_distance_value 		= $restaurant_distance->rows[0]->elements[0]->distance->value;
	$restaurant_distance 			= round( $restaurant_distance_value / 1000, $rounding_precision );

	$distance 						+= $restaurant_distance + $limit_distance;

	// CHECK DISTANCE FOR PRICE
	$order_cost = 0;
	foreach ( $package["contents"] as $product ) {
		$order_cost += $product["line_total"];
	}

	foreach ( $sections as $key => $rule ) {

		if( $rule_found ){
			continue;
		}

		$min_match = false;
		$max_match = false;
		$shipping_cost = null;

		$rule_distance_min 	=  ( isset( $rule['dms_section_distance_minimum'] ) ) ? floatval( $rule['dms_section_distance_minimum'] ) : false;
		$rule_distance_max 	=  ( isset( $rule['dms_section_distance_maximum'] ) ) ? floatval( $rule['dms_section_distance_maximum'] ) : false;

		if ( !$rule_distance_min || $distance >= $rule_distance_min ) {
			$min_match = true;
		}

		if ( !$rule_distance_max || $distance <= $rule_distance_max ) {
			$max_match = true;
		}

		if ( $min_match && $max_match ) {

			$rule_price_free =  ( isset( $rule['dms_section_free'] ) ) ? floatval( $rule['dms_section_free'] ) : false;
			$rule_price_min =  ( isset( $rule['dms_section_order'] ) ) ? floatval( $rule['dms_section_order'] ) : false;

			if( $rule_price_free && $order_cost < $rule_price_free ){
				$data["price_free"] = sprintf( __( "Free delivery on orders above %s€. Add %s€ to get free delivery.", THEME_NAME ), $rule_price_free, ( $rule_price_free - $order_cost ) );
			}

			if ( $rule_price_min && $order_cost < $rule_price_min ) {
				$data["price_min"] = sprintf( __( "Minimum order cost to get a delivery service is %s€", THEME_NAME ), $rule_price_min );
			}

			$rule_found = true;
		}
	}

	if( !$rule_found ){

		if( $distance >= $rule_distance_max ){
			// $data["limit"] = __("We don't deliver to your address, choose a place nearer to the restaurant. Or you can pick up your order at the restaurant.", THEME_NAME);
			$data["limit"] = sprintf( __( "We don't deliver to your address, choose a place nearer to the restaurant. Or you can pick up your order at the restaurant. <a href='%s' target='_blank'>Tell us if you want a delivery to your area.</a>", THEME_NAME ), $sugest_area_link );
		}

		if( $distance <= $rule_distance_min ){
			$data["limit"] = __("MIN: We don't deliver to your address, but you can pick up your order at the restaurant.", THEME_NAME);
		}

		if( $order_cost == 0 ){
			$data["limit"] = __("Add a product to your cart to get a delivery fee.", THEME_NAME);
		}
	}

	return $data;
}
/* END print shipping */

/* ACF google map api include api key (doesn't work)*/
function dms_acf_google_map_api( $api ){
	$api['key'] = 'AIzaSyBIO8BKXqA894JAPH84-Fwb4znwjpekPMA';

	return $api;
}
add_filter('acf/fields/google_map/api', 'dms_acf_google_map_api');

add_filter('acf/settings/google_api_key', function () {
    return 'AIzaSyBIO8BKXqA894JAPH84-Fwb4znwjpekPMA';
});
/* END maps api */

/* Get restaurant data for restaurants list */
function get_restaurant_data_to_print_list( $restaurants ){
	$restaurants_data = array();

	if ( ! empty( $restaurants->results ) ) {
		foreach ( $restaurants->results as $index => $restaurant ) {

			$restaurant_id 						= $restaurant->ID;
			$acf_id 							= "user_" . $restaurant_id;

			$restaurant_logo 					= wp_get_attachment_image_src( get_field( "dms_restaurant_logo", $acf_id ), 'medium' ); $restaurant_logo = $restaurant_logo[0];
			$restaurant_name 					= get_field( "dms_restaurant_name", $acf_id );
			$restaurant_redirect				= get_author_posts_url( $restaurant_id );
			$restaurant_types 					= ( get_field( "dms_restaurant_types", $acf_id ) ) ? get_field( "dms_restaurant_types", $acf_id ) : array();

			$restaurant_type_string				= "";
			foreach ($restaurant_types as $key => $restaurant_type) {

				$type = $restaurant_type["dms_restaurant_type"];
				$type_label = get_restaurant_category_label( $type );

				if( $key == 0 ){
					$restaurant_type_string .= $type_label . " ";
				}

				if( $key == 1 ){
					$restaurant_type_string .= "- " . $type_label;
				}

				if( $key > 1 ){
					$restaurant_type_string .= ", " . $type_label;
				}

			}

			$restaurant_street 					= get_field( "dms_restaurant_street", $acf_id );
			$restaurant_street_number 			= get_field( "dms_restaurant_street_number", $acf_id );
			$restaurant_pc 						= get_field( "dms_restaurant_pc", $acf_id );
			$restaurant_city 					= get_field( "dms_restaurant_city", $acf_id );
			$restaurant_province 				= get_field( "dms_restaurant_province", $acf_id );

			$restaurant_address 				= $restaurant_street . " " . $restaurant_street_number . ", " . $restaurant_city;

			$restaurant_closed = check_restaurant_closed( $restaurant_id );

			if( !$restaurant_closed ){
				$array_key = "A - " . $index;
			}else{
				switch ( $restaurant_closed["type"] ) {
					case 'nodel_until':
						$array_key = "B - " . $index;
						break;

					case 'nodel':
						$array_key = "C - " . $index;
						break;

					case 'closed_until':
						$array_key = "D - " . $index;
						break;

					case 'closed':
						$array_key = "E - " . $index;
						break;
				}
			}

			$restaurants_data[ $array_key ] = array(
				"restaurant_redirect" 		=> $restaurant_redirect,
				"restaurant_logo" 			=> $restaurant_logo,
				"restaurant_name" 			=> $restaurant_name,
				"restaurant_type_string" 	=> $restaurant_type_string,
				"restaurant_address" 		=> $restaurant_address,
				"restaurant_closed" 		=> $restaurant_closed["label"],
			);
		}
	}

	return $restaurants_data;
}
/* END get data */

/* Check if restaurant is delivering at the moment */
function check_restaurant_closed( $restaurant_id, $package = array() ){

	$restaurant_closed   	= false;
	$current_time 			= ( isset( $package["preorder"] ) ) ? strtotime( $package["preorder"] ) : current_time( 'timestamp' );
	$current_week       	= date( 'w', $current_time );
	$restaurant_schedule    = ( get_field( "dms_restaurant_schedule", "user_" . $restaurant_id ) ) ? get_field( "dms_restaurant_schedule", "user_" . $restaurant_id ) : array();

	$week_found = false;
	if( !empty( $restaurant_schedule ) ){
		foreach ( $restaurant_schedule as $key => $restaurant_hour ) {

			if( $week_found ) continue;

			$day = $restaurant_hour["dms_restaurant_hours_day"];

			if( $day == $current_week ){
				$morning_start    = $restaurant_hour["dms_restaurant_hours_morning_start"];
				$morning_end      = $restaurant_hour["dms_restaurant_hours_morning_end"];
				$afternoon_start  = $restaurant_hour["dms_restaurant_hours_afternoon_start"];
				$afternoon_end    = $restaurant_hour["dms_restaurant_hours_afternoon_end"];

				$time_morning_start    = ( $morning_start ) ? strtotime( $morning_start ) : false;
				$time_morning_end      = ( $morning_end ) ? strtotime( $morning_end ) : false;
				$time_afternoon_start  = ( $afternoon_start ) ? strtotime( $afternoon_start ) : false;
				$time_afternoon_end    = ( $afternoon_end ) ? strtotime( $afternoon_end ) : false;

				if( $current_time < $time_morning_start ){
					$restaurant_closed["type"] = "closed_until";
					$restaurant_closed["label"] = sprintf( __( "Restaurant is closed until %s.", THEME_NAME ), $morning_start );
				}

				if( $time_morning_end && $time_afternoon_start ){

					if( $current_time >= $time_morning_end && $current_time < $time_afternoon_start ){
						$restaurant_closed["type"] = "closed_until";
						$restaurant_closed["label"] = sprintf( __( "Restaurant is closed until %s.", THEME_NAME ), $afternoon_start );
					}
				}

				if( $current_time >= $time_afternoon_end ){
					$restaurant_closed = __( "Restaurant is already closed.", THEME_NAME );
				}

				$week_found = true;
			}
		}
	}

	if( !$week_found ){
		$restaurant_closed["type"] = "closed";
		$restaurant_closed["label"] = __( 'Restaurant is closed today.', THEME_NAME );
	}

	if( $restaurant_closed ){
		return $restaurant_closed;
	}

	$delivery_id 		= get_field( "dms_restaurant_delivery", "user_" . $restaurant_id );
	$restaurant_hours   = ( get_field( "dms_restaurant_hours", $delivery_id ) ) ? get_field( "dms_restaurant_hours", $delivery_id ) : array();
	$week_found 		= false;

	if( !empty( $restaurant_hours ) ){
		foreach ( $restaurant_hours as $key => $restaurant_hour ) {

			if( $week_found ) continue;

			$day = $restaurant_hour["dms_restaurant_hours_day"];

			if( $day == $current_week ){
				$morning_start    = $restaurant_hour["dms_restaurant_hours_morning_start"];
				$morning_end      = $restaurant_hour["dms_restaurant_hours_morning_end"];
				$afternoon_start  = $restaurant_hour["dms_restaurant_hours_afternoon_start"];
				$afternoon_end    = $restaurant_hour["dms_restaurant_hours_afternoon_end"];

				$time_morning_start    = ( $morning_start ) ? strtotime( $morning_start ) : false;
				$time_morning_end      = ( $morning_end ) ? strtotime( $morning_end ) : false;
				$time_afternoon_start  = ( $afternoon_start ) ? strtotime( $afternoon_start ) : false;
				$time_afternoon_end    = ( $afternoon_end ) ? strtotime( $afternoon_end ) : false;

				if( $current_time < $time_morning_start ){
					$restaurant_closed["type"] = "nodel_until";
					$restaurant_closed["label"] = sprintf( __( "Until %s we don't have a delivery service. But you can pickup your order at the restaurant.", THEME_NAME ), $morning_start );
				}

				if( $time_morning_end && $time_afternoon_start ){

					if( $current_time >= $time_morning_end && $current_time < $time_afternoon_start ){
						$restaurant_closed["type"] = "nodel_until";
						$restaurant_closed["label"] = sprintf( __( "Until %s we don't have a delivery service. But you can pickup your order at the restaurant.", THEME_NAME ), $afternoon_start );
					}
				}

				if( $current_time >= $time_afternoon_end ){
					$restaurant_closed["type"] = "nodel";
					$restaurant_closed["label"] = __( "We already don't have a delivery service. But you can pickup your order at the restaurant.", THEME_NAME );
				}

				$week_found = true;
			}
		}
	}

	if( !$week_found ){
		$restaurant_closed["type"] = "nodel";
		$restaurant_closed["label"] = __( "We don't have a delivery service today. But you can pickup your order at the restaurant.", THEME_NAME );
	}

	return $restaurant_closed;
}
/* END check delivering */

/* Print restaurant hours selector for previous orders */
function print_restaurant_previous_order_time_select( $restaurant_id ){
	$output   				= false;
	$current_week       	= date( 'w', strtotime( current_time( 'mysql' ) ) );
	$current_hour       	= strtotime( date( 'H:i', strtotime( current_time( 'mysql' ) ) ) );
	$current_start 			= strtotime( "+60 MINUTES", $current_hour );
	$restaurant_schedule   	= ( get_field( "dms_restaurant_schedule", "user_" . $restaurant_id ) ) ? get_field( "dms_restaurant_schedule", "user_" . $restaurant_id ) : array();

	$week_found = false;
	if( !empty( $restaurant_schedule ) ){
		foreach ( $restaurant_schedule as $key => $restaurant_hour ) {

			if( $week_found ) continue;

			$day = $restaurant_hour["dms_restaurant_hours_day"];

			if( $day == $current_week ){
				$morning_start    		= $restaurant_hour["dms_restaurant_hours_morning_start"];
				$morning_end      		= $restaurant_hour["dms_restaurant_hours_morning_end"];
				$afternoon_start  		= $restaurant_hour["dms_restaurant_hours_afternoon_start"];
				$afternoon_end    		= $restaurant_hour["dms_restaurant_hours_afternoon_end"];

				$time_morning_start    	= ( $morning_start ) 	? strtotime( "+60 MINUTES", strtotime( $morning_start ) ) : false;
				$time_morning_end      	= ( $morning_end ) 		? strtotime( "-60 MINUTES", strtotime( $morning_end ) ) : false;
				$time_afternoon_start  	= ( $afternoon_start ) 	? strtotime( "+60 MINUTES", strtotime( $afternoon_start ) ) : false;
				$time_afternoon_end    	= ( $afternoon_end ) 	? strtotime( "-60 MINUTES", strtotime( $afternoon_end ) ) : false;

				$hours 					= date('H', $current_start );
				$minutes 				= date('i', $current_start );
				$minutes 				= ( $minutes < 45 ) ? $minutes - ( $minutes % 15 ) + 15 : $minutes - ( $minutes % 15 );
				$quarter_current_start 	= strtotime( $hours . ":" . $minutes );

				if( $time_morning_start < $current_start ){
					$select_time 	= $quarter_current_start;
				}else{
					$select_time 	= $time_morning_start;
				}

				$output = '<select id="dms_order_previous_hour">';
				while ( $select_time <= $time_afternoon_end ) {

					if( $time_morning_end && $time_afternoon_start && $select_time > $time_morning_end && $select_time < $time_afternoon_start ){

						if( $time_afternoon_start < $current_start ){
							$select_time 	= $quarter_current_start;
						}else{
							$select_time 	= $time_afternoon_start;
						}
					}

					$formated_date 	= date( "Y-m-d H:i:s", $select_time );
					$formated_time 	= date( "H:i", $select_time );

					$output .= '<option value="'. $formated_date .'">'. $formated_time .'</option>';

					$select_time = strtotime( "+15 MINUTES", $select_time );
				}
				$output .= '</select>';

				$week_found = true;
			}
		}
	}

	return $output;
}
/* END print select */

/* SMS Notification with Mensatek api */
function mensatek_sms_envia_sms( $configuracion, $telefono, $mensaje ) {
	$respuesta = wp_remote_get( "https://api.mensatek.com/sms/v5/enviar.php?Correo=" . $configuracion['username_mensatekSMS'] . "&Passwd=" . $configuracion['password_mensatekSMS'] . "&Destinatarios=" . $telefono . "&Mensaje=" . $mensaje . "&Remitente=" . $configuracion['sender ID']. "&Resp=JSON" );
}

function mensatek_sms_check_phone( $phone ) {
	$prefijo = "34";
	$prefijo_internacional = "34";

	$phone = str_replace( array( '+','-' ), '', filter_var( $phone, FILTER_SANITIZE_NUMBER_INT ) );

	preg_match( "/(\d{1,4})[0-9.\- ]+/", $phone, $prefijo );
	if ( isset( $prefijo_internacional ) ) {
		if ( strpos( $prefijo[1], $prefijo_internacional ) === false ) {
			$phone = $prefijo_internacional . $phone;
		}
	}

	return $phone;
}

function mensatek_sms_set_message_parameters( $order, $notificacion = false ) {
	global $woocommerce;

	$order = new WC_Order( $order );
	$order_id = $order->id;
	$order_restaurant = get_field( "dms_order_restaurant", $order_id );

	$restaurant_id = $order_restaurant->id;
	$restaurant_phone = get_field( "dms_restaurant_phone_main", "user_" . $restaurant_id );

	$phone_destination = mensatek_sms_check_phone( $restaurant_phone );
	$message = "Nuevo pedido: #" . $order_id;
	$message = urlencode( html_entity_decode( $message, ENT_QUOTES, "UTF-8" ) );

	$parameters = array();
	$parameters['servicio'] = "mensatekSMS";
	$parameters['username_mensatekSMS'] = urlencode( "bnavarro@deideasmarketing.com" );
	$parameters['password_mensatekSMS'] = 6064551;
	$parameters['sender ID'] = urlencode( html_entity_decode( "ZASCOM", ENT_QUOTES, "UTF-8" ) );

	mensatek_sms_envia_sms( $parameters, $phone_destination, $message );
}
/* END sms notification */

/* Android push notifications on restaurant accept order AND set custom times */
add_action( 'acf/save_post', 'change_order_custom_status_on_update', 1 );
function change_order_custom_status_on_update( $post_id ){
	// Check if status mofied
	if ( empty( $_POST['acf'] ) ) {
		return;
	}

	$prev_value = get_field('dms_order_status', $post_id);

	// Check if rest has accepted
	$acf = $_POST['acf'];
	$current_time = current_time( 'Y-m-d H:i:s' );

	if( ( $new_driver_id = $acf['field_57d15d29a6b98'] ) !== ( $old_driver_id = get_post_meta( $post_id, "dms_order_driver", true ) ) ){

		if( $new_driver_id && $old_driver_id ){
			$title 		= "Te han asignado nuevo pedido";
			$message 	= "Nuevo pedido: #" . $post_id;
			push_notification_to_single_driver( $new_driver_id, $message, $title );
		}
	}

	if( $acf['field_5747129addb9f'] == "problem" && $prev_value != "problem" ){
		$_POST['acf']['field_576a57d7aa7c9'] = $current_time;
		$title 		= "Incidencia";
		$message 	= "Incidencia con tu pedido";
		$driver_id 	= $acf['field_57d15d29a6b98'];
		push_notification_to_single_driver( $driver_id, $message, $title );
		return;
	}

	if( ( $acf['field_5747129addb9f'] == "cancelled_return_money" && $prev_value != "cancelled_return_money" ) || ( $acf['field_5747129addb9f'] == "order_cancelled" && $prev_value != "order_cancelled" ) ){
		$_POST['acf']['field_580a0ea69b9cb'] = $current_time;
		return;
	}

	if( $acf['field_5747129addb9f'] == "rest_has_accepted"  ){

		if( $prev_value != "rest_has_accepted" ){
			$_POST['acf']['field_576a56edaa7c4'] = $current_time;

			dms_send_mail_to_customer_on_rest_accepted( $post_id );
		}

		$order 				= wc_get_order( $post_id );
		$shipping_items 	= $order->get_items( 'shipping' );
		$shipping_id 		= $shipping_items[0]['method_id'];

		if( $shipping_id != "local_pickup" ){
			$notification_sent = get_post_meta( $post_id, "dms_order_notification_sent", true );

			if( $notification_sent != 1 ){
				$driver_id 	= $acf['field_57d15d29a6b98'];

				if( $driver_id != "" ){
					$_POST['acf']['field_5747129addb9f'] = "driver_has_accepted";
					$_POST['acf']['field_576a574daa7c5'] = $current_time;
					$title 		= "Te han asignado nuevo pedido";
					$message 	= "Nuevo pedido: #" . $post_id;
					push_notification_to_single_driver( $driver_id, $message, $title );
				}else{
					$title 		= "Nuevo pedido";
					$message 	= "Nuevo pedido: #" . $post_id;
					push_notification_to_all_drivers( $post_id, $message, $title );
				}

				update_post_meta( $post_id, "dms_order_notification_sent", 1 );
			}
		}

		return;
	}

	if( $acf['field_5747129addb9f'] == "order_delivered" && $prev_value != "order_delivered" ){
		$_POST['acf']['field_576a57bcaa7c8'] = $current_time;
		return;
	}
}

function push_notification_to_all_drivers( $post_id, $message, $title ) {

	$order 				= wc_get_order( $post_id );
	$shipping_items 	= $order->get_items( 'shipping' );

	$order_shipping_method_id = $shipping_items[0]['method_id'];

	if( $order_shipping_method_id == "local_pickup" ){
		return;
	}


	if ( ! defined('API_ACCESS_KEY') ) {
		define( "API_ACCESS_KEY", "AIzaSyBEW7Fwnf9UDMnm8n_HjAohvPHlVtqbSYI" );
	}

	if ( ! defined('GOOGLE_FCM_URL') ) {
		define( "GOOGLE_FCM_URL", "https://fcm.googleapis.com/fcm/send" );
	}

	global $wpdb;

	$registrationIds = array();

	$devices_data = $wpdb->get_results( "SELECT * FROM 042lvG4_devices;" );

	global $dms;
	$driver_max_accept = $dms["driver_max_accept"];

	foreach( $devices_data as $key => $device ){
		$device_id 	= $device->registration_id;
		$user_id 	= $device->user_id;

		$active 	= get_user_meta( $user_id, "dms_driver_active", true );

		$orders_args = array(
			'post_type'  => 'shop_order',
			'posts_per_page' 	=> ( intval( $driver_max_accept ) + 1 ),
			'meta_query' => array(
				array(
					'key'     => 'dms_order_driver',
					'value'   => $user_id,
				),
                array(
                    'key' => 'dms_order_status',
                    'value' => array( 'driver_has_accepted', 'driver_in_rest', 'driver_on_road', 'problem' ),
                    'compare' => 'IN',
                ),
			),
		);
		$orders 		= new WP_Query( $orders_args );
		$orders_count 	= $orders->post_count;

		if( !in_array( $device_id, $registrationIds ) && $active == 1 && $orders_count <= $driver_max_accept ){
			array_push( $registrationIds, $device_id);
		}
	}

	if( empty( $registrationIds ) ){
		return;
	}

	// $message = "Nuevo pedido: #" . $post_id;

    $fields = array(
		'registration_ids' 			=> $registrationIds,
		'priority'					=> "high",
        'notification'              => array( "title" => $title, "body" => $message, 'sound'	=> 'default' ),
    );

    $headers = array(
        'Authorization: key=' . API_ACCESS_KEY,
        'Content-Type: application/json'
    );

    $ch = curl_init();
    curl_setopt( $ch, CURLOPT_URL, GOOGLE_FCM_URL );
    curl_setopt( $ch, CURLOPT_POST, true );
    curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
    curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 0 );
    curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
    curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode( $fields ) );

    $result = curl_exec( $ch );
    if ( $result === FALSE ) {
        die( 'Problem occurred: ' . curl_error( $ch ) );
    }

    curl_close( $ch );

    // echo $result;
    return true;
}
/* END push notifications */

/* Notification to single driver */
function push_notification_to_single_driver( $driver_id, $messsage = "Nuevo aviso", $title = "Control: Nuevo aviso" ) {

	if ( ! defined('API_ACCESS_KEY') ) {
		define( "API_ACCESS_KEY", "AIzaSyBEW7Fwnf9UDMnm8n_HjAohvPHlVtqbSYI" );
	}

	if ( ! defined('GOOGLE_FCM_URL') ) {
		define( "GOOGLE_FCM_URL", "https://fcm.googleapis.com/fcm/send" );
	}

	global $wpdb;

	$registrationIds = array();

	$devices_data = $wpdb->get_results( "SELECT * FROM 042lvG4_devices WHERE user_id = " . $driver_id . ";" );

	$registration_id = $devices_data[0]->registration_id;

    $fields = array(
		'to' 				=> $registration_id,
		'priority'			=> "high",
        'notification'      => array( "title" => $title, "body" => $message, 'sound'	=> 'default' ),
    );

    $headers = array(
        'Authorization: key=' . API_ACCESS_KEY,
        'Content-Type: application/json'
    );

    $ch = curl_init();
    curl_setopt( $ch, CURLOPT_URL, GOOGLE_FCM_URL );
    curl_setopt( $ch, CURLOPT_POST, true );
    curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
    curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 0 );
    curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
    curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode( $fields ) );

    $result = curl_exec( $ch );
    if ( $result === FALSE ) {
       $result = curl_error( $ch );
    }

    curl_close( $ch );

    // echo $result;
    return $result;
}
/* END push notifications */

/* Send custom email to order customer */
function dms_send_mail_to_customer_on_rest_accepted( $order_id ){
	$order 			= wc_get_order( $order_id );
	$custom_email 	= $order->billing_email;
	$shipping_items = $order->get_items( 'shipping' );
	$restaurant 	= get_field( "dms_order_restaurant", $order_id );
	$restaurant_id 	= $restaurant["ID"];

	$time_extra 			= get_field( "dms_order_time_extra", $order_id );
	$time_extra_default 	= ( get_field( 'dms_restaurant_time', "user_" . $restaurant_id ) ) ? get_field( 'dms_restaurant_time', "user_" . $restaurant_id ) : 0;
	$time_extra_delivery 	= 15;

	$email_from = "info@zascomidaentuboca.com";
	$email_bcc 	= "";

	$subject = "Tu pedido #" . $order_id . " ha sido aceptado.";
	$message = "El restaurante ha aceptado tu pedido y procederá a prepararlo.";

	foreach( $shipping_items as $el ){
		$order_shipping_method_id = $el['method_id'];
	}

	if( $order_shipping_method_id == "local_pickup" ){
		$time_extra_full = intval( $time_extra ) + intval( $time_extra_default );
		$message 		.= "<br/>Lo podrás recoger aproximadamente en " . $time_extra_full . " minutos.";
	}else{
		$time_extra_full = intval( $time_extra ) + intval( $time_extra_default ) + intval( $time_extra_delivery );
		$message 		.= "<br/>Lo tendras en casa aproximadamente en " . $time_extra_full . " minutos.";

		$restaurant_delivery_id = get_field( "dms_restaurant_delivery", "user_" . $restaurant_id );
		if( true == get_field( "dms_delivery_delay", $restaurant_delivery_id ) ){
			$message .= "<br/><br/>AVISO: " . get_field( "dms_delivery_delay_text", $restaurant_delivery_id );
		}
	}

	global $dms_emails;
	$dms_emails->send( $subject, "ZAS Comida en tu boca", $email_from, $email_bcc, $custom_email, $message );
}
/* END send email */

/* Custom author archive comments redirect */
add_filter('comment_post_redirect', 'redirect_after_comment');
function redirect_after_comment($location){
	global $wpdb;
	return $_SERVER["HTTP_REFERER"];
}

add_filter( 'comment_reply_link', 'edit_reply_comment_url', 10, 4 );
function edit_reply_comment_url( $link, $args, $comment, $post ){
	if ( get_option( 'comment_registration' ) && !is_user_logged_in() ) {
		return;
	}

	$user_id = get_current_user_id();
	$user = new WP_User( $user_id );

	if( !in_array("administrator", $user->roles) && !in_array("restaurante", $user->roles) ){
		return;
	}

	$onclick = sprintf( 'return addComment.moveForm( "%1$s-%2$s", "%2$s", "%3$s", "%4$s" )',
		$args['add_below'], $comment->comment_ID, $args['respond_id'], $post->ID
	);

	$link = sprintf( "<a rel='nofollow' class='comment-reply-link' href='%s' onclick='%s' aria-label='%s'>%s</a>",
		esc_url( add_query_arg( 'replytocom', $comment->comment_ID, $_SERVER["HTTP_REFERER"] ) ) . "#" . $args['respond_id'],
		$onclick,
		esc_attr( sprintf( $args['reply_to_text'], $comment->comment_author ) ),
		$args['reply_text']
	);

    return $link;
}
/* END redirect */

/* Custom author archive comments rating */
add_filter( 'comment_form_logged_in_after', 'add_rating_fields_to_comment' );
function add_rating_fields_to_comment(){
	$user_id = get_current_user_id();
	$user = new WP_User( $user_id );

	if( in_array( "administrator", $user->roles ) || in_array( "restaurante", $user->roles ) ){
		return;
	}

	?>
	<p class="comment-form-rating">
		<label for="rating_price"><?php _e('Price', THEME_NAME); ?></label>
		<span class="stars-container">

		<?php for( $i=1; $i <= 5; $i++ ){
			$star_type = "fa-star-o";

			if( $i <= 3 ){
				$star_type = "fa-star";
			}
			?>

			<span class="single-star"><input type="radio" name="rating_price" id="rating_price_<?php echo $i; ?>" value="<?php echo $i; ?>"/><label for="rating_price_<?php echo $i; ?>" class="star-click fa <?php echo $star_type; ?>"></label></span>

		<?php } ?>

		</span>
	</p>

	<p class="comment-form-rating">
		<label for="rating_food"><?php _e('Food', THEME_NAME); ?></label>
		<span class="stars-container">

		<?php for( $i=1; $i <= 5; $i++ ){
			$star_type = "fa-star-o";

			if( $i <= 3 ){
				$star_type = "fa-star";
			}
			?>
			<span class="single-star"><input type="radio" name="rating_food" id="rating_food_<?php echo $i; ?>" value="<?php echo $i; ?>"/><label for="rating_food_<?php echo $i; ?>" class="star-click fa <?php echo $star_type; ?>"></label></span>

		<?php } ?>

		</span>
	</p>

	<p class="comment-form-rating">
		<label for="rating_service"><?php _e('ZAS Service', THEME_NAME); ?></label>
		<span class="stars-container">

		<?php for( $i=1; $i <= 5; $i++ ){
			$star_type = "fa-star-o";

			if( $i <= 3 ){
				$star_type = "fa-star";
			}
			?>
			<span class="single-star"><input type="radio" name="rating_service" id="rating_service_<?php echo $i; ?>" value="<?php echo $i; ?>"/><label for="rating_service_<?php echo $i; ?>" class="star-click fa <?php echo $star_type; ?>"></label></span>

		<?php } ?>

		</span>
	</p>
	<?php
}

add_action( 'comment_post', 'save_comment_meta_data' );
function save_comment_meta_data( $comment_id ) {

	if ( ( isset( $_POST['rating_price'] ) ) && ( $_POST['rating_price'] != '') ){
		$rating_price = wp_filter_nohtml_kses($_POST['rating_price']);
		add_comment_meta( $comment_id, 'rating_price', $rating_price );
	}

	if ( ( isset( $_POST['rating_food'] ) ) && ( $_POST['rating_food'] != '') ){
		$rating_food = wp_filter_nohtml_kses($_POST['rating_food']);
		add_comment_meta( $comment_id, 'rating_food', $rating_food );
	}

	if ( ( isset( $_POST['rating_service'] ) ) && ( $_POST['rating_service'] != '') ){
		$rating_service = wp_filter_nohtml_kses($_POST['rating_service']);
		add_comment_meta( $comment_id, 'rating_service', $rating_service );
	}
}

add_filter( 'preprocess_comment', 'verify_comment_meta_data' );
function verify_comment_meta_data( $commentdata ) {

	// if ( ! isset( $_POST['rating_price'] ) ){
	// 	wp_die( __( 'Error: You did not add a rating. Hit the Back button on your Web browser and resubmit your comment with a rating.' ) );
	// }

	return $commentdata;
}

add_filter( 'comment_text', 'modify_comment');
function modify_comment( $text ){

	$comment = get_comment( get_comment_ID() );

	$user_id = $comment->user_id;
	$user = new WP_User( $user_id );

	if( in_array("administrator", $user->roles) || in_array("restaurante", $user->roles) ){
		return $text;
	}

	$rating_price 		= ( get_comment_meta( get_comment_ID(), 'rating_price', true ) ) ? get_comment_meta( get_comment_ID(), 'rating_price', true ) : 0;
	$rating_food 		= ( get_comment_meta( get_comment_ID(), 'rating_food', true ) ) ? get_comment_meta( get_comment_ID(), 'rating_food', true ) : 0;
	$rating_service 	= ( get_comment_meta( get_comment_ID(), 'rating_service', true ) ) ? get_comment_meta( get_comment_ID(), 'rating_service', true ) : 0;


	// PRICE
	$text_rating = '<p class="comment-rating"><label for="rating_price">' . __('Price', THEME_NAME) . '</label><span class="stars-container">';
	for( $i = 1; $i <= 5; $i++ ){
		$star_type = "fa-star-o";

		if( $rating_price >= $i ){
			$star_type = "fa-star";
		}

		$text_rating .= '<span class="single-star"><label for="rating_price_' . $i . '" class="fa ' . $star_type . '"></label></span>';
	}
	$text_rating .= '</span></p>';

	// FOOD
	$text_rating .= '<p class="comment-rating"><label for="rating_food">' . __('Food', THEME_NAME) . '</label><span class="stars-container">';
	for( $i = 1; $i <= 5; $i++ ){
		$star_type = "fa-star-o";

		if( $rating_food >= $i ){
			$star_type = "fa-star";
		}

		$text_rating .= '<span class="single-star"><label for="rating_food_' . $i . '" class="fa ' . $star_type . '"></label></span>';
	}
	$text_rating .= '</span></p>';

	// SERVICE
	$text_rating .= '<p class="comment-rating"><label for="rating_service">' . __('ZAS Service', THEME_NAME) . '</label><span class="stars-container">';
	for( $i = 1; $i <= 5; $i++ ){
		$star_type = "fa-star-o";

		if( $rating_service >= $i ){
			$star_type = "fa-star";
		}

		$text_rating .= '<span class="single-star"><label for="rating_service_' . $i . '" class="fa ' . $star_type . '"></label></span>';
	}
	$text_rating .= '</span></p>';



	$text = $text_rating . $text;

	return $text;
}
/* END Custom rating */

/* Redirect to orders list on order save */
add_filter( 'redirect_post_location', 'dms_redirect_on_save_shop_order', 10 , 2 );
function dms_redirect_on_save_shop_order( $location, $post_id ) {

	if( get_post_type( $post_id ) != "shop_order" ){
		return $location;
	}

	if ( isset($_POST['save']) || isset($_POST['publish']) ) {
		$current_user = wp_get_current_user();
		$is_admin = in_array( "administrator", $current_user->roles );

		$location = menu_page_url( 'admin_menu_orders', false );
    }

	return $location;
}
/* END redirect to list */

/* Remove qtranslate fields value sintax from woocommerce titles */
add_filter( 'the_title' , 'dms_qtranslate_items_name', 10, 1 );
add_filter( 'esc_html' , 'dms_qtranslate_items_name', 10, 1 );
add_filter('get_terms', 'dms_qtranslate_items_name', 10, 1);
add_filter('get_the_terms', 'dms_qtranslate_items_name', 10, 1);
function dms_qtranslate_items_name( $title ) {
    if( function_exists( "qtranxf_use" ) && function_exists( "qtrans_getLanguage" ) ){

        if ( is_array( $title ) ) {
            foreach ( $title as $value ) {
                if( is_array( $value ) ){
                    $value['name'] = qtranxf_use( qtrans_getLanguage(), $value['name'], false );
                }elseif( is_object( $value ) ){
                    $value->name = qtranxf_use( qtrans_getLanguage(), $value->name, false );
                }
            }
        } else {
            $title = qtranxf_use( qtrans_getLanguage(), $title, false );
        }
    }

    return $title;
}
/* END remove qtranslate sintax */

/* Remove qtranslate fields from wp emails */
add_filter( 'wp_mail', 'dms_qtranslate_email_parameters' );
function dms_qtranslate_email_parameters( $args ) {

	$new_wp_mail = array(
		'to'          => $args['to'],
		'subject'     => qtranxf_use( qtrans_getLanguage(), $args['subject'], false ),
		'message'     => qtranxf_use( qtrans_getLanguage(), $args['message'], false ),
		'headers'     => $args['headers'],
		'attachments' => $args['attachments'],
	);

	return $new_wp_mail;
}
/* END remove qtranslate sintax */

/* Check if notification if needed on every new order */
add_action( 'save_post', 'check_if_need_notification', 10, 3 );
function check_if_need_notification( $post_id, $post, $update ) {

    global $dms;
    $current_time 				= current_time('timestamp');
    $notification_time 			= strtotime( $dms["notification_time"] );
    $notification_frequency 	= intval( $dms["notification_frequency"] );
    $minute_difference 			= ( $current_time - $notification_time ) / 60;

    if( $minute_difference < $notification_frequency ){
    	return;
    }

    $restaurant_inactive 	= intval( $dms["notification_restaurant_inactive"] );
    $time_limit 			= date( "Y-m-d H:i:s", strtotime( "-". $restaurant_inactive ." MINUTES", $current_time ) );
    $date_start 			= date( "Y-m-d H:i:s", strtotime( "-1 day", $current_time ) );

	$args_orders_on_hold = array(
		'post_type'  	=> 'shop_order',
		'post_status' 	=> array( 'wc-completed', 'wc-pending', 'wc-processing', 'wc-on-hold' ),
		'date_query' 	=> array(
			array(
				'after'     => $date_start,
				'before'    => $time_limit,
				'inclusive' => false,
			),
		),
		'posts_per_page' 	=> 5,
		'fields' 			=> 'ids',
		'meta_query' 		=> array(
			array(
				'key' 		=> 'dms_order_status',
				'value' 	=> array( 'order_on_hold' ),
				'compare' 	=> 'IN',
			),
		),
	);

	$results_orders_on_hold = new WP_Query( $args_orders_on_hold );

    if( $results_orders_on_hold->post_count > 0 ){
    	send_orders_notification_to_admin( 'restaurant_inactive' );
    }

    $args_counting = array(
		'post_type'  	=> 'shop_order',
		'post_status' 	=> array( 'wc-completed', 'wc-pending', 'wc-processing', 'wc-on-hold' ),
		'date_query' 	=> array(
			array(
				'after'     => $date_start,
				'inclusive' => false,
			),
		),
		'posts_per_page' 	=> -1,
		'fields' 			=> 'ids',
		'meta_query' 		=> array(
			array(
				'key' 		=> 'dms_order_status',
				'value' 	=> array( 'rest_has_accepted', 'driver_has_accepted', 'driver_in_rest', 'problem' ),
				'compare' 	=> 'IN',
			),
		),
	);

	$results_orders_counting = new WP_Query( $args_counting );

    if( $results_orders_counting->have_posts() ){

    	$late_orders 			= 0;
    	$time_orders_late 		= intval( $dms["notification_orders_late"] );
    	$multiplicator 			= floatval( $dms["notification_active_orders_value"] );
		$drivers_active_count 	= count( get_users( array( 'role'=> 'repartidora', 'meta_key' => 'dms_driver_active', 'meta_value' => 1, ) ) );
		$comparison_value 		= $drivers_active_count * $multiplicator;

	    foreach( $results_orders_counting->posts as $order_id ){

	    	if( $late_orders > $comparison_value ){
				continue;
			}

	    	$restaurant_id 			= get_post_meta( $order_id, "dms_order_restaurant", true );
	    	$rest_has_accepted 		= str_replace( array( "[:es]", "[:]", "T", ".000Z" ), "", get_post_meta( $order_id, "dms_order_time_rest_has_accepted", true ) );
	    	$time_extra 			= intval( get_post_meta( $order_id, "dms_order_time_extra", true ) );
	    	$time_min 				= intval( get_user_meta( $restaurant_id, "dms_restaurant_time", true ) );
	    	$time 					= $time_min + $time_extra;
	    	$time_estimated 		= strtotime( "+". $time ." minute", strtotime( $rest_has_accepted ) );
	    	$late_minute_difference = intval( ( $time_estimated - $current_time ) / 60 );

	    	if( $late_minute_difference < $time_orders_late ){
	    		$late_orders++;
	    	}
	    }

		if( $late_orders > $comparison_value ){
			send_orders_notification_to_admin( 'orders_excess' );
		}
	}

    $dms["notification_time"] = date( 'Y-m-d H:i:s', $current_time );
 	update_option( "dms", $dms );
}
/* END check notification */

/* Check if valid address */
add_action( 'save_post', 'check_if_valid_address_and_send_notification', 10, 3 );
function check_if_valid_address_and_send_notification( $post_id, $post, $update ) {

    $slug = 'shop_order';

    if ( $slug != $post->post_type ) {
        return;
    }

    $wrong_address = ( isset( $_POST["dms_order_wrong_address"] ) ) ? $_POST["dms_order_wrong_address"] : 0;

    if( $wrong_address == 0 || $wrong_address == "0" ) return;

    update_post_meta( $post_id, "_wrong_shipping_address", 1 );
    send_orders_notification_to_admin( 'wrong_address', $post_id );
}
/* END valid address */

/* Send notification to admin */
function send_orders_notification_to_admin( $type, $post_id = 0 ){
	global $dms;

	$email_from = $dms["notification_email"];
	$email_to = $dms["notification_email"];
	$phone_to = mensatek_sms_check_phone( $dms["notification_phone"] );

	if( $type == 'restaurant_inactive' ){
		$subject = "ZAS - Restaurante inactivo";
		$html_content = "Tienes restaurantes con pedidos sin aceptar.";
	}

	if( $type == 'orders_excess' ){
		$subject = "ZAS - Atasco de pedidos";
		$html_content = "Tienes un posible atasco en los pedidos.";
	}

	if( $type == 'wrong_address' ){
		$subject = "ZAS - Direccion invalida";
		$html_content = "Pedido #" . $post_id . " tiene direccion incorrecta.";
	}

	// SEND EMAIL
	$headers[] = 'From: ZASCOM <'. $email_from .'>';
	wp_mail( $email_to, $subject, $html_content, $headers);

	// SEND SMS
	$message = urlencode( html_entity_decode( $subject, ENT_QUOTES, "UTF-8" ) );
	$parameters = array(
		'servicio' 				=> "mensatekSMS",
		'username_mensatekSMS' 	=> urlencode( "bnavarro@deideasmarketing.com" ),
		'password_mensatekSMS' 	=> 6064551,
		'sender ID' 			=> urlencode( html_entity_decode( "ZASCOM", ENT_QUOTES, "UTF-8" ) ),
	);
	// mensatek_sms_envia_sms( $parameters, $phone_to, $message );
}
/* END send notification */

/* Send email to paid customer on order cancelation */
add_action( 'acf/save_post', 'send_email_to_paid_customer_on_cancel', 1 );
function send_email_to_paid_customer_on_cancel( $post_id ) {

	// Check if status mofied
	if ( empty( $_POST['acf'] ) ) {
		return;
	}

	// Check if rest has accepted
	$acf = $_POST['acf'];
	if( $acf['field_5747129addb9f'] != "return_money" && $acf['field_5747129addb9f'] != "cancelled_return_money" ){
		return;
	}

	$prev_value = get_field('dms_order_status', $post_id);
	if( $prev_value == "return_money" || $prev_value == "cancelled_return_money" ){
		return;
	}

	WC()->mailer()->emails['WC_Email_Customer_Refunded_Order']->trigger( $post_id );
}
/* END paid cancelation */

/* Set custom metabox order in shop orders for all existing restaurants: UNCOMMENT TO EXECUTE ONCE AND COMMENT BACK */
// add_action( 'init', 'set_custom_metaboxes_order' );
function set_custom_metaboxes_order() {

	$order_metabox 			= get_user_meta( 1, 'meta-box-order_shop_order', true );
	$order_metabox_closed 	= get_user_meta( 1, 'closedpostboxes_shop_order', true );
	$order_metabox_hidden 	= get_user_meta( 1, 'metaboxhidden_shop_order', true );
	$order_metabox_layout 	= get_user_meta( 1, 'screen_layout_shop_order', true );

	$product_metabox 			= get_user_meta( 1, 'meta-box-order_product', true );
	$product_metabox_closed 	= get_user_meta( 1, 'closedpostboxes_product', true );
	$product_metabox_hidden 	= get_user_meta( 1, 'metaboxhidden_product', true );
	$product_metabox_columns 	= get_user_meta( 1, 'manageedit-productcolumnshidden', true );

	$edit_product_cat_per_page 	= get_user_meta( 1, 'edit_product_cat_per_page', true );

   	$restaurants = get_users( array( "role" => "restaurante" ) );

    foreach ( $restaurants as $key => $restaurant ) {
    	$user_id = $restaurant->ID;

    	if( $order_metabox ) 			update_user_meta( $user_id, 'meta-box-order_shop_order', 	$order_metabox );
		if( $order_metabox_closed ) 	update_user_meta( $user_id, 'closedpostboxes_shop_order', 	$order_metabox_closed );
		if( $order_metabox_hidden ) 	update_user_meta( $user_id, 'metaboxhidden_shop_order', 	$order_metabox_hidden );
		if( $order_metabox_layout ) 	update_user_meta( $user_id, 'screen_layout_shop_order', 	$order_metabox_layout );

		if( $product_metabox ) 			update_user_meta( $user_id, 'meta-box-order_product', 			$product_metabox );
		if( $product_metabox_closed ) 	update_user_meta( $user_id, 'closedpostboxes_product', 			$product_metabox_closed );
		if( $product_metabox_hidden ) 	update_user_meta( $user_id, 'metaboxhidden_product', 			$product_metabox_hidden );
		if( $product_metabox_columns ) 	update_user_meta( $user_id, 'manageedit-productcolumnshidden', 	$product_metabox_columns );

		if( $edit_product_cat_per_page ) 	update_user_meta( $user_id, 'edit_product_cat_per_page', 	$edit_product_cat_per_page );
    }
}

add_action( 'user_register', 'set_custom_metaboxes_order_for_new_users' );
function set_custom_metaboxes_order_for_new_users( $user_id ) {

	$user 			= get_user_by( "ID", $user_id );
	$user_roles 	= $user->roles;

	if( in_array( "restaurante", $user_roles ) || in_array( "regulator", $user_roles )  ){
		$order_metabox 			= get_user_meta( 1, 'meta-box-order_shop_order', true );
		$order_metabox_closed 	= get_user_meta( 1, 'closedpostboxes_shop_order', true );
		$order_metabox_hidden 	= get_user_meta( 1, 'metaboxhidden_shop_order', true );
		$order_metabox_layout 	= get_user_meta( 1, 'screen_layout_shop_order', true );

		$product_metabox 			= get_user_meta( 1, 'meta-box-order_product', true );
		$product_metabox_closed 	= get_user_meta( 1, 'closedpostboxes_product', true );
		$product_metabox_hidden 	= get_user_meta( 1, 'metaboxhidden_product', true );
		$product_metabox_columns 	= get_user_meta( 1, 'manageedit-productcolumnshidden', true );

		$edit_product_cat_per_page 	= get_user_meta( 1, 'edit_product_cat_per_page', true );

		if( $order_metabox ) 			update_user_meta( $user_id, 'meta-box-order_shop_order', 	$order_metabox );
		if( $order_metabox_closed ) 	update_user_meta( $user_id, 'closedpostboxes_shop_order', 	$order_metabox_closed );
		if( $order_metabox_hidden ) 	update_user_meta( $user_id, 'metaboxhidden_shop_order', 	$order_metabox_hidden );
		if( $order_metabox_layout ) 	update_user_meta( $user_id, 'screen_layout_shop_order', 	$order_metabox_layout );

		if( $product_metabox ) 			update_user_meta( $user_id, 'meta-box-order_product', 			$product_metabox );
		if( $product_metabox_closed ) 	update_user_meta( $user_id, 'closedpostboxes_product', 			$product_metabox_closed );
		if( $product_metabox_hidden ) 	update_user_meta( $user_id, 'metaboxhidden_product', 			$product_metabox_hidden );
		if( $product_metabox_columns ) 	update_user_meta( $user_id, 'manageedit-productcolumnshidden', 	$product_metabox_columns );

		if( $edit_product_cat_per_page ) 	update_user_meta( $user_id, 'edit_product_cat_per_page', 	$edit_product_cat_per_page );
	}
}
/* END set metabox */

/* Clone paid order to modify products */
add_action( 'admin_footer', 'fill_user_data_on_cloned_order' );
function fill_user_data_on_cloned_order() {
	global $post, $post_type;

	if ( false ) return;

	if( 'shop_order' != $post_type ) return;

	$order_id = ( isset( $_GET["cloned_order"] ) ) ? $_GET["cloned_order"] : false;

	if( !$order_id ) return;

	$current_post 			= $post;
	$current_post_id		= $current_post->ID;
	$current_order 			= wc_get_order( $current_post_id );

	$order 					= wc_get_order( $order_id );
	$post 					= $order->post;

	$order_date 			= date_i18n( 'Y-m-d', strtotime( $post->post_date ) );
	$order_hour 			= date_i18n( 'H', strtotime( $post->post_date ) );
	$order_minute 			= date_i18n( 'i', strtotime( $post->post_date ) );

	$customer_user			= $order->get_user_id();
	// $customer_note			= get_post_meta( $order_id, '_customer_note', true );

	$user_string = '';
	if ( $customer_user ) {
		$user        = get_user_by( 'id', $customer_user );
		$user_string = sprintf(
			esc_html__( '%1$s (#%2$s &ndash; %3$s)', 'woocommerce' ),
			$user->display_name,
			absint( $user->ID ),
			$user->user_email
		);
	}

	$custom_fee = ( object )[
		'name' 		=> "Pago online del pedido #" . $order_id,
		'taxable' 	=> false,
		'amount' 	=> -1 * $order->get_total(),
		'tax' 		=> 0,
		'tax_data' 	=> array()
	];
	$current_order->add_fee( $custom_fee );
	$fee_line_items 		= $current_order->get_fees();

	$order_items 			= $order->get_items();
	foreach ( $order_items as $item_id => $item ) {
		$item_product_id 				= $item["product_id"];
		$item_product 					= get_product( $item_product_id );
		$item_product_variation_id 		= $item["product_variation_id"];
		$item_qty 						= $item["qty"];
		$item_meta 						= $item["item_meta"];

        $new_item_id = $current_order->add_product(
            $item_product,
            $item_qty,
            array(
                'variation' => $item_product_variation_id,
                'totals'    => array(
                    'subtotal'     => $item_meta['_line_subtotal'][0],
                    'subtotal_tax' => $item_meta['_line_subtotal_tax'][0],
                    'total'        => $item_meta['_line_total'][0],
                    'tax'          => $item_meta['_line_tax'][0],
                    'tax_data'     => $item_meta['_line_tax_data'][0] // Since 2.2
                )
            )
        );

        foreach ( $item_meta as $item_meta_key => $item_meta_value ) {

        	if( substr( $item_meta_key, 0, 1 ) === '_' ) continue;

        	wc_add_order_item_meta( $new_item_id, wc_attribute_label( $item_meta_key, $item_product ), $item_meta_value[0] );
        }
	}

	$line_items = $current_order->get_items();

	?>
	<script type="text/javascript" id="custom_cloned_order_filler">
		(function($) {
			$(function () {

				function htmlDecode(value) {
					return $('<div />').html(value).text();
				}

				$(document).ready(function() {

					$("#order_date").val( 				"<?php echo $order_date; ?>" );
					$("#order_date_hour").val( 			"<?php echo $order_hour; ?>" );
					$("#order_date_minute").val( 		"<?php echo $order_minute; ?>" );

					$("#customer_user").val( 			"<?php echo $customer_user; ?>" );
					$("#customer_user").attr( "data-selected", "<?php echo $user_string; ?>" );
					$("#customer_user").trigger( "change" );

					<?php
					if( !empty( $fee_line_items ) ){
						?>
						$( '#woocommerce-order-items' ).find( '.inside' ).append( '<tbody id="order_line_items"></tbody>' );
						<?php
						foreach ( $line_items as $item_id => $item ) {
							ob_start();
							include( WP_PLUGIN_DIR . "/woocommerce/includes/admin/meta-boxes/views/html-order-item.php" );
						    $order_items = (string) ob_get_clean();
							$order_items = htmlentities( str_replace( "'", "\'", preg_replace( '/^\s+|\n|\r|\s+$/m', '', $order_items ) ) );

							?>
							$( '#order_line_items' ).append( htmlDecode( '<?php echo $order_items; ?>' ) );
							<?php
						}
					}

					if( !empty( $fee_line_items ) ){
						?>
						$( '#woocommerce-order-items' ).find( '.inside' ).append( '<tbody id="order_fee_line_items"></tbody>' );
						<?php
						foreach ( $fee_line_items as $item_id => $item ) {
							ob_start();
							include( WP_PLUGIN_DIR . "/woocommerce/includes/admin/meta-boxes/views/html-order-fee.php" );
						    $order_fee_items = (string) ob_get_clean();
							$order_fee_items = htmlentities( str_replace( "'", "\'", preg_replace( '/^\s+|\n|\r|\s+$/m', '', $order_fee_items ) ) );

							?>
							$( '#order_fee_line_items' ).append( htmlDecode( '<?php echo $order_fee_items; ?>' ) );
							<?php
						}
					}

					?>

					// $("#_order_total").val("-<?php echo $order_total; ?>");
					// $("#custom_cloned_order_filler").remove();
				});
			});
		})(jQuery);
	</script>
	<?php
}
/* END clone order */

/* Refund cloned order after succefully saving new */
add_action( 'acf/save_post', 'refund_paid_order_after_cloned_saved', 1 );
function refund_paid_order_after_cloned_saved( $new_order_id ) {

	if ( empty( $_POST['acf'] ) ) return;

	$cloned_order_id = $_POST['acf']['field_581b1f90d8112'];
	if( !$cloned_order_id ) return;

	$new_order_status = $_POST['acf']['field_5747129addb9f'];
	if( $new_order_status != "rest_has_accepted" ) return;

	$prev_value = get_field( 'dms_order_replacement', $new_order_id );
	if( $prev_value != "" ) return;

	$cloned_order 		= wc_get_order( $cloned_order_id );
	$new_order 			= wc_get_order( $new_order_id );
	$new_order_total 	= $new_order->get_total();

	if( $new_order_total < 0 ){
		$refund_args = array(
			'amount'     => $new_order_total * -1,
			'reason'     => "Sustitución pedido #" . $new_order_id,
			'order_id'   => $cloned_order_id
		);

		wc_create_refund( $refund_args );
		$cloned_order_new_status = "cancelled_return_money";
		WC()->mailer()->emails['WC_Email_Customer_Refunded_Order']->trigger( $cloned_order_id, true );

		$fee_line_items = $new_order->get_fees();
		foreach ( $fee_line_items as $item_id => $item ) {
			$fee_args = array(
				'name' 			=> "Pago online del pedido #" . $cloned_order_id . " con resto reembolsado",
				'line_total' 	=> floatval( $item["line_total"] ) - $new_order_total,
			);
			$new_order->update_fee( $item_id, $fee_args );
		}

		$new_order->calculate_totals();
	}else{
		$cloned_order_new_status = "order_cancelled";
		WC()->mailer()->emails['WC_Email_Cancelled_Order']->trigger( $new_order_id );
	}

	$cloned_order->update_status( "cancelled", "Pedido sustituido por #" . $new_order_id . ".", false );
	update_post_meta( $cloned_order_id, "dms_order_status", $cloned_order_new_status );

	WC()->mailer()->emails['WC_Email_Customer_Processing_Order']->trigger( $new_order_id );

    return;
}
/* END refund cloned */

/* Set order restaurant before field loads */
add_filter('acf/load_field/name=dms_order_restaurant', 'set_order_restaurant_field');
function set_order_restaurant_field( $field ) {

	if( isset( $_GET["cloned_order"] ) ){
		$order_id 					= $_GET["cloned_order"];
		$restaurant_id 				= get_post_meta( $order_id, 'dms_order_restaurant', true );
		$field['default_value'] 	= $restaurant_id;

		return $field;
	}

	$current_user 		= wp_get_current_user();
	$is_restaurant 		= in_array( "restaurante", $current_user->roles );

	if( $is_restaurant ){
		$field['default_value'] = $current_user->ID;

		return $field;
	}

	return $field;
}
/* END set restaurant */

/* Set order restaurant before field loads */
add_filter('acf/load_field/name=dms_order_driver', 'set_order_driver_field');
function set_order_driver_field( $field ) {

	if( isset( $_GET["cloned_order"] ) ){
		$order_id 					= $_GET["cloned_order"];
		$driver_id 					= get_post_meta( $order_id, 'dms_order_driver', true );
		$field['default_value'] 	= $driver_id;

		return $field;
	}

	return $field;
}
/* END set restaurant */

/* Set order replacement before field loads */
add_filter('acf/load_field/name=dms_order_replacement', 'set_order_replacement_field');
function set_order_replacement_field( $field ) {

	if( isset( $_GET["cloned_order"] ) ){
		$order_id 					= $_GET["cloned_order"];
		$field['default_value'] 	= $order_id;

		return $field;
	}

	return $field;
}
/* END set replacement */

/* Set restaurants categories field */
add_filter('acf/load_field/name=dms_restaurants_category', 'set_restaurants_categories_field');
add_filter('acf/load_field/key=field_57483f1c955f9', 'set_restaurants_categories_field');
function set_restaurants_categories_field( $field ) {

	$field['choices'] 	= array();
	$control_array 		= array();

	$field['choices'][] = "Seleccionar categoría";

	$restaurant_types = ( get_field( "dms_options_restaurants_types", 109 ) ) ? get_field( "dms_options_restaurants_types", 109 ) : array();

	foreach ($restaurant_types as $key => $restaurant_type) {
		$type_key 	= $restaurant_type["dms_options_restaurant_type_key"];
		$type 		= $restaurant_type["dms_options_restaurant_type"];

		$field['choices'][ $type_key ] = $type;
	}

    // return the field
    return $field;
}
/* END set categories */

/* Redirect user to orders on login */
add_filter( 'login_redirect', 'dashboard_redirect', 10, 3 );
function dashboard_redirect( $redirect_to, $requested_redirect_to, $user ) {

    $redirect_to = esc_url( admin_url() . "admin.php?page=admin_menu_orders" );

    return $redirect_to;
}
/* END redirect login */

/* Show product_cat author field */
// add_action( 'product_cat_add_form_fields', 'dms_add_product_cat_author_field', 15 );
// add_action( 'product_cat_edit_form_fields', 'dms_edit_product_cat_author_field', 15 );
// add_action( 'created_term', 'dms_save_product_cat_author_field', 10, 3 );
// add_action( 'edit_term', 'dms_save_product_cat_author_field', 10, 3 );
// add_filter( 'manage_edit-product_cat_columns', 'dms_product_cat_author_columns' );
// add_filter( 'manage_product_cat_custom_column', 'dms_product_cat_author_column', 10, 3 );

function dms_add_product_cat_author_field() {
	?>
	<div class="form-field term-author-wrap">
		<label for="term_author">Restaurante asignado</label>
		<select id="term_author" name="term_author" class="postform">
			<option value=""><?php _e( 'No asignado', THEME_NAME ); ?></option>
		<?php
		$users = get_users( array( 'role'=> 'restaurante' ) );
		foreach ( $users as $user ) {
			$user_id 	= $user->ID;
			$user_name 	= $user->display_name;
			?>
			<option value="<?php echo $user_id; ?>"><?php echo $user_name; ?></option>
			<?php
		}
		?>
		</select>
	</div>
	<?php
}

function dms_edit_product_cat_author_field( $term ) {
	?>
	<tr class="form-field term-author-wrap">
		<th scope="row" valign="top"><label for="term_author">Restaurante asignado</label></th>
		<td>
			<select id="term_author" name="term_author" class="postform">
				<option value=""><?php _e( 'No asignado', THEME_NAME ); ?></option>
			<?php
			$selected_user_id 	= get_term_meta( $term->term_id, 'term_author', true );
			$users 				= get_users( array( 'role'=> 'restaurante' ) );

			foreach ( $users as $user ) {
				$user_id 	= $user->ID;
				$user_name 	= $user->display_name;
				?>
				<option value="<?php echo $user_id; ?>" <?php selected( $user_id, $selected_user_id ); ?>><?php echo $user_name; ?></option>
				<?php
			}
			?>
			</select>
		</td>
	</tr>
	<?php
}

function dms_save_product_cat_author_field( $term_id, $tt_id = '', $taxonomy = '' ) {

	if ( isset( $_POST['term_author'] ) && 'product_cat' === $taxonomy ) {
		update_term_meta( $term_id, 'term_author', absint( $_POST['term_author'] ) );
	}
}

function dms_product_cat_author_columns( $columns ) {
	$new_columns = array();
	$new_columns['author'] = __( 'Restaurante', THEME_NAME );
	return array_merge( $columns, $new_columns );
}

function dms_product_cat_author_column( $columns, $column, $id ) {
	if ( 'author' == $column ) {
		$user_id 	= get_term_meta( $id, 'term_author', true );
		$user_name 	= "";

		if( $user_id ){
			$user = get_user_by( "ID", $user_id );
			$user_name = $user->display_name;
		}

		$columns .= $user_name;
	}

	return $columns;
}

/* END product_cat author */

/* Show product_cat visibility field */
add_action( 'product_cat_add_form_fields', 'dms_add_product_cat_visibility_field', 15 );
add_action( 'product_cat_edit_form_fields', 'dms_edit_product_cat_visibility_field', 15 );
add_action( 'created_term', 'dms_save_product_cat_visibility_field', 10, 3 );
add_action( 'edit_term', 'dms_save_product_cat_visibility_field', 10, 3 );

function dms_add_product_cat_visibility_field() {
	$selected_visibility 	= get_term_meta( $term->term_id, 'term_visibility', true );
	?>
	<div class="form-field term-author-wrap">
		<label for="term_visibility">Visibilidad</label>
		<div>
			<input type="checkbox" name="term_visibility" id="term_visibility" value="1" <?php checked( "1", $selected_visibility ); ?>><span>Oculto</span>
		</div>
	</div>
	<?php
}

function dms_edit_product_cat_visibility_field( $term ) {
	$selected_visibility 	= get_term_meta( $term->term_id, 'term_visibility', true );
	?>
	<tr class="form-field term-author-wrap">
		<th scope="row" valign="top"><label for="term_visibility">Visibilidad</label></th>
		<td>
			<div>
				<input type="checkbox" name="term_visibility" id="term_visibility" value="1" <?php checked( "1", $selected_visibility ); ?>><span>Oculto</span>
			</div>
		</td>
	</tr>
	<?php
}

function dms_save_product_cat_visibility_field( $term_id, $tt_id = '', $taxonomy = '' ) {

	if ( 'product_cat' === $taxonomy ) {
		if( isset( $_POST['term_visibility'] ) ){
			update_term_meta( $term_id, 'term_visibility', absint( $_POST['term_visibility'] ) );
		}else{
			update_term_meta( $term_id, 'term_visibility', absint( "0" ) );
		}
	}
}
/* END product_cat visibility */

/* Get restaurant category label */
function get_restaurant_category_label( $category ){
	$types = ( get_field( "dms_options_restaurants_types", 109 ) ) ? get_field( "dms_options_restaurants_types", 109 ) : array();
	$label 	= false;

	foreach ( $types as $type) {

		if( $label ) continue;

		$type_key = $type["dms_options_restaurant_type_key"];

		if( $type_key == $category ){
			$label = $type["dms_options_restaurant_type"];
		}
	}

	return $label;
}
/* END get label */

/* Remove posts columns from users list */
add_filter('manage_users_columns' , 'remove_users_posts_column');
function remove_users_posts_column( $columns ) {
    unset( $columns["posts"] );

	return $columns;
}
/* END remove column */

/* Add custom elements to admin sidebar */
add_action( 'adminmenu', 'add_custom_elements_to_admin_sidebar' );
function add_custom_elements_to_admin_sidebar() {
	global $dms;

	$phone 				= $dms["admin_phone"];
	$phone_text 		= $dms["admin_phone_text"];
	$delivery_schedule 	= $dms["admin_delivery_schedule"];

	if( $delivery_schedule ){
		?>
		<li id="admin-delivery-schedule">
			<div class="admin-element-container">
				<p class="normal-text">Horario de reparto</p>
				<p class="highlight-text"><?php echo $delivery_schedule; ?></p>
			</div>
		</li>
		<?php
	}

	if( $phone ){
		?>
		<li id="admin-phone">
			<div class="admin-element-container">
				<p class="normal-text"><?php echo $phone_text; ?></p>
				<p class="highlight-text"><?php echo $phone; ?></p>
			</div>
		</li>
		<?php
	}
};
/* END admin sidebar */

/* Generate receipt in pdf */
function generate_receipt_pdf( $order_id ){
	require_once('inc/fpdf/fpdf.php');

	class DMS_PDF extends FPDF{
		var $widths;
		var $aligns;
		var $line_height;
		var $page_width;

		function SetWidths($w){
		    //Set the array of column widths
		    $this->widths=$w;
		}

		function SetAligns($a){
		    //Set the array of column alignments
		    $this->aligns=$a;
		}

		function SetLineHeight($h){
		    //Set the array of column alignments
		    $this->line_height=$h;
		}

		function GetLineHeight(){
		    //GET the array of column alignments
		    return $this->line_height;
		}

		function SetPageWidth($w){
		    //Set the array of column alignments
		    $this->page_width=$w;
		}

		function GetPageWidth(){
		    //GET the array of column alignments
		    return $this->page_width;
		}

		function getDocumentWidth(){
			$doc_width = intval( $this->page_width ) - 4;

			return $doc_width;
		}

		function Row($data, $type = 'product'){

			switch ($type) {
				case 'product':
					$border_type = 0;
					break;
				case 'subtotal':
					$border_type = 0;
					break;
				case 'total':
					$border_type = 0;
					break;
			}
		    //Calculate the height of the row
		    $nb=0;
		    for($i=0;$i<count($data);$i++)
		        $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
		    $h=3*$nb;
		    //Issue a page break first if needed
		    $this->CheckPageBreak($h);
		    //Draw the cells of the row
		    for($i=0;$i<count($data);$i++)
		    {
				$w=$this->widths[$i];

				switch ($i) {
					case 0:
						$a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';

						if( $type == "subtotal" || $type == "total" ){
							$a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'R';
						}

						break;

					case 1:
						$a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'C';
						break;

					case 2:
						$a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'R';
						break;
				}

		        //Save the current position
		        $x=$this->GetX();
		        $y=$this->GetY();
		        //Draw the border
		        // $this->Rect($x,$y,$w,$h);
		        //Print the text
		        $this->MultiCell($w,3,$data[$i], $border_type,$a);
		        //Put the position to the right of the cell
		        $this->SetXY($x+$w,$y);
		    }
		    //Go to the next line
		    $this->Ln($h);
		}

		function CheckPageBreak($h){
		    //If the height h would cause an overflow, add a new page immediately
		    if($this->GetY()+$h>$this->PageBreakTrigger)
		        $this->AddPage($this->CurOrientation);
		}

		function NbLines($w,$txt){
		    //Computes the number of lines a MultiCell of width w will take
		    $cw=&$this->CurrentFont['cw'];
		    if($w==0)
		        $w=$this->w-$this->rMargin-$this->x;
		    $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
		    $s=str_replace("\r",'',$txt);
		    $nb=strlen($s);
		    if($nb>0 and $s[$nb-1]=="\n")
		        $nb--;
		    $sep=-1;
		    $i=0;
		    $j=0;
		    $l=0;
		    $nl=1;
		    while($i<$nb)
		    {
		        $c=$s[$i];
		        if($c=="\n")
		        {
		            $i++;
		            $sep=-1;
		            $j=$i;
		            $l=0;
		            $nl++;
		            continue;
		        }
		        if($c==' ')
		            $sep=$i;
		        $l+=$cw[$c];
		        if($l>$wmax)
		        {
		            if($sep==-1)
		            {
		                if($i==$j)
		                    $i++;
		            }
		            else
		                $i=$sep+1;
		            $sep=-1;
		            $j=$i;
		            $l=0;
		            $nl++;
		        }
		        else
		            $i++;
		    }
		    return $nl;
		}

		function pdf_order_header( $order_id, $order_date, $order_restaurant, $logo ){
			$this->Image( $logo, 2, 2, $this->getDocumentWidth() );
			$this->Ln(15);
			$this->Cell( 10, $this->line_height, "Pedido: ".$order_id );
			$this->Cell( 66, $this->line_height, $order_date, 0, 0, "R" );
			$this->Ln(4);
			$this->SetFont( 'Arial', 'B', $this->line_height + 2 );
			$this->Cell( $this->getDocumentWidth(), $this->line_height + 4, $order_restaurant, 0, 0, "C" );
			$this->SetFont( 'Arial', '', $this->line_height );
			$this->Ln(10);
		}

		function pdf_order_data_table( $data, $header = array( "Producto", "N", "Total" ) ){

		    $w = $this->widths;

		    for( $i = 0; $i < count( $header ); $i++ ){
		        $this->Cell( $w[$i], $this->line_height, $header[$i], "T", 0, 'C' );
			}

		    $this->Ln();
		    foreach( $data as $row ){
				$product_name = $row["name"];

				if( $row["variations"] ){
					$product_name .= "\n";

					foreach ( $row["variations"] as $key => $variation ) {
						$product_name .= $variation . "\n";
					}
				}

				$product_name = dms_decode_special_chars( $product_name );

				$this->Cell( $this->getDocumentWidth(), 0, "", "T", 0, 'C' );
				$this->Ln(1);
				$this->Row( array( $product_name, $row["qty"], $row["total"] . " " . chr(128) ) );
		        $this->Ln(1);
		    }
			$this->Ln(2);
		}

		function pdf_order_data_table_custom_receipt( $product_types, $header = array( "Producto", "N", "Total" ) ){

		    $w = $this->widths;

		    for( $i = 0; $i < count( $header ); $i++ ){
		        $this->Cell( $w[$i], $this->line_height, $header[$i], "T", 0, 'C' );
			}

		    $this->Ln();
			foreach ( $product_types as $type_key => $products ) {

				switch ( $type_key ) {
					case 0:
						$type = "Primeros";
						break;

					case 1:
						$type = "Segundos";
						break;

					case 2:
						$type = "Otros";
						break;
				}

				$this->SetFont( 'Arial', 'B', $this->line_height );
				$this->Cell( $this->getDocumentWidth(), 1, $type, 0, 0, "C" );
				$this->SetFont( 'Arial', '', $this->line_height );
				$this->Ln(3);

				foreach( $products as $row ){
					$product_name = $row["name"];

					if( $row["variations"] ){
						$product_name .= "\n";

						foreach ( $row["variations"] as $key => $variation ) {
							$product_name .= $variation . "\n";
						}
					}

					$product_name = dms_decode_special_chars( $product_name );

					$this->Cell( $this->getDocumentWidth(), 0, "", "T", 0, 'C' );
					$this->Ln(1);
					$this->Row( array( $product_name, $row["qty"], $row["total"] . " " . chr(128) ) );
			        $this->Ln(1);
			    }

				$this->Ln( $this->line_height );
			}
			$this->Ln(2);
		}

		function pdf_order_note( $order_note ){

			if( $order_note != "" ){
				$this->Cell( $this->getDocumentWidth(), 0, "", "T", 0, 'C' );
				$this->Ln(3);
				$this->MultiCell( $this->getDocumentWidth(), 3, "Notas: " . $order_note );
				$this->Ln(6);
			}
		}

		function pdf_order_total( $order_total, $shipping_cost, $shipping_method_text ){
		    // Anchuras de las columnas
		    $w=$this->widths;
		    // Línea de cierre
		    // $this->Cell( array_sum( $w ), 0, '', 'T' );
			$this->Row( array( $shipping_method_text, "", $shipping_cost ), "subtotal" );
			$this->Ln(1);
			$this->SetFont( 'Arial', 'B', $this->line_height + 1 );
			$this->Row( array( "Total del pedido", "", $order_total ), "total" );
			$this->SetFont( 'Arial', '', $this->line_height );
			$this->Ln(2);
		}

		function pdf_footer_customer( $customer ){

			if( $customer != "" ){
				$text =  chr(161) . "Que aproveche ". $customer ."!";
			}else{
				$text = chr(161) . "Que aproveche!";
			}

			$this->Ln(6);
			$this->Cell( $this->getDocumentWidth(), $this->line_height, $text, 0, 0, "C" );
		}
	}

	$doc_height = 72;
	$doc_width 	= 80;
	$doc_columns_widths = array( 52, 6, 16 );

	$receipts_qty = 4;

	$custom_receipt_restaurants_ids = array( 28 );
	$custom_receipt = false;

	$order_kinds 			= array();
	$order_products 		= array();
	$order 					= wc_get_order( $order_id );
	$order_date 			= $order->order_date;
	$items 					= $order->get_items();
	$order_customer 		= dms_decode_special_chars( $order->billing_first_name );
	$order_note 			= dms_decode_special_chars( $order->customer_message );
	$shipping_cost  		= number_format( floatval( $order->get_total_shipping() ), 2 ) . " " . chr(128);
	$shipping_method_text 	= preg_replace('/ \([^\)]+\)/', '', $order->get_shipping_method() );
	$order_total 			= number_format( floatval( $order->get_total() ), 2 ) . " " . chr(128);
	$order_restaurant_id	= get_post_meta( $order_id, "dms_order_restaurant", true );
	$order_restaurant 		= dms_decode_special_chars( get_field( "dms_restaurant_name", "user_" . $order_restaurant_id ) );
	$receipt_logo_url 		= wp_get_attachment_url( 5010 );

	if( in_array( $order_restaurant_id, $custom_receipt_restaurants_ids ) ){
		$custom_receipt = true;
	}

	$receipt_restaurant = new DMS_PDF();
	$receipt_restaurant->SetPageWidth( $doc_width );
	$receipt_restaurant->SetLineHeight( 9 );
	$receipt_restaurant->SetFont( 'Arial', '', $receipt_restaurant->line_height );
	$receipt_restaurant->SetMargins( 2, 2, 2 );
	$receipt_restaurant->SetAutoPageBreak( true, 2 );
	$receipt_restaurant->SetWidths( $doc_columns_widths );

	foreach ( $items as $key => $item ) {
		$product_id = $item["product_id"];
		$product   	= $order->get_product_from_item( $item );
        $item_meta 	= new WC_Order_Item_Meta( $item, $product );

		$name 		= get_the_title( $product_id );
		$qty 		= $item["qty"];
		$total 		= $item["line_total"];
		$metas 		= $item_meta->get_formatted();
		$variations = array();

		$doc_height += $receipt_restaurant->NbLines( $doc_columns_widths[0], $name ) * 3;
		$doc_height += 2;

		foreach ( $metas as $key => $meta ) {
			$meta_label = $meta["label"];
			$meta_value = $meta["value"];
			$meta_text 	= "-" . $meta_label . ": " . $meta_value;

			$variations[] = $meta_text;

			$doc_height += $receipt_restaurant->NbLines( $doc_columns_widths[0], $meta_text ) * 3;
		}

		$product_line = array(
			"name" 			=> $name,
			"variations" 	=> $variations,
			"qty" 			=> $qty,
			"total" 		=> number_format( floatval( $total), 2 )
		);

		$order_products[] = $product_line;

		if( $custom_receipt ){
			$product_kind = get_field( "dms_product_kind", $product_id );

			switch ($product_kind) {
				case 'Primeros':
					$order_kinds[ 0 ][] = $product_line;
					break;

				case 'Segundos':
					$order_kinds[ 1 ][] = $product_line;
					break;

				default:
					$order_kinds[ 2 ][] = $product_line;
					break;
			}
		}
	}

	if( $custom_receipt && count( $order_kinds ) > 1 ){
		$doc_height += count( $order_kinds ) * ( $receipt_restaurant->line_height + 4 );

		ksort( $order_kinds );

		$receipts_qty = 5;
	}

	if( $order_note ){
		$note_lines = $receipt_restaurant->NbLines( $receipt_restaurant->getDocumentWidth(), "Notas: " . $order_note );
		$doc_height += ( $note_lines * $receipt_restaurant->line_height ) + 10;
	}

	if( $doc_height < $doc_width + 5 ){
		$doc_height = $doc_width + 5;
	}

	for ( $i=0; $i < $receipts_qty; $i++ ) {
		$receipt_restaurant->AddPage( 'P', array( $doc_width, $doc_height ) );
		$receipt_restaurant->pdf_order_header( $order_id, $order_date, $order_restaurant, $receipt_logo_url );

		if( $custom_receipt && count( $order_kinds ) > 1 ){
			$receipt_restaurant->pdf_order_data_table_custom_receipt( $order_kinds );
		}else{
			$receipt_restaurant->pdf_order_data_table( $order_products );
		}

		$receipt_restaurant->pdf_order_note( $order_note );
		$receipt_restaurant->pdf_order_total( $order_total, $shipping_cost, $shipping_method_text );
		$receipt_restaurant->pdf_footer_customer( $order_customer );
	}

	$receipt_restaurant->Output('D', 'zas-recibos-' . $order_id . '.pdf');
}
/* END generate receipt */

/* Decode special chars for fpdf usage */
function dms_decode_special_chars( $word ) {

    $word = str_replace("@","%40",$word);
    $word = str_replace("`","%60",$word);
    $word = str_replace("¢","%A2",$word);
    $word = str_replace("£","%A3",$word);
    $word = str_replace("¥","%A5",$word);
    $word = str_replace("|","%A6",$word);
    $word = str_replace("«","%AB",$word);
    $word = str_replace("¬","%AC",$word);
    $word = str_replace("¯","%AD",$word);
    $word = str_replace("º","%B0",$word);
    $word = str_replace("±","%B1",$word);
    $word = str_replace("ª","%B2",$word);
    $word = str_replace("µ","%B5",$word);
    $word = str_replace("»","%BB",$word);
    $word = str_replace("¼","%BC",$word);
    $word = str_replace("½","%BD",$word);
    $word = str_replace("¿","%BF",$word);
    $word = str_replace("À","%C0",$word);
    $word = str_replace("Á","%C1",$word);
    $word = str_replace("Â","%C2",$word);
    $word = str_replace("Ã","%C3",$word);
    $word = str_replace("Ä","%C4",$word);
    $word = str_replace("Å","%C5",$word);
    $word = str_replace("Æ","%C6",$word);
    $word = str_replace("Ç","%C7",$word);
    $word = str_replace("È","%C8",$word);
    $word = str_replace("É","%C9",$word);
    $word = str_replace("Ê","%CA",$word);
    $word = str_replace("Ë","%CB",$word);
    $word = str_replace("Ì","%CC",$word);
    $word = str_replace("Í","%CD",$word);
    $word = str_replace("Î","%CE",$word);
    $word = str_replace("Ï","%CF",$word);
    $word = str_replace("Ð","%D0",$word);
    $word = str_replace("Ñ","%D1",$word);
    $word = str_replace("Ò","%D2",$word);
    $word = str_replace("Ó","%D3",$word);
    $word = str_replace("Ô","%D4",$word);
    $word = str_replace("Õ","%D5",$word);
    $word = str_replace("Ö","%D6",$word);
    $word = str_replace("Ø","%D8",$word);
    $word = str_replace("Ù","%D9",$word);
    $word = str_replace("Ú","%DA",$word);
    $word = str_replace("Û","%DB",$word);
    $word = str_replace("Ü","%DC",$word);
    $word = str_replace("Ý","%DD",$word);
    $word = str_replace("Þ","%DE",$word);
    $word = str_replace("ß","%DF",$word);
    $word = str_replace("à","%E0",$word);
    $word = str_replace("á","%E1",$word);
    $word = str_replace("â","%E2",$word);
    $word = str_replace("ã","%E3",$word);
    $word = str_replace("ä","%E4",$word);
    $word = str_replace("å","%E5",$word);
    $word = str_replace("æ","%E6",$word);
    $word = str_replace("ç","%E7",$word);
    $word = str_replace("è","%E8",$word);
    $word = str_replace("é","%E9",$word);
    $word = str_replace("ê","%EA",$word);
    $word = str_replace("ë","%EB",$word);
    $word = str_replace("ì","%EC",$word);
    $word = str_replace("í","%ED",$word);
    $word = str_replace("î","%EE",$word);
    $word = str_replace("ï","%EF",$word);
    $word = str_replace("ð","%F0",$word);
    $word = str_replace("ñ","%F1",$word);
    $word = str_replace("ò","%F2",$word);
    $word = str_replace("ó","%F3",$word);
    $word = str_replace("ô","%F4",$word);
    $word = str_replace("õ","%F5",$word);
    $word = str_replace("ö","%F6",$word);
    $word = str_replace("÷","%F7",$word);
    $word = str_replace("ø","%F8",$word);
    $word = str_replace("ù","%F9",$word);
    $word = str_replace("ú","%FA",$word);
    $word = str_replace("û","%FB",$word);
    $word = str_replace("ü","%FC",$word);
    $word = str_replace("ý","%FD",$word);
    $word = str_replace("þ","%FE",$word);
    $word = str_replace("ÿ","%FF",$word);
	$word = str_replace("€",chr(128),$word);

	$word = urldecode($word);

    return $word;
}
/* END decode chars */

/* Calculate full shipping distance */
function calculate_shipping_full_distance( $order_id ) {

    $data 			= array();
	$order 			= wc_get_order( $order_id );
	$order_cost 	= $order->get_subtotal();

	$restaurant_id		= get_post_meta( $order_id, "dms_order_restaurant", true );
	$restaurant_map     = get_field( "dms_restaurant_map", "user_" . $restaurant_id );
    $restaurant_latlng  = $restaurant_map["lat"] . "," . $restaurant_map["lng"];

	$shipping_id 	= get_field( "dms_restaurant_shipping", "user_" . $restaurant_id );
	$center 		= get_field( "dms_shipping_center", $shipping_id );

    $address 		= $order->get_address( 'shipping' );
	$address_latlng = $order->__get("shipping_latlng");

    if( $address_latlng != "" && $address_latlng != null ){
        $client_address = $address_latlng;
        $has_latlng     = true;
    }else{
        $address["state"] 	= "Illes Balears";
        $address["country"] = "Spain";
        $client_address 	= join( ", ", array_filter( array_values( $address ) ) );
        $has_latlng 		= false;
    }

    require_once WP_PLUGIN_DIR . "/woocommerce-distance-rate-shipping/includes/class-wc-shipping-distance-rate.php";
    $distance_rate_class = new WC_Shipping_Distance_Rate();
	$rounding_precision = apply_filters( 'woocommerce_distance_rate_shipping_distance_rounding_precision', 1 );

	$limit_distance = $distance_rate_class->get_api()->get_distance( $client_address, $center, false, 'driving', 'none', 'metric' );

    if ( ! isset( $limit_distance->rows[0] ) || 'OK' !== $limit_distance->rows[0]->elements[0]->status ) {
		$data["not_found"]  = true;
		return $data;
    }

    $limit_distance_value       	= $limit_distance->rows[0]->elements[0]->distance->value;
	$data["center_client_distance"] = $limit_distance_value;

    // SAVE DISTANCE BETWEEN CLIENT AND RESTAURANT
    $client_distance = $distance_rate_class->get_api()->get_distance( $restaurant_latlng, $client_address, false, 'driving', 'none', 'metric' );

    if ( ! isset( $client_distance->rows[0] ) || 'OK' !== $client_distance->rows[0]->elements[0]->status ) {
        $data["not_found"]  = true;
		return $data;
    }

    $client_distance_value         = $client_distance->rows[0]->elements[0]->distance->value;
	$data["rest_client_distance"]  = $client_distance_value;

    // SAVE DISTANCE BETWEEN CENTRAL AND RESTAURANT
    $restaurant_distance    = $distance_rate_class->get_api()->get_distance( $center, $restaurant_latlng, false, 'driving', 'none', 'metric' );

    if ( ! isset( $restaurant_distance->rows[0] ) || 'OK' !== $restaurant_distance->rows[0]->elements[0]->status ) {
        $data["not_found"]  = true;
		return $data;
    }

    $restaurant_distance_value     = $restaurant_distance->rows[0]->elements[0]->distance->value;
	$data["rest_center_distance"]  = $restaurant_distance_value;

    $distance_value 		= $client_distance_value + $restaurant_distance_value + $limit_distance_value;
	$distance 				= round( $distance_value / 1000, $rounding_precision );
	$data["full_distance"] 	= $distance;

    return $data;
}
/* END calculate distance */


function print_in_order_products( $restaurant_id ){

	$cats_args = [
        'hide_empty' => false,
        'meta_query' => [
            [
                'key' => 'term_author',
                'value' => $restaurant_id,
            ]
        ],
		'hide_empty' => true,
    ];

    $plates_cats = get_terms( 'product_cat', $args );

    $plates_args = array (
        'post_type'              => array( 'product' ),
        'orderby'                => 'menu_order title',
        'order'                  => 'ASC',
        'posts_per_page'         => '-1',
        'author'                 => $restaurant_id,
    );

    foreach ( $plates_cats as $key => $plates_cat ) {
        $plates_args['tax_query'] = array(
            array(
                'taxonomy' => 'product_cat',
                'field'    => 'term_id',
                'terms'    => $plates_cat->term_id,
            ),
        );

        $plates = new WP_Query( $plates_args );

        if ( $plates->have_posts() ) {
        ?>

            <div class="backend-products-container">
                <label for="container-term-<?php echo $plates_cat->term_id; ?>"><h3><?php echo $plates_cat->name; ?></h3></label><input class="products-container-radio" type="radio" id="container-term-<?php echo $plates_cat->term_id; ?>" name="products-container">
				<div class="term-products-list">
					<?php
	                while ( $plates->have_posts() ) {
	                    $plates->the_post();

	                    $product_id = get_the_ID();
	                    $product 	= get_product( $product_id );

	                    $product_title = get_the_title();
	                    $product_price = $product->get_price_html();

	                    $button_addtocart_type 	= "action-addtocart";

	                    $product_variables 		= array();
	                    $variation_slug 		= array();

	                    if( $product->is_type( 'simple' ) ){
	                        $product_type = "product-simple";
	                    }elseif( $product->is_type( 'variable' ) ){
	                        $product_type = "product-variable";
	                        $product_variables = $product->get_available_variations();
	                    }

	                    $product_addons = get_product_addons( $product_id );

	                    if ( is_array( $product_addons ) && sizeof( $product_addons ) > 0 ){
	                        $has_addons = true;
	                    }else{
	                        $has_addons = false;
	                    }

	                    if ( $has_addons || $product->is_type( 'variable' ) ) {
	                        $button_addtocart_type = "action-openaditional";
	                    }

	                    ?>
	                    <div id="product-<?php echo $product_id; ?>" class="single-plate <?php echo $button_addtocart_type; ?>">
	                        <div class="product-text">
	                            <span><?php echo $product_title; ?> - <i><?php echo $product_price; ?></i></span>
	                            <i class="fa fa-plus"></i>
	                        </div>

	                        <?php if ( $has_addons || $product->is_type( 'variable' ) ) { ?>
	                            <div class="popup-cover">
	                                <div class="plates-aditional-popup aditional-hidden">
	                                    <h3><?php echo $product_title; ?> - <i><?php echo $product_price; ?></i></h3>
	                                    <?php if( !empty( $product_variables ) ){ ?>
	                                        <div class="aditional-variations">
	                                            <?php
	                                            $attributes = $product->get_variation_attributes();
												$defaults 	= $product->get_variation_default_attributes();

	                                            $attribute_name 		= "";
												$default_attribute_name = "";

	                                            if( count( $attributes ) == 1 ){
	                                                $is_single_variaton = true;

	                                                $attribute_name = key( $attributes );

													if(  in_array( strtolower( key( $attributes ) ), array_keys( $defaults ) ) ){
														$default_attribute_name = key( $attributes );
													}
	                                            }elseif( count( $attributes ) > 1 ){
	                                                $is_multiple_variation = true;

	                                                foreach( array_keys( $attributes ) as $index => $key ) {

														if(  in_array( strtolower( $key ), array_keys( $defaults ) ) ){

															if( $index == 0 ){
		                                                        $default_attribute_name .= $key;
		                                                    }else{
		                                                        $default_attribute_name .= " / " . $key;
		                                                    }
														}

	                                                    if( $index == 0 ){
	                                                        $attribute_name .= $key;
	                                                    }else{
	                                                        $attribute_name .= " / " . $key;
	                                                    }
	                                                }
	                                            }
	                                            ?>

	                                            <div class="product-addon" id="attribute_<?php echo sanitize_title( $attribute_name ); ?>">
	                                                <h4 class="addon-name"><?php echo $attribute_name; ?> <i class="addon-required"><?php _e("Required", THEME_NAME); ?></i></h4>
	                                                <div class="addon-group">
	                                                    <?php

	                                                        foreach ( $product_variables as $key => $single_variable ){
	                                                            $variation_id 			= $single_variable["variation_id"];
	                                                            $variation_price 		= $single_variable["price_html"];
	                                                            $variation_attributes 	= $single_variable["attributes"];

	                                                            $variation_name 		= "";
																$default_variation_name = "";

	                                                            if( $is_single_variaton ){

	                                                                $variation_name = array_values( $variation_attributes )[0];

																	if( $default_attribute_name == $attribute_name ){
																		$default_variation_name = array_values( $variation_attributes )[0];
																	}
	                                                            }elseif( $is_multiple_variation ){

	                                                                foreach( array_values( $variation_attributes ) as $index => $value ) {

																		if( $default_attribute_name == $attribute_name ){
																			if( array_values( $defaults ) == $value ){

																				if( $index == 0 ){
							                                                        $default_variation_name .= $value;
							                                                    }else{
							                                                        $default_variation_name .= " / " . $value;
							                                                    }
																			}
																		}

	                                                                    if( $index == 0 ){
	                                                                        $variation_name .= $value;
	                                                                    }else{
	                                                                        $variation_name .= " / " . $value;
	                                                                    }
	                                                                }
	                                                            }

																if( $variation_name == $default_variation_name ){
																	$checked = checked( $variation_name, $default_variation_name, false );
																}else{
																	$checked = checked( $key, 0, false );
																}

	                                                            ?>
	                                                                <div class="single-addon-input">
	                                                                    <input type="radio" id="variation-<?php echo $variation_id; ?>" <?php echo $checked; ?> name="variation_group_<?php echo $product_id; ?>" value="<?php echo $variation_id; ?>">
	                                                                    <label for="variation-<?php echo $variation_id; ?>"><?php echo $variation_name; ?><?php if( $variation_price ){ echo " - <i>" . $variation_price . "</i>"; } ?></label>
	                                                                </div>
	                                                            <?php
	                                                        }
	                                                    ?>
	                                                </div>
	                                            </div>
	                                        </div>
	                                    <?php } ?>

	                                    <?php if( $has_addons ){ ?>
	                                        <div class="aditional-addons">
	                                            <?php

	                                            foreach ( $product_addons as $key => $addon_group ) {
	                                                $attribute_name = $addon_group["name"];
	                                                $options 		= $addon_group["options"];
	                                                $attribute_slug = $addon_group["field-name"];
	                                                ?>
	                                                <div class="product-addon" id="attribute_<?php echo sanitize_title( $attribute_name ); ?>">
	                                                    <h4 class="addon-name"><?php echo $attribute_name; ?></h4>
	                                                    <div class="addon-group">
	                                                        <?php
	                                                        foreach( $options as $single_variable ){
	                                                            $variation_name 	= $single_variable["label"];
	                                                            $raw_price 			= $single_variable["price"];
	                                                            $variation_price 	= str_replace(".", ",", number_format( $raw_price, 2 ) ) . "€";
	                                                            $variation_title 	= $attribute_name . " (" . $variation_price . ")";

	                                                            ?>
	                                                                <div class="single-addon-input">
	                                                                    <input type="checkbox" id="variation-<?php echo $single_variation_id; ?>" class="addon-checkbox" name="<?php echo $attribute_slug; ?>" data-group="<?php echo $variation_title; ?>" data-addon="<?php echo $variation_name; ?>" data-price="<?php echo $raw_price; ?>" value="<?php echo $single_variation_id; ?>">
	                                                                    <label for="variation-<?php echo $single_variation_id; ?>"><?php echo $variation_name; ?><?php if( $variation_price ){ echo " - <i>" . $variation_price . "</i>"; } ?></label>
	                                                                </div>
	                                                            <?php
	                                                        }
	                                                        ?>
	                                                    </div>
	                                                </div>
	                                                <?php
	                                            }
	                                            ?>
	                                        </div>
	                                    <?php } ?>

	                                    <div class="aditional-action">
	                                        <input type="button" class="popup-button button-primary dms-action-button button-ok aditional-action-addtocart <?php if( !empty( $product_variables ) ){ echo 'has-variations'; } ?> <?php if( $has_addons ){ echo 'has-addons'; } ?>" value="<?php _e("Add to cart", THEME_NAME); ?>" >
	                                    </div>
	                                </div>
	                            </div>
	                        <?php } ?>
	                    </div>
	                    <?php
	                }

	                $plates->wp_reset_postdata();
	                $post = $saved_global_post;
	                ?>
				</div>
            </div>
        <?php
        }
    }
}

function dms_get_restaurant_hours( $restaurant_id, $type = "kitchen" ){

	if( !$restaurant_id ) return false;

	$return_string 		= "";
	$current_time 		= current_time( 'timestamp' );
	$current_week       = date( 'w', $current_time );

	switch ( $type ) {
		case 'delivery':
			$delivery_id 		= get_field( "dms_restaurant_delivery", "user_" . $restaurant_id );
			$restaurant_hours   = ( get_field( "dms_restaurant_hours", $delivery_id ) ) ? get_field( "dms_restaurant_hours", $delivery_id ) : array();
			break;

		case 'kitchen':
		default:
			$restaurant_hours   = ( get_field( "dms_restaurant_schedule", "user_" . $restaurant_id ) ) ? get_field( "dms_restaurant_schedule", "user_" . $restaurant_id ) : array();
			break;
	}

	$week_found = false;
	if( !empty( $restaurant_hours ) ){
		foreach ( $restaurant_hours as $key => $restaurant_hour ) {

			if( $week_found ) continue;

			$day = $restaurant_hour["dms_restaurant_hours_day"];

			if( $day == $current_week ){
				$morning_start    = $restaurant_hour["dms_restaurant_hours_morning_start"];
				$morning_end      = $restaurant_hour["dms_restaurant_hours_morning_end"];
				$afternoon_start  = $restaurant_hour["dms_restaurant_hours_afternoon_start"];
				$afternoon_end    = $restaurant_hour["dms_restaurant_hours_afternoon_end"];

				$time_morning_start    = ( $morning_start ) ? ( $morning_start ) : false;
				$time_morning_end      = ( $morning_end ) ? ( $morning_end ) : false;
				$time_afternoon_start  = ( $afternoon_start ) ? ( $afternoon_start ) : false;
				$time_afternoon_end    = ( $afternoon_end ) ? ( $afternoon_end ) : false;

				if( $time_morning_end && $time_afternoon_start ){
					$return_string = sprintf(
						__('%s to %s and %s to %s', THEME_NAME),
						$time_morning_start,
						$time_morning_end,
						$time_afternoon_start,
						$time_afternoon_end
					);
				}else{
					$return_string = sprintf(
						__('%s to %s ', THEME_NAME),
						$time_morning_start,
						$time_afternoon_end
					);
				}

				$week_found = true;
			}
		}
	}

	if( !$week_found ){
		$return_string = false;
	}

	return $return_string;
}

function dms_dump($var){
	$current_user = wp_get_current_user();

	if ( 1 == $current_user->ID ) {
	    // ONLY AUTHOR DMS
	    var_dump($var);
	}
}

/* Change admin notice on post lock */
// add_filter( 'show_post_locked_dialog', 'filter_show_post_locked_dialog', 10, 3 );
function filter_show_post_locked_dialog( $true, $post, $user ) {
	$slug = 'shop_order';

    if ( $slug != $post->post_type ) return $true;

    return false;
};

function dms_get_true_order_creation_date( $order_id ){
	$order 		= get_post( $order_id );
	$order_name = $order->post_name;
	$order_date = convert_order_postname_to_date( $order_name );

	return $order_date;
}

function convert_order_postname_to_date( $postname ){

	$date_string = translate_spanish_months_to_english( str_replace( "order-", "", $postname ) );

	if( ( $position = strrpos( $date_string, '-am' ) ) !== false ){
		$meridiem = "a";
		$date_string = substr( $date_string, 0, $position + 3 );

	}elseif( ( $position = strrpos( $date_string, '-pm' ) ) !== false ){
		$meridiem = "A";
		$date_string = substr( $date_string, 0, $position + 3 );
	}

	$date = date_create_from_format( 'F-d-Y-hi-' . $meridiem, $date_string );
	$formated_date = date_format( $date, 'Y-m-d H:i:s' );

	return $formated_date;
}

function translate_spanish_months_to_english( $string ){
	$string = str_replace( "enero", 		"january", $string );
	$string = str_replace( "febrero", 		"february", $string );
	$string = str_replace( "marzo", 		"march", $string );
	$string = str_replace( "abril", 		"april", $string );
	$string = str_replace( "mayo", 			"may", $string );
	$string = str_replace( "junio", 		"june", $string );
	$string = str_replace( "julio", 		"july", $string );
	$string = str_replace( "agosto", 		"august", $string );
	$string = str_replace( "septiembre", 	"september", $string );
	$string = str_replace( "octubre", 		"october", $string );
	$string = str_replace( "noviembre", 	"november", $string );
	$string = str_replace( "diciembre", 	"december", $string );

	return $string;
}

function secondsToTime($inputSeconds) {
    $secondsInAMinute = 60;
    $secondsInAnHour  = 60 * $secondsInAMinute;
    $secondsInADay    = 24 * $secondsInAnHour;

    // extract days
    $days = floor($inputSeconds / $secondsInADay);

    // extract hours
    $hourSeconds = $inputSeconds % $secondsInADay;
    $hours = floor($hourSeconds / $secondsInAnHour);

    // extract minutes
    $minuteSeconds = $hourSeconds % $secondsInAnHour;
    $minutes = floor($minuteSeconds / $secondsInAMinute);

    // extract the remaining seconds
    $remainingSeconds = $minuteSeconds % $secondsInAMinute;
    $seconds = ceil($remainingSeconds);

    // return the final array
	$formated_time = "";

	if( $days > 0 ) $formated_time .= $days . " dias ";
	if( $hours > 0 ) $formated_time .= $hours . " h. ";
	if( $minutes > 0 ) $formated_time .= $minutes . " min. ";
	if( $seconds > 0 ) $formated_time .= $seconds . " sec. ";

    return $formated_time;
}
