<?php

function l2u_plugin_install()
{
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');	

	update_option( 'l2u_plugin_show_wizard_notice', '1' );

	create_l2u_plugin_settings_tbl();

	insert_l2u_pre_settings();

	create_booking_page();
}



include ( 'custom-query.php' );