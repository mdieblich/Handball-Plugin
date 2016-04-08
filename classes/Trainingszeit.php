<?php
namespace handball;
require_once 'WPDBObject.php';

class Trainingszeit extends WPDBObject{
	
	private $mannschaft;
	
	private $halle;
	private $wochentag;
	private $uhrzeit;
	private $dauer;
	
	private $hinweis;
	
	public function __construct($wochentag, $uhrzeit, $dauer, $halle=null, $mannschaft=null, $hinweis="", $id=null){
		$this->mannschaft = $mannschaft;
		$this->halle = $halle;
		$this->wochentag = $wochentag;
		$this->uhrzeit = $uhrzeit;
		$this->dauer = $dauer;
		$this->hinweis = $hinweis;
		parent::__construct($id);
	}
	
	protected function to_array(){
		$array = parent::to_array();
		$array['mannschaft'] = static::as_id($this->mannschaft);
		$array['halle'] = static::as_id($this->halle);
		$array['wochentag'] = $this->wochentag;
		$array['uhrzeit'] = $this->uhrzeit;
		$array['dauer'] = $this->dauer;
		$array['hinweis'] = $this->hinweis;
		return $array;
	}

	public static function install(){
		global $wpdb;
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	
		$charset_collate = $wpdb->get_charset_collate();
		$sql =
		"CREATE TABLE ".static::table_name()." (
			  id mediumint(9) NOT NULL AUTO_INCREMENT,
			  mannschaft mediumint(9) unsigned,
			  halle mediumint(9) unsigned,
			  wochentag ENUM('MONDAY', 'TUESDAY', 'WEDNESDAY', 'THURSDAY', 'FRIDAY', 'SATURDAY', 'SUNDAY'),
			  uhrzeit char(5) NOT NULL,
			  dauer tinyint NOT NULL,
			  hinweis text NULL,
			  PRIMARY KEY (id),
 			  FOREIGN KEY (mannschaft) REFERENCES ".Mannschaft::table_name()."(id),
 			  FOREIGN KEY (halle) REFERENCES ".Halle::table_name()."(id)
		) ".$charset_collate.";";
	
		dbDelta( $sql );
	}
	protected static function row_to_object($row_object){
		return new Trainingszeit(
				$row_object->wochentag, 
				$row_object->uhrzeit, 
				$row_object->dauer, 
				$row_object->halle, 
				$row_object->mannschaft, 
				$row_object->hinweis, 
				$row_object->id);
	}
	
	public static function get_fullcalender_io_events($trainingszeiten){
		if(!is_array($trainingszeiten)){
			throw new \Exception('Trainingszeiten ist kein array: '.$trainingszeiten);
		}
		$fullcalendar_io_events = array();
		foreach($trainingszeiten as $trainingszeit){
			if(!($trainingszeit instanceof Trainingszeit)){
				throw new \Exception('Element ('.$trainingszeit.') im Array ist keine Trainingszeit');
			}
			$fullcalendar_io_events[] = $trainingszeit->to_fullcalendar_io_event();
		}
		return $fullcalendar_io_events;
	}
	
	private function to_fullcalendar_io_event(){
		return 
			"{\n"
				."id: ".$this->get_id().",\n"
				."title: 'Training',\n"
				.'start: \''.$this->get_start_in_current_week()."',\n"
				.'end: \''.$this->get_end_in_current_week()."'\n"
			.'}';
	}
		
	private function get_start_in_current_week(){
		return date('Y-m-d', $this->get_day_in_current_week()).'T'.date('H:i', strtotime($this->uhrzeit)).':00';
	}
	
	private function get_day_in_current_week(){
		$weekday = date('w', strtotime($this->wochentag));
		$currentWeekDay = date('w');
		return strtotime(($weekday-$currentWeekDay).' day');
	}
	private function get_end_in_current_week(){
		return date('Y-m-d', $this->get_day_in_current_week()).'T'.$this->get_endzeit().':00';
	}
	private function get_endzeit(){
		$startzeit = strtotime($this->uhrzeit);
		$endzeit = strtotime($this->dauer.' min', $startzeit);
		return date('H:i', $endzeit);
	}
}
?>