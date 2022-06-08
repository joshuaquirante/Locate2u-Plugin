<?php 
/*
Template Name: Shipment Template
*/

get_header(); ?>


<?php 
$check_business_name = check_business_name();
$check_logo_url = check_business_logo();
$check_thankyou_page = check_thankyou_page();
?>


<section id="business-credentials">
	<div class="container">
		<div class="row">
			<div class="business-identity-container">
				<?php if($check_logo_url["settings_value"] != 0) : ?>
					<img class="img-fluid" src="<?php echo esc_attr($check_logo_url["settings_value"]); ?>" />
				<?php endif; ?>

				<?php if($check_business_name["settings_value"] != 0) : ?>
					<h1><?php echo esc_attr($check_business_name["settings_value"]); ?></h1>
				<?php endif; ?>	
			</div>
			
		</div>
	</div>
</section>



<section  id="booking">
	<div class="container">
		<div class="row">
			<form id="booking_form" class="w-100">
				<div class="col-12 col-lg-10 offset-lg-1 col-xl-6 offset-xl-3">

				<?php
					include( L2U_PLUGIN_PATH . 'template-parts/bookingform/form-pickup-address-details.php');
					include( L2U_PLUGIN_PATH . 'template-parts/bookingform/form-drop-address-details.php');
					include( L2U_PLUGIN_PATH . 'template-parts/bookingform/form-notes.php');
				?>

					<div class="input-wrapper d-flex flex-column align-items-center my-3">
						<button formnovalidate="formnovalidate" type="submit" id="form_submit">Book Now</button>
					</div>
				</div>
			</form>
		</div>
	</div>
</section>

<div class="v-box hideloader" id="v-box">
    <div class="loader"></div>
</div>

<!-- Modal -->
<div class="modal fade" id="form_modal" tabindex="-1" role="dialog" aria-labelledby="modal1" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="modal_title"></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<p id="modal_content"></p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">

var full_url = "<?php echo admin_url('admin-ajax.php'); ?>";
var site_url ="<?php echo site_url(); ?>"

$(document).ready(function(){
	$('#booking_form').on('submit', function(event){
		event.preventDefault();
		var booking_form = validateBookingForm("#booking_form");
		var formData = getFormData("#booking_form");
		var thank_you_page_name = "<?php echo $check_thankyou_page['settings_value'] ?>";


		if(!booking_form.valid())
			return;

		$('#v-box').addClass('d-block');

	    $.ajax({
			type: 'POST',
			url: full_url,
			data: {
			action: "create_shipment",
			data: formData
			},
			success: function(result){
				
				$('#v-box').removeClass('d-block');
				
				var c_result = JSON.parse(result);
				var errlist = c_result['translated_error'];
				

				if (c_result['is_success'] == 'success') {
					document.getElementById("booking_form").reset();
					
					if(thank_you_page_name != 0){
						window.location.href = site_url + '/' + thank_you_page_name + '/';						
					}else{
						$('#modal_title').text('Thank You');
						$('#modal_content').text('Booking Successful!');
						$('#form_modal').modal('show');
					}
					
				}
				else if (c_result['is_success'] == 'error') {
					$('#modal_title').text('Error');
					const ul = document.createElement('ul');
					ul.setAttribute('id', 'errors');
					
					for(i = 0; i <= errlist.length -1; i++){
						const li = document.createElement('li');

						li.innerHTML = errlist[i];
						ul.appendChild(li);
					}
					$('#modal_content').text("");
					$('#modal_content').append(ul);					
					$('#form_modal').modal('show');
				}
			},
		}).done(function(result) {
			var c_result = JSON.parse(result);
			document.getElementById("booking_form").reset();
			$('#v-box').removeClass('d-block');
		});

	});
});
</script>

<?php get_footer(); ?>