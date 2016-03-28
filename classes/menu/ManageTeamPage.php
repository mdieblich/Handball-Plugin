<?php

namespace handball\menu;
use handball\Mannschaft;
require_once (ABSPATH . 'wp-content/plugins/handball-basisplugin/classes/Mannschaft.php');

class ManageTeamPage {
	private static $MENU_SLUG = 'handball_mannschaft_manage';
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
		require_once (HANDBASE_PLUGIN_DIR . '/classes/input/Team_Select.php');
		require_once (HANDBASE_PLUGIN_DIR . '/classes/input/User_Select.php');
		require_once (HANDBASE_PLUGIN_DIR . '/classes/Mannschaft.php');
		echo \handball\input\team_select('mannschaft', "window.location.href='admin.php?page=".static::$MENU_SLUG."&mannschaft='+this.value");
		if(isset($_GET['mannschaft'])){
			$team = \handball\Mannschaft::get_by_id(intval($_GET['mannschaft']));
			
			echo "<h3>Mitglieder verwalten</h3>";
			\handball\input\select_multiple_users($team);
			
		}
	}
	public static function add_stammspieler() {
		$mannschaft_id = intval ( $_POST ['team'] );
		$user_id = intval ( $_POST ['user'] );
		echo \handball\Mannschaft::add_stammspieler_to_team( $mannschaft_id, $user_id );
		wp_die ();
	}
	public static function remove_stammspieler() {
		$mannschaft_id = intval ( $_POST ['team'] );
		$user_id = intval ( $_POST ['user'] );
		echo \handball\Mannschaft::remove_stammspieler_from_team( $mannschaft_id, $user_id );
		wp_die ();
	}
	public static function add_zusatzspieler() {
		$mannschaft_id = intval ( $_POST ['team'] );
		$user_id = intval ( $_POST ['user'] );
		echo \handball\Mannschaft::add_zusatzspieler_to_team( $mannschaft_id, $user_id );
		wp_die ();
	}
	public static function remove_zusatzspieler() {
		$mannschaft_id = intval ( $_POST ['team'] );
		$user_id = intval ( $_POST ['user'] );
		echo \handball\Mannschaft::remove_zusatzspieler_from_team( $mannschaft_id, $user_id );
		wp_die ();
	}
}
?>