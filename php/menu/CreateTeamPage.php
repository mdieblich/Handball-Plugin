<?php

namespace handball\menu;

require_once(HANDBASE_PLUGIN_DIR.'/php/classes/Team.php');
require_once(HANDBASE_PLUGIN_DIR.'/php/input/User_Select.php');

class CreateTeamPage {
	private static $MENU_SLUG = 'handball_create_team';
	public function __construct() {
		add_action ( 'admin_menu', array (
				$this,
				'add_plugin_page' 
		) );
		add_action ( 'wp_ajax_set_trainer', 'handball\menu\CreateTeamPage::set_trainer' );
		add_action ( 'wp_ajax_set_cotrainer', 'handball\menu\CreateTeamPage::set_cotrainer' );
	}
	public function add_plugin_page() {
		add_submenu_page ( 'handball', // parent_slug
		'Mannschaften', // page_title
		'Mannschaften', // menu_title
		'manage_options', // capability
		static::$MENU_SLUG, // menu_slug
		array (
				$this,
				'create_team_page' 
		) ); // function

	}
	public function create_team_page() {
		
		if (isset ( $_POST ['createTeam'] )) {
			new \handball\Team ( $_POST ['Teamname'], intval($_POST ['Trainer']), intval($_POST ['Cotrainer']) );
		}
		
		if (isset ( $_GET ['delete_team_id'] )) {
			$delete_id = intval ( $_GET ['delete_team_id'] );
			\handball\Team::delete ( $delete_id );
		}
		
		?>
<script type="text/javascript">
function setAsTrainer(teamID, nutzerID){
	var data = {
		'action': 'set_trainer',
		'team_id': teamID,
		'user_id': nutzerID
	};
	
	// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
	jQuery.post(ajaxurl, data, function(response) {
		if(response != nutzerID){
			alert("Der Trainer konnte nicht richtig gesetzt werden: " + response + " statt " + nutzerID);
		}
	});
}
function setAsCotrainer(teamID, nutzerID){
	var data = {
		'action': 'set_cotrainer',
		'team_id': teamID,
		'user_id': nutzerID
	};
	
	// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
	jQuery.post(ajaxurl, data, function(response) {
		if(response != nutzerID){
			alert("Der Co-Trainer konnte nicht richtig gesetzt werden: " + response + " statt " + nutzerID);
		}
	});
}
			</script>
<div class="wrap">
    <h2>Mannschaften verwalten</h2>
    <table>
        <tr>
            <th>Mannschaft</th>
            <th>Trainer</th>
            <th>Cotrainer</th>
            <th>
                <!-- Anlegen/Löschen -->
            </th>
        </tr>
	            	<?php
		$all_teams = \handball\Team::get_all ();
		foreach ( $all_teams as $team ) {
			?>
			        	<tr>
            <td><?php echo $team->get_name(); ?></td>
            <td><?php echo \handball\input\select_user('Trainer', 'setAsTrainer('.$team->get_id().',this.value)', $team->get_trainer()->get_id()); ?></td>
            <td><?php echo \handball\input\select_user('Cotrainer','setAsCotrainer('.$team->get_id().',this.value)', $team->get_cotrainer()->get_id()); ?></td>
            <td><a
                href="admin.php?page=<?php echo static::$MENU_SLUG;?>&delete_team_id=<?php echo $team->get_id(); ?>">Löschen</a></td>
        </tr>
			        	<?php
		}
		?>
            		<form method="post">
            <tr>
                <td><input type="hidden" name="createTeam" value="true">
                    <input type="text" name="Teamname"
                    placeholder="Name"></td>
                <td>
	            			<?php echo \handball\input\select_user('Trainer'); ?>
	            		</td>
                <td>
	            			<?php echo \handball\input\select_user('Cotrainer'); ?>
	            		</td>
                <td>
	            			<?php submit_button('Anlegen', 'primary','Anlegen', false); ?>
            			</td>
            </tr>
        </form>
    </table>
</div>
<?php
	}
	public static function my_action_callback() {
		global $wpdb; // this is how you get access to the database
		
		$whatever = intval ( $_POST ['whatever'] );
		
		$whatever += 10;
		
		echo $whatever;
		
		wp_die (); // this is required to terminate immediately and return a proper response
	}
	public static function set_trainer() {
		$team_id = intval ( $_POST ['team_id'] );
		$user_id = intval ( $_POST ['user_id'] );
		echo \handball\Team::set_trainer ( $team_id, $user_id );
		wp_die ();
	}
	public static function set_cotrainer() {
		$team_id = intval ( $_POST ['team_id'] );
		$user_id = intval ( $_POST ['user_id'] );
		echo \handball\Team::set_cotrainer ( $team_id, $user_id );
		wp_die ();
	}
}
?>