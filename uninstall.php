<?php

if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ){
	exit();
}

require_once 'classes/Mannschaft.php';
handball\Mannschaft::uninstall();
	
?>