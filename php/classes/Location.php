<?php

namespace handball;

require_once(ABSPATH.'wp-admin/includes/upgrade.php');
require_once(HANDBASE_PLUGIN_DIR.'/php/classes/WPDBObject.php');

class Location extends WPDBObject{
	private $name;
	private $abkuerzung;
	private $adresse;
	private $color;
	
	public function __construct($name, $abkuerzung, $adresse, $color, $id=null){
		// TODO SQLInjection abfangen 
		$this->name = $name;
		$this->abkuerzung = $abkuerzung;
		$this->adresse = $adresse;
		$this->color = $color;
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
	public function get_color(){
		return $this->color;
	}
	
	protected function to_array(){
		$array = parent::to_array();
		$array['name'] = $this->name;
		$array['abkuerzung'] = $this->abkuerzung;
		$array['adresse'] = $this->adresse;
		$array['color'] = $this->color;
		return $array;
	}
	
	public static function install(){
		global $wpdb;
	
		$charset_collate = $wpdb->get_charset_collate();
		$sql =
		"CREATE TABLE ".static::table_name()." (
			  id mediumint(9) unsigned NOT NULL AUTO_INCREMENT,
			  name tinytext NOT NULL,
			  abkuerzung tinytext NOT NULL,
			  adresse text NOT NULL,
			  color tinytext NOT NULL,
			  PRIMARY KEY (id)
		) ".$charset_collate.";";
	
		dbDelta( $sql );
	}
	
	protected static function row_to_object($row_object){
		return new Location($row_object->name, $row_object->abkuerzung, $row_object->adresse, $row_object->color, $row_object->id);
	}
	
	public function get_fullcalendar_io_event_source_name(){
		return 'trainingTimes'.$this->get_abkuerzung();
	}
	
	public function get_trainingszeiten_as_fullcalendar_io_event_source(){
		$fullcalender_events = Trainingszeit::get_fullcalender_io_events($this->get_trainingszeiten());
		return 	"{\n"
				."color: '".$this->get_color()."',\n"
				.'events: ['.implode(", \n", $fullcalender_events)."]\n"
				."\n}";
	}
	
	private function get_trainingszeiten(){
		return Trainingszeit::get('location='.$this->get_id());
	}
}
?>