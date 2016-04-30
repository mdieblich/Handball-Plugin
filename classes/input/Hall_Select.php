<?php
namespace handball\input;

use handball\Location;
function hall_select($name, $html_id=null, $onchange=null, $select=-1){
	$onchange = (is_null($onchange)) ? '': 'onchange="'.$onchange.'"'; 
	if($select == -1 && isset($_GET[$name])){
		$select = $_GET[$name];
	} 
	if(is_null($html_id)){
		$html_id = $name;
	}
	?>
	<select name="<?php echo $name; ?>" id="<?php echo $html_id; ?>" <?php echo $onchange;?>>
		<option value="" style="color:silver; font-style:italic">bitte wählen</option>
	<?php 
		require_once (ABSPATH . 'wp-content/plugins/handball-basisplugin/classes/Location.php');
		$all_halls = Location::get_all();
		foreach ($all_halls as $hall){
			$selected = ( $select == $hall->get_id() ) ? ' selected' : '';
			echo '<option value="'.$hall->get_id().'"'.$selected.'>'.$hall->get_name().'</option>';	
		}
	?>
	</select>
	<?php 
}
?>