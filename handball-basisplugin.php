<?php
/*
Plugin Name: Handball-Basisplugin
Plugin URI: http://dieblich.com/wordpress-handball-base
Description: Dieses Plugin bietet Basis-Klassen und Attribute für Handball an
Version: 0.0.1
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

		<tr>
			<th><label for="position">Position</label></th>

			<td>
				<input type="text" name="position" id="position" value="<?php echo esc_attr( get_the_author_meta( 'position', $user->ID ) ); ?>" class="regular-text" /><br />
				<span class="description">Auf welcher Position spielst du hauptsächlich?</span>
			</td>
		</tr>

	</table>
<?php }

function handbase_save_extra_profile_fields( $user_id ) {

	if ( !current_user_can( 'edit_user', $user_id ) )
		return false;

	/* Copy and paste this line for additional fields. Make sure to change 'twitter' to the field ID. */
	update_usermeta( $user_id, 'position', $_POST['position'] );
}

?>