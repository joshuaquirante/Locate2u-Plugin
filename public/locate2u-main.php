<?php
/**
 * Locate2u Plugin File.
 *
 * @package Locate2uPlugin\Main
 */


if ( ! defined( 'L2U_PLUGIN_PATH' ) ) {
	define( 'L2U_PLUGIN_PATH', plugin_dir_path( L2U_PLUGIN_FILE ) );
}

if ( ! defined( 'L2U_PLUGIN_URL' ) ) {
	define( 'L2U_PLUGIN_URL', plugin_dir_url( L2U_PLUGIN_FILE ) );
}



/*** INCLUDE THE API FORM FILE ***/
if( !function_exists("locate2u_plugin") ) {

    function locate2u_plugin(){
        include( L2U_PLUGIN_PATH . 'admin/template-parts/locate2u-api-form.php');
    }

}

/*** START FOR LOCATE2U PLUGIN ADMIN PART ***/

include ( L2U_PLUGIN_PATH . 'library/Locate2uApi.php');
include ( L2U_PLUGIN_PATH . 'library/Locate2uApiConfig.php');





/* load Locate2u Plugin assets and Js File */

add_action('wp_enqueue_scripts', 'load_locate2u_assets', 99);
function load_locate2u_assets()
{

    // Bootstrap
	wp_enqueue_style( 'bootstrap-style', L2U_PLUGIN_URL . 'assets/bootstrap-5.1.3/css/bootstrap.min.css', 99 );
	wp_enqueue_script( 'bootstrap-js' , L2U_PLUGIN_URL . 'assets/bootstrap-5.1.3/js/bootstrap.bundle.min.js', array('jquery') , 99 );

	wp_deregister_script('jquery');
	wp_enqueue_script( 'jquery', L2U_PLUGIN_URL . 'js/jquery.min.js' , 99);
	wp_enqueue_script( 'jquery-ui', L2U_PLUGIN_URL . 'js/jquery-ui.min.js' , 99);

	wp_enqueue_style( 'jquery-ui', L2U_PLUGIN_URL . 'assets/css/jquery-ui.theme.min.css', array() , 99);

	if ( ! is_admin() ) {
		wp_enqueue_style( 'locate2u-wp-v1-main', L2U_PLUGIN_URL . 'assets/css/main.css' , 99);
	}

	if ( is_admin() ) {
		wp_enqueue_style( 'locate2u-wp-v1-main', L2U_PLUGIN_URL . 'assets/css/admin.css' , 99);
	}

	wp_enqueue_script( 'locate2u-wp-v1-navigation', L2U_PLUGIN_URL . 'js/navigation.js', 99);		
	wp_enqueue_script( 'jquery-validator', L2U_PLUGIN_URL . 'js/jquery-validate.js' , 99);
	wp_enqueue_script( 'main', L2U_PLUGIN_URL . 'js/main.js', array(), rand(100,999), true );

}

/* load Locate2u Plugin assets and Js File in admin Area */
add_action( 'admin_enqueue_scripts', 'load_locate2u_assets_admin' );
function load_locate2u_assets_admin($hook)
    {

    $current_screen = get_current_screen();

    if ( strpos($current_screen->base, 'locate2u-plugin') === false) {
        return;
    } else {
		load_locate2u_assets();       
        }
    }

/* Upload Engine */
function load_wp_media_files() {
	wp_enqueue_media();
}
add_action( 'admin_enqueue_scripts', 'load_wp_media_files' );

/* Display post state in Page List */
add_filter( 'display_post_states', 'ecs_add_post_state', 10, 2 );

function ecs_add_post_state( $post_states, $post ) {

	if( $post->post_name == 'booking-page' ) {
		$post_states[] = 'Locate2u Booking Page';
	}

	if( $post->post_name == 'thank-you' ) {
		$post_states[] = 'Locate2u Thank You Page';
	}

	return $post_states;
}


/* Set Data to store in DB */
add_action('wp_ajax_nopriv_verify_locate2u', 'verify_locate2u');
add_action('wp_ajax_verify_locate2u', 'verify_locate2u');
function verify_locate2u(){
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

		//$data = $_POST['data'];
		$data = array_map( 'sanitize_text_field', $_POST['data'] );

		check_to_locate2u($data);  		

		if(http_response_code() == 200){	

			$insert_cid_result = insert_plugin_settings($data);
			if(isset($insert_cid_result["success"])){				
				echo json_encode($insert_cid_result, JSON_PRETTY_PRINT);
			}
    	}       
	}
    wp_die();
}

