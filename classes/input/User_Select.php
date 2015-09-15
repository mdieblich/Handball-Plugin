<?php
namespace handball\input;

function user_select($name, $onchange=null, $select=-1){
	$onchange = (is_null($onchange)) ? '': 'onchange="'.$onchange.'"';  
	?>
	<select name="<?php echo $name; ?>" <?php echo $onchange;?>>
		<option value="-1" style="color:silver; font-style:italic">niemand</option>
	<?php 
		require_once( ABSPATH . 'wp-includes/user.php' );
		// TODO $all_users cachen oder als Singleton
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
?>