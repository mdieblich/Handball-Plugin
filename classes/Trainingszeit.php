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
	
	public function __construct($mannschaft, $halle, $wochentag, $uhrzeit, $dauer, $hinweis="", $id=-1){
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
			  mannschaft mediumint(9) NOT NULL,
			  halle mediumint(9) NOT NULL,
			  wochentag ENUM('MONDAY', 'TUESDAY', 'WEDNESDAY', 'THURSDAY', 'FRIDAY', 'SATURDAY', 'SUNDAY'),
			  uhrzeit char(4) NOT NULL,
			  dauer tinyint NOT NULL,
			  hinweis text NULL,
			  PRIMARY KEY id (id),
 			  FOREIGN KEY (mannschaft) REFERENCES ".Mannschaft::table_name()."(id),
 			  FOREIGN KEY (halle) REFERENCES ".Halle::table_name()."(id),
		) ".$charset_collate.";";
	
		dbDelta( $sql );
	}
	protected static function row_to_object($row_object){
		return new Trainingszeit(
				$row_object->mannschaft, 
				$row_object->halle, 
				$row_object->wochentag, 
				$row_object->uhrzeit, 
				$row_object->dauer, 
				$row_object->hinweis, 
				$row_object->id);
	}
}
?>