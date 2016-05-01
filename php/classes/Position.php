<?php
namespace handball;

class Position{
	private $name;
	private $abbreviation;
	
	function __construct($name, $abbreviation){
		$this->name = $name;
		$this->abbreviation = $abbreviation;
	}
	
	public function get_name(){
		return $this->name;
	}
	
	public function get_abbreviation(){
		return $this->abbreviation;
	}
	
	public function get_meta_name(){
		return 'Position_'.$this->get_abbreviation();
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