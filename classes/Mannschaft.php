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
		if($trainer==-1){
			$this->trainer = Handballer::get_nobody_id();	
		}else{
			$this->trainer = $trainer;
		}
		if($cotrainer==-1){
			$this->cotrainer = Handballer::get_nobody_id();	
		}else{
			$this->cotrainer = $cotrainer;
		}
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
		require_once (HANDBASE_PLUGIN_DIR . '/classes/Handballer.php');
	
		$charset_collate = $wpdb->get_charset_collate();
		$sql =
		"CREATE TABLE ".static::table_name()." (
			  id mediumint(9) NOT NULL AUTO_INCREMENT,
			  name tinytext NOT NULL,
			  trainer bigint(20) unsigned NOT NULL,
			  cotrainer bigint(20) unsigned NOT NULL,
			  PRIMARY KEY (id),
 			  FOREIGN KEY (trainer) REFERENCES ".Handballer::table_name()."(ID),
 			  FOREIGN KEY (cotrainer) REFERENCES ".Handballer::table_name()."(ID)
		) ".$charset_collate.";";
	
		dbDelta( $sql );

		$sql2 =
		"CREATE TABLE ".static::table_stammspieler()." (
			  team mediumint(9) NOT NULL,
			  user bigint(20) unsigned NOT NULL,
 			  FOREIGN KEY (team) REFERENCES ".static::table_name()."(id),
 			  FOREIGN KEY (user) REFERENCES ".Handballer::table_name()."(id)
		) ".$charset_collate.";";
		dbDelta( $sql2 );
		
		$sql3 =
		"CREATE TABLE ".static::table_zusatzspieler()." (
			  team mediumint(9) NOT NULL,
			  user bigint(20) unsigned NOT NULL,
 			  FOREIGN KEY (team) REFERENCES ".static::table_name()."(id),
 			  FOREIGN KEY (user) REFERENCES ".Handballer::table_name()."(id)
		) ".$charset_collate.";";
		dbDelta( $sql3 );
		
		static::create_no_team();
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
	
	private static $NO_TEAM_ID_OPTION = 'handball_no_team_id';
	private static $NO_TEAM_NAME = 'keine Mannschaft';
	
	public static function get_no_team(){
		if ( static::no_team_id_option_is_already_set() ) {
			return static::get_by_id(static::get_no_team_id());
		}else{
			return static::create_no_team();
		}
	}
	private static function no_team_id_option_is_already_set(){
		return get_option( static::$NO_TEAM_ID_OPTION ) !== false;
	}
	
	private static function get_no_team_id(){
		if ( static::no_team_id_option_is_already_set() ) {
			return get_option(static::$NO_TEAM_ID_OPTION );
		}else{
			return static::create_no_team()->get_id();
		}
		
	}
	
	private static function create_no_team(){
		$no_team = new Mannschaft(static::$NO_TEAM_NAME);
		$deprecated = null;
		$autoload = 'no';
		add_option( static::$NO_TEAM_ID_OPTION, $no_team->get_id(), $deprecated, $autoload );
		return $no_team;
	}
	
	public static function get_all(){
		return static::get('id!='.static::get_no_team_id());
	}
}

?>