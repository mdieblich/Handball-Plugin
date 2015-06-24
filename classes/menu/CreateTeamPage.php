<?php
namespace handball\menu;

class CreateTeamPage{

	public function __construct(){
		add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
	}
	

	public function add_plugin_page(){
    	add_submenu_page( 
    			'handball', // parent_slug
    			'Mannschaften',  // page_title
    			'Mannschaften',  // menu_title
    			'manage_options',  // capability
    			'handball_mannschaft',   // menu_slug
    			array( $this, 'create_team_page' )   // function
    	);
	}

	public function create_team_page(){
		echo getcwd();
		?>
	        <div class="wrap">
	            <h2>Mannschaften verwalten</h2>
	            <table>
	            	<tr>
	            		<th> Mannschaft </th>
	            		<th> Trainer </th>
	            		<th> Cotrainer </th>
	            		<th> <!-- Anlegen/Löschen --> </th>
	            	</tr>
	            	
            		<form method="post">
	            	<tr>
	            		<td>
            				<input type="hidden" name="createTeam" value="true">
            				<input type="text" name="Teamname" placeholder="Name">
            			</td>
	            		<td>
	            			<input type="text" name="Trainer" placeholder="Trainer">
	            		</td>
	            		<td>
	            			<input type="text" name="Cotrainer" placeholder="Cotrainer">
	            			</td>
	            		<td>
	            			<?php submit_button("Anlegen"); ?>
            			</td>
	            	</tr>
	            	</form>
	            </table>
	        </div>
        <?php
        if(isset($_POST['createTeam'])){
        	require_once "../wp-content/plugins/handball-basisplugin/classes/Mannschaft.php";
        	$mannschaft = new \handball\Mannschaft($_POST['Teamname']);
        }
	}
}
?>