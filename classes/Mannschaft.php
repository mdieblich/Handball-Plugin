<?php

namespace handball;
require_once 'WPDBObject.php';
require_once 'Handballer.php';

/**
 * TODO Datenbank-Schema-Upgrade: https://codex.wordpress.org/Creating_Tables_with_Plugins
 */
class Mannschaft extends WPDBObject{
	
	private $id;
	
	private $name;
	
	private $trainer;
	private $cotrainer;
	/** Kern der Mannschaft */
	private $stammspieler; // 1xN Relation (ein Spieler kann höchstens in einer Mannschaft sein)
	
	/** Anfragen einzelner Spieler, teil der Mannschaft zu werden */
	private $spieleranfragen; // 1xN Relation (ein Spieler kann nur einer neuen Mannschaft beitreten)
	
	/** Hier kann sich jeder selbst eintragen */
	private $zusatzspieler; // NxM Relation (ein Spieler kann beliebig viele Mannschaften "abonnieren")
	
	public function __construct($name, $trainer = -1, $cotrainer=-1, $id=-1){
		$this->name = $name;
		$this->trainer = $trainer;
		$this->cotrainer = $cotrainer;
		parent::__construct($id);
	}
	

	protected function to_array(){
		$array = parent::to_array();
		$array['name'] = $this->name;
		$array['trainer'] = static::as_id($this->trainer);
		$array['cotrainer'] = static::as_id($this->cotrainer);
		return $array;
	}
	
	public function get_name(){
		return $this->name;
	}

	public function get_trainer(){
		return new Handballer($this->trainer);
	}
	public function get_cotrainer(){
		return new Handballer($this->cotrainer);
	}
	
	public function is_stammspieler($user){
		if(is_null($this->stammspieler)){
			$this->load_stammspieler();
		}
		return in_array($user->ID, $this->stammspieler);
	}
	
	private function load_stammspieler(){
		global $wpdb;
		$sql = "SELECT user FROM ". static::table_stammspieler()." WHERE team=".$this->get_id();
		$this->stammspieler = array();
		foreach($wpdb->get_results($sql) as $stammspieler_id){
			$this->stammspieler[] = $stammspieler_id->user;
		}
	}
	
	public function add_stammspieler($user){
		if($this->is_stammspieler($user)){
			return;
		}
		global $wpdb;
		$wpdb->insert(
				static::table_stammspieler(),
				array(
						'team' => $this->id,
						'user' => $user->ID
				)
		);
		$this->stammspieler[] = $user->ID;
	}
	
	public function is_zusatzspieler($user){
		if(is_null($this->zusatzspieler)){
			$this->load_zusatzspieler();
		}
		return in_array($user->ID, $this->zusatzspieler);
	}
	private function load_zusatzspieler(){
		global $wpdb;
		$sql = "SELECT user FROM ". static::table_zusatzspieler()." WHERE team=".$this->get_id();
		$this->zusatzspieler = array();
		foreach($wpdb->get_results($sql) as $zusatzspieler_id){
			$this->zusatzspieler[] = $zusatzspieler_id->user;
		}
	}
	public function add_zusatzspieler($user){
		if($this->is_zusatzspieler($user)){
			return;
		}
		global $wpdb;
		$wpdb->insert(
				static::table_zusatzspieler(),
				array(
						'team' => $this->id,
						'user' => $user->ID
				)
		);
		$this->zusatzspieler[] = $user->ID;
	}

	public static function add_stammspieler_to_team($team_id, $user_id){
		global $wpdb;
		static::remove_zusatzspieler_from_team($team_id, $user_id);
		
		$team_user = array(	'team' => $team_id,	'user' => $user_id	);
		$wpdb->insert(static::table_stammspieler(),	$team_user );
		return $user_id;
	}
	public static function remove_stammspieler_from_team($team_id, $user_id){
		global $wpdb;
		$team_user = array(	'team' => $team_id,	'user' => $user_id	);
		$wpdb->delete(static::table_stammspieler(), $team_user );
		return $user_id;
	}
	public static function add_zusatzspieler_to_team($team_id, $user_id){
		global $wpdb;
		static::remove_stammspieler_from_team($team_id, $user_id);
		$team_user = array(	'team' => $team_id,	'user' => $user_id	);
		$wpdb->insert(static::table_zusatzspieler(),	$team_user );
		return $user_id;
	}
	public static function remove_zusatzspieler_from_team($team_id, $user_id){
		global $wpdb;
		$team_user = array(	'team' => $team_id,	'user' => $user_id	);
		$wpdb->delete (static::table_zusatzspieler(), $team_user );
		return $user_id;
	}
	
	public static function install(){
		global $wpdb;
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	
		$charset_collate = $wpdb->get_charset_collate();
		$sql =
		"CREATE TABLE ".static::table_name()." (
			  id mediumint(9) NOT NULL AUTO_INCREMENT,
			  name tinytext NOT NULL,
			  trainer mediumint(9) NULL,
			  cotrainer mediumint(9) NULL,
			  PRIMARY KEY id (id)
		) ".$charset_collate.";";
	
		dbDelta( $sql );

		$sql2 =
		"CREATE TABLE ".static::table_stammspieler()." (
			  team mediumint(9) NOT NULL,
			  user mediumint(9) NOT NULL
		) ".$charset_collate.";";
		dbDelta( $sql2 );
		
		$sql3 =
		"CREATE TABLE ".static::table_zusatzspieler()." (
			  team mediumint(9) NOT NULL,
			  user mediumint(9) NOT NULL
		) ".$charset_collate.";";
		dbDelta( $sql3 );
	}

	public static function table_stammspieler(){
		return static::table_name().'_stammspieler';
	}
	public static function table_zusatzspieler(){
		return static::table_name().'_zusatzspieler';
	}
	
	protected static function row_to_object($row_object){
		return new Mannschaft($row_object->name, $row_object->trainer, $row_object->cotrainer, $row_object->id);
	}
	

	/** Brauchbar als Ajax-Callbackfunction */
	public static function set_trainer($mannschaft_id, $user_id){
		global $wpdb;
		$wpdb->update(static::table_name(), array('trainer' => $user_id), array('id' => $mannschaft_id));
		$row = $wpdb->get_row( 'SELECT trainer FROM '.static::table_name().' WHERE id = '.$mannschaft_id );
		return $row->trainer;
	}
	
	/** Brauchbar als Ajax-Callbackfunction */
	public static function set_cotrainer($mannschaft_id, $user_id){
		global $wpdb;
		$wpdb->update(static::table_name(), array('cotrainer' => $user_id), array('id' => $mannschaft_id));
		$row = $wpdb->get_row( 'SELECT cotrainer FROM '.static::table_name().' WHERE id = '.$mannschaft_id );
		return $row->cotrainer;
	}
	
}

?>