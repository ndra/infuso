<?

class mod_field_bigint extends mod_field {

	public function typeID() {
		return "gklv-0ijh-uh7g-7fhu-4jtg";
	}

	public function typeName() {
		return "Большое целое";
	}

	public function mysqlType() {
		return "bigint(20)";
	}

	public function mysqlIndexType() {
		return "index";
	}

	public function editorInx() {
		return array(
		    "type" => "inx.textfield",
		    "width" => 70,
		    "value" => $this->value(),
		);
	}

	public function filterType() {
		return "number";
	}

	public function prepareValue($val) {
		return floor($val);
	}

	public function tableCol() {
		return array(
	    	width=>50
		);
	}

	public function tableRender() {
	    return $this->rvalue();
	}

	public function defaultValue() {
		return intval($this->prepareValue($this->conf("default")));
	}

}
