<?php

$checkbox_address = check_address_checkbox();
$checkbox_details = check_pickup_default_checkbox();
$default_name = check_pickup_default_name();
$default_email = check_pickup_default_email();
$default_phone = check_pickup_default_phone();

?>



<?php if( !isset($args['useSection']) || (isset($args['useSection']) && $args['useSection'])): ?>
<div class="form-section b2 mt-5">
<?php endif; ?>
	<div class="<?php if(isset($args['addMargin']) && $args['addMargin']) { echo "mt-5"; } else { echo ""; } ?>">
		<h2 class="text-center form-section-title mb-4">
			<?php if(isset($args['title'])) : echo $args['title']; ?>
			<?php else : echo "Contact Details"?>
			<?php endif; ?>
		</h2>



		<?php if($checkbox_address["settings_value"] == 1 && $checkbox_details["settings_value"] == 1)  : ?>
			<div class="input-wrapper mb-2">
				<input class="w-100" type="text" name="<?php if(isset($args['inputprefix'])) { echo esc_attr($args['inputprefix']); } else { echo ""; } ?>name" placeholder="Name" value="<?php if(isset($args['inputprefix']) && $args['inputprefix'] == "pickup_") { echo esc_attr($default_name["settings_value"]); } ?>">
			</div>
			<div class="input-wrapper mb-2">
				<input class="w-100" type="email" name="<?php if(isset($args['inputprefix'])) { echo esc_attr($args['inputprefix']); } else { echo ""; } ?>email" placeholder="Email address" value="<?php if(isset($args['inputprefix']) && $args['inputprefix'] == "pickup_") { echo esc_attr($default_email["settings_value"]); } ?>">
			</div>
			<div class="input-wrapper mb-2">
				<input class="w-100" type="phone" name="<?php if(isset($args['inputprefix'])) { echo esc_attr($args['inputprefix']); } else { echo ""; } ?>phone" placeholder="Phone number" value="<?php if(isset($args['inputprefix']) && $args['inputprefix'] == "pickup_") { echo esc_attr($default_phone["settings_value"]); } ?>">
			</div>
		
		
		<?php else : ?>


			<div class="input-wrapper mb-2">
				<input class="w-100" type="text" name="<?php if(isset($args['inputprefix'])) { echo esc_attr($args['inputprefix']); } else { echo ""; } ?>name" placeholder="Name">
			</div>
			<div class="input-wrapper mb-2">
				<input class="w-100" type="email" name="<?php if(isset($args['inputprefix'])) { echo esc_attr($args['inputprefix']); } else { echo ""; } ?>email" placeholder="Email address">
			</div>
			<div class="input-wrapper mb-2">
				<input class="w-100" type="phone" name="<?php if(isset($args['inputprefix'])) { echo esc_attr($args['inputprefix']); } else { echo ""; } ?>phone" placeholder="Phone number">
			</div>

		<?php endif; ?>


	</div>
<?php if(!isset($args['useSection']) || (isset($args['useSection']) && $args['useSection'])): ?>
</div>
<?php endif; ?>