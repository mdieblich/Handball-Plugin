<?php

if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ){
	exit();
}

require_once 'classes/Team.php';
handball\Team::uninstall();
	
?>