/*
REVOKE LOCATE2U CREDENTIALS
*/
add_action('wp_ajax_revoke_locate2u_credentials', 'revoke_locate2u_credentials');
add_action('wp_ajax_revoke_locate2u_credentials', 'revoke_locate2u_credentials');
function revoke_locate2u_credentials(){
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	 	$revoke_credentials = l2u_revoke_app_credentials();

		if(isset($revoke_credentials["success"])){				
			echo json_encode($revoke_credentials, JSON_PRETTY_PRINT);
		}
		
    }
    wp_die();
}

/* Cheking Client ID and Client Secret in Locate2u App */
function check_to_locate2u($data) {
    $my_data = process_form_data($data);

    $test_client_id = $my_data['client_id'];
	$test_secret_key = $my_data['client_secret'];
	$locate2u = new \Locate2uApiConnection($test_client_id, $test_secret_key);
    return $locate2u;
}

/* Cheking for Null or Empty Input Data */
function l2u_empty_null_str($str){
    return (!isset($str) || trim($str) === '');
}


/* Cheking for Null or Empty Input Data */
function process_form_data($data){
    if(!l2u_empty_null_str($data['locate2u_client_id'])){
        $client_id = $data['locate2u_client_id'];
    }

    if(!l2u_empty_null_str($data['locate2u_secret_key'])){
        $client_secret = $data['locate2u_secret_key'];
    }

    return array(
        'client_id' => $client_id,
        'client_secret' => $client_secret
    );    
}

/*** END FOR LOCATE2U PLUGIN ADMIN PART ***/





/*** START FOR LOCATE2U BOOKING FUNCTION ***/

add_action('wp_ajax_nopriv_create_stop', 'create_stop');
add_action('wp_ajax_create_stop', 'create_stop');
function create_stop(){
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		//$data = $_POST['data'];

		$data = array_map( 'sanitize_text_field', $_POST['data'] );

		// You can add additional code here if you need to process more data for stops like adding bookingID
		$canCreateStops = can_create_stops($data);

		if($canCreateStops["isExecute"] == false){
			echo json_encode($canCreateStops, JSON_PRETTY_PRINT);
			wp_die();
		}

		$result = send_to_locate2u($data);
		echo json_encode($result, JSON_PRETTY_PRINT);
	}
	WP_DIE();
}

add_action('wp_ajax_nopriv_create_shipment', 'create_shipment');
add_action('wp_ajax_create_shipment', 'create_shipment');
function create_shipment(){
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		//$data = $_POST['data'];
		$data = array_map( 'sanitize_text_field', $_POST['data'] );

		$canCreateShipment = can_create_shipment($data);

		if($canCreateShipment["isExecute"] == false){
			echo json_encode($canCreateShipment, JSON_PRETTY_PRINT);
			wp_die();
		}
		$result = send_to_locate2u($data, true);
		echo json_encode($result, JSON_PRETTY_PRINT);
	}

	WP_DIE();
}


function can_create_stops($data){
	$translatedError = array();

	$date_now = date('m-d-Y');
	$date = str_replace('/', '-', $date_now);
	$current_tripDate = DateTime::createFromFormat('m-d-Y', $date)->format('Y-m-d');

	$original_date = $data['date'];
	$date = str_replace('/', '-', $original_date);
	$tripDate = DateTime::createFromFormat('m-d-Y', $date)->format('Y-m-d');


	$local_time  = current_time('mysql');
	$time_value = explode(" ", $local_time);


	if(format_null_empty_str($data["name"]))
		$translatedError[] = "Name is empty.";

	if(format_null_empty_str($data["email"]) || (!filter_var($data["email"], FILTER_VALIDATE_EMAIL)))
		$translatedError[] = "Pickup email is invalid or empty.";

	if(format_null_empty_str($data["phone"]) || !is_numeric($data["phone"]))
		$translatedError[] = "Pickup phone is empty or invalid.";

	if(format_null_empty_str($data["street_name"]) && format_null_empty_str($data["street_number"])  && format_null_empty_str($data["suburb"]) && format_null_empty_str($data["postal_code"]) && format_null_empty_str($data["state"]))
		$translatedError[] = "Please check the pickup address.";

	if(format_null_empty_str($data["time"]) )
		$translatedError[] = "Date is empty.";

	if($tripDate <= $current_tripDate && $data["time"] <= $time_value[1] )
		$translatedError[] = "Invalid time, past time is selected.";

	if(count($translatedError) >= 1){
		return array(
			"isExecute" => false,
			"is_success" => "error",
			"translated_error" => $translatedError
		);		
	}else{
		return array(
			"isExecute" => true
		);
	}

}

