<?

class lang_type_textarea extends lang_type_textfield {

	public function typeID() {
		return "fl2n-mvbo-yvs7-wern-y5bt";
	}

	public function mysqlType() {
		return "longtext";
	}

	public function mysqlIndexFields() {
		return $this->name()."(1)";
	}

	public function editorInx() {
		return array(
		    "editor" => "inx.mod.reflex.fields.textarea",
		    "type" => "inx.mod.lang.fields.textarea",
		    "value" => $this->value(),
		);
	}

	public function typeName() {
		return "Текстовое поле (мультияз.)";
	}

}
