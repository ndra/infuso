<?

class lang_type_textfield extends mod_field {

	public function typeID() {
		return "lhxc-e0jk-uhjw-a27c-g0o8";
	}

	public function typeName() {
		return "Строка (мультияз.)";
	}

	public function mysqlIndexFields() {
		return $this->name()."(1)";
	}

	public function mysqlType() {
		return "longtext";
	}

	public function editorInx() {
		return array(
		    "editor" => "inx.textfield",
		    "type" => "inx.mod.lang.fields.textfield",
		    "value" => $this->value(),
		);
	}

	public function prepareValue($val) {

		// Приводим значение к строке
		$val.="";

		if(!preg_match("/^\{/",$val)) {
		    $ret = array();
		    foreach(lang::all() as $lang) {
		        $ret[$lang->name()] = $val;
		    }
		    $val = json_encode($ret);
		}

		return $val;
	}

	public function pvalue($params=array()) {
		$val = $this->value();
		$ret = json_decode($val,true);
		$key = lang::active()->name();
		$ret = $ret[$key];

		// Прогоняем через преобразование контента
		$ret = reflex_content_processor::getDefault()->params($params)->process($ret);
		return $ret;
	}

}
