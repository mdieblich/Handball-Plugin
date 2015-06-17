<?php
class Spielposition{
	private $bezeichnung;
	private $abkuerzung;
	
	// TODO statische Objekte
	
	private function __construct($bezeichnung, $abkuerzung){
		$this->bezeichnung = $bezeichnung;
		$this->abkuerzung = $abkuerzung;
	}
	
	public function getBezeichnung(){
		return $this->bezeichnung;
	}
	
	public function getAbkuerzung(){
		return $this->abkuerzung;
	}
	
	public static $TRAINER, $TORWART;
	
}
$TORWART = new Spielposition("Torwart", "TW")
?>