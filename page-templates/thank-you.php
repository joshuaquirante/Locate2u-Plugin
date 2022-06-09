<?php 
/*
Template Name: Thank You Page
*/
get_header(); ?>

<style>
	.booking-ty-box{
	border: 1px solid #efefef;
    width: 100%;
    max-width: 750px;
    margin: 0 auto;
    padding: 50px;
	}

	.l2u-booking-button, .l2u-booking-button:hover, .l2u-booking-button:focus{
	text-decoration: none;
    display: inline-block;
    background-color: green;
    width: 250px;
    padding: 10px;
    border-radius: 8px;
    color: #fff;
	margin: 0 auto;
	}
</style>

<section  id="booking">
	<div class="container">
		<div class="row text-center">

		<div class="booking-ty-box">
			<h1>Thank You!</h1>
			<h5>You successfully created your booking.</h5>
			<p>Pleae check your email for your booking details.</p>
			<a class="l2u-booking-button" href="#">Go Back to Booking Page</a>
		</div>
	</div>
</section>

<?php get_footer(); ?>