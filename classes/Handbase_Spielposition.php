<?php
class Handbase_Spielposition{
	private $bezeichnung;
	private $abkuerzung;
	
	function __construct($bezeichnung, $abkuerzung){
		$this->bezeichnung = $bezeichnung;
		$this->abkuerzung = $abkuerzung;
	}
	
	public function get_bezeichnung(){
		return $this->bezeichnung;
	}
	
	public function get_abkuerzung(){
		return $this->abkuerzung;
	}
	
	public function get_meta_name(){
		return 'Position_'.$this->get_abkuerzung();
	}
	
	public static $TRAINER;
	public static $TORWART, $LINKS_AUSSEN, $RUECKRAUM_LINKS, $MITTE, $RUECKRAUM_RECHTS, $RECHTS_AUSSEN, $KREIS;
	
	public static function alle_positionen(){
		return array(
				static::$TRAINER,
				static::$TORWART,
				static::$LINKS_AUSSEN,
				static::$RUECKRAUM_LINKS,
				static::$MITTE,
				static::$RUECKRAUM_RECHTS,
				static::$RECHTS_AUSSEN,
				static::$KREIS
		);
	}
	
}
Handbase_Spielposition::$TRAINER = new Handbase_Spielposition("Trainer", "Tr");

Handbase_Spielposition::$TORWART = new Handbase_Spielposition("Torwart", "TW");
Handbase_Spielposition::$LINKS_AUSSEN = new Handbase_Spielposition("Links außen", "LA");
Handbase_Spielposition::$RUECKRAUM_LINKS = new Handbase_Spielposition("Rückraum links", "RL");
Handbase_Spielposition::$MITTE = new Handbase_Spielposition("Mitte", "RM");
Handbase_Spielposition::$RUECKRAUM_RECHTS = new Handbase_Spielposition("Rückraum rechts", "RR");
Handbase_Spielposition::$RECHTS_AUSSEN = new Handbase_Spielposition("Rechts außen", "RA");
Handbase_Spielposition::$KREIS = new Handbase_Spielposition("Kreis", "KR");
?>