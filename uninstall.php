<?php

if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ){
	exit();
}

require_once (HANDBASE_PLUGIN_DIR . '/php/model/Team.php');
handball\Team::uninstall();
	
?>