function can_create_shipment($data){

	$translatedError = array();
	$date_now = date("m-d-Y");
	$date = str_replace('/', '-', $date_now);

	if($data['pickup_date'] > $data['drop_date'] || $data['pickup_date'] == $data['drop_date'])
		$translatedError[] = "Pickup date is greater than drop date.";

	/*Pickup Form*/
	if(format_null_empty_str($data["pickup_date"]))
		$translatedError[] = "Pickup date is empty.";

	if($data["pickup_date"] < $date)
		$translatedError[] = "Invalid pickup date, past date is selected.";
	
	if(format_null_empty_str($data["pickup_name"]))
		$translatedError[] = "Pickup name is empty.";

	if(format_null_empty_str($data["pickup_email"]) || (!filter_var($data["pickup_email"], FILTER_VALIDATE_EMAIL)))
		$translatedError[] = "Pickup eail is invalid or empty.";

	if(format_null_empty_str($data["pickup_phone"]) || !is_numeric($data["pickup_phone"]))
		$translatedError[] = "Pickup phone is empty or invalid.";

	if(format_null_empty_str($data["pickup_street_name"]) && format_null_empty_str($data["pickup_street_number"])  && format_null_empty_str($data["pickup_suburb"]) && format_null_empty_str($data["pickup_postal_code"]) && format_null_empty_str($data["pickup_state"]))
		$translatedError[] = "Please check the pickup address.";
	
	/*Drop Form*/

	if(format_null_empty_str($data["drop_date"]))
		$translatedError[] = "Drop date is empty.";

	if($data["drop_date"] < $date)
		$translatedError[] = "Invalid drop date, past date is selected.";

	if(format_null_empty_str($data["drop_name"]))
		$translatedError[] = "Drop name is empty.";

	if(format_null_empty_str($data["drop_email"]) || (!filter_var($data["drop_email"], FILTER_VALIDATE_EMAIL)))
		$translatedError[] = "Drop email is invalid or empty.";

	if(format_null_empty_str($data["drop_phone"]) || !is_numeric($data["drop_phone"]))
		$translatedError[] = "Pickup phone is empty or invalid.";

	if(format_null_empty_str($data["drop_street_name"]) && format_null_empty_str($data["drop_street_number"])  && format_null_empty_str($data["drop_suburb"]) && format_null_empty_str($data["drop_postal_code"]) && format_null_empty_str($data["drop_state"]))
	$translatedError[] = "Please check the pickup address.";

	

	if(count($translatedError) >= 1){
		return array(
			"isExecute" => false,
			"is_success" => "error",
			"translated_error" => $translatedError
		);		
	}else{
		return array(
			"isExecute" => true
		);
	}
}

function error_list(){
	return array(
		"BATCH_IMPORT_REQUIRED" => "Address is required.",
		"BATCH_IMPORT_TRIPDATE_IN_PAST" => "Delivery date is in the past."
	);
}

function send_to_locate2u($data, $isShipment = false) {
	$credential_results = l2u_get_app_credentials();
	$client_id = $credential_results[0]["settings_value"];
	$client_secret =  $credential_results[1]["settings_value"];

	$l2u_client_id = encrypt_decrypt_data($client_id,'d');
	$l2u_client_secret = encrypt_decrypt_data($client_secret,'d');

	$locate2u = new \Locate2uApi($l2u_client_id, $l2u_client_secret);

	$data_array = [];
	$response = null;

	$success_result = null;
	$errorCodes = error_list();

	if($isShipment){		
		$data_array = format_shipment_data($data);
		$response = $locate2u->importShipment( $data_array );
	}else {
		$data_array = format_stop_data($data);
		$response = $locate2u->importStop( $data_array );
	}


	if($response->errorCode){		
		$success_result = array("is_success" => "error", "data" => $response);			
		$success_result['translated_error'] = $errorCodes[$response->errorCode] ?? "Something went wrong. please try again.";
	}else{		
		if($response->success != NULL || isset($response->success)){
			$success_result = array("is_success" => "error", "data" => $response);	
			$success_result['translated_error'] = "Something went wrong. please try again.";
		}else{
			$success_result = array("is_success" => "success", "data" => $response);
		}
	}

	return $success_result;

}



