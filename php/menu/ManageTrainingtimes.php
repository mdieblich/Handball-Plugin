<?php
namespace handball\menu;

use handball\Trainingtime; // TODO kann weg?

require_once (HANDBASE_PLUGIN_DIR.'/php/classes/Location.php');
require_once (HANDBASE_PLUGIN_DIR.'/php/classes/Team.php');
require_once (HANDBASE_PLUGIN_DIR.'/php/classes/Trainingtime.php');
require_once (HANDBASE_PLUGIN_DIR.'/php/input/Team_Select.php');
require_once (HANDBASE_PLUGIN_DIR.'/php/input/Location_Select.php');

class ManageTrainingtimes{
	
	private static $MENU_SLUG = 'trainingszeiten';

	public function __construct(){
		add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
		add_action( 'wp_ajax_add_trainingtime', 'handball\menu\ManageTrainingtimes::add_trainingtime' );
		add_action( 'wp_ajax_change_start', 'handball\menu\ManageTrainingtimes::change_start' );
		add_action( 'wp_ajax_change_duration', 'handball\menu\ManageTrainingtimes::change_duration' );
	}
	
	public function add_plugin_page(){
		add_submenu_page(
				'handball', // parent_slug
			'Handball - Trainingszeiten verwalten', // page_title
			'Trainingsorte & -zeiten', // menu_title
			'manage_options', // capability
			static::$MENU_SLUG,  // menu_slug
			array( $this, 'create_manage_training_times_page' ),   // function
			HANDBASE_IMAGE_DIR.'/handball_white.png'   // icon_url
			// position = null
		);
	}
	
