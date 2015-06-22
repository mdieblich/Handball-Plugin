<?php
/*
Plugin Name: Handball-Basisplugin
Plugin URI: http://dieblich.com/wordpress-handball-base
Description: Dieses Plugin bietet Basis-Klassen und Attribute für Handball an.
Version: 0.0.4
Author: Martin Dieblich
Author URI: http://dieblich.com
License: GPLv2
*/
namespace handball;

define( 'HANDBASE_PLUGIN_FILE', __FILE__ );

add_action( 'personal_options_update', 'handball\save_extra_profile_fields' );
add_action( 'edit_user_profile_update', 'handball\save_extra_profile_fields' );
add_action( 'show_user_profile', 'handball\show_extra_profile_fields' );
add_action( 'edit_user_profile', 'handball\show_extra_profile_fields' );

function show_extra_profile_fields( $user ) {
	require_once 'classes/Handballer.php';
	$handballer = new Handballer($user->ID);
	$handballer->show_profile_extras();
}

function save_extra_profile_fields( $user_id ) {
	if ( !current_user_can( 'edit_user', $user_id ) )
		return false;

	require_once 'classes/Handballer.php';
	$handballer = new Handballer($user_id);
	$handballer->save_from_post();

}

?>