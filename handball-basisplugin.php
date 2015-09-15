<?php
/*
Plugin Name: Handball-Basisplugin
Plugin URI: http://dieblich.com/wordpress-handball-base
Description: Dieses Plugin bietet Basis-Klassen und Attribute für Handball an.
Version: 0.0.8
Author: Martin Dieblich
Author URI: http://dieblich.com
License: GPLv2

TODO Nutzer löschen -> aus Mannschaften entfernen
TODO eigene Rollen entfwerfen (Trainer, Mannschaftskapitän, Spieler, Präsident ...)
TODO eigene Capabilities entwerfen (edit_teams...)
*/
namespace handball;

define( 'HANDBASE_PLUGIN_FILE', __FILE__ );
define( 'HANDBASE_PLUGIN_DIR', plugin_dir_path( __FILE__ ));
define( 'HANDBASE_IMAGE_DIR', '../wp-content/plugins/handball-basisplugin/images/');

register_activation_hook( __FILE__, 'handball\activate' );
register_deactivation_hook( __FILE__, 'handball\deactivate' );

add_action( 'personal_options_update', 'handball\save_extra_profile_fields' );
add_action( 'edit_user_profile_update', 'handball\save_extra_profile_fields' );
add_action( 'show_user_profile', 'handball\show_extra_profile_fields' );
add_action( 'edit_user_profile', 'handball\show_extra_profile_fields' );

function activate(){
	require_once 'classes/Mannschaft.php';
	Mannschaft::install();
}

function deactivate(){
// 	require_once 'classes/Mannschaft.php';
// 	Mannschaft::uninstall();
}

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


if( is_admin() ){
	require_once 'classes/menu/Hauptmenu.php';
	require_once 'classes/menu/CreateTeamPage.php';
	new menu\Hauptmenu();
	new menu\CreateTeamPage();
}
// function my_action_callback() {
// 	global $wpdb; // this is how you get access to the database

// 	$whatever = intval( $_POST['whatever'] );

// 	$whatever += 10;

// 	echo $whatever;

// 	wp_die(); // this is required to terminate immediately and return a proper response
// }
?>