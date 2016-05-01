<?php
namespace handball;

require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
require_once(HANDBASE_PLUGIN_DIR.'/php/classes/WPDBObject.php');

class Trainingtime extends WPDBObject{
	
	private $team;
	
	private $location;
	private $weekday;
	private $time;
	private $duration_minutes;
	
	private $note;
	
	public function __construct($weekday, $time, $duration_minutes, $location=null, $team=null, $note="", $id=null){
		$this->team = $team;
		$this->location = $location;
		$this->weekday = $weekday;
		$this->time = $time;
		$this->duration_minutes = $duration_minutes;
		$this->note = $note;
		parent::__construct($id);
	}
	
	protected function to_array(){
		$array = parent::to_array();
		$array['team'] = static::as_id($this->team);
		$array['location'] = static::as_id($this->location);
		$array['weekday'] = $this->weekday;
		$array['time'] = $this->time;
		$array['duration_minutes'] = $this->duration_minutes;
		$array['note'] = $this->note;
		return $array;
	}

	public static function install(){
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();
		$sql =
		"CREATE TABLE ".static::table_name()." (
			  id mediumint(9) NOT NULL AUTO_INCREMENT,
			  team mediumint(9) unsigned,
			  location mediumint(9) unsigned,
			  weekday ENUM('MONDAY', 'TUESDAY', 'WEDNESDAY', 'THURSDAY', 'FRIDAY', 'SATURDAY', 'SUNDAY'),
			  time char(5) NOT NULL,
			  duration_minutes int NOT NULL,
			  note text NULL,
			  PRIMARY KEY (id),
 			  FOREIGN KEY (team) REFERENCES ".Team::table_name()."(id),
 			  FOREIGN KEY (location) REFERENCES ".Location::table_name()."(id)
		) ".$charset_collate.";";
	
		dbDelta( $sql );
	}
	protected static function row_to_object($row_object){
		return new Trainingtime(
				$row_object->weekday, 
				$row_object->time, 
				$row_object->duration_minutes, 
				$row_object->location, 
				$row_object->team, 
				$row_object->note, 
				$row_object->id);
	}
	
	public static function get_fullcalender_io_events($trainingtimes){
		if(!is_array($trainingtimes)){
			throw new \Exception('Trainingszeiten ist kein array: '.$trainingtimes);
		}
		$fullcalendar_io_events = array();
		foreach($trainingtimes as $trainingtime){
			if(!($trainingtime instanceof Trainingtime)){
				throw new \Exception('Element ('.$trainingtime.') im Array ist keine Trainingszeit');
			}
			$fullcalendar_io_events[] = $trainingtime->to_fullcalendar_io_event();
		}
		return $fullcalendar_io_events;
	}
	
	private function to_fullcalendar_io_event(){
		$teamname = is_null($this->team) ? 'Kein Team' : Team::get_by_id($this->team)->get_name();
		return 
			"{\n"
				."id: ".$this->get_id().",\n"
				."title: '$teamname',\n"
				.'start: \''.$this->get_start_in_current_week()."',\n"
				.'end: \''.$this->get_end_in_current_week()."',\n"
				.'location_id: \''.$this->location."',\n"
				.'team_id: \''.$this->team."',\n"
				.'note: \''.$this->note."'\n"
			.'}';
	}
		
	private function get_start_in_current_week(){
		return date('Y-m-d', $this->get_day_in_current_week()).'T'.date('H:i', strtotime($this->time)).':00';
	}
	
	private function get_day_in_current_week(){
		$weekday = date('w', strtotime($this->weekday));
		if($weekday == 0){ $weekday=7;}
		$currentWeekDay = date('w');
		if($currentWeekDay == 0){ $currentWeekDay=7;}
		return strtotime(($weekday-$currentWeekDay).' day');
	}
	private function get_end_in_current_week(){
		return date('Y-m-d', $this->get_day_in_current_week()).'T'.$this->get_endtime().':00';
	}
	private function get_endtime(){
		$starttime = strtotime($this->time);
		$endtime = strtotime($this->duration_minutes.' min', $starttime);
		return date('H:i', $endtime);
	}

	public function set_time($time){
		$this->time = $time;
	}
	public function set_weekday($weekday){
		$this->weekday = $weekday;
	}
	public function set_duration_minutes($duration_minutes){
		$this->duration_minutes = $duration_minutes;
	}
	public function set_team($team_id){
		$this->team = $team_id;
	}
	public function set_location($location_id){
		$this->location = $location_id;
	}
	public function set_note($note){
		$this->note = $note;
	}
}
?>