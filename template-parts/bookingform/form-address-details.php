<?php

$checkbox_address= check_address_checkbox();
$street_num = check_address_streetnum();
$street_name = check_address_streetname();
$address_suburb = check_address_suburb();
$address_state = check_address_state();
$address_postcode = check_address_postcode();
$address_region = check_address_region();

?>


<?php if(!isset($args['useSection']) || (isset($args['useSection']) && $args['useSection'])): ?>
<div class="form-section b2 mt-5">
<?php endif; ?>
	<div class="address-details <?php if(isset($args['addMargin']) && $args['addMargin']) { echo "mt-5"; } else { echo ""; } ?>">
		<h2 class="text-center form-section-title mb-4">
			<?php if(isset($args['title'])) : esc_html_e( $args['title'], 'text-domain' ); ?>
			<?php else : esc_html_e( 'Location Details', 'text-domain' ); ?>
			<?php endif; ?>
		</h2>


		<?php if($checkbox_address["settings_value"] == 1) : ?>

			<div class="two-column-input">
				<div class="input-wrapper mb-2">
					<input class="w-100" type="text" name="<?php if(isset($args['inputprefix'])) { echo esc_attr($args['inputprefix']); } else { echo ""; } ?>unit_suite_number" placeholder="Unit/Suite number">
				</div>				

				<div class="input-wrapper mb-2">
					<input class="w-100" type="text" name="<?php if(isset($args['inputprefix'])) { echo esc_attr($args['inputprefix']); } else { echo ""; } ?>street_number" placeholder="Street number" value="<?php if(isset($args['inputprefix']) && $args['inputprefix'] == "pickup_") { echo esc_attr($street_num["settings_value"]); } ?>">
				</div>				
			</div>


			<div class="two-column-input">
				<div class="input-wrapper mb-2">
					<input class="w-100" type="text" name="<?php if(isset($args['inputprefix'])) { echo esc_attr($args['inputprefix']); } else { echo ""; } ?>street_name" placeholder="Street name" value="<?php if(isset($args['inputprefix']) && $args['inputprefix'] == "pickup_") { echo esc_attr($street_name["settings_value"]); } ?>">
				</div>

				<div class="input-wrapper mb-2">
					<input class="w-100" type="text" name="<?php if(isset($args['inputprefix'])) { echo esc_attr($args['inputprefix']); } else { echo ""; } ?>suburb" placeholder="Suburb" value="<?php if(isset($args['inputprefix']) && $args['inputprefix'] == "pickup_") { echo esc_attr($address_suburb["settings_value"]); } ?>">
				</div>
			</div>


			<div class="two-column-input">
				<div class="input-wrapper mb-2">
					<input class="w-100" type="text" name="<?php if(isset($args['inputprefix'])) { echo esc_attr($args['inputprefix']); } else { echo ""; } ?>postal_code" placeholder="Postal code" value="<?php if(isset($args['inputprefix']) && $args['inputprefix'] == "pickup_") { echo esc_attr($address_postcode["settings_value"]); } ?>">
				</div>
				<div class="input-wrapper mb-2">
					<input class="w-100" type="text" name="<?php if(isset($args['inputprefix'])) { echo esc_attr($args['inputprefix']); } else { echo ""; } ?>state" placeholder="State" value="<?php if(isset($args['inputprefix']) && $args['inputprefix'] == "pickup_") { echo esc_attr($address_state["settings_value"]); } ?>">
				</div>
			</div>

			

			


		<?php else: ?>







		<div class="two-column-input">
			<div class="input-wrapper mb-2">
				<input class="w-100" type="text" name="<?php if(isset($args['inputprefix'])) { echo esc_attr($args['inputprefix']); } else { echo ""; } ?>unit_suite_number" placeholder="Unit/Suite number">
			</div>
			<div class="input-wrapper mb-2">
				<input class="w-100" type="text" name="<?php if(isset($args['inputprefix'])) { echo esc_attr($args['inputprefix']); } else { echo ""; } ?>street_number" placeholder="Street number">
			</div>
		</div>

		<div class="two-column-input">
			<div class="input-wrapper mb-2">
				<input class="w-100" type="text" name="<?php if(isset($args['inputprefix'])) { echo esc_attr($args['inputprefix']); } else { echo ""; } ?>street_name" placeholder="Street name">
			</div>
			<div class="input-wrapper mb-2">
				<input class="w-100" type="text" name="<?php if(isset($args['inputprefix'])) { echo esc_attr($args['inputprefix']); } else { echo ""; } ?>suburb" placeholder="Suburb">
			</div>
		</div>

		<div class="two-column-input">
			<div class="input-wrapper mb-2">
				<input class="w-100" type="text" name="<?php if(isset($args['inputprefix'])) { echo esc_attr($args['inputprefix']); } else { echo ""; } ?>postal_code" placeholder="Postal code">
			</div>
			<div class="input-wrapper mb-2">
				<input class="w-100" type="text" name="<?php if(isset($args['inputprefix'])) { echo esc_attr($args['inputprefix']); } else { echo ""; } ?>state" placeholder="State">
			</div>
		</div>

		<?php endif; ?>
		

	</div>
<?php if(!isset($args['useSection']) || (isset($args['useSection']) && $args['useSection'])): ?>
</div>
<?php endif; ?>