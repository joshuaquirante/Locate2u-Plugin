jQuery(document).ready(function($){
	$('.menu-toggler').on('click', function (){
		$('.main-navigation-wrapper').toggleClass('slide-from-right');
	});

	$('[id$=date]').datepicker({
		// beforeShowDay: function(date) {
		// 	var day = date.getDay();
		// 	return [(day != 0 && day != 6 && day != 2), ''];
		// },
		dateFormat: 'mm/dd/yy',
		minDate: '0'
	});

	$('#time').on('keyup', function(){
		$('.timeslot').each(function(){
			$(this).removeClass('selected');
		});
		timeslot = $('#time').val();
	});

	$('.timeslot').on('click', function(){
		$('.timeslot').each(function(){
			$(this).removeClass('selected');
		})
		$('#time').val($(this).data('time'));
		$(this).addClass('selected');
		timeslot = $(this).data('time');
	});

	$('.timeselect').on('click', function(){
		$('#time_selector').toggleClass('d-flex');
		timeslot = $(this).data('time');
		$('#time').val($(this).data('time'));
		$('.timeslot').each(function(){
			$(this).removeClass('selected');
		})
	});

});

function getExistingRules(){
	return {
		email :  {
				required  :  true,
				email     :  true
			},
		phone :  {
				required  :  true,
				number    : true
			},
		date :  "required",
		time :  {
				required: true,
				checkValidTime: true
			},
		address	:  "required",
		// select : {
		// 		required: true,
		// 		valueNotEquals: true
		// }
	}
}

function getExistingMessages(){

	// checks if time is in a valid military time format
	$.validator.addMethod("checkValidTime", function(value, element) { 
    	return this.optional(element) || /^([0-9]|0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$/i.test(value);  
    }, "Please enter a valid time.");

	$.validator.addMethod("valueNotEquals", function(value, element, arg){
		return arg !== value;
	}, "Value must not equal arg.");

	return  {
		email :  {
			required  :  "Email is required",
			email     :  "Please enter a valid email"
		},
		phone :  {
			required  :  "Phone number is required",
			number    :  "Please enter a valid phone number"
		},
		date :  "Please select booking date",
		time :  {
			required: "Please enter a booking time.",
			checkValidTime: "Please enter a valid time"
		},
		address	:  "Please enter your location",
	}
}

function validateBusinessForm(businessFormId){
	var form = $(businessFormId);

	form.validate({
		rules : getExistingRules(),
		messages : getExistingMessages()
	});

	$("input[name*='locate2u_business_name']").each(function() {
        $(this).rules('add', {
            required: true,
            messages: {
                required: "Business name is required",
            }
        });
    });

	$("select[name*='locate2u_thankyou_page']").each(function() {
        $(this).rules('add', {
            valueNotEquals: "default",
            messages: {
                valueNotEquals:  "Please select page",
            }
        });
    });


	$("select[name*='locate2u_booking_type']").each(function() {
        $(this).rules('add', {
            valueNotEquals: "default",
            messages: {
                valueNotEquals:  "Please select booking type",
            }
        });
    });

	$("input[name*='locate2u_address_streetadd']").each(function() {
        $(this).rules('add', {
            required: true,
            messages: {
                required: "Street Address is required",
            }
        });
    });

	$("input[name*='locate2u_address_suburb']").each(function() {
        $(this).rules('add', {
            required: true,
            messages: {
                required: "Suburb is required",
            }
        });
    });

	$("input[name*='locate2u_address_state']").each(function() {
        $(this).rules('add', {
            required: true,
            messages: {
                required: "State is required",
            }
        });
    });

	$("input[name*='locate2u_address_postcode']").each(function() {
        $(this).rules('add', {
            required: true,
            messages: {
                required: "Postal Code is required",
            }
        });
    });

	$("input[name*='locate2u_address_region']").each(function() {
        $(this).rules('add', {
            required: true,
            messages: {
                required: "Country/Region is required",
            }
        });
    });


    $("input[name*='locate2u_pickup_default_name']").each(function() {
        $(this).rules('add', {
            required: true,
            messages: {
                required: "Pickup Name is required",
            }
        });
    });

    $("input[name*='locate2u_pickup_default_email']").each(function() {
        $(this).rules('add', {
            required: true,
            messages: {
                required: "Pickup Email is required",
            }
        });
    });

    $("input[name*='locate2u_pickup_default_phone']").each(function() {
        $(this).rules('add', {
            required: true,
            messages: {
                required: "Pickup Phone is required",
            }
        });
    });






	return form;

}

function validateBookingForm(formId){
	var form = $(formId);

	form.validate({
		rules : getExistingRules(),
		messages : getExistingMessages()
	});


	// to support shipments
	$("input[name*='email']").each(function() {
        $(this).rules('add', {
            required: true,
            messages: {
                required: "Email is required",
            }
        });
    });

    $("input[name*='phone']").each(function() {
        $(this).rules('add', {
            required: true,
            messages: {
                required: "Phone is required",
            }
        });
    });

     $("input[name*='date']").each(function() {
        $(this).rules('add', {
            required: true,
            messages: {
                required: "Date is required",
            }
        });
    });

     	$("input[name*='street_name']").each(function() {
        $(this).rules('add', {
            required: true,
            messages: {
                required: "Street name is required",
            }
        });
    });
	
	$("input[name*='street_number']").each(function() {
        $(this).rules('add', {
            required: true,
            messages: {
                required: "Street number is required",
            }
        });
    });
	
	$("input[name*='postal_code']").each(function() {
        $(this).rules('add', {
            required: true,
            messages: {
                required: "Postal code is required",
            }
        });
    });


	// for API client ID and client ID Secret Form
	$("input[name*='locate2u_client_id']").each(function() {
        $(this).rules('add', {
            required: true,
            messages: {
                required: "Client ID is required",
            }
        });
    });

	$("input[name*='locate2u_secret_key']").each(function() {
        $(this).rules('add', {
            required: true,
            messages: {
                required: "Client Secret Key is required",
            }
        });
    });

     // un comment if time is required for pickup and drop
    // $("input[name*='time']").each(function() {
    //     $(this).rules('add', {
    //         required: true,
    //         messages: {
    //             required: "Phone is required",
    //         }
    //     });
    // });

	return form;
}

function getFormData(formId){

	var formInput = `form${formId} :input`;
	var formData = {};

	$(formInput).each(function(){
		var input = $(this);
	 	var inputName = input.attr("name");
	 	if(inputName){
	 		formData[inputName] = input.val();
	 	}
	});

	return formData;
}