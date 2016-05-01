<?php
namespace handball\menu;

use handball\Player;

require_once(HANDBASE_PLUGIN_DIR.'/php/classes/Player.php');

class Hauptmenu{

	public function __construct(){
		add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
	}
	
	public function add_plugin_page(){
		add_menu_page(
		'Handball - allgemeine Einstellungen', // page_title
		'Handball', // menu_title
		'manage_options', // capability
		'handball',  // menu_slug
		array( $this, 'create_handball_page' ),   // function
		HANDBASE_IMAGE_DIR.'/handball_white.png'   // icon_url
		// position = null
		);
	}
	
	public function create_handball_page(){
		$current_user = wp_get_current_user();
		new Player($current_user->ID);
		?>
	        <div class="wrap">
	            <h2>Handball</h2>
                    TODO: Alle Plugin-Daten löschen
	        </div>
        <?php
    }
}
?>