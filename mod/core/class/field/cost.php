<?

class mod_field_cost extends mod_field_decimal {

	public function typeID() {
		return "nmu2-78a6-tcl6-owus-t4vb";
	}

	public function typeName() {
		return "Стоимость";
	}

	public function mysqlType() {
		return "decimal(16,2)";
	}

	public function tableRender() {
		return number_format($this->value(),2,"."," ");
	}

	public function tableCol() {
		return array(
		    width=>50,
		    align=>"right",
		);
	}

}
