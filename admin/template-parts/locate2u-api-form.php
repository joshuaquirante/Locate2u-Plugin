<div class="mt-5">
    <form id="<?php if(l2u_check_app_credentials() != false ){ echo "revoke_form"; }else{ echo "oauth_form"; } ?>" class="w-100">
        <div class="col-md-12">
            <h1 class="admin-plugin-title">Enter Your Locate2u App Credentials.</h1>
			<h5 class="api_response_message" id="api_response_message"></h5>
			<table id="admin-credential-form" class="w-100">
            	<?php include_once( 'form/locate2u-api-formfield.php'); ?>  
			</table>          
        </div>
    </form>
</div>


<?php if(l2u_check_app_credentials() != false) { ?>
	<form id="branding_form" class="w-100">
		
		<?php include_once( 'form/locate2u-branding-settings.php'); ?>
				
		<table>
			<tr>
				<th><button class="button-primary" type="submit" id="form_submit">Save Changes</button></th>
				<td></td>
			</tr>
		</table>
		
	</form>
<?php } ?>


<!-- Loader -->
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
			<!-- <div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
			</div> -->
		</div>
	</div>
</div>





<script type="text/javascript">
var full_url = "<?php echo admin_url('admin-ajax.php'); ?>";

$(document).ready(function(){
	$('#oauth_form').on('submit', function(event){
		event.preventDefault();
		
		var formData = getFormData("#oauth_form");
		var booking_form = validateBookingForm("#oauth_form");

		if(!booking_form.valid())
			return;

		$('#v-box').addClass('d-block');

	    $.ajax({
			type: 'POST',
			url: full_url,
			data: {
			action: "verify_locate2u",
			data: formData,
			},		

			success: function (result){
				
				$('#v-box').removeClass('d-block');
				var test = JSON.parse(result);
				$('#modal_title').text('Message');
				$('#modal_content').text("Activating API Success!");
				$('#form_modal').modal('show');
				//$('#api_response_message').text('Connection Success!');
				setInterval('location.reload()', 2000);
			},

			error: function (result){
				
				$('#v-box').removeClass('d-block');
				$('#modal_title').text('Error');
				$('#modal_content').text("API Connection Errror or API Credentials did not match.");
				$('#form_modal').modal('show');
			},
		
		});

	});
});




$(document).ready(function(){
	$('#revoke_form').on('submit', function(event){
		event.preventDefault();
		
		var formData = getFormData("#revoke_form");

		$('#v-box').addClass('d-block');

		$.ajax({
			type: 'POST',
			url: full_url,
			data: {
			action: "revoke_locate2u_credentials",
			data: formData,
			},

			success: function (result){
				
				$('#v-box').removeClass('d-block');
				var test = JSON.parse(result);
				$('#modal_title').text('Message');
				$('#modal_content').text("Deactivating API Success!");
				$('#form_modal').modal('show');
				//$('#api_response_message').text('Deactivating Success!');
				setInterval('location.reload()', 2000);
			},
		});

	});
});



$(document).ready(function(){
	$('#upload-btn').click(function(e) {
        e.preventDefault();
        var image = wp.media({ 
            title: 'Upload Image',
            // mutiple: true if you want to upload multiple files at once
            multiple: false
        }).open()
        .on('select', function(e){
            // This will return the selected image from the Media Uploader, the result is an object
            var uploaded_image = image.state().get('selection').first();
            // We convert uploaded_image to a JSON object to make accessing it easier
            // Output to the console uploaded_image
            //console.log(uploaded_image);
            var image_url = uploaded_image.toJSON().url;
            // Let's assign the url value to the input field
            $('#locate2u_logo_url').val(image_url);
			$('#locate2u_logo_url').append('<img src="' + image_url + '" />');
        });
    });

});








</script>

<script>
var full_url = "<?php echo admin_url('admin-ajax.php'); ?>";

$(document).ready(function(){
	$('#branding_form').on('submit', function(event){
		event.preventDefault();
		
		var formData2 = getFormData("#branding_form");
		var booking_form2 = validateBusinessForm("#branding_form");

		if(!booking_form2.valid())
			return;

		$('#v-box').addClass('d-block');

		$.ajax({
			type: 'POST',
			url: full_url,
			data: {
			action: "save_branding_form",
			data: formData2,
			},
			success: function (result){
				$('#v-box').removeClass('d-block');
				$('#modal_title').text('');
				$('#modal_content').text("Changes have been saved successfully");
				$('#form_modal').modal('show');
				
				setInterval('location.reload()', 2000);			
			},

			error: function (result){
				$('#v-box').removeClass('d-block');
				$('#modal_title').text('ERROR');
				$('#modal_content').text("Please check data");
				$('#form_modal').modal('show');	
				setInterval('location.reload()', 2000);			
			},
		});

	});
});
</script>