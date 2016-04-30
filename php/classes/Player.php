<?php

namespace handball;

require_once(HANDBASE_PLUGIN_DIR.'/php/classes/Position.php');

class Player{
	
	private $id;
	/** Auf welcher Position spielt dieser Spieler? */
	private $positions;
	
	public function __construct($user_id){
		if(!is_int($user_id)){
			throw new \Exception($user_id.' ist kein Integer.');
		}else if($user_id<0){
			throw new \Exception($user_id.' ist negativ');
		}
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
				$this->list_main_teams ();
				$this->list_additional_teams ();
			?>
		</table>
		<?php
	}
	
	public function show_position_checkboxes($enabled = true){
		if(!is_bool($enabled)){
			throw new \Exception($enabled.' ist kein boolescher Wert');	
		}
		?><tr>
			<th>
				<label for="position">Positionen</label>
			</th>
			<td>
				<fieldset><?php 
					foreach(Position::alle_positionen() as $position){
						$abbreviation = $position->get_abkuerzung();
						$name = $position->get_bezeichnung();
						$checked = $this->plays_on_position($position)?'checked':'';
						$disabled = $enabled ? "":"disabled";
						echo '<label for="'.$abbreviation.'">';
						echo '<input type="checkbox" name="position_'.$abbreviation.'" value="true" id="'.$abbreviation.'" '.$checked.' '.$disabled.'>';
						echo $name.' </label>'; 
					}
				?></fieldset>
				<span class="description">
					Auf welchen Positionen spielst du hauptsächlich?
				</span>
			</td>
		</tr><?php 
	}
		
	
	public function plays_on_position($position){
		if(is_null($this->positions)){
			$this->load_positions();
		}
		return in_array($position, $this->positions);
	}
	
	private function load_positions(){
		$this->positions = array();
		foreach (Position::alle_positionen() as $position){
			if($this->get_meta($position->get_meta_name())){
				$this->positions[] = $position;
			}	
		}
	}

	private function get_meta($key){
		return get_user_meta($this->id, $key);
	}
	
	public function plays_position($position){
		if(! ($position instanceof Position) ){
			throw new \Exception($position.' ist keine Spielposition');	
		}
		$this->ensure_position($position);
		if(!$this->plays_on_position($position)){
			$this->positions[] = $position;
		}
	}
	
	public function does_not_play_on_position($position){
		if(! ($position instanceof Position) ){
			throw new \Exception($position.' ist keine Spielposition');	
		}
		$this->ensure_position($position);
		if(is_null($this->positions)){
			$this->load_positions();
		}
		if(($key = array_search($position, $this->positions)) !== false) {
			unset($this->positions[$key]);
		}
	}
	
	private function ensure_position($position){
		if(! ($position instanceof Position)){
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
		foreach(Position::alle_positionen() as $position){
			$abbreviation = $position->get_abkuerzung();
			$position_field_name = 'position_'.$abbreviation;
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
		$this->save_positions();
	}
	
	private function save_positions(){
		var_dump($this->positions);
		foreach(Position::alle_positionen() as $position){
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

	public function get_main_teams(){
		$teams = array();

		global $wpdb;
		$sql = "SELECT team FROM ". Team::table_stammspieler()." WHERE user=$this->id";
		foreach($wpdb->get_results($sql) as $row){
			$teams[] = Team::get_by_id($row->team);
		}
		
		return $teams;
	}
	public function get_additional_teams(){
		$teams = array();

		global $wpdb;
		$sql = "SELECT team FROM ". Team::table_zusatzspieler()." WHERE user=$this->id";
		foreach($wpdb->get_results($sql) as $row){
			$teams[] = Team::get_by_id($row->team);
		}
		
		return $teams;
	}
	public function list_main_teams(){
		?><tr>
			<th>
				<label for="additional_teams">Stammmannschaften</label>
			</th>
			<td>
				<?php 
				foreach ($this->get_main_teams() as $main_team){
					echo $main_team->get_name()."<br>";	
				}
				?>
			</td>
		</tr><?php 
	}
	public function list_additional_teams(){
		?><tr>
			<th>
				<label for="additional_teams">Weitere Mannschaften</label>
			</th>
			<td>
				<?php
				foreach ($this->get_additional_teams() as $additional_team){
					echo $additional_team->get_name()."<br>";
				}
				?>
			</td>
		</tr><?php 
	}
	
	public static function table_name(){
		global $wpdb;
		return $wpdb->prefix."users";
	}
	
	public static function ensure_player($player){
		if(is_null($player)){
			return $player;
		}
		if(is_int($player)){
			return $player;
		}
		if($player instanceof Player){
			return $player;
		}
		throw new \Exception($player.' ist weder null, noch eine id, noch ein Spieler');
	}
	
	public static function as_id_or_null($player){
		if(is_null($player)){
			return null;
		}
		if(is_int($player)){
			return $player;
		}
		if($player instanceof Player){
			return $player->id;
		}
		throw new \Exception($player.' ist weder eine id, noch ein Spieler');
	}
}
?>