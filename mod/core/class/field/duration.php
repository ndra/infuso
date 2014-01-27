<? class mod_field_duration extends mod_field_bigint {

public function typeID() { return "p5id-pl2o-clzt-p903-yv6o"; }
public function typeName() { return "Длительность"; }

public function tableCol() { return array(
    "width" => 50,
	"align" => "right"
); }

public function rvalue() {

	// Секунды
	$ret = "";
	$ret.= str_pad($this->value()%60,2,"0",STR_PAD_LEFT);
	
	// Минуты
	//if($this->value()>=60) {
	    $min = floor($this->value()/60)%60;
	    $min = str_pad($min,2,"0",STR_PAD_LEFT);
		$ret = $min.":".$ret;
	//}
	
	// Часы
//	if($this->value()>=3600) {
	    $h = floor($this->value()/3600);
		$ret = $h.":".$ret;
//	}
	
	return $ret;
}

}
