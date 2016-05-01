<?php
namespace handball;

abstract class WPDBObject{
	
	private $id;

	public function __construct($id=null){
		if(is_null($id)){
			global $wpdb;
			$wpdb->insert( static::table_name(), $this->to_array_without_id()	);
			$this->id = $wpdb->insert_id;
		}else if(is_numeric($id)){
			$this->id = intval($id);
		}else{
			throw new \Exception('Die ID ist kein Integer oder null: '.$id);
		}
	}
	
	protected function to_array(){
		return array('id' => $this->id);
	}
	
	private function to_array_without_id(){
		$array = $this->to_array();
		unset($array['id']);
		return $array;
	}
	
	public function get_id(){
		return $this->id;
	}
	
	public static function table_name(){
		global $wpdb;
		$classname = get_called_class();
		$splitted_classname = explode('\\', $classname);
		$last_part_of_classname = end($splitted_classname);
		return $wpdb->prefix .HANDBASE_TABLE_PREFIX.'_'. $last_part_of_classname;
	}

	public static function install(){
		// wird von Kindklassen überschrieben
	}
	public static function uninstall(){}
	
	public static function as_id($object){
		if(is_null($object)){
			return null; // vorher stand hier null
		}if($object instanceof DBObject){
			return $object->get_id();
		}else if(is_numeric($object)){
			return $object;
		}
		throw new \Exception($object.' ist weder null, ein DBObject, noch ein Integer');
	}
	
	public static function get_by_id($id){
		global $wpdb;
		$sql = "SELECT * FROM ". static::table_name()." WHERE id=$id";
		$row = $wpdb->get_row( $sql );
		
		return static::row_to_object($row);
	}
	
	public static function get($where){
		global $wpdb;
		$sql = 'SELECT * FROM '. static::table_name().' WHERE '.$where;
		$objects = array();
		foreach($wpdb->get_results($sql) as $raw_object){
			$objects[] = static::row_to_object($raw_object);
		}
		return $objects;
	}
	
	public static function get_all(){
		return static::get('1'); // ist das ein get_all?
	}
	
	public static function delete($delete_id){
		if (is_int ( $delete_id )) {
			global $wpdb;
			$wpdb->delete ( static::table_name (), array ('id' => $delete_id ) );
		}
	}
	
	protected static function row_to_object($row_object){
		// wird von Kindklassen überschrieben
	}
	
	public function is_in_db(){
		return $this->id != -1;
	}
	
	public function toJSON(){
		return json_encode($this->to_array());
	}
	
	public function save(){
		if(is_null($this->id)){
			throw new \Exception('ID ist null für '.$this);
		}
		global $wpdb;
		return false !== $wpdb->update(
				static::table_name(), 
				$this->to_array_without_id(), 
				array('id' => intval($this->id)));
		
	}
	
}
?>