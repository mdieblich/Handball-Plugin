<?php

if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ){
	exit();
}

require_once (HANDBASE_PLUGIN_DIR . '/php/classes/Team.php');
handball\Team::uninstall();
	
?>