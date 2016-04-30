<?php
namespace handball;

class Position{
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
	
	public static $TORWART, $LINKS_AUSSEN, $RUECKRAUM_LINKS, $MITTE, $RUECKRAUM_RECHTS, $RECHTS_AUSSEN, $KREIS;
	
	public static function alle_positionen(){
		return array(
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

Position::$TORWART = new Position("Torwart", "TW");
Position::$LINKS_AUSSEN = new Position("Links außen", "LA");
Position::$RUECKRAUM_LINKS = new Position("Rückraum links", "RL");
Position::$MITTE = new Position("Mitte", "RM");
Position::$RUECKRAUM_RECHTS = new Position("Rückraum rechts", "RR");
Position::$RECHTS_AUSSEN = new Position("Rechts außen", "RA");
Position::$KREIS = new Position("Kreis", "KR");
?>