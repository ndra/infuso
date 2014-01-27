<?

/**
 * Класс текстового поля
 **/
class mod_field_textarea extends mod_field {

	public function typeID() {
		return "kbd4-xo34-tnb3-4nxl-cmhu";
	}
	
	public function typeName() {
		return "Текстовое поле";
	}

	public function mysqlType() {
		return "longtext";
	}

	public function mysqlIndexFields() {
		return $this->name()."(1)";
	}

	public function editorInx() {
		return array(
		    "type" => "inx.mod.reflex.fields.textarea",
		    "value" => $this->value(),
		);
	}

	public function pvalue($params=array()) {
		return reflex_content_processor::getDefault()->params($params)->process($this->value());
	}

	public function prepareValue($val) {
		return trim($val);
	}
	
}
