<?php

namespace handball;
require_once 'Handballer.php';

/**
 * TODO von gemeinsamer DB-Object Klasse ableiten
 * TODO Datenbank-Schema-Upgrade: https://codex.wordpress.org/Creating_Tables_with_Plugins
 * 
 */
class Mannschaft {
	
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
		if(-1 === $id){
			global $wpdb;
			$wpdb->insert(
				static::table_name(),
				array(
					'name' => $name,
					'trainer' => static::as_id($this->trainer),
					'cotrainer' => static::as_id($this->cotrainer)
				)
			);
			$this->id = $wpdb->insert_id;
		}else{
			$this->id = $id;
		}
	}
	
	public function get_id(){
		return $this->id;
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
	}
	
	private static function table_name(){
		global $wpdb;
		return $wpdb->prefix . end(explode('\\', get_called_class()));
	}
	
	public static function as_id($object){
		if($object instanceof DBObject){
			return $object->get_id();
		}else{
			return $object;
		}
	}
	
	public static function get_all(){
		global $wpdb;
		$sql = "SELECT * FROM ". static::table_name();
		$teams = array();
		foreach($wpdb->get_results($sql) as $raw_team){
			$teams[] = new Mannschaft($raw_team->name, $raw_team->trainer, $raw_team->cotrainer, $raw_team->id);
		}
		return $teams;
	}
	
	public static function delete($delete_id){
		if (is_int ( $delete_id )) {
			global $wpdb;
			$wpdb->delete ( static::table_name (), array ('id' => $delete_id ) );
		}
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