<?

class mod_field_textfield extends mod_field {

	public function typeID() {
		return "v324-89xr-24nk-0z30-r243";
	}

	public function typeName() {
		return "Строка";
	}

	public function mysqlType() {
		return "varchar(".$this->length().")";
	}

	public function mysqlIndexType() {
		return "index";
	}

	public function prepareValue($val) {
		return trim($val);
	}

	public function length() {
		$l = $this->conf("length");
		if(!$l)
		    $l = 255;
		return $l;
	}

	public function extraConf() {
		return array(
		    array(
				"name" => "length",
				"label" => "Длина (символов)",
			)
		);
	}

}
