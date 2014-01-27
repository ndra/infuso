<?

class mod_field_html extends mod_field {

	public function typeID() { return "fgkn-o95h-uikx-c878-k4bi"; }
	public function mysqlType() { return "longtext"; }

	public function mysqlIndexFields() {
		return $this->name()."(1)";
	}

	public function editorInx() {
		return array(
		    "type" => "inx.code",
		    "value" => $this->value(),
		);
	}

	public function typeName() { return "Код HTML"; }

}
