<?php
namespace handball;

require_once(HANDBASE_PLUGIN_DIR.'/php/classes/WPDBObject.php');

class Trainingtime extends WPDBObject{
	
	private $team;
	
	private $location;
	private $wochentag;
	private $uhrzeit;
	private $dauer;
	
	private $hinweis;
	
	public function __construct($wochentag, $uhrzeit, $dauer, $location=null, $team=null, $hinweis="", $id=null){
		$this->team = $team;
		$this->location = $location;
		$this->wochentag = $wochentag;
		$this->uhrzeit = $uhrzeit;
		$this->dauer = $dauer;
		$this->hinweis = $hinweis;
		parent::__construct($id);
	}
	
	protected function to_array(){
		$array = parent::to_array();
		$array['team'] = static::as_id($this->team);
		$array['location'] = static::as_id($this->location);
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
			  team mediumint(9) unsigned,
			  location mediumint(9) unsigned,
			  wochentag ENUM('MONDAY', 'TUESDAY', 'WEDNESDAY', 'THURSDAY', 'FRIDAY', 'SATURDAY', 'SUNDAY'),
			  uhrzeit char(5) NOT NULL,
			  dauer int NOT NULL,
			  hinweis text NULL,
			  PRIMARY KEY (id),
 			  FOREIGN KEY (team) REFERENCES ".Team::table_name()."(id),
 			  FOREIGN KEY (location) REFERENCES ".Location::table_name()."(id)
		) ".$charset_collate.";";
	
		dbDelta( $sql );
	}
	protected static function row_to_object($row_object){
		return new Trainingtime(
				$row_object->wochentag, 
				$row_object->uhrzeit, 
				$row_object->dauer, 
				$row_object->location, 
				$row_object->team, 
				$row_object->hinweis, 
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
				.'comment: \''.$this->hinweis."'\n"
			.'}';
	}
		
	private function get_start_in_current_week(){
		return date('Y-m-d', $this->get_day_in_current_week()).'T'.date('H:i', strtotime($this->uhrzeit)).':00';
	}
	
	private function get_day_in_current_week(){
		$weekday = date('w', strtotime($this->wochentag));
		if($weekday == 0){ $weekday=7;}
		$currentWeekDay = date('w');
		if($currentWeekDay == 0){ $currentWeekDay=7;}
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

	public function set_uhrzeit($uhrzeit){
		$this->uhrzeit = $uhrzeit;
	}
	public function set_wochentag($wochentag){
		$this->wochentag = $wochentag;
	}
	public function set_dauer($dauer){
		$this->dauer = $dauer;
	}
	public function set_team($team_id){
		$this->team = $team_id;
	}
	public function set_location($location_id){
		$this->location = $location_id;
	}
	public function set_comment($comment){
		$this->hinweis = $comment;
	}
}
?>