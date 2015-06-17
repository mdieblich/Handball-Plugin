<?php
/*
Plugin Name: Handball-Anwesenheit
Plugin URI: http://dieblich.com/wordpress-handball
Description: Dieses Plugin dient zur Erfassung von Spieler-Anwesenheit. Aktuell kann es aber noch nix!
Version: 0.0.2
Author: Martin Dieblich
Author URI: http://dieblich.com
License: GPLv2
*/
define( 'HANDBAW_PLUGIN_FILE', __FILE__ );
register_activation_hook( HBAW_PLUGIN_FILE, 'handbaw_activation' );
register_deactivation_hook( MM_PLUGIN_FILE, 'handbaw_deactivation' );

function handbaw_activation(){
	// TODO Nutzer Registrierungs-Infos in "register form" und "user register" einbauen
}

function handbaw_deactivation(){
	
}
?>