<?php

add_action( "admin_init", function(){
    if ( get_option( $opt_name = "l2u_plugin_show_wizard_notice" ) ) {
        delete_option( $opt_name );
        add_action( "admin_notices", "l2u_plugin_wizard_notice" );
    } return;
});

/**
  * Check if user has completed wizard already
  * if so then return true (don't show notice)
  *
  */
  function l2u_plugin_wizard_completed() {
    return false;
}

function l2u_plugin_wizard_notice() {

    if ( l2u_plugin_wizard_completed() ) return; // completed already
    ?>

    <div class="updated notice is-dismissible">
        <p>Welcome to my plugin! You're almost there, but we think this wizard might help you setup the plugin.</p>
        <p><a href="admin.php?page=locate2u-plugin" class="button button-primary">Setup Locate2u</a> <a href="javascript:window.location.reload()" class="button">dismiss</a></p>
    </div>

    <?php

}



// Encrypt and Decript the CID and Secret
function encrypt_decrypt_data( $stringToHandle = "", $encryptDecrypt = 'e'){
    // Set default output value
    $output = null;
    // Set secret keys
    $secret_key = AUTH_SALT; // Change this!
    $secret_iv = SECURE_AUTH_SALT; // Change this!

    $key = hash('sha256',$secret_key);
    $iv = substr(hash('sha256',$secret_iv),0,16);
    // Check whether encryption or decryption
    if($encryptDecrypt == 'e'){
       // We are encrypting
       $output = base64_encode(openssl_encrypt($stringToHandle,"AES-256-CBC",$key,0,$iv));
    }else if($encryptDecrypt == 'd'){
       // We are decrypting
       $output = openssl_decrypt(base64_decode($stringToHandle),"AES-256-CBC",$key,0,$iv);
    }
    // Return the final value
    return $output;
}


function create_booking_page() {
 
   if ( ! current_user_can( 'activate_plugins' ) ) return;
   
   global $wpdb;
   
   if ( null === $wpdb->get_row( "SELECT post_name FROM {$wpdb->prefix}posts WHERE post_name = 'booking-page'", 'ARRAY_A' ) ) {
      
     $current_user = wp_get_current_user();
     
     // create post object
     $create_booking_page = array(
       'post_title'  => __( 'Booking Page' ),
       'post_status' => 'private',
       'post_author' => $current_user->ID,
       'post_type'   => 'page',
     );
     
     // insert the post into the database
     wp_insert_post( $create_booking_page );
   }

   if ( null === $wpdb->get_row( "SELECT post_name FROM {$wpdb->prefix}posts WHERE post_name = 'thank-you'", 'ARRAY_A' ) ) {
      
       $current_user = wp_get_current_user();
       
       // create post object
       $thankyou_page = array(
         'post_title'  => __( 'Thank You' ),
         'post_status' => 'private',
         'post_author' => $current_user->ID,
         'post_type'   => 'page',
       );
       
       // insert the post into the database
       wp_insert_post( $thankyou_page );
   }

   //set Thank you page template in Thank you page
   $args = array(
       'post_type' => 'page',
       'post_status' => array('private')
   );

   $posts = get_posts($args);

   foreach ($posts as $post ) {
       if( $post->post_name == "thank-you" && $post->post_status == "private" ){
           $post_meta_id = $post->ID;
           $post_meta_name = $post->post_name;
           update_post_meta( $post_meta_id, '_wp_page_template', 'page-templates/thank-you.php' );
           update_thankyou_page_value($post_meta_name);
       }
   }	
}


function update_thankyou_page_value($post_id){
   global $wpdb;
   $sql = $wpdb->prepare( "UPDATE {$wpdb->prefix}l2u_plugin_settings SET settings_value = '$post_id'  WHERE settings_name = 'locate2u_thankyou_page'" );
   $result_data = $wpdb->query( $sql, ARRAY_A );
}



