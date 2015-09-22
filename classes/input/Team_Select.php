<?php
namespace handball\input;

use handball\Mannschaft;
function team_select($name, $onchange=null, $select=-1){
	$onchange = (is_null($onchange)) ? '': 'onchange="'.$onchange.'"'; 
	if($select == -1 && isset($_GET[$name])){
		$select = $_GET[$name];
	} 
	?>
	<select name="<?php echo $name; ?>" <?php echo $onchange;?>>
		<option value="-1" style="color:silver; font-style:italic">bitte wählen</option>
	<?php 
		require_once (ABSPATH . 'wp-content/plugins/handball-basisplugin/classes/Mannschaft.php');
		$all_teams = Mannschaft::get_all();
		foreach ($all_teams as $team){
			$selected = ( $select == $team->get_id() ) ? ' selected' : '';
			echo '<option value="'.$team->get_id().'"'.$selected.'>'.$team->get_name().'</option>';	
		}
	?>
	</select>
	<?php 
}
?>