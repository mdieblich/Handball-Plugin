<?php

namespace handball;
require_once 'WPDBObject.php';

class Halle extends WPDBObject{
	private $name;
	private $abkuerzung;
	private $adresse;
	
	public function __construct($name, $abkuerzung, $adresse, $id=null){
		// TODO SQLInjection abfangen 
		$this->name = $name;
		$this->abkuerzung = $abkuerzung;
		$this->adresse = $adresse;
		parent::__construct($id);
	}
	
	public function get_name(){
		return $this->name;
	}
	
	public function get_abkuerzung(){
		return $this->abkuerzung;
	}
	public function get_adresse(){
		return $this->adresse;
	}
	
	public function get_name_for_button(){
		return 'halle'.$this->get_id();
	}
	
	protected function to_array(){
		$array = parent::to_array();
		$array['name'] = $this->name;
		$array['abkuerzung'] = $this->abkuerzung;
		$array['adresse'] = $this->adresse;
		return $array;
	}
	
	public static function install(){
		global $wpdb;
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	
		$charset_collate = $wpdb->get_charset_collate();
		$sql =
		"CREATE TABLE ".static::table_name()." (
			  id mediumint(9) unsigned NOT NULL AUTO_INCREMENT,
			  name tinytext NOT NULL,
			  abkuerzung tinytext NOT NULL,
			  adresse text NOT NULL,
			  PRIMARY KEY (id)
		) ".$charset_collate.";";
	
		dbDelta( $sql );
	}
	
	protected static function row_to_object($row_object){
		return new Halle($row_object->name, $row_object->abkuerzung, $row_object->adresse, $row_object->id);
	}
	
	public function get_trainingszeiten_as_fullcalendar_io_event_source(){
		
	}
}
?>