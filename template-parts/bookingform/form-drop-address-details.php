<div class="form-section b2 mt-5">
	<?php
    //  include(L2U_PLUGIN_PATH . 'template-parts/bookingform/form-datepicker.php');
    //  include(L2U_PLUGIN_PATH . 'template-parts/bookingform/form-address-details.php');
    //  include(L2U_PLUGIN_PATH . 'template-parts/bookingform/form-contact-details.php');


    function DropDate(){
        $args = array(
            'title'   => 'Drop appointment date',
            'inputprefix'  => 'drop_',
            'useSection' => false,
            'timetype' => null
        );
        include(L2U_PLUGIN_PATH . 'template-parts/bookingform/form-datepicker.php');

    }
    echo DropDate();

    function DropAddress(){
        $args = array(
            'title'   => 'Drop Address Details',
            'inputprefix'  => 'drop_',
            'useSection' => false,
            'addMargin' => true
        );
        include(L2U_PLUGIN_PATH . 'template-parts/bookingform/form-address-details.php');

    }
    echo DropAddress();


    function DropContact(){
        $args = array(
            'title'   => 'Drop Contact Details',
            'inputprefix'  => 'drop_',
            'useSection' => false,
            'addMargin' => true
        );
        include(L2U_PLUGIN_PATH . 'template-parts/bookingform/form-contact-details.php');

    }
    echo DropContact();

    ?>
</div>