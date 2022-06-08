<style>
	/**
 * Checkbox Toggle UI
 */
input[type="checkbox"].wppd-ui-toggle {
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;

    -webkit-tap-highlight-color: transparent;

    width: auto;
    height: auto;
    vertical-align: middle;
    position: relative;
    border: 0;
    outline: 0;
    cursor: pointer;
    margin: 0 4px;
    background: none;
    box-shadow: none;
}
input[type="checkbox"].wppd-ui-toggle:focus {
    box-shadow: none;
}
input[type="checkbox"].wppd-ui-toggle:after {
    content: '';
    font-size: 8px;
    font-weight: 400;
    line-height: 18px;
    text-indent: -14px;
    color: #ffffff;
    width: 36px;
    height: 18px;
    display: inline-block;
    background-color: #a7aaad;
    border-radius: 72px;
    box-shadow: 0 0 12px rgb(0 0 0 / 15%) inset;
}
input[type="checkbox"].wppd-ui-toggle:before {
    content: '';
    width: 14px;
    height: 14px;
    display: block;
    position: absolute;
    top: 2px;
    left: 2px;
    margin: 0;
    border-radius: 50%;
    background-color: #ffffff;
}
input[type="checkbox"].wppd-ui-toggle:checked:before {
    left: 20px;
    margin: 0;
    background-color: #ffffff;
}
input[type="checkbox"].wppd-ui-toggle,
input[type="checkbox"].wppd-ui-toggle:before,
input[type="checkbox"].wppd-ui-toggle:after,
input[type="checkbox"].wppd-ui-toggle:checked:before,
input[type="checkbox"].wppd-ui-toggle:checked:after {
    transition: ease .15s;
}
input[type="checkbox"].wppd-ui-toggle:checked:after {
    content: 'ON';
    background-color: #2271b1;
}
</style>


<?php 
$check_business_name = check_business_name();
$check_logo_url = check_business_logo();
$check_booking_type = check_booking_type();
$check_thankyou_page = check_thankyou_page();
$check_address_checkbox = check_address_checkbox();
$check_address_streetnum = check_address_streetnum();
$check_address_streetname = check_address_streetname();
$check_address_suburb = check_address_suburb();
$check_address_state = check_address_state();
$check_address_postcode = check_address_postcode();
$check_address_region = check_address_region();
$check_pickup_default_checkbox = check_pickup_default_checkbox();
$check_pickup_default_name = check_pickup_default_name();
$check_pickup_default_email = check_pickup_default_email();
$check_pickup_default_phone = check_pickup_default_phone();

?>

<table id="business-credential-form" class="w-100">
	<div class="mt-5">		
		<div class="col-md-12">
			<h5>Business Branding.</h5>

			<tr>
				<th><label>Business Name</label></th>
				<td>
					<?php if($check_business_name["settings_value"] == 0) { ?>
						<input class="" type="text" name="locate2u_business_name" placeholder="Enter business name...">
					<?php } else { ?>
						<input class="" type="text" name="locate2u_business_name" placeholder="Enter business name..." value="<?php echo esc_attr($check_business_name["settings_value"]); ?>">
					<?php } ?>
				
				</td>
			</tr>


			<tr>
				<th><label>Business Logo</label></th>
				<td>
					<?php if($check_logo_url["settings_value"] == 0) { ?>
						<input type="text" name="locate2u_logo_url" id="locate2u_logo_url" class="regular-text" disabled>
					<?php } else { ?>
						<input type="text" name="locate2u_logo_url" id="locate2u_logo_url" class="regular-text" value="<?php echo esc_attr($check_logo_url["settings_value"]) ; ?>" disabled>
					<?php } ?>
					<input type="button" name="upload-btn" id="upload-btn" class="button-secondary" value="Select Image">
				</td>
			</tr>

			<tr>
				<th><label>Booking Type</label></th>
				<td>
					<select name="locate2u_booking_type" class="w-100" id="locate2u_booking_type">
						<option value="default">Select Booking Type</option>
						<option value="single-stop">Single Stop Booking</option>
						<option value="shipment">Shipment Booking</option>
					</select>
				
				</td>
			</tr>

			<?php if($check_booking_type["settings_value"] != 0) { ?>

			<tr>
				<th><label>Thank You Page</label></th>
				<td>
					<select name="locate2u_thankyou_page" class="w-100" id="locate2u_thankyou_page">

						<?php // Query for listing all pages in the select box loop
						echo '<option value="0"> Select Page </option>';
						$my_wp_query = new WP_Query();

						$all_wp_pages = $my_wp_query->query( array(
							'post_type' => 'page',
							// 'posts_per_page' => -1,
							'post_status' => array('publish', 'private')
						));

						foreach ($all_wp_pages as $value){
							$post = get_page($value);
							$title = $post->post_title;
							$id =  $post->post_name;

							// For example
							// <option value="pageId32">Page title</option>

							echo '<option value="' . $id . '">' . $title . '</option>';

						}; ?>

					</select>
				</td>
			</tr>

			<?php } ?>

			

			<tr>
				<th><label>Default Pickup Address? <br>(for shipment booking)</label></th>

				<!-- <td>
					<input type="checkbox" name="social-share" value="1">
				</td> -->

				<td><input type="checkbox" class="wppd-ui-toggle" id="locate2u_address_checkbox" name="locate2u_address_checkbox" ></td>
			</tr>

		</div>		
	</div>
