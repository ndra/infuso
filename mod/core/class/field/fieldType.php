<?

class mod_field_fieldType extends mod_field {

	public function typeID() {
		return "z34g-rtfv-i7fl-zjyv-iome";
	}
	
	public function typeName() {
		return "Тип поля";
	}

	public function mysqlType() {
		return "varchar(50)";
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
				"cmd" => "reflex_editor_fieldController:getFieldTypes",
			),
		);
	}

	public function pvalue() {
		return mod_field::get(array(
		    "editable" => 1,
		    "name" => "field",
		    "type" => $this->value(),
		));
	}

}
