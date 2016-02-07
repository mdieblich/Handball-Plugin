<?php
namespace handball\input;

use handball\Halle;
function hall_select($name, $onchange=null, $select=-1){
	$onchange = (is_null($onchange)) ? '': 'onchange="'.$onchange.'"'; 
	if($select == -1 && isset($_GET[$name])){
		$select = $_GET[$name];
	} 
	?>
	<select name="<?php echo $name; ?>" <?php echo $onchange;?>>
		<option value="-1" style="color:silver; font-style:italic">bitte wählen</option>
	<?php 
		require_once (ABSPATH . 'wp-content/plugins/handball-basisplugin/classes/Halle.php');
		$all_halls = Halle::get_all();
		foreach ($all_halls as $hall){
			$selected = ( $select == $hall->get_id() ) ? ' selected' : '';
			echo '<option value="'.$hall->get_id().'"'.$selected.'>'.$hall->get_name().'</option>';	
		}
	?>
	</select>
	<?php 
}
?>