function update_page_status(){
// Publish Booking and Thank You Page
   $pages_status = check_page_status();
   
   $my_post_booking = array(
       'ID'     		=> $pages_status[0]["booking_page_id"],
       'post_status'   => 'publish',
   );

   $my_post_thankyou = array(
       'ID'     		=> $pages_status[1]["thank_you_page_id"],
       'post_status'   => 'publish',
   );	

   // Update the post into the database
   $post_booking = wp_update_post( $my_post_booking );
   $post_thankyou = wp_update_post( $my_post_thankyou );

   if(isset($post_booking) && isset($post_thankyou)){
       $booking_post_id = $pages_status[0]["booking_page_id"];
       update_page_template($booking_post_id);
   }else{
       return array(
           "error" => "publishing error"
       );
   }

}


function update_page_template($booking_page_id){

   $post_id = $booking_page_id;
   $booking_type_value = check_booking_type();


   if($booking_type_value["settings_value"] == "single-stop"){
       update_post_meta( $post_id, '_wp_page_template', 'page-templates/stop-template.php' );
   }else{
       update_post_meta( $post_id, '_wp_page_template', 'page-templates/shipment-template.php' );
   }

}



function check_page_status(){
   if ( ! current_user_can( 'activate_plugins' ) ) return;

   global $wpdb;

   $args = array(
       'post_type' => 'page',
       'post_status' => array('private', 'publish'),
   );
   $posts = get_posts($args);


   foreach ($posts as $post ) {
       if($post->post_name == "booking-page"){
           $post_results[] = array(
               "booking_page_id" => $post->ID,
               "booking_page_status" => $post->post_status
           );	
       }

       if($post->post_name == "thank-you"){
           $post_results[] = array(
               "thank_you_page_id" => $post->ID,
               "thank_you_page_status" => $post->post_status
           );	
       }
   }

   return $post_results;
}


function update_business_settings($data){
   global $wpdb;	

   foreach($data as $key => $val){
       if($key == "upload-btn") continue;
       if($val == "") $val = 0;
       $sql = $wpdb->prepare( "UPDATE {$wpdb->prefix}l2u_plugin_settings SET settings_value = '$val'  WHERE settings_name = '$key'" );
       $results[] = $wpdb->query($sql, ARRAY_A);
       
   }

   if ( $wpdb->last_error ) {
       error_log( 'DB ERROR: ' . ( __FUNCTION__ . ' failed.' . "\n" . $wpdb->last_error ) );
   }else{		

       $check_page_status = check_page_status();

       if($check_page_status[0]["booking_page_status"] == "private" && $check_page_status[1]["thank_you_page_status"] == "private"){
           update_page_status();
       }else{
           update_page_template($check_page_status[0]["booking_page_id"]);
       }

       return array(
           "success" => $results
       );
   }
}



/**
* Create create_l2u_plugin_settings table if not exists
*/
function create_l2u_plugin_settings_tbl()
{
   global $wpdb;

   $table_name = $wpdb->prefix . 'l2u_plugin_settings';
   
   $charset_collate = $wpdb->get_charset_collate();

   $sql = "CREATE TABLE IF NOT EXISTS $table_name (
       id int(11) NOT NULL AUTO_INCREMENT,
       settings_name varchar(255) NOT NULL,
       settings_value varchar(255) NOT NULL,
       PRIMARY KEY  (id)
   ) $charset_collate;";

   dbDelta( $sql );

}