function format_address($address_data){
	$fullAddress = "";

	if(!format_null_empty_str($address_data["unit_suite_number"]))
		$fullAddress .= ($address_data["unit_suite_number"] . " ");

	if(!format_null_empty_str($address_data["street_number"]))
		$fullAddress .= ($address_data["street_number"] . " ");

	if(!format_null_empty_str($address_data["street_name"]))
		$fullAddress .= ($address_data["street_name"] . ", ");

	if(!format_null_empty_str($address_data["suburb"]))
		$fullAddress .= ($address_data["suburb"] . ", ");

	if(!format_null_empty_str($address_data["postal_code"]))
		$fullAddress .= ($address_data["postal_code"] . ", ");

	if(!format_null_empty_str($address_data["state"]))
		$fullAddress .= ($address_data["state"] . " ");

	return $fullAddress;
}

function format_null_empty_str($str){
    return (!isset($str) || trim($str) === '');
}

function format_stop_data($data){
	return	array(process_single_stop($data));
}

function format_shipment_data($data){
	// This function should format the data from the client side in a form that the Locate2u Api will accept.
	$pickupPattern = "/pickup_/i";
	$dropPattern =  "/drop_/i";

	$pickupData = process_shipment_address_and_contact($data, $pickupPattern);
	$dropData = process_shipment_address_and_contact($data, $dropPattern);
	
		return	array(
			array (
			'notes' 			=> $data["note"],
			'pickupStop'        => process_single_stop($pickupData),
			'dropStop'			=> process_single_stop($dropData)
			),
		);		
	
}

function process_shipment_address_and_contact($data, $pattern){

	$processedData = [];

	foreach ($data as $key => $value) {
		if(preg_match($pattern, $key)){
			$finalKey = preg_replace($pattern, '', $key);
			$processedData[$finalKey] = $value;
		}else {
			$processedData[$key] = $value;
		}
	}

	return $processedData;
}


function process_single_stop($data){
	$original_date = $data['date'];
	$date = str_replace('/', '-', $original_date);
	$tripDate = DateTime::createFromFormat('m-d-Y', $date)->format('Y-m-d');

	$contact = 	array (
					'name' 	=> $data['name'],
					'phone' => $data['phone'],
					'email' => $data['email'],
					// 'name' 	=> $data['name'],
					// 'phone' => $data['phone'],
					// 'email' => $data['email'],
				);

	return array (
		'contact' 			=> $contact,
		//'address' 			=> NULL,
		'address' 			=> format_address($data),
		'notes' 			=> $data["note"],
		'tripDate' 			=> $tripDate,
		'appointmentTime'	=> $data['time']
	);
}


/**
 * Implement the Custom Header feature.
 */
if( !function_exists("locate2u_plugin_inc") ) {

    function locate2u_plugin_inc(){
        include( L2U_PLUGIN_PATH . '/inc/custom-header.php');
        include( L2U_PLUGIN_PATH . '/inc/template-tags.php');
        include( L2U_PLUGIN_PATH . '/inc/template-functions.php');
        include( L2U_PLUGIN_PATH . '/inc/customizer.php');
        include( L2U_PLUGIN_PATH . '/inc/library.php');
    }

}

/*** END FOR LOCATE2U BOOKING FUNCTION ***/


add_action('wp_ajax_nopriv_save_branding_form', 'save_branding_form');
add_action('wp_ajax_save_branding_form', 'save_branding_form');
function save_branding_form(){

	if ($_SERVER['REQUEST_METHOD'] == 'POST') {

		//$data = $_POST['data'];
		$data = array_map( 'sanitize_text_field', $_POST['data'] );   	
		
		$results = update_business_settings($data);	

		if(isset($results["success"])){				
			echo json_encode($results, JSON_PRETTY_PRINT);
		}		
		
	}
	wp_die();
}





