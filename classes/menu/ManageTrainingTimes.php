<?php
namespace handball\menu;

use handball\handball;
class ManageTrainingTimes{
	
	private static $MENU_SLUG = 'trainingszeiten';

	public function __construct(){
		add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
	}
	
	public function add_plugin_page(){
		add_submenu_page(
				'handball', // parent_slug
			'Handball - Trainingszeiten verwalten', // page_title
			'Hallen & Trainingszeiten', // menu_title
			'manage_options', // capability
			static::$MENU_SLUG,  // menu_slug
			array( $this, 'create_manage_training_times_page' ),   // function
			HANDBASE_IMAGE_DIR.'/handball_white.png'   // icon_url
			// position = null
		);
	}
	
	public function create_manage_training_times_page(){
		require_once (HANDBASE_PLUGIN_DIR . '/classes/Halle.php');
		require_once (HANDBASE_PLUGIN_DIR . '/classes/Mannschaft.php');
		require_once (HANDBASE_PLUGIN_DIR . '/classes/input/Team_Select.php');
		require_once (HANDBASE_PLUGIN_DIR . '/classes/input/Hall_Select.php');
		require_once (HANDBASE_PLUGIN_DIR . '/classes/input/Weekday_Select.php');
		if (isset ( $_POST ['createHall'] )) {
			$neue_halle = new \handball\Halle( $_POST ['Hallenname'], $_POST ['Hallenabkuerzung'], $_POST ['Adresse'] );
		}
		if (isset ( $_GET ['delete'] )) {
			$delete_id = intval ( $_GET ['delete'] );
			\handball\Halle::delete ( $delete_id );
		}
		echo "<h3>Hallen</h3>";
		echo "<strong>Hinweis:</strong> Hallen können (noch) nicht geändert, nur gelöscht werden.<br><br>";
		$alle_hallen = \handball\Halle::get_all ();
		$alle_mannschaften = \handball\Mannschaft::get_all ();
		?>
        <form method="post">
        <table>
            <tr>
                <th>Halle</th>
                <th>Abkürzung</th>
                <th>Adresse</th>
                <td></td>
            </tr>
            <?php foreach ( $alle_hallen as $hall ) { ?>
            <tr>
	            <td><?php echo $hall->get_name(); ?></td>
	            <td><?php echo $hall->get_abkuerzung(); ?></td>
	            <td><?php echo $hall->get_adresse(); ?></td>
	            <td><a
	                href="admin.php?page=<?php echo static::$MENU_SLUG;?>&delete=<?php echo $hall->get_id(); ?>">Löschen</a></td>
            </tr>
            <?php } ?>
	        <tr>
	            <td><input type="text" name="Hallenname" placeholder="Name"></td>
	            <td><input type="text" name="Hallenabkuerzung" placeholder="Abkürzung"></td>
	            <td><input type="text" name="Adresse" placeholder="Adresse"></td>
	            <td><input type="hidden" name="createHall" value="true">
	                <?php submit_button('Anlegen', 'primary','Anlegen', false); ?></td>
	        </tr>
        </table>
        </form>

		<br clear="all" />
		<h3>Trainingszeiten</h3>
        <div id="hallen" style="max-width:900px; margin: 0.8em 2em;">
            Folgende <b>Hallen</b> anzeigen:<br>
        <?php foreach($alle_hallen as $halle){
            $id = 'halle_'.$halle->get_id();
            echo '<span><label for="'.$id.'">'.$halle->get_abkuerzung().'</label>';
            echo '<input type="checkbox" id="'.$id.'" value="'.$id.'" checked></span>';
        } ?>
        </div>
        <div id="mannschaften" style="max-width:900px; margin: 0.8em 2em;">
            Folgende <b>Mannschaften</b> anzeigen:<br>
	        <?php foreach($alle_mannschaften as $mannschaft){
	            $id = 'mannschaft_'.$mannschaft->get_id();
	            echo '<span><label for="'.$id.'">'.$mannschaft->get_name().'</label>';
	            echo '<input type="checkbox" id="'.$id.'" value="'.$id.'" checked></span>';
	        } ?>
        </div>
        <div id="currenttermin" style="margin: 1em 2em 0em; padding: 0.5em; background: white;  display:inline-block; ">
            <b>Aktuell ausgewählt:</b><br>
            <form method="post">
            <input type="hidden" name="edit_id" value="-1">
            <?php
            	echo \handball\input\team_select('Mannschaft'); 
            	echo \handball\input\hall_select('Halle');
            ?>
            <br>
            <b>Trainingshinweis:</b><br>
            <textarea name="trainingshinweis"></textarea><br>
             <?php submit_button('Speichern', 'primary','Speichern', false); ?>
            </form>
            <form method="post">
                <input type="hidden" name="delete_id" value="-1">
             <?php submit_button('Löschen', 'primary','Löschen', false); ?>
            </form>
        </div>
        
        <?php 
		
		wp_enqueue_style('fullcalendar', plugins_url('/handball-basisplugin/css/fullcalendar.css'));
		//          wp_enqueue_style('fullcalendar-print', plugins_url('/handball-basisplugin/css/fullcalendar.print.css'), array('fullcalendar'));
		
		wp_enqueue_script('moment', plugins_url('/handball-basisplugin/javascript/moment.min.js'));
		wp_enqueue_script('fullcalendar', plugins_url('/handball-basisplugin/javascript/fullcalendar.min.js'), array('jquery'));
		wp_enqueue_script('fullcalendar-de', plugins_url('/handball-basisplugin/javascript/fullcalendar-de.js'), array('fullcalendar'));
		
		?>
		                        
        <script type="text/javascript">
        // siehe http://fullcalendar.io/docs/
            var clickOnHallenLabelCount = 0;
            jQuery(document).ready(function($) {
                $('#calendar').fullCalendar({

                    customButtons: {
                        hallenLabel: {
                            text: 'Verfügbare Hallen:',
                            click: function() {
                                clickOnHallenLabelCount++;
                                if(clickOnHallenLabelCount > 20){
                                    message = "";
                                    for(i=0; i<clickOnHallenLabelCount; i++){
                                        for(j=0; j<35; j++){
                                            message += "A";
                                        }
                                        message += "\n";
                                    }
                                    alert(message);
                                }else if(clickOnHallenLabelCount == 10){
                                    alert('Aufhören!!');
                                }else if(clickOnHallenLabelCount == 5){
                                    alert('Dieser Knopf hat keine Funktion.\nHör auf ihn zu drücken!');
                                }
                            }
                        }
                    },
                    header: {
                        left: false,
                        center: false,
                        right: false
                    },
                    lang: 'de',
                    editable: true,
                    timeFormat: 'H:mm',
                    defaultView: 'agendaWeek',
                    columnFormat: 'ddd',
                    eventSources:[
						{
                            events: [{
						             title: 'Training',
						             start: '<?php echo date('Y-m-d');?>T17:00:00',
						             end: '<?php echo date('Y-m-d');?>T18:30:00'
						    }],
                            color: 'red',
                            halle: 'NPT'
                        },
						{
                            events: [{
						             title: 'Training',
						             start: '<?php echo date('Y-m-d');?>T19:00:00',
						             end: '<?php echo date('Y-m-d');?>T20:30:00'
						    }],
                            color: 'black',
                            halle: 'JDS'
                        }
                    ],
                    eventDrop: function(event, delta, revertFunc) {

                        alert(event.title + " ("+event.source.halle+") was dropped on " + event.start.format());

                        if (!confirm("Are you sure about this change?")) {
                            revertFunc();
                        }
                    },
                    eventResize: function(event, delta, revertFunc) {

                        alert(event.title + " ("+event.source.halle+") end is now " + event.end.format());

                        if (!confirm("is this okay?")) {
                            revertFunc();
                        }

                    },
                    eventClick: function(event, jsEvent, view) {

                        alert(event.title + " ("+event.source.halle+") löschen?");


                    },
                    dayClick: function(date, jsEvent, view) {

                        alert('Clicked on: ' + date.format());

                    }
                 });
            });
        </script>
        <div id="calendar" style="max-width:900px; float:left"></div>
        <?php 
    }
}
?>