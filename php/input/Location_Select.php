<?php
namespace handball\input;

use handball\Location;

require_once(HANDBASE_PLUGIN_DIR.'/php/classes/Location.php');

function location_select($name, $html_id=null, $onchange=null, $select=-1){
	$onchange = (is_null($onchange)) ? '': 'onchange="'.$onchange.'"'; 
	if($select == -1 && isset($_GET[$name])){
		$select = $_GET[$name];
	} 
	if(is_null($html_id)){
		$html_id = $name;
	}
	?>
	<select name="<?php echo $name; ?>" id="<?php echo $html_id; ?>" <?php echo $onchange;?>>
		<option value="" style="color:silver; font-style:italic">(kein Trainingsort)</option>
	<?php 
		$all_locations = Location::get_all();
		foreach ($all_locations as $location){
			$selected = ( $select == $location->get_id() ) ? ' selected' : '';
			echo '<option value="'.$location->get_id().'"'.$selected.'>'.$location->get_name().'</option>';	
		}
	?>
	</select>
	<?php 
}
?>