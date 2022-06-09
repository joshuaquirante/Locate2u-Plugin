<div class="form-section b2 mt-5">

    <?php
    
    function PickupDate(){
        $args = array(
            'title'   => 'Pickup Appointment Date',
            'inputprefix'  => 'pickup_',
            'useSection' => false
        );
        include(L2U_PLUGIN_PATH . 'template-parts/bookingform/form-datepicker.php');

    }
    echo PickupDate();


    function PickupAddress(){
        $args = array(
            'title'   => 'Pickup Address Details',
            'inputprefix'  => 'pickup_',
            'useSection' => false,
            'addMargin' => true
        );
        include(L2U_PLUGIN_PATH . 'template-parts/bookingform/form-address-details.php');      
    }
    echo PickupAddress();

    function PickupContact(){
        $args = array(
            'title'   => 'Pickup Contact Details',
            'inputprefix'  => 'pickup_',
            'useSection' => false,
            'addMargin' => true
        );
        include(L2U_PLUGIN_PATH . 'template-parts/bookingform/form-contact-details.php');
    }
    echo PickupContact();

?>
    
    
</div>