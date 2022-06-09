<?php
	$credential_results = l2u_get_app_credentials();
	$client_id = $credential_results[0]["settings_value"];
	$client_secret =  $credential_results[1]["settings_value"];


	
//	$decrypted_cid = wpcodetips_twoway_encrypt($client_id,'d');
//	$decrypted_secret = wpcodetips_twoway_encrypt($client_secret,'d');


?>
<?php if(l2u_check_app_credentials() != false) { ?>
	<tr>
		<th><label>Client ID</label></th>
		<td><input class="w-100" type="text" name="locate2u_client_id" placeholder="Client Id" value="<?php echo esc_attr($client_id); ?>" disabled></td>

		
	</tr>

	<tr>
		<th><label>Client Secret</label></th>
		<td><input class="w-100" type="password" name="locate2u_secret_key" placeholder="Secret Key" value="<?php echo esc_attr($client_secret ); ?>" disabled></td>
	</tr>

	<tr>
		<th><button class="button-primary" type="submit" id="form_submit_credentials">DEACTIVATE</button></th>
		<td></td>
	</tr>



	

<?php } else { ?>	
	<tr>
		<th><label>Client ID</label></th>
		<td><input class="" type="text" name="locate2u_client_id" placeholder="Enter client id..."></td>
	</tr>

	<tr>
		<th><label>Client Secret</label></th>
		<td><input class="" type="text" name="locate2u_secret_key" placeholder="Enter secret key..."></td>
	</tr>

	<tr>
		<th><button class="button-primary" type="submit" id="form_submit_credentials">SUBMIT</button></th>
		<td></td>
	</tr>	


<?php } ?>