function insert_l2u_pre_settings(){
   global $wpdb;

   $sql = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}l2u_plugin_settings" );

   if($sql == 0){
       $wpdb->insert( "{$wpdb->prefix}l2u_plugin_settings", array(
           'settings_name' => 'locate2u_client_id',
              'settings_value' => '0',
       ) );

       $wpdb->insert( "{$wpdb->prefix}l2u_plugin_settings", array(
           'settings_name' => 'locate2u_secret_key',
           'settings_value' => '0',
       ) );		

       $wpdb->insert( "{$wpdb->prefix}l2u_plugin_settings", array(
           'settings_name' => 'locate2u_business_name',
           'settings_value' => '0',
       ) );

       $wpdb->insert( "{$wpdb->prefix}l2u_plugin_settings", array(
           'settings_name' => 'locate2u_logo_url',
           'settings_value' => '0',
       ) );

       $wpdb->insert( "{$wpdb->prefix}l2u_plugin_settings", array(
           'settings_name' => 'locate2u_booking_type',
           'settings_value' => '0',
       ) );

       $wpdb->insert( "{$wpdb->prefix}l2u_plugin_settings", array(
           'settings_name' => 'locate2u_thankyou_page',
           'settings_value' => '0',
       ) );		

       $wpdb->insert( "{$wpdb->prefix}l2u_plugin_settings", array(
           'settings_name' => 'locate2u_address_checkbox',
           'settings_value' => '0',
       ) );

       $wpdb->insert( "{$wpdb->prefix}l2u_plugin_settings", array(
           'settings_name' => 'locate2u_address_streetnumber',
           'settings_value' => '0',
       ) );

       $wpdb->insert( "{$wpdb->prefix}l2u_plugin_settings", array(
           'settings_name' => 'locate2u_address_streetname',
           'settings_value' => '0',
       ) );

       $wpdb->insert( "{$wpdb->prefix}l2u_plugin_settings", array(
           'settings_name' => 'locate2u_address_suburb',
           'settings_value' => '0',
       ) );

       $wpdb->insert( "{$wpdb->prefix}l2u_plugin_settings", array(
           'settings_name' => 'locate2u_address_state',
           'settings_value' => '0',
       ) );

       $wpdb->insert( "{$wpdb->prefix}l2u_plugin_settings", array(
           'settings_name' => 'locate2u_address_postcode',
           'settings_value' => '0',
       ) );

       $wpdb->insert( "{$wpdb->prefix}l2u_plugin_settings", array(
           'settings_name' => 'locate2u_address_region',
           'settings_value' => '0',
       ) );

       $wpdb->insert( "{$wpdb->prefix}l2u_plugin_settings", array(
           'settings_name' => 'locate2u_pickup_default_checkbox',
           'settings_value' => '0',
       ) );

       $wpdb->insert( "{$wpdb->prefix}l2u_plugin_settings", array(
           'settings_name' => 'locate2u_pickup_default_name',
           'settings_value' => '0',
       ) );

       $wpdb->insert( "{$wpdb->prefix}l2u_plugin_settings", array(
           'settings_name' => 'locate2u_pickup_default_email',
           'settings_value' => '0',
       ) );

       $wpdb->insert( "{$wpdb->prefix}l2u_plugin_settings", array(
           'settings_name' => 'locate2u_pickup_default_phone',
           'settings_value' => '0',
       ) );
   }

   
}



function l2u_check_app_credentials(){
   global $wpdb;
   $sql_cid = $wpdb->prepare( "SELECT settings_value FROM {$wpdb->prefix}l2u_plugin_settings WHERE settings_name = 'locate2u_client_id'" );
   $cid_results = $wpdb->get_row( $sql_cid, ARRAY_A );

   $sql_cid_secret = $wpdb->prepare( "SELECT settings_value FROM {$wpdb->prefix}l2u_plugin_settings WHERE settings_name = 'locate2u_secret_key'" );
   $cid_secret_results = $wpdb->get_row( $sql_cid_secret, ARRAY_A );

   if($cid_results["settings_value"] == 0 && $cid_secret_results["settings_value"] ==0){
       return false;
   }else{
       return true;
   }
}


function l2u_get_app_credentials(){
   global $wpdb;
   $sql_cid = $wpdb->prepare( "SELECT settings_value FROM {$wpdb->prefix}l2u_plugin_settings WHERE settings_name = 'locate2u_client_id'" );
   $cid_app_credentials[] = $wpdb->get_row( $sql_cid, ARRAY_A );

   $sql_cid_secret = $wpdb->prepare( "SELECT settings_value FROM {$wpdb->prefix}l2u_plugin_settings WHERE settings_name = 'locate2u_secret_key'" );
   $cid_app_credentials[] = $wpdb->get_row( $sql_cid_secret, ARRAY_A );

   return $cid_app_credentials;
}



