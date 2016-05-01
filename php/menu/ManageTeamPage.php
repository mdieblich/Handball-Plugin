<?php

namespace handball\menu;

use handball\Team;

require_once (HANDBASE_PLUGIN_DIR.'/php/classes/Team.php');
require_once (HANDBASE_PLUGIN_DIR.'/php/input/Team_Select.php');
require_once (HANDBASE_PLUGIN_DIR.'/php/input/User_Select.php');

class ManageTeamPage {
	private static $MENU_SLUG = 'handball_manage_team';
	public function __construct() {
		add_action ( 'admin_menu', array ($this, 'add_plugin_page') );
		add_action ( 'wp_ajax_add_stammspieler', 'handball\menu\ManageTeamPage::add_stammspieler' );
		add_action ( 'wp_ajax_remove_stammspieler', 'handball\menu\ManageTeamPage::remove_stammspieler' );
		add_action ( 'wp_ajax_add_zusatzspieler', 'handball\menu\ManageTeamPage::add_zusatzspieler' );
		add_action ( 'wp_ajax_remove_zusatzspieler', 'handball\menu\ManageTeamPage::remove_zusatzspieler' );
	}
	public function add_plugin_page() {
		add_submenu_page ( 'handball', // parent_slug
		'Mannschaft - Mitglieder verwalten', // page_title
		'Mannschaft - Mitglieder', // menu_title
		'manage_options', // capability
		static::$MENU_SLUG, // menu_slug
		array (
				$this,
				'manage_team_page' 
		) ); // function

	}
	public function manage_team_page() {
		echo \handball\input\team_select('team_id', 'team_id', "window.location.href='admin.php?page=".static::$MENU_SLUG."&team_id='+this.value");
		if(isset($_GET['team_id'])){
			$team = \handball\Team::get_by_id(intval($_GET['team_id']));
			
			echo "<h3>Mitglieder verwalten</h3>";
			\handball\input\select_multiple_users($team);
			
		}
	}
	public static function add_stammspieler() {
		$team_id = intval ( $_POST ['team_id'] );
		$user_id = intval ( $_POST ['user_id'] );
		echo \handball\Team::add_main_player_to_team( $team_id, $user_id );
		wp_die ();
	}
	public static function remove_stammspieler() {
		$team_id = intval ( $_POST ['team_id'] );
		$user_id = intval ( $_POST ['user_id'] );
		echo \handball\Team::remove_additional_player_from_team( $team_id, $user_id );
		wp_die ();
	}
	public static function add_zusatzspieler() {
		$team_id = intval ( $_POST ['team_id'] );
		$user_id = intval ( $_POST ['user_id'] );
		echo \handball\Team::add_additional_player_to_team( $team_id, $user_id );
		wp_die ();
	}
	public static function remove_zusatzspieler() {
		$team_id = intval ( $_POST ['team_id'] );
		$user_id = intval ( $_POST ['user_id'] );
		echo \handball\Team::remove_additional_player_from_team( $team_id, $user_id );
		wp_die ();
	}
}
?>