</table>


<table  id="l2u-address-container" style="<?php if($check_address_checkbox["settings_value"] == 0) { ?> display:none <?php } ?>" class="w-100">
	<tr>
		<th><label>Street Number</label></th>
		<td>
			<?php if($check_address_streetnum["settings_value"] == 0) : ?>
				<input type="text" name="locate2u_address_streetnumber" id="locate2u_address_streetnumber" class="regular-text">
			<?php else : ?>
				<input type="text" name="locate2u_address_streetnumber" id="locate2u_address_streetnumber" class="regular-text" value="<?php echo esc_attr($check_address_streetnum["settings_value"]); ?>">
			<?php endif; ?>
		</td>
	</tr>

	<tr>
		<th><label>Street Name</label></th>
		<td>
			<?php if($check_address_streetname["settings_value"] == 0) : ?>
				<input type="text" name="locate2u_address_streetname" id="locate2u_address_streetname" class="regular-text">
			<?php else : ?>
				<input type="text" name="locate2u_address_streetname" id="locate2u_address_streetname" class="regular-text" value="<?php echo esc_attr($check_address_streetname["settings_value"]); ?>">
			<?php endif; ?>
		</td>
	</tr>

	<tr>
		<th><label>Suburb</label></th>
		<td>
			<?php if($check_address_suburb["settings_value"] == 0) : ?>
				<input type="text" name="locate2u_address_suburb" id="locate2u_address_suburb" class="regular-text">
			<?php else : ?>
				<input type="text" name="locate2u_address_suburb" id="locate2u_address_suburb" class="regular-text" value="<?php echo esc_attr($check_address_suburb["settings_value"]); ?>">
			<?php endif; ?>
		</td>
	</tr>

	<tr>
		<th><label>State</label></th>
		<td>
			<?php if($check_address_state["settings_value"] == 0) : ?>
				<input type="text" name="locate2u_address_state" id="locate2u_address_state" class="regular-text">
			<?php else : ?>
				<input type="text" name="locate2u_address_state" id="locate2u_address_state" class="regular-text"  value="<?php echo esc_attr($check_address_state["settings_value"]); ?>">
			<?php endif; ?>
		</td>
	</tr>

	<tr>
		<th><label>Postal Code</label></th>
		<td>
			<?php if($check_address_postcode["settings_value"] == 0) : ?>
				<input type="text" name="locate2u_address_postcode" id="locate2u_address_postcode" class="regular-text">
			<?php else : ?>
				<input type="text" name="locate2u_address_postcode" id="locate2u_address_postcode" class="regular-text" value="<?php echo esc_attr( $check_address_postcode["settings_value"]); ?>">
			<?php endif; ?>
		</td>
	</tr>

	<tr>
		<th><label>Region/Country</label></th>
		<td>
			<?php if($check_address_region["settings_value"] == 0) : ?>
				<input type="text" name="locate2u_address_region" id="locate2u_address_region" class="regular-text">
			<?php else : ?>
				<input type="text" name="locate2u_address_region" id="locate2u_address_region" class="regular-text" value="<?php echo esc_attr($check_address_region["settings_value"]); ?>">
			<?php endif; ?>
		</td>
	</tr>

	<tr>
		<th><label>Default Pickup Details?</label></th>
		<td><input type="checkbox" class="wppd-ui-toggle" id="locate2u_pickup_default_checkbox" name="locate2u_pickup_default_checkbox" ></td>
	</tr>
	

	
