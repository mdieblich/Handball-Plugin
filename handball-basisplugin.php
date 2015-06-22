<?php
/*
Plugin Name: Handball-Basisplugin
Plugin URI: http://dieblich.com/wordpress-handball-base
Description: Dieses Plugin bietet Basis-Klassen und Attribute f�r Handball an
Version: 0.0.3
Author: Martin Dieblich
Author URI: http://dieblich.com
License: GPLv2
*/
define( 'HANDBASE_PLUGIN_FILE', __FILE__ );

add_action( 'personal_options_update', 'handbase_save_extra_profile_fields' );
add_action( 'edit_user_profile_update', 'handbase_save_extra_profile_fields' );
add_action( 'show_user_profile', 'handbase_show_extra_profile_fields' );
add_action( 'edit_user_profile', 'handbase_show_extra_profile_fields' );

function handbase_show_extra_profile_fields( $user ) { ?>

	<h3>Handballer-Info</h3>

	<table class="form-table">
	
	<?php handbase_show_positions_profile_fields($user); ?>

	</table>
<?php }

function handbase_show_positions_profile_fields($user){
	require_once 'classes/Handbase_Handballer.php';
	$handballer = new Handbase_Handballer($user->ID);
	?><tr>
<th><label for="position">Positionen</label></th>

<td><fieldset><?php 

	foreach(Handbase_Spielposition::alle_positionen() as $position){
		$abkuerzung = $position->get_abkuerzung();
		$bezeichnung = $position->get_bezeichnung();
		$checked = $handballer->plays_on_position($position)?'checked':'';
		echo '<label for="'.$abkuerzung.'">';
		echo '<input type="checkbox" name="position_'.$abkuerzung.'" value="true" id="'.$abkuerzung.'" '.$checked.'>';
		echo $bezeichnung.' </label>'; 
	}
	
	?>
	</fieldset>
	<span class="description">Auf welchen Positionen spielst du haupts&auml;chlich?</span>
	</td>
	</tr>
	<?php 
}

function handbase_save_extra_profile_fields( $user_id ) {

	// TODO Methode anpassen: Checkboxen auswerten
	if ( !current_user_can( 'edit_user', $user_id ) )
		return false;

	require_once 'classes/Handbase_Handballer.php';
	$handballer = new Handbase_Handballer($user_id);

	/* Copy and paste this line for additional fields. Make sure to change 'twitter' to the field ID. */
// 	update_usermeta( $user_id, 'position', $_POST['position'] );

	foreach(Handbase_Spielposition::alle_positionen() as $position){
		$abkuerzung = $position->get_abkuerzung();
		$position_field_name = 'position_'.$abkuerzung;
		$plays_position = $_POST[$position_field_name];
		if($plays_position){
			$handballer->plays_position($position);
		}else{
			$handballer->does_not_play_on_position($position);
		}
		$handballer->save();
	}
}

?>