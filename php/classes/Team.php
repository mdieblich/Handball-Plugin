<?php

namespace handball;

require_once(ABSPATH.'wp-admin/includes/upgrade.php');
require_once(HANDBASE_PLUGIN_DIR.'/php/classes/WPDBObject.php');
require_once(HANDBASE_PLUGIN_DIR.'/php/classes/Player.php');

/**
 * TODO Datenbank-Schema-Upgrade: https://codex.wordpress.org/Creating_Tables_with_Plugins
 */
class Team extends WPDBObject{
	
	private $id;
	
	private $name;
	
	private $coach;
	private $assistant_coach;
	/** Kern der Mannschaft */
	private $main_players; // 1xN Relation (ein Spieler kann höchstens in einer Mannschaft sein)
	
	/** Hier kann sich jeder selbst eintragen */
	private $additional_players; // NxM Relation (ein Spieler kann beliebig viele Mannschaften "abonnieren")
	
	public function __construct($name, $coach = null, $assistant_coach=null, $id=null){
		$this->name = $name;
		$this->coach = Player::ensure_player($coach);
		$this->assistant_coach = Player::ensure_player($assistant_coach);
		parent::__construct($id);
	}

	protected function to_array(){
		$array = parent::to_array();
		$array['name'] = $this->name;
		$array['coach'] = Player::as_id_or_null($this->coach);
		$array['assistant_coach'] = Player::as_id_or_null($this->assistant_coach);
		return $array;
	}
	
	public function get_name(){
		return $this->name;
	}

	public function get_coach(){
		return new Player($this->coach);
	}
	public function get_assistant_coach(){
		return new Player($this->assistant_coach);
	}
	
	public function is_main_player($user){
		if(is_null($this->main_players)){
			$this->load_main_players();
		}
		return in_array($user->ID, $this->main_players);
	}
	
	private function load_main_players(){
		global $wpdb;
		$sql = "SELECT user FROM ". static::table_main_players()." WHERE team=".$this->get_id();
		$this->main_players = array();
		foreach($wpdb->get_results($sql) as $main_player_id){
			$this->main_players[] = $main_player_id->user;
		}
	}
	
	public function add_main_player($user){
		if(is_null($user)){
			throw new Exception('Stammspieler ist null');
		}
		if($this->is_main_player($user)){
			return;
		}
		global $wpdb;
		$wpdb->insert(
				static::table_main_players(),
				array(
						'team' => $this->id,
						'user' => $user->ID
				)
		);
		$this->main_players[] = $user->ID;
	}
	
	public function is_additional_player($user){
		if(is_null($this->additional_players)){
			$this->load_additional_players();
		}
		return in_array($user->ID, $this->additional_players);
	}
	private function load_additional_players(){
		global $wpdb;
		$sql = "SELECT user FROM ". static::table_additional_players()." WHERE team=".$this->get_id();
		$this->additional_players = array();
		foreach($wpdb->get_results($sql) as $additional_player_id){
			$this->additional_players[] = $additional_player_id->user;
		}
	}
	public function add_additional_player($user){
		if($this->is_additional_player($user)){
			return;
		}
		global $wpdb;
		$wpdb->insert(
				static::table_additional_players(),
				array(
						'team' => $this->id,
						'user' => $user->ID
				)
		);
		$this->additional_players[] = $user->ID;
	}

	public static function add_main_player_to_team($team_id, $user_id){
		global $wpdb;
		static::remove_additional_player_from_team($team_id, $user_id);
		
		$team_user = array(	'team' => $team_id,	'user' => $user_id	);
		$wpdb->insert(static::table_main_players(),	$team_user );
		return $user_id;
	}
	public static function remove_main_player_from_team($team_id, $user_id){
		global $wpdb;
		$team_user = array(	'team' => $team_id,	'user' => $user_id	);
		$wpdb->delete(static::table_main_players(), $team_user );
		return $user_id;
	}
	public static function add_additional_player_to_team($team_id, $user_id){
		global $wpdb;
		static::remove_main_player_from_team($team_id, $user_id);
		$team_user = array(	'team' => $team_id,	'user' => $user_id	);
		$wpdb->insert(static::table_additional_players(),	$team_user );
		return $user_id;
	}
	public static function remove_additional_player_from_team($team_id, $user_id){
		global $wpdb;
		$team_user = array(	'team' => $team_id,	'user' => $user_id	);
		$wpdb->delete (static::table_additional_players(), $team_user );
		return $user_id;
	}
	
	public static function install(){
		global $wpdb;
	
		$charset_collate = $wpdb->get_charset_collate();
		$sql =
		"CREATE TABLE ".static::table_name()." (
			  id mediumint(9) unsigned NOT NULL AUTO_INCREMENT,
			  name tinytext NOT NULL,
			  coach bigint(20) unsigned,
			  assistant_coach bigint(20) unsigned,
			  PRIMARY KEY (id),
 			  FOREIGN KEY (coach) REFERENCES ".Player::table_name()."(ID),
 			  FOREIGN KEY (assistant_coach) REFERENCES ".Player::table_name()."(ID)
		) ".$charset_collate.";";
	
		dbDelta( $sql );

		$sql2 =
		"CREATE TABLE ".static::table_main_players()." (
			  team mediumint(9) unsigned NOT NULL,
			  user bigint(20) unsigned NOT NULL,
 			  FOREIGN KEY (team) REFERENCES ".static::table_name()."(id),
 			  FOREIGN KEY (user) REFERENCES ".Player::table_name()."(id)
		) ".$charset_collate.";";
		dbDelta( $sql2 );
		
		$sql3 =
		"CREATE TABLE ".static::table_additional_players()." (
			  team mediumint(9) unsigned NOT NULL,
			  user bigint(20) unsigned NOT NULL,
 			  FOREIGN KEY (team) REFERENCES ".static::table_name()."(id),
 			  FOREIGN KEY (user) REFERENCES ".Player::table_name()."(id)
		) ".$charset_collate.";";
		dbDelta( $sql3 );
	}

	public static function table_main_players(){
		return static::table_name().'_main_players';
	}
	public static function table_additional_players(){
		return static::table_name().'_additional_players';
	}
	
	protected static function row_to_object($row_object){
		return new Team(
				$row_object->name, 
				intval($row_object->coach), 
				intval($row_object->assistant_coach), 
				intval($row_object->id)
		);
	}
	

	/** Brauchbar als Ajax-Callbackfunction */
	public static function set_coach($team_id, $user_id){
		global $wpdb;
		$wpdb->update(static::table_name(), array('coach' => $user_id), array('id' => $team_id));
		$row = $wpdb->get_row( 'SELECT coach FROM '.static::table_name().' WHERE id = '.$team_id );
		return $row->coach;
	}
	
	/** Brauchbar als Ajax-Callbackfunction */
	public static function set_assistant_coach($team_id, $user_id){
		global $wpdb;
		$wpdb->update(static::table_name(), array('assistant_coach' => $user_id), array('id' => $team_id));
		$row = $wpdb->get_row( 'SELECT assistant_coach FROM '.static::table_name().' WHERE id = '.$team_id );
		return $row->assistant_coach;
	}
}

?>