</table>


<table  id="default-pickup-details" style="<?php if($check_pickup_default_checkbox["settings_value"] == 0) { ?> display:none <?php } ?>" class="w-100">
	<tr>
		<th><label>Pickup Name</label></th>
		<td>
			<?php if($check_pickup_default_name["settings_value"] == 0) : ?>
				<input type="text" class="regular-text" id="locate2u_pickup_default_name" name="locate2u_pickup_default_name" >
			<?php else : ?>
				<input type="text" class="regular-text" id="locate2u_pickup_default_name" name="locate2u_pickup_default_name" value="<?php echo esc_attr($check_pickup_default_name["settings_value"]); ?>">
			<?php endif; ?>
		</td>
	</tr>

	<tr>
		<th><label>Pickup Email</label></th>
		<td>
			<?php if($check_pickup_default_email["settings_value"] == 0) : ?>
				<input type="email" class="regular-text" id="locate2u_pickup_default_email" name="locate2u_pickup_default_email" >
			<?php else : ?>
				<input type="email" class="regular-text" id="locate2u_pickup_default_email" name="locate2u_pickup_default_email" value="<?php echo esc_attr($check_pickup_default_email["settings_value"]); ?>">
			<?php endif; ?>
		</td>
	</tr>

	<tr>
		<th><label>Pickup Phone</label></th>
		<td>
			<?php if($check_pickup_default_phone["settings_value"] == 0) : ?>
				<input type="text" class="regular-text" id="locate2u_pickup_default_phone" name="locate2u_pickup_default_phone" >
			<?php else : ?>
				<input type="text" class="regular-text" id="locate2u_pickup_default_phone" name="locate2u_pickup_default_phone" value="<?php echo esc_attr($check_pickup_default_phone["settings_value"]); ?>">
			<?php endif; ?>
		</td>
	</tr>

	
</table>





<?php if($check_booking_type["settings_value"] != 0) { ?>
	<script type="text/javascript">
		$('#locate2u_booking_type').val("<?php echo esc_attr($check_booking_type["settings_value"]); ?>");
	</script>
<?php } ?>



<?php if($check_thankyou_page["settings_value"] != 0) { ?>
	<script type="text/javascript">
		$('#locate2u_thankyou_page').val("<?php echo esc_attr($check_thankyou_page["settings_value"]); ?>");
	</script>
<?php } ?>





<?php if($check_address_checkbox["settings_value"] != 0) { ?>
	<script type="text/javascript">		
        $('#locate2u_address_checkbox').attr("checked", "checked");
	</script>
<?php } ?>

<?php if($check_pickup_default_checkbox["settings_value"] != 0) { ?>
	<script type="text/javascript">		
        $('#locate2u_pickup_default_checkbox').attr("checked", "checked");
	</script>
<?php } ?>




<script type="text/javascript">
$('#locate2u_address_checkbox').on('click',function() {
    $('#l2u-address-container').toggle();
});

$('#locate2u_address_checkbox').on('change', function(){
   	this.value = this.checked ? 1 : 0;

}).change();


$('#locate2u_pickup_default_checkbox').on('click',function() {
    $('#default-pickup-details').toggle();
});

$('#locate2u_pickup_default_checkbox').on('change', function(){
   	this.value = this.checked ? 1 : 0;
}).change();


</script>