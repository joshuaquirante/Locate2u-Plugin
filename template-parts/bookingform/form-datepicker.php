<?php
if(is_page_template( 'page-templates/stop-template.php' )){
	$args = array (
		'timetype' => 'appointmenttime'
	);
}
?>

<?php if(!isset($args['useSection']) || (isset($args['useSection']) && $args['useSection'])): ?>
<div class="form-section bg-lightblue mt-4 mt-md-5">
<?php endif;?>
<div>
	<h2 class="text-center form-section-title mb-3 mb-md-4">
			<?php if(isset($args['title'])) : echo $args['title']; ?>
			<?php else : echo "Book an appointment"?>
			<?php endif; ?>
	</h2>
	<div class="input-wrapper date-cont w-100">
		<input type="text" id="<?php if(isset($args['inputprefix'])) { echo esc_attr($args['inputprefix']); } else { echo ""; } ?>date" name="<?php if(isset($args['inputprefix'])) { echo esc_attr($args['inputprefix']); } else { echo ""; } ?>date" class="w-100 dateinput" placeholder="Select a Date" autocomplete="off">
	</div>

	<?php if(isset($args['timetype']) && $args['timetype'] == "appointmenttime"): ?>

	<div class="d-flex justify-content-around ampm-wrapper my-2">
		<span>AM</span>
		<span>PM</span>
	</div>
	<div class="d-flex flex-wrap">
		<div class="timeslot" data-time="09:00" data-display="9:00 AM">9:00 AM</div>
		<div class="timeslot" data-time="14:00" data-display="2:00 PM">2:00 PM</div>
		<div class="timeslot" data-time="11:00" data-display="11:00 AM">11:00 AM</div>
		<div class="timeslot" data-time="16:00" data-display="4:00 PM">4:00 PM</div>
	</div>
	<div class="input-wrapper time-cont w-100">
		<input type="text" id="time" name="time" class="w-100" placeholder="Add specific time">
		<div class="mt-3">*Note: This field only accepts time in military format.</div>
	</div>
	<?php endif;?>
</div>
<?php if(!isset($args['useSection']) || (isset($args['useSection']) && $args['useSection'])): ?>
</div>
<?php endif; ?>