	public function create_manage_training_times_page(){
		if (isset ( $_POST ['create_location'] )) {
			new \handball\Location( 
					$_POST ['location_name'], 
					$_POST ['location_abbreviation'], 
					$_POST ['location_address'],
					$_POST ['Farbe']);
		}
		if (isset ( $_GET ['delete_location_id'] )) {
			$delete_id = intval ( $_GET ['delete_location_id'] );
			\handball\Location::delete ( $delete_id );
		}
		if (isset ( $_POST ['delete_id'] )) {
			$delete_id = intval ( $_POST ['delete_id'] );
			\handball\Trainingtime::delete ( $delete_id );
		}
		if( isset ( $_POST['edit_id'] ) ){
			$edit_id = intval($_POST['edit_id']);
			$team_id = null;
			if($_POST['team_id'] != ''){
				$team_id = intval($_POST['team_id']);
			}
			$location = null;
			if($_POST['location_id'] != ''){
				$location = intval($_POST['location_id']);
			}
			$note = $_POST['note'];
			$trainigtime = \handball\Trainingtime::get_by_id($edit_id);
			$trainigtime->set_team($team_id);
			$trainigtime->set_loca0ion($location);
			$tra0nigszeit->set_note($note);
			$trainigtime->save();
		}
		
		echo "<h3>Trainingsorte</h3>";
		echo "<strong>Hinweis:</strong> Trainingsorte können (noch) nicht geändert, nur gelöscht werden.<br><br>";
		$all_locations = \handball\Location::get_all ();
		$all_teams = \handball\Team::get_all ();
		?>
		<script type="text/javascript">
        // siehe http://fullcalendar.io/docs/
        
        	var unassignedColor = 'red';
        	var selectedEvent = null;
        	var originalColorOfSelectedEvent = '';
        	var originalTextColorOfSelectedEvent = '';

            var trainingTimesWithoutLocation = {
				color: unassignedColor,
				events:
					[
					<?php 
					$unassignedTrainingtimes = Trainingtime::get('location is null');
					$fullcalender_events = Trainingtime::get_fullcalender_io_events($unassignedTrainingtimes);
					echo implode(", \n", $fullcalender_events);
					?>
					]
            };
            <?php 
            // erstellen der Event-Sources für alle zugewiesenen Triingszeiten
            foreach($all_locations as $location){
            	echo 'var '
					.$location->get_fullcalendar_io_event_source_name()
	            	.' = '
					.$location->get_trainingtimes_as_fullcalendar_io_event_source()
					.";\n";
            }
            ?>

            var allEventSources = [ 
            	<?php 
                $trainingTimesVariableNames = array('trainingTimesWithoutLocation');
                foreach($all_locations as $location){
                	$trainingTimesVariableNames[] = $location->get_fullcalendar_io_event_source_name();	
                }
                echo implode(',', $trainingTimesVariableNames)
                ?>
			];
            var teamVisibility = new Array();
            teamVisibility["Kein Team"] = true;
<?php 
            foreach($all_teams as $team){
            	echo "            teamVisibility[\"".$team->get_name()."\"] = true;\n";
			}
?>
            function toggleTeam(teamName, visible){
            	teamVisibility[teamName] = visible;
                jQuery('#calendar').fullCalendar('rerenderEvents');
            }
        
            jQuery(document).ready(function($) {
                
                $('#calendar').fullCalendar({
                    header: {
                        left: false,
                        center: false,
                        right: false
                    },
                    height: 500,
                    lang: 'de',
                    editable: true,
                    timeFormat: 'H:mm',
                    defaultView: 'agendaWeek',
                    columnFormat: 'ddd',
                    scrollTime: '16:00',
                    snapDuration: '00:15',
                    allDaySlot: false,
                    eventSources: allEventSources,
                    eventDrop: function(event, delta, revertFunc) {
                    	callBackFunctionOnSuccess = null;
                    	callBackFunctionOnFailure = function(response){
							alert(response);
							revertFunc();
                    	}
                    	changeStart(event, callBackFunctionOnSuccess, callBackFunctionOnFailure);
                    },
                    eventResize: function(event, delta, revertFunc) {
                    	callBackFunctionOnSuccess = null;
                    	callBackFunctionOnFailure = function(response){
							alert(response);
							revertFunc();
                    	}
                    	changeDuration(event, callBackFunctionOnSuccess, callBackFunctionOnFailure);

                    },
                    eventClick: function(event, jsEvent, view) {

                    	selectEvent(event);

                    },
                    dayClick: function(date, jsEvent, view) {

                        var newTrainingtime = {
                                title: 'kein Team',
                                start: date.format(),
                                end: date.add(90, 'minutes').format()
                        }; 
                        createTraningszeitOnServer(newTrainingtime, function(createdId){
                            $('#calendar').fullCalendar('removeEventSource', trainingTimesWithoutLocation);
                            newTrainingtime.id = createdId;
                            trainingTimesWithoutLocation.events.push(newTrainingtime);
                            $('#calendar').fullCalendar('addEventSource', trainingTimesWithoutLocation);

                        });

                    },
                    eventRender: function(event, element){
						return teamVisibility[event.title];
                    }
                 });


                function selectEvent(event){
                	if(selectedEvent){
                		selectedEvent.color = originalColorOfSelectedEvent;
                		selectedEvent.textColor = originalTextColorOfSelectedEvent;
                        $('#calendar').fullCalendar('updateEvent', selectedEvent);
                	}
                	originalColorOfSelectedEvent = event.color;
                	originalTextColorOfSelectedEvent = event.textColor;
                    event.color = 'white';
                    event.textColor = 	'black';
                	selectedEvent = event;

                    $('#calendar').fullCalendar('updateEvent', event);
                    $('#edit_id').val(event.id);
                    $('#delete_id').val(event.id);
                    $('#edit_team_id').val(event.team_id);
                    $('#edit_location_id').val(event.location_id);
                    $('#edit_note').val(event.note);
                }
                
            });

            function createTraningszeitOnServer(trainingtime, trainingtimeWasCreated){
                var data = eventToAJAXData(trainingtime);
                data['action'] = 'add_trainingtime';
                
                // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
                jQuery.post(ajaxurl, data, function(response) {
                    trainingtimeCreated = JSON.parse(response);
                    if(trainingtimeCreated != 'undefined' && trainingtimeWasCreated){
                    	trainingtimeWasCreated(trainingtimeCreated.id);
                    }else{
						alert("Die Trainingszeit konnte nicht angelegt werden:\n"+response);
                    }
                });
            }

			function eventToAJAXData(event){
                start = moment(event.start);
                end = moment(event.end);
                var data = {
                    'weekday': start.locale('en').format('dddd'),
                    'time': start.format('H:mm'),
                    'duration': end.diff(start, 'minutes')
                };
                return data;
			}
            

            function changeStart(trainingtime, callBackFunctionOnSuccess, callBackFunctionOnFailure){
                start = moment(trainingtime.start);
                var data = {
                	'action': 'change_start',
					'id': trainingtime.id,
					'time': start.format('H:mm'),
                    'weekday': start.locale('en').format('dddd')
                }
                jQuery.post(ajaxurl, data, function(response) {
                    try{
	                    trainingtimeCreated = JSON.parse(response);
	                    if(trainingtimeCreated != 'undefined'){
	                        if(callBackFunctionOnSuccess){
	                        	callBackFunctionOnSuccess(trainingtimeCreated);
	                        }
	                    }else{
	                        if(callBackFunctionOnFailure){
	                        	callBackFunctionOnFailure(response);
	                        }
	                    }
                    }catch(e){
                        console.log(e);
						console.log(response);
                        if(callBackFunctionOnFailure){
                        	callBackFunctionOnFailure(response);
                        }
                    }
                });
            }
            function changeDuration(trainingtime, callBackFunctionOnSuccess, callBackFunctionOnFailure){
                start = moment(trainingtime.start);
                end = moment(trainingtime.end);
                var data = {
                    'action': 'change_duration',
    				'id': trainingtime.id,
                    'duration': end.diff(start, 'minutes')
                };
                jQuery.post(ajaxurl, data, function(response) {
                    try{
	                    trainingtimeCreated = JSON.parse(response);
	                    if(trainingtimeCreated != 'undefined'){
	                        if(callBackFunctionOnSuccess){
	                        	callBackFunctionOnSuccess(trainingtimeCreated);
	                        }
	                    }else{
	                        if(callBackFunctionOnFailure){
	                        	callBackFunctionOnFailure(response);
	                        }
	                    }
                    }catch(e){
                        console.log(e);
						console.log(response);
                        if(callBackFunctionOnFailure){
                        	callBackFunctionOnFailure(response);
                        }
                    }
                });
            }

            function toggleLocation(eventSource, visible){
	            fullcalendarAction = visible ? 'addEventSource': 'removeEventSource';
                jQuery('#calendar').fullCalendar(fullcalendarAction, eventSource);
            }

        </script>
        
        <form method="post">
        <table>
            <tr>
                <th>Trainingsort</th>
                <th>Abkürzung</th>
                <th>Adresse</th>
                <th>Farbe</th>
                <td></td>
            </tr>
            <?php foreach ( $all_locations as $location ) { ?>
            <tr>
	            <td><?php echo $location->get_name(); ?></td>
	            <td><?php echo $location->get_abbreviation(); ?></td>
	            <td><?php echo $location->get_address(); ?></td>
	            <td><?php echo $location->get_color(); ?></td>
	            <td><a
	                href="admin.php?page=<?php echo static::$MENU_SLUG;?>&delete_location_id=<?php echo $location->get_id(); ?>">Löschen</a></td>
            </tr>
            <?php } ?>
	        <tr>
	            <td><input type="text" name="location_name" placeholder="Name"></td>
	            <td><input type="text" name="location_abbreviation" placeholder="Abkürzung"></td>
	            <td><input type="text" name="location_address" placeholder="Adresse"></td>
	            <td><input type="text" name="Farbe" placeholder="Farbe"></td>
	            <td><input type="hidden" name="create_location" value="true">
	                <?php submit_button('Anlegen', 'primary','Anlegen', false); ?></td>
	        </tr>
        </table>
        </form>

		<br clear="all" />
		<h3>Trainingszeiten</h3>
        <div id="location" style="max-width:900px; margin: 0.8em 2em;">
        <?php foreach($all_locations as $location){
            $checkbox_id = 'checkbox_location_'.$location->get_id();
            echo '<span style="background-color: '.$location->get_color().'; color: white; padding: 3px; margin: 5px;"><label for="'.$checkbox_id.'">'.$location->get_abbreviation().'&nbsp;</label>';
            echo '<input type="checkbox" id="'.$checkbox_id.'" value="'.$checkbox_id.'" onchange="toggleLocation('.$location->get_fullcalendar_io_event_source_name().', this.checked)" checked></span>';
        } ?>
        	<span style="background-color: red; color:white; padding: 3px; margin: 5px;">
        		<label for="checkbox_location_unassigned"><i>(ohne Trainigsort)</i>&nbsp;</label>
        		<input type="checkbox" id="checkbox_location_unassigned" value="(ohne Trainingsort)" onchange="toggleLocation(trainingTimesWithoutLocation, this.checked)" checked>
        	</span>
        </div>
        <div id="teams" style="max-width:900px; margin: 0.8em 2em;">
	        <?php foreach($all_teams as $team){
	            $checkbox_id = 'checkbox_team_'.$team->get_id();
	            echo '<span style="padding: 3px; margin: 5px;"><label for="'.$checkbox_id.'">'.$team->get_name().'&nbsp;</label>';
	            echo '<input type="checkbox" id="'.$checkbox_id.'" value="'.$checkbox_id.'" onchange="toggleTeam(\''.$team->get_name().'\', this.checked);" checked></span>';
	        } ?>
	        <span style="padding: 3px; margin: 5px;">
        		<label for="checkboxNoTeam"><i>(Kein Team)</i>&nbsp;</label>
        		<input type="checkbox" id="checkboxNoTeam" value="(ohne Trainingsort)" onchange="toggleTeam('Kein Team', this.checked)" checked>
        	</span>
        </div>
        <div id="currenttermin" style="margin: 1em 2em 0em; padding: 0.5em; background: white;  display:inline-block; ">
            <b>Aktuell ausgewählt:</b><br>
            <form method="post">
            <?php
            	echo \handball\input\team_select('team_id', 'edit_team_id'); 
            	echo \handball\input\location_Select('location_id', 'edit_location_id');
            ?>
            <br>
            <b>Trainingshinweis:</b><br>
            <textarea name="note" id="edit_note"></textarea><br>
            <input type="hidden" name="edit_id" id="edit_id" value="-1" size="3">
             <?php submit_button('Speichern', 'primary','Speichern', false); ?>
            </form>
            <form method="post">
                <input type="hidden" name="delete_id" id="delete_id" value="-1">
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
		                        
        <div id="calendar" style="max-width:900px; float:left"></div>
        <?php 
        
    }
    public static function add_trainingtime() {
       	$weekDay = $_POST ['weekday'];
       	$time = $_POST['time'];
       	$duration =  intval ( $_POST ['duration'] );
       	$trainigtime = new Trainingtime($weekDay, $time, $duration);
       	echo $trainigtime->toJSON();
       	wp_die ();
    }
    public static function change_start() {
       	$id = intval($_POST ['id']);
       	$time = $_POST['time'];
       	$weekDay = $_POST ['weekday'];
       	
       	$trainingtime = Trainingtime::get_by_id($id);
       	if(is_null($trainingtime)){
       		echo "Fehler: Die Trainingszeit mit der ID $id konnte nicht gefunden werden.";
       		wp_die();
       	}
       	$trainingtime->set_time($time);
       	$trainingtime->set_weekday($weekDay);
       	if($trainingtime->save()){
       		echo $trainingtime->toJSON();
       	}else{
       		global $wpdb;
       		echo "Fehler beim Speichern der neuen Startzeit:\n";
       		$wpdb->print_error();
       	}
       	wp_die ();
    }
    public static function change_duration() {
       	$id = intval($_POST ['id']);
       	$duration = intval($_POST['duration']);
       	
       	$trainingtime = Trainingtime::get_by_id($id);
       	if(is_null($trainingtime)){
       		echo "Fehler: Die Trainingszeit mit der ID $id konnte nicht gefunden werden.";
       		wp_die();
       	}
       	$trainingtime->set_duration_minutes($duration);
       	if($trainingtime->save()){
       		echo $trainingtime->toJSON();
       	}else{
       		global $wpdb;
       		echo "Fehler beim Speichern der neuen Trainingsdauer:\n";
       		$wpdb->print_error();
       	}
       	wp_die ();
    }
}
?>