<?php

namespace handball;

require_once(ABSPATH.'wp-admin/includes/upgrade.php');
require_once(HANDBASE_PLUGIN_DIR.'/php/classes/WPDBObject.php');
require_once(HANDBASE_PLUGIN_DIR.'/php/classes/Handballer.php');

/**
 * TODO Datenbank-Schema-Upgrade: https://codex.wordpress.org/Creating_Tables_with_Plugins
 */
class Team extends WPDBObject{
	
	private $id;
	
	private $name;
	
	private $trainer;
	private $cotrainer;
	/** Kern der Mannschaft */
	private $main_players; // 1xN Relation (ein Spieler kann höchstens in einer Mannschaft sein)
	
	/** Hier kann sich jeder selbst eintragen */
	private $additional_players; // NxM Relation (ein Spieler kann beliebig viele Mannschaften "abonnieren")
	
	public function __construct($name, $trainer = null, $cotrainer=null, $id=null){
		$this->name = $name;
		$this->trainer = Handballer::ensure_handballer($trainer);
		$this->cotrainer = Handballer::ensure_handballer($cotrainer);
		parent::__construct($id);
	}

	protected function to_array(){
		$array = parent::to_array();
		$array['name'] = $this->name;
		$array['trainer'] = Handballer::as_id_or_null($this->trainer);
		$array['cotrainer'] = Handballer::as_id_or_null($this->cotrainer);
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
		if(is_null($this->main_players)){
			$this->load_stammspieler();
		}
		return in_array($user->ID, $this->main_players);
	}
	
	private function load_stammspieler(){
		global $wpdb;
		$sql = "SELECT user FROM ". static::table_stammspieler()." WHERE team=".$this->get_id();
		$this->main_players = array();
		foreach($wpdb->get_results($sql) as $stammspieler_id){
			$this->main_players[] = $stammspieler_id->user;
		}
	}
	
	public function add_stammspieler($user){
		if(is_null($user)){
			throw new Exception('Stammspieler ist null');
		}
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
		$this->main_players[] = $user->ID;
	}
	
	public function is_zusatzspieler($user){
		if(is_null($this->additional_players)){
			$this->load_zusatzspieler();
		}
		return in_array($user->ID, $this->additional_players);
	}
	private function load_zusatzspieler(){
		global $wpdb;
		$sql = "SELECT user FROM ". static::table_zusatzspieler()." WHERE team=".$this->get_id();
		$this->additional_players = array();
		foreach($wpdb->get_results($sql) as $zusatzspieler_id){
			$this->additional_players[] = $zusatzspieler_id->user;
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
		$this->additional_players[] = $user->ID;
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
	
		$charset_collate = $wpdb->get_charset_collate();
		$sql =
		"CREATE TABLE ".static::table_name()." (
			  id mediumint(9) unsigned NOT NULL AUTO_INCREMENT,
			  name tinytext NOT NULL,
			  trainer bigint(20) unsigned,
			  cotrainer bigint(20) unsigned,
			  PRIMARY KEY (id),
 			  FOREIGN KEY (trainer) REFERENCES ".Handballer::table_name()."(ID),
 			  FOREIGN KEY (cotrainer) REFERENCES ".Handballer::table_name()."(ID)
		) ".$charset_collate.";";
	
		dbDelta( $sql );

		$sql2 =
		"CREATE TABLE ".static::table_stammspieler()." (
			  team mediumint(9) unsigned NOT NULL,
			  user bigint(20) unsigned NOT NULL,
 			  FOREIGN KEY (team) REFERENCES ".static::table_name()."(id),
 			  FOREIGN KEY (user) REFERENCES ".Handballer::table_name()."(id)
		) ".$charset_collate.";";
		dbDelta( $sql2 );
		
		$sql3 =
		"CREATE TABLE ".static::table_zusatzspieler()." (
			  team mediumint(9) unsigned NOT NULL,
			  user bigint(20) unsigned NOT NULL,
 			  FOREIGN KEY (team) REFERENCES ".static::table_name()."(id),
 			  FOREIGN KEY (user) REFERENCES ".Handballer::table_name()."(id)
		) ".$charset_collate.";";
		dbDelta( $sql3 );
	}

	public static function table_stammspieler(){
		return static::table_name().'_main_players';
	}
	public static function table_zusatzspieler(){
		return static::table_name().'_additional_players';
	}
	
	protected static function row_to_object($row_object){
		return new Team(
				$row_object->name, 
				intval($row_object->trainer), 
				intval($row_object->cotrainer), 
				intval($row_object->id)
		);
	}
	

	/** Brauchbar als Ajax-Callbackfunction */
	public static function set_trainer($team_id, $user_id){
		global $wpdb;
		$wpdb->update(static::table_name(), array('trainer' => $user_id), array('id' => $team_id));
		$row = $wpdb->get_row( 'SELECT trainer FROM '.static::table_name().' WHERE id = '.$team_id );
		return $row->trainer;
	}
	
	/** Brauchbar als Ajax-Callbackfunction */
	public static function set_cotrainer($team_id, $user_id){
		global $wpdb;
		$wpdb->update(static::table_name(), array('cotrainer' => $user_id), array('id' => $team_id));
		$row = $wpdb->get_row( 'SELECT cotrainer FROM '.static::table_name().' WHERE id = '.$team_id );
		return $row->cotrainer;
	}
}

?>