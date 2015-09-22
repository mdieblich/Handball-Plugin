<?php
namespace handball\menu;

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
		?>
	        <div class="wrap">
	            <h2>Handball-Plugin - Einstellungen</h2>
	            <p>
	            	Hier gibt es momentan nix zu sehen.
	            </p>
	        </div>
        <?php
    }
}
?>