<?php
namespace handball\input;

use handball\Handballer;

function select_user($name, $onchange=null, $select=-1){
	$onchange = (is_null($onchange)) ? '': 'onchange="'.$onchange.'"';  
	?>
	<select name="<?php echo $name; ?>" <?php echo $onchange;?>>
		<option value="-1" style="color:silver; font-style:italic">niemand</option>
	<?php 
		require_once( ABSPATH . 'wp-includes/user.php' );
		// TODO $all_users cachen oder als Singleton
		// TODO dies �ber Handballer::get_all() machen.
		$all_users = get_users(array(
				'orderby'      => 'nicename',
				'order'        => 'ASC'
		));
		foreach ($all_users as $user){
			$selected = ( $select == $user->ID ) ? ' selected' : '';
			echo '<option value="'.$user->ID.'"'.$selected.'>'.$user->first_name.' '.$user->last_name.'</option>';	
		}
	?>
	</select>
	<?php 
}
function select_multiple_users($team){
	wp_enqueue_script('multiselect', plugins_url('/handball-basisplugin/javascript/multiselect.min.js'), array('jquery'));
	wp_enqueue_script('multiselect-team', plugins_url('/handball-basisplugin/javascript/multiselect-team.js'), array('multiselect'));
	?>
	<script type="text/javascript">
	var teamid=<?php echo $team->get_id(); ?>;
	</script>
	<div style="float:left;">
		Alle Spieler<br>
		<select name="from[]" id="multiselect" style="width:100%" size="20" multiple="multiple">
		<?php 
		$all_users = get_users(array(
				'orderby'      => 'nicename',
				'order'        => 'ASC'
		));
		$stammspieler = array();
		$zusatzspieler = array();
		foreach ($all_users as $user){
			if($team->is_stammspieler($user)){
				$stammspieler[] = $user;
			}else if($team->is_zusatzspieler($user)){
				$zusatzspieler[] = $user;
			}else{
				echo '<option value="'.$user->ID.'">'.$user->first_name.' '.$user->last_name.'</option>';
			}
		}
		?>
		</select>
	</div>
	
	<div class="col-xs-2" style="float:left; margin: 1em; text-align:center">
		<button type="button" id="multi_d_rightSelected" class="btn btn-block">Hinzufügen</button><br>
		<button type="button" id="multi_d_leftSelected" class="btn btn-block">Entfernen</button><br>
		<button type="button" id="multi_d_leftAll" class="btn btn-block">Alle entfernen</button>
		
		<hr style="margin: 40px 0 60px;" />
		
		<button type="button" id="multi_d_rightSelected_2" class="btn btn-default btn-block">Hinzufügen</button><br>
		<button type="button" id="multi_d_leftSelected_2" class="btn btn-default btn-block">Entfernen</button><br>
		<button type="button" id="multi_d_leftAll_2" class="btn btn-default btn-block">Alle entfernen</button>
	</div>
	
	<div class="col-xs-5" style="width:25%; float:left;">
		Stammspieler<br>
		<select name="to[]" id="multi_d_to" class="form-control" size="8" multiple="multiple">
		<?php
		foreach ($stammspieler as $user){
			echo '<option value="'.$user->ID.'">'.$user->first_name.' '.$user->last_name.'</option>';
		}	
		?>
		</select>
		
		<br/><hr/><br/>
				
		Zusatzspieler<br>
		<select name="to_2[]" id="multi_d_to_2" class="form-control" size="8" multiple="multiple">
		<?php
		foreach ($zusatzspieler as $user){
			echo '<option value="'.$user->ID.'">'.$user->first_name.' '.$user->last_name.'</option>';
		}	
		?>
		</select>
	</div>
	<?php 
}
?>