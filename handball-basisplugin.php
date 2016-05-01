<?php
/*
Plugin Name: Handball-Basisplugin
Plugin URI: http://dieblich.com/wordpress-handball-base
Description: Dieses Plugin bietet Basis-Klassen und Attribute für Handball an.
Version: 0.1.0
Author: Martin Dieblich
Author URI: http://dieblich.com
License: GPLv2

TODO Nutzer löschen -> aus Mannschaften entfernen
TODO eigene Rollen entfwerfen (Trainer, Mannschaftskapitän, Spieler, Präsident ...)
TODO eigene Capabilities entwerfen (edit_teams...)
*/
namespace handball;

define( 'HANDBASE_TABLE_PREFIX', 'handbase');
define( 'HANDBASE_PLUGIN_FILE', __FILE__ );
define( 'HANDBASE_PLUGIN_DIR', plugin_dir_path( __FILE__ ));
define( 'HANDBASE_IMAGE_DIR', '../wp-content/plugins/handball-basisplugin/images');
define( 'HANDBASE_JAVASCRIPT_DIR', '../wp-content/plugins/handball-basisplugin/javascript');

register_activation_hook( __FILE__, 'handball\activate' );
register_deactivation_hook( __FILE__, 'handball\deactivate' );

add_action( 'personal_options_update', 'handball\save_extra_profile_fields' );
add_action( 'edit_user_profile_update', 'handball\save_extra_profile_fields' );
add_action( 'show_user_profile', 'handball\show_extra_profile_fields' );
add_action( 'edit_user_profile', 'handball\show_extra_profile_fields' );

function activate(){
	require_once (HANDBASE_PLUGIN_DIR . '/php/classes/Team.php');
	require_once (HANDBASE_PLUGIN_DIR . '/php/classes/Location.php');
	require_once (HANDBASE_PLUGIN_DIR . '/php/classes/Trainingtime.php');
	
	Team::install();
	Location::install();
	Trainingtime::install();
}

function deactivate(){
// 	require_once 'classes/Team.php';
// 	Team::uninstall();
}

function show_extra_profile_fields( $user ) {
	require_once (HANDBASE_PLUGIN_DIR . '/php/classes/Player.php');
	$player = new Player($user->ID);
	$player->show_profile_extras();
}

function save_extra_profile_fields( $user_id ) {
	require_once (HANDBASE_PLUGIN_DIR . '/php/classes/Player.php');
	if ( !current_user_can( 'edit_user', $user_id ) )
		return false;
	
	$player = new Player($user_id);
	$player->save_from_post();

}


if( is_admin() ){
	require_once (HANDBASE_PLUGIN_DIR . '/php/menu/Hauptmenu.php');
	require_once (HANDBASE_PLUGIN_DIR . '/php/menu/CreateTeamPage.php');
	require_once (HANDBASE_PLUGIN_DIR . '/php/menu/ManageTeamPage.php');
	require_once (HANDBASE_PLUGIN_DIR . '/php/menu/ManageTrainingtimes.php');
	new menu\Hauptmenu();
	new menu\ManageTrainingtimes();
	new menu\CreateTeamPage();
	new menu\ManageTeamPage();
	// TODO "mein Team"-Seite
}
?>