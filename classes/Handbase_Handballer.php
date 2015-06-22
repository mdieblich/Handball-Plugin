<?php

require_once("Handbase_Spielposition.php");

class Handbase_Handballer{
	
	private $user_id;
	/** Auf welcher Position spielt dieser Spieler? */
	private $positionen;
	
	public function __construct($user_id){
		$this->user_id = $user_id;
	}
	
	public function plays_on_position($position){
		if(is_null($this->positionen)){
			$this->load_positionen();
		}
		return in_array($position, $this->positionen);
	}
	
	private function load_positionen(){
		$this->positionen = array();
		foreach (Handbase_Spielposition::alle_positionen() as $position){
			if($this->get_meta($position->get_meta_name())){
				$this->positionen[] = $position;
			}	
		}
	}

	private function get_meta($key){
		return get_user_meta($this->user_id, $key);
	}
	
	public function plays_position($position){
		$this->ensure_spielposition($position);
		if(!$this->plays_on_position($position)){
			$this->positionen[] = $position;
		}
	}
	
	public function does_not_play_on_position($position){
		$this->ensure_spielposition($position);
		if(is_null($this->positionen)){
			$this->load_positionen();
		}
		if(($key = array_search($position, $this->positionen)) !== false) {
			unset($this->positionen[$key]);
		}
	}
	
	private function ensure_spielposition($position){
		if(! ($position instanceof Handbase_Spielposition)){
			ob_start();
			var_dump($position);
			throw new Exception("Position ist murks: " + $ob_get_clean());
		}
	}
	
	public function save(){
		$this->save_positionen();
	}
	
	private function save_positionen(){
		foreach(Handbase_Spielposition::alle_positionen() as $position){
			$this->set_meta($position->get_meta_name(), $this->plays_on_position($position));
		}
	}
	
	private function set_meta($key, $value){
		update_usermeta( $this->user_id, $key, $value );
	}
}
?>