function l2u_revoke_app_credentials(){
   global $wpdb;
   $sql_cid = $wpdb->prepare( "UPDATE {$wpdb->prefix}l2u_plugin_settings SET settings_value = '0'  WHERE settings_name = 'locate2u_client_id'" );
   $cid_app_credentials[] = $wpdb->query( $sql_cid, ARRAY_A );

   $sql_cid_secret = $wpdb->prepare( "UPDATE {$wpdb->prefix}l2u_plugin_settings SET settings_value = '0' WHERE settings_name = 'locate2u_secret_key'" );
   $cid_app_credentials[] = $wpdb->query( $sql_cid_secret, ARRAY_A );

   if ( $wpdb->last_error ) {
       error_log( 'DB ERROR: ' . ( __FUNCTION__ . ' failed.' . "\n" . $wpdb->last_error ) );
   }else{
       return array(
           "success" => $cid_app_credentials
       );
   }
}



function insert_plugin_settings($data){
   global $wpdb;

   foreach($data as $key => $val){

       $encryptedPassword = encrypt_decrypt_data($val,'e');
       $sql = $wpdb->prepare( "UPDATE {$wpdb->prefix}l2u_plugin_settings SET settings_value = '$encryptedPassword'  WHERE settings_name = '$key'" );
       $cid_app_credentials[] = $wpdb->query( $sql, ARRAY_A );
   }

   if ( $wpdb->last_error ) {
       error_log( 'DB ERROR: ' . ( __FUNCTION__ . ' failed.' . "\n" . $wpdb->last_error ) );
   }else{
       return array(
           "success" => $cid_app_credentials
       );
   }   
}







function check_business_name(){
   global $wpdb;	
   
   $sql_business_name = $wpdb->prepare( "SELECT settings_value FROM {$wpdb->prefix}l2u_plugin_settings WHERE settings_name = 'locate2u_business_name'" );
   $result_business_name = $wpdb->get_row( $sql_business_name, ARRAY_A );

   return $result_business_name;
}

function check_business_logo(){
   global $wpdb;	
   
   $sql_business_logo = $wpdb->prepare( "SELECT settings_value FROM {$wpdb->prefix}l2u_plugin_settings WHERE settings_name = 'locate2u_logo_url'" );
   $result_business_logo = $wpdb->get_row( $sql_business_logo, ARRAY_A );

   return $result_business_logo;
}

function check_thankyou_page(){
   global $wpdb;	
   
   $sql_thankyou_page = $wpdb->prepare( "SELECT settings_value FROM {$wpdb->prefix}l2u_plugin_settings WHERE settings_name = 'locate2u_thankyou_page'" );
   $result_thankyou_page = $wpdb->get_row( $sql_thankyou_page, ARRAY_A );

   return $result_thankyou_page;
}

function check_booking_type(){
   global $wpdb;	
   
   $sql_booking_type = $wpdb->prepare( "SELECT settings_value FROM {$wpdb->prefix}l2u_plugin_settings WHERE settings_name = 'locate2u_booking_type'" );
   $result_booking_type = $wpdb->get_row( $sql_booking_type, ARRAY_A );

   return $result_booking_type;
}

function check_address_checkbox(){
   global $wpdb;	
   
   $sql_address_checkbox = $wpdb->prepare( "SELECT settings_value FROM {$wpdb->prefix}l2u_plugin_settings WHERE settings_name = 'locate2u_address_checkbox'" );
   $result_address_checkbox = $wpdb->get_row( $sql_address_checkbox, ARRAY_A );

   return $result_address_checkbox;
}

