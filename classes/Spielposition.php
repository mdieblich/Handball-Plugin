<?php
class Spielposition{
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
Spielposition::$TRAINER = new Spielposition("Trainer", "Tr");

Spielposition::$TORWART = new Spielposition("Torwart", "TW");
Spielposition::$LINKS_AUSSEN = new Spielposition("Links außen", "LA");
Spielposition::$RUECKRAUM_LINKS = new Spielposition("Rückraum links", "RL");
Spielposition::$MITTE = new Spielposition("Mitte", "RM");
Spielposition::$RUECKRAUM_RECHTS = new Spielposition("Rückraum rechts", "RR");
Spielposition::$RECHTS_AUSSEN = new Spielposition("Rechts außen", "RA");
Spielposition::$KREIS = new Spielposition("Kreis", "KR");
?>