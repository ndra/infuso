<?

class mod_field_id extends mod_field {

	public function typeID() { return "jft7-kef8-ccd6-kg85-iueh"; }

	public function typeName() { return "Первичный ключ"; }

	public function mysqlType() { return "bigint(20)"; }

	public function mysqlIndexType() { return "primary"; }

	public function mysqlAutoincrement() { return true; }

	public function prepareValue($val) { return floor($val); }

}
