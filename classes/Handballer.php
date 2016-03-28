<?php

namespace handball;
require_once("Spielposition.php");

class Handballer{
	
	private $id;
	/** Auf welcher Position spielt dieser Spieler? */
	private $positionen;
	
	/** Hauptmannschaft, in der man drin ist */
	private $stammmanschaft;
	
	/** Mannschaft, in die man rein möchte (überschreibt stammmannschaft) */
	private $wunschmannschaft;
	
	/** Mannschaften, die man "abonniert" hat */
	private $zusatzmannschaften;
	
	public function __construct($user_id){
		$this->id = $user_id;
	}
	
	public function get_id(){
		return $this->id;
	}
	
	public function show_profile_extras(){
		?>
		<h3>Handballer-Info</h3>
		<table class="form-table">
			<?php
				$this->show_position_checkboxes ();
				$this->list_stammmannschaften ();
				$this->list_zusatzmannschaften ();
			?>
		</table>
		<?php
	}
	
	public function show_position_checkboxes($enabled = true){
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
						$disabled = $enabled ? "":"disabled";
						echo '<label for="'.$abkuerzung.'">';
						echo '<input type="checkbox" name="position_'.$abkuerzung.'" value="true" id="'.$abkuerzung.'" '.$checked.' '.$disabled.'>';
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
		return get_user_meta($this->id, $key);
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
			throw new Exception("Position ist murks: " + $ob_get_clean());
		}
	}
	
	public function save_from_post(){
		$this->get_position_from_post();
		$this->save();
	}
	
	public function __toString(){
		$user = get_userdata($this->id);
		return $user->first_name.' '.$user->last_name;
	}
	
	private function get_position_from_post(){
		foreach(Spielposition::alle_positionen() as $position){
			$abkuerzung = $position->get_abkuerzung();
			$position_field_name = 'position_'.$abkuerzung;
			if(isset($_POST[$position_field_name])){
				$plays_on_position = $_POST[$position_field_name];
				if($plays_on_position){
					$this->plays_position($position);
				}else{
					$this->does_not_play_on_position($position);
				}
			}else{
				$this->does_not_play_on_position($position);
			}
		}
	}
	
	public function save(){
		$this->save_positionen();
	}
	
	private function save_positionen(){
		var_dump($this->positionen);
		foreach(Spielposition::alle_positionen() as $position){
			if($this->plays_on_position($position)){
				$this->set_meta($position->get_meta_name(), true);
			}else{
				$this->delete_meta($position->get_meta_name());
			}
		}
	}

	private function set_meta($key, $value){
		update_user_meta( $this->id, $key, $value );
	}
	private function delete_meta($key){
		delete_user_meta( $this->id, $key );
	}

	public function get_stammmannschaften(){
		$teams = array();

		global $wpdb;
		$sql = "SELECT team FROM ". Mannschaft::table_stammspieler()." WHERE user=$this->id";
		foreach($wpdb->get_results($sql) as $row){
			$teams[] = Mannschaft::get_by_id($row->team);
		}
		
		return $teams;
	}
	public function get_zusatzmannschaften(){
		$teams = array();

		global $wpdb;
		$sql = "SELECT team FROM ". Mannschaft::table_zusatzspieler()." WHERE user=$this->id";
		foreach($wpdb->get_results($sql) as $row){
			$teams[] = Mannschaft::get_by_id($row->team);
		}
		
		return $teams;
	}
	public function list_stammmannschaften(){
		?><tr>
			<th>
				<label for="stammmannschaft">Stammmannschaften</label>
			</th>
			<td>
				<?php 
				foreach ($this->get_stammmannschaften() as $stammmannschaft){
					echo $stammmannschaft->get_name()."<br>";	
				}
				?>
			</td>
		</tr><?php 
	}
	public function list_zusatzmannschaften(){
		?><tr>
			<th>
				<label for="zusatzmannschaft">Weitere Mannschaften</label>
			</th>
			<td>
				<?php
				foreach ($this->get_zusatzmannschaften() as $zusatzmannschaft){
					echo $zusatzmannschaft->get_name()."<br>";
				}
				?>
			</td>
		</tr><?php 
	}
	
	public static function table_name(){
		global $wpdb;
		return $wpdb->prefix."users";
	}
}
?>