<?php
namespace handball;

// TODO hier geht's weiter! Mannschaft und Mannschaft_Meta davon ableiten
abstract class DBObject{
	private $id;
	
	public function __construct(){
		global $wpdb;
		$wpdb->insert(static::table_name(), $this->to_array());
		$this->id = $wpdb->insert_id;
	}
	
	protected abstract function to_array();
	
	public function get_id(){
		return $this->id;
	}

	public static function install(){
		global $wpdb;
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	
		$charset_collate = $wpdb->get_charset_collate();
	
		$sql =
		"CREATE TABLE ".static::table_name()." (
			  id mediumint(9) NOT NULL AUTO_INCREMENT,
			  ".static::get_column_sql().",
			  PRIMARY KEY id (id)
		) ".$charset_collate.";";
	
		dbDelta( $sql );
	}

	protected static function table_name(){
		global $wpdb;
		return $wpdb->prefix . end(explode('\\', get_called_class()));
	}
	
	protected abstract static function get_column_sql();

	public static function uninstall(){
		global $wpdb;
		$sql = "DROP TABLE IF EXISTS ". static::table_name().";";
		$wpdb->query($sql);
	}
	
	public static function get_all(){
		global $wpdb;
		$sql = "SELECT * FROM ". static::table_name();
		$found_objects = array();
		foreach($wpdb->get_results($sql) as $object){
			$found_objects[] = static::create_from_array($object);
		}
		return $found_objects;
	}
	
	public abstract static function create_from_array($array);
	
	public static function as_id($object){
		if($object instanceof DBObject){
			return $object->get_id();
		}else{
			return $object;
		}
	}
}
?>