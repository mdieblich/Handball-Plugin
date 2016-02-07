<?php
namespace handball;

abstract class WPDBObject{
	
	private $id;

	public function __construct($id=-1){
		if(-1 === $id){
			global $wpdb;
			$parameters = $this->to_array();
			unset($parameters['id']);
			$wpdb->insert( static::table_name(), $parameters	);
			$this->id = $wpdb->insert_id;
		}else{
			$this->id = $id;
		}
	}
	
	protected function to_array(){
		return array('id' => $this->id);
	}
	
	public function get_id(){
		return $this->id;
	}
	
	protected static function table_name(){
		global $wpdb;
		return $wpdb->prefix . end(explode('\\', get_called_class()));
	}

	public abstract static function install();
	public static function uninstall(){}
	
	public static function as_id($object){
		if($object instanceof DBObject){
			return $object->get_id();
		}else{
			return $object;
		}
	}
	
	public static function get($id){
		global $wpdb;
		$sql = "SELECT * FROM ". static::table_name()." WHERE id=$id";
		$row = $wpdb->get_row( $sql );
		
		return static::row_to_object($row);
	}
	
	public static function get_all(){
		global $wpdb;
		$sql = "SELECT * FROM ". static::table_name();
		$objects = array();
		foreach($wpdb->get_results($sql) as $raw_object){
			$objects[] = static::row_to_object($raw_object);
		}
		return $objects;
	}
	
	public static function delete($delete_id){
		if (is_int ( $delete_id )) {
			global $wpdb;
			$wpdb->delete ( static::table_name (), array ('id' => $delete_id ) );
		}
	}
	
	protected abstract static function row_to_object($row_object);
	
}
?>