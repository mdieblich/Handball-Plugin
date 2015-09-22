<?php

namespace handball\menu;
use handball\Mannschaft;
require_once (ABSPATH . 'wp-content/plugins/handball-basisplugin/classes/Mannschaft.php');

class ManageTeamPage {
	private static $MENU_SLUG = 'handball_mannschaft_manage';
	public function __construct() {
		add_action ( 'admin_menu', array ($this, 'add_plugin_page') );
		add_action ( 'wp_ajax_add_stammspieler', 'handball\menu\ManageTeamPage::add_stammspieler' );
		add_action ( 'wp_ajax_remove_stammspieler', 'handball\menu\ManageTeamPage::remove_stammspieler' );
		add_action ( 'wp_ajax_add_zusatzspieler', 'handball\menu\ManageTeamPage::add_zusatzspieler' );
		add_action ( 'wp_ajax_remove_zusatzspieler', 'handball\menu\ManageTeamPage::remove_zusatzspieler' );
	}
	public function add_plugin_page() {
		add_submenu_page ( 'handball', // parent_slug
		'Mannschaft verwalten', // page_title
		'Mannschaft verwalten', // menu_title
		'manage_options', // capability
		static::$MENU_SLUG, // menu_slug
		array (
				$this,
				'manage_team_page' 
		) ); // function

	}
	public function manage_team_page() {
		require_once (HANDBASE_PLUGIN_DIR . '/classes/input/Team_Select.php');
		require_once (HANDBASE_PLUGIN_DIR . '/classes/input/User_Select.php');
		require_once (HANDBASE_PLUGIN_DIR . '/classes/Mannschaft.php');
		echo \handball\input\team_select('mannschaft', "window.location.href='admin.php?page=".static::$MENU_SLUG."&mannschaft='+this.value");
		if(isset($_GET['mannschaft'])){
			$team = \handball\Mannschaft::get(intval($_GET['mannschaft']));
			echo "<p>Hallo, Welt!!</p>";
			echo '<script type="text/javascript">';
			require_once (HANDBASE_JAVASCRIPT_DIR . '/multiselect.min.js');
			?>
			jQuery(document).ready(function($) {
				$('#multiselect').multiselect({
						right: '#multi_d_to, #multi_d_to_2',
						rightSelected: '#multi_d_rightSelected, #multi_d_rightSelected_2',
						leftSelected: '#multi_d_leftSelected, #multi_d_leftSelected_2',
						rightAll: '#multi_d_rightAll, #multi_d_rightAll_2',
						leftAll: '#multi_d_leftAll, #multi_d_leftAll_2',
				        
						moveToRight: function(Multiselect, options, event, silent, skipStack) {
							var button = $(event.currentTarget).attr('id');
				            
							if (button == 'multi_d_rightSelected') {
								var left_options = Multiselect.left.find('option:selected');
								Multiselect.right.eq(0).append(left_options);
								
				                for(i=0; i < options.length; i++){
									addStammspieler(options[i].value);
								}
								
								if ( typeof Multiselect.callbacks.sort == 'function' && !silent ) {
									Multiselect.right.eq(0).find('option').sort(Multiselect.callbacks.sort).appendTo(Multiselect.right.eq(0));
								}
							} else if (button == 'multi_d_rightSelected_2') {
								var left_options = Multiselect.left.find('option:selected');
								Multiselect.right.eq(1).append(left_options);
								
				                for(i=0; i < options.length; i++){
									addZusatzspieler(options[i].value);
								}
								
								if ( typeof Multiselect.callbacks.sort == 'function' && !silent ) {
									Multiselect.right.eq(1).find('option').sort(Multiselect.callbacks.sort).appendTo(Multiselect.right.eq(1));
								}
							}
						},
				        
						moveToLeft: function(Multiselect, options, event, silent, skipStack) {
							var button = $(event.currentTarget).attr('id');
				            
							if (button == 'multi_d_leftSelected') {
								var right_options = Multiselect.right.eq(0).find('option:selected');
								Multiselect.left.append(right_options);
								
				                for(i=0; i < options.length; i++){
									removeStammspieler(options[i].value);
								}
				                
								if ( typeof Multiselect.callbacks.sort == 'function' && !silent ) {
									Multiselect.left.find('option').sort(Multiselect.callbacks.sort).appendTo(Multiselect.left);
								}
							} else if (button == 'multi_d_leftAll') {
								var right_options = Multiselect.right.eq(0).find('option');
								Multiselect.left.append(right_options);
				                
				                for(i=0; i < right_options.length; i++){
									removeStammspieler(right_options[i].value);
								}
				                
								if ( typeof Multiselect.callbacks.sort == 'function' && !silent ) {
									Multiselect.left.find('option').sort(Multiselect.callbacks.sort).appendTo(Multiselect.left);
								}
							} else if (button == 'multi_d_leftSelected_2') {
								var right_options = Multiselect.right.eq(1).find('option:selected');
								Multiselect.left.append(right_options);
				                
				                for(i=0; i < options.length; i++){
									removeZusatzspieler(options[i].value);
								}
				                
								if ( typeof Multiselect.callbacks.sort == 'function' && !silent ) {
									Multiselect.left.find('option').sort(Multiselect.callbacks.sort).appendTo(Multiselect.left);
								}
							} else if (button == 'multi_d_leftAll_2') {
								var right_options = Multiselect.right.eq(1).find('option');
								Multiselect.left.append(right_options);
				                
				                for(i=0; i < right_options.length; i++){
									removeZusatzspieler(right_options[i].value);
								}
				                
								if ( typeof Multiselect.callbacks.sort == 'function' && !silent ) {
									Multiselect.left.find('option').sort(Multiselect.callbacks.sort).appendTo(Multiselect.left);
								}
							}
						}
<!-- 					moveToRight: function(Multiselect, options, event, silent, skipStack) { -->
<!-- 						for(i=0; i < options.length; i++){ -->
<!-- 							addStammspieler(options[i].value); -->
<!-- 						} -->
<!-- 					} -->
				});
			});
			function addStammspieler(nutzerID){
				var data = {
					'action': 'add_stammspieler',
					'team': <?php echo $team->get_id(); ?>,
					'user': nutzerID
				};
				
				// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
				jQuery.post(ajaxurl, data, function(response) {
					if(response != nutzerID){
						alert("Der Stammspieler konnte nicht hinzugefügt werden: " + response + " statt " + nutzerID);
					}
				});
			}
			function removeStammspieler(nutzerID){
				var data = {
					'action': 'remove_stammspieler',
					'team': <?php echo $team->get_id(); ?>,
					'user': nutzerID
				};
				
				// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
				jQuery.post(ajaxurl, data, function(response) {
					if(response != nutzerID){
						alert("Der Stammspieler konnte nicht entfernt werden: " + response + " statt " + nutzerID);
					}
				});
			}
			function addZusatzspieler(nutzerID){
				var data = {
					'action': 'add_zusatzspieler',
					'team': <?php echo $team->get_id(); ?>,
					'user': nutzerID
				};
				
				// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
				jQuery.post(ajaxurl, data, function(response) {
					if(response != nutzerID){
						alert("Der Stammspieler konnte nicht hinzugefügt werden: " + response + " statt " + nutzerID);
					}
				});
			}
			function removeZusatzspieler(nutzerID){
				var data = {
					'action': 'remove_zusatzspieler',
					'team': <?php echo $team->get_id(); ?>,
					'user': nutzerID
				};
				
				// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
				jQuery.post(ajaxurl, data, function(response) {
					if(response != nutzerID){
						alert("Der Stammspieler konnte nicht entfernt werden: " + response + " statt " + nutzerID);
					}
				});
			}
			<?php 
			echo '</script>';
			\handball\input\select_multiple_users($team);
// 			// TODO Nutzer hinzufügen
		}
	}
	public static function add_stammspieler() {
		$mannschaft_id = intval ( $_POST ['team'] );
		$user_id = intval ( $_POST ['user'] );
		echo \handball\Mannschaft::add_stammspieler_to_team( $mannschaft_id, $user_id );
		wp_die ();
	}
	public static function remove_stammspieler() {
		$mannschaft_id = intval ( $_POST ['team'] );
		$user_id = intval ( $_POST ['user'] );
		echo \handball\Mannschaft::remove_stammspieler_from_team( $mannschaft_id, $user_id );
		wp_die ();
	}
	public static function add_zusatzspieler() {
		$mannschaft_id = intval ( $_POST ['team'] );
		$user_id = intval ( $_POST ['user'] );
		echo \handball\Mannschaft::add_zusatzspieler_to_team( $mannschaft_id, $user_id );
		wp_die ();
	}
	public static function remove_zusatzspieler() {
		$mannschaft_id = intval ( $_POST ['team'] );
		$user_id = intval ( $_POST ['user'] );
		echo \handball\Mannschaft::remove_zusatzspieler_from_team( $mannschaft_id, $user_id );
		wp_die ();
	}
}
?>