<?

class mod_field_widget extends mod_field{

	public function typeID() {
		return "bnk1-2ow5-zdyl-ityl-zo4l";
	}

	public function typeName() {
		return "Виджет";
	}

	public function mysqlType() {
		return "varchar(255)";
	}

	public function mysqlIndexType() {
		return "index";
	}

	public function prepareValue($val) {
		return trim($val);
	}

	public function editorInx() {
		return array(
		    "type" => "inx.select",
		    "value" => $this->value(),
	        "loader" => array(
	            "cmd" => "reflex:editor:fieldController:listWidgets",
	        ),
		);
	}

	public function pvalue() {
	    return tmp_widget::get($this->value());
	}

}