function check_address_streetnum(){
   global $wpdb;	
   
   $sql_address_streetnum = $wpdb->prepare( "SELECT settings_value FROM {$wpdb->prefix}l2u_plugin_settings WHERE settings_name = 'locate2u_address_streetnumber'" );
   $result_address_streetnum = $wpdb->get_row( $sql_address_streetnum, ARRAY_A );

   return $result_address_streetnum;
}



function check_address_streetname(){
   global $wpdb;	
   
   $sql_address_streetname = $wpdb->prepare( "SELECT settings_value FROM {$wpdb->prefix}l2u_plugin_settings WHERE settings_name = 'locate2u_address_streetname'" );
   $result_address_streetname = $wpdb->get_row( $sql_address_streetname, ARRAY_A );

   return $result_address_streetname;
}


function check_address_suburb(){
   global $wpdb;	
   
   $sql_address_suburb = $wpdb->prepare( "SELECT settings_value FROM {$wpdb->prefix}l2u_plugin_settings WHERE settings_name = 'locate2u_address_suburb'" );
   $result_address_suburb = $wpdb->get_row( $sql_address_suburb, ARRAY_A );

   return $result_address_suburb;
}


function check_address_state(){
   global $wpdb;	
   
   $sql_address_state = $wpdb->prepare( "SELECT settings_value FROM {$wpdb->prefix}l2u_plugin_settings WHERE settings_name = 'locate2u_address_state'" );
   $result_address_state = $wpdb->get_row( $sql_address_state, ARRAY_A );

   return $result_address_state;
}

function check_address_postcode(){
   global $wpdb;	
   
   $sql_address_postcode = $wpdb->prepare( "SELECT settings_value FROM {$wpdb->prefix}l2u_plugin_settings WHERE settings_name = 'locate2u_address_postcode'" );
   $result_address_postcode = $wpdb->get_row( $sql_address_postcode, ARRAY_A );

   return $result_address_postcode;
}


function check_address_region(){
   global $wpdb;	
   
   $sql_address_region = $wpdb->prepare( "SELECT settings_value FROM {$wpdb->prefix}l2u_plugin_settings WHERE settings_name = 'locate2u_address_region'" );
   $result_address_region = $wpdb->get_row( $sql_address_region, ARRAY_A );

   return $result_address_region;
}

function check_pickup_default_checkbox(){
   global $wpdb;	
   
   $sql_defualt_address_checkbox = $wpdb->prepare( "SELECT settings_value FROM {$wpdb->prefix}l2u_plugin_settings WHERE settings_name = 'locate2u_pickup_default_checkbox'" );
   $result_address_default_checkbox = $wpdb->get_row( $sql_defualt_address_checkbox, ARRAY_A );

   return $result_address_default_checkbox;
}

function check_pickup_default_name(){
   global $wpdb;	
   
   $sql_pickup_default_name = $wpdb->prepare( "SELECT settings_value FROM {$wpdb->prefix}l2u_plugin_settings WHERE settings_name = 'locate2u_pickup_default_name'" );
   $result_pickup_default_name = $wpdb->get_row( $sql_pickup_default_name, ARRAY_A );

   return $result_pickup_default_name;
}

function check_pickup_default_email(){
   global $wpdb;	
   
   $sql_pickup_default_email = $wpdb->prepare( "SELECT settings_value FROM {$wpdb->prefix}l2u_plugin_settings WHERE settings_name = 'locate2u_pickup_default_email'" );
   $result_pickup_default_email = $wpdb->get_row( $sql_pickup_default_email, ARRAY_A );

   return $result_pickup_default_email;
}

function check_pickup_default_phone(){
   global $wpdb;	
   
   $sql_pickup_default_phone = $wpdb->prepare( "SELECT settings_value FROM {$wpdb->prefix}l2u_plugin_settings WHERE settings_name = 'locate2u_pickup_default_phone'" );
   $result_pickup_default_phone = $wpdb->get_row( $sql_pickup_default_phone, ARRAY_A );

   return $result_pickup_default_phone;
}