<?php

namespace handball;
require_once 'Handballer.php';

class Mannschaft{
	
	private $meta_info;
	
	private $trainer;
	private $cotrainer;
	/** Kern der Mannschaft */
	private $stammspieler; // 1xN Relation (ein Spieler kann höchstens in einer Mannschaft sein)
	
	/** Anfragen einzelner Spieler, teil der Mannschaft zu werden */
	private $spieleranfragen; // 1xN Relation (ein Spieler kann nur einer neuen Mannschaft beitreten)
	
	/** Hier kann sich jeder selbst eintragen */
	private $zusatzspieler; // NxM Relation (ein Spieler kann beliebig viele Mannschaften "abonnieren")
	
	public static function install(){

		global $wpdb;
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		
		$charset_collate = $wpdb->get_charset_collate();
		
		$sql =
		"CREATE TABLE ".static::table_name()." (
			  id mediumint(9) NOT NULL AUTO_INCREMENT,
			  meta_info mediumint(9) NOT NULL,
			  trainer mediumint(9) NOT NULL,
			  cotrainer mediumint(9) NOT NULL,
			  UNIQUE KEY id (id)
			) ".$charset_collate.";";
		
		dbDelta( $sql );

		// TODO Zusatzspieler
		
		Mannschaft_Meta_Info::install();
	}
	
	private static function table_name(){
		global $wpdb;
		return $wpdb->prefix . "mannschaften";
	}
	
	public static function uninstall(){
		global $wpdb;
		
		$sql = "DROP TABLE IF EXISTS ". static::table_name().";";
		$wpdb->query($sql);

		Mannschaft_Meta_Info::uninstall();
	}
	
}

class Mannschaft_Meta_Info{
	private $name;
	
	public static function install(){
		global $wpdb;
	
		$charset_collate = $wpdb->get_charset_collate();
	
		$sql = 
			"CREATE TABLE ".static::table_name()." (
			  id mediumint(9) NOT NULL AUTO_INCREMENT,
			  name tinytext NOT NULL,
			  UNIQUE KEY id (id)
			) ".$charset_collate.";";
		
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		
		dbDelta( $sql );
	}

	private static function table_name(){
		global $wpdb;
		return $wpdb->prefix . "mannschaft_meta";
	}

	public static function uninstall(){
		global $wpdb;
		$sql = "DROP TABLE IF EXISTS ". static::table_name().";";
		$wpdb->query($sql);
	}
	
}

?>