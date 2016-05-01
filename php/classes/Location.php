<?php

namespace handball;

require_once(ABSPATH.'wp-admin/includes/upgrade.php');
require_once(HANDBASE_PLUGIN_DIR.'/php/classes/WPDBObject.php');

class Location extends WPDBObject{
	private $name;
	private $abbreviation;
	private $address;
	private $color;
	
	public function __construct($name, $abbreviation, $address, $color, $id=null){
		// TODO prevent MySQLInjection 
		$this->name = $name;
		$this->abbreviation = $abbreviation;
		$this->address = $address;
		$this->color = $color;
		parent::__construct($id);
	}
	
	public function get_name(){
		return $this->name;
	}
	
	public function get_abbreviation(){
		return $this->abbreviation;
	}
	public function get_address(){
		return $this->address;
	}
	public function get_color(){
		return $this->color;
	}
	
	protected function to_array(){
		$array = parent::to_array();
		$array['name'] = $this->name;
		$array['abbreviation'] = $this->abbreviation;
		$array['address'] = $this->address;
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
			  abbreviation tinytext NOT NULL,
			  address text NOT NULL,
			  color tinytext NOT NULL,
			  PRIMARY KEY (id)
		) ".$charset_collate.";";
	
		dbDelta( $sql );
	}
	
	protected static function row_to_object($row_object){
		return new Location($row_object->name, $row_object->abbreviation, $row_object->address, $row_object->color, $row_object->id);
	}
	
	public function get_fullcalendar_io_event_source_name(){
		return 'trainingtimes'.$this->get_abbreviation();
	}
	
	public function get_trainingtimes_as_fullcalendar_io_event_source(){
		$fullcalender_events = Trainingtime::get_fullcalender_io_events($this->get_trainingtimes());
		return 	"{\n"
				."color: '".$this->get_color()."',\n"
				.'events: ['.implode(", \n", $fullcalender_events)."]\n"
				."\n}";
	}
	
	private function get_trainingtimes(){
		return Trainingtime::get('location='.$this->get_id());
	}
}
?>