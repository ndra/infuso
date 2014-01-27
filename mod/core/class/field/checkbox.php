<?

class mod_field_checkbox extends mod_field {

	public function typeID() { return "fsxp-lhdw-ghof-1rnk-5bqp"; }
	public function typeName() { return "Чекбокс"; }

	public function mysqlType() { return "tinyint(1)"; }
	public function mysqlIndexType() { return "index"; }

	public function editorInx() {
		return array(
		    "type" => "inx.checkbox",
		    "value" => $this->value(),
		);
	}

	public function tableCol() { return array(
		type=>image
	); }

	public function tableRender() {
		return $this->value() ? "ok" : "";
	}

	public function rvalue() {
		return $this->value() ? "да" : "нет";
	}

	public function filterType() { return "checkbox"; }

	public function prepareValue($val) {
		return $val*1 ? 1 : 0;
	}

}
