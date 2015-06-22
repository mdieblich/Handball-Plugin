<?php

namespace handball;
require_once("Spielposition.php");

class Handballer{
	
	private $user_id;
	/** Auf welcher Position spielt dieser Spieler? */
	private $positionen;
	
	public function __construct($user_id){
		$this->user_id = $user_id;
	}
	
	public function show_profile_extras(){
		?>
		<h3>Handballer-Info</h3>
		<table class="form-table">
			<?php $this->show_position_checkboxes(); ?>
		</table>
		<?php
	}
	
	private function show_position_checkboxes(){
		?><tr>
			<th>
				<label for="position">Positionen</label>
			</th>
			<td>
				<fieldset><?php 
					foreach(Spielposition::alle_positionen() as $position){
						$abkuerzung = $position->get_abkuerzung();
						$bezeichnung = $position->get_bezeichnung();
						$checked = $this->plays_on_position($position)?'checked':'';
						echo '<label for="'.$abkuerzung.'">';
						echo '<input type="checkbox" name="position_'.$abkuerzung.'" value="true" id="'.$abkuerzung.'" '.$checked.'>';
						echo $bezeichnung.' </label>'; 
					}
				?></fieldset>
				<span class="description">
					Auf welchen Positionen spielst du hauptsächlich?
				</span>
			</td>
		</tr><?php 
	}
		
	
	public function plays_on_position($position){
		if(is_null($this->positionen)){
			$this->load_positionen();
		}
		return in_array($position, $this->positionen);
	}
	
	private function load_positionen(){
		$this->positionen = array();
		foreach (Spielposition::alle_positionen() as $position){
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
		if(! ($position instanceof Spielposition)){
			ob_start();
			var_dump($position);
			throw new Exception("Position ist murks: " + $ob_get_clean());
		}
	}
	
	public function save_from_post(){
		$this->get_position_from_post();
		$this->save();
	}
	
	private function get_position_from_post(){
		foreach(Spielposition::alle_positionen() as $position){
			$abkuerzung = $position->get_abkuerzung();
			$position_field_name = 'position_'.$abkuerzung;
			$plays_position = $_POST[$position_field_name];
			if($plays_position){
				$this->plays_position($position);
			}else{
				$this->does_not_play_on_position($position);
			}
		}
	}
	
	public function save(){
		$this->save_positionen();
	}
	
	private function save_positionen(){
		foreach(Spielposition::alle_positionen() as $position){
			$this->set_meta($position->get_meta_name(), $this->plays_on_position($position));
		}
	}
	
	private function set_meta($key, $value){
		update_usermeta( $this->user_id, $key, $value );
	}
}
?>