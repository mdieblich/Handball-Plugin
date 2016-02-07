<?php
namespace handball\input;

function weekday_select($name, $onchange=null, $select=-1){
	$onchange = (is_null($onchange)) ? '': 'onchange="'.$onchange.'"';
	if($select == -1 && isset($_GET[$name])){
		$select = $_GET[$name];
	}
	?>
	<select name="<?php echo $name; ?>" <?php echo $onchange;?>>
		<option value="-1" style="color:silver; font-style:italic">bitte wählen</option>
        <option value="MONDAY" <?php if($select == 'MONDAY'){ echo 'selected'; } ?>>Montag</option>
        <option value="TUESDAY" <?php if($select == 'TUESDAY'){ echo 'selected'; } ?>>Dienstag</option>
        <option value="WEDNESDAY" <?php if($select == 'WEDNESDAY'){ echo 'selected'; } ?>>Mittwoch</option>
        <option value="THURSDAY" <?php if($select == 'THURSDAY'){ echo 'selected'; } ?>>Donnerstag</option>
        <option value="FRIDAY" <?php if($select == 'FRIDAY'){ echo 'selected'; } ?>>Freitag</option>
        <option value="SATURDAY" <?php if($select == 'SATURDAY'){ echo 'selected'; } ?>>Samstag</option>
        <option value="SUNDAY" <?php if($select == 'SUNDAY'){ echo 'selected'; } ?>>Sonntag</option>
	</select>
	<?php 
}
?>