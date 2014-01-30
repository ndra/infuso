<?

class TinyMCE_field extends mod_field_textarea {

	public function typeID() {
		return "boya-itpg-z30q-fgid-wuzd";
	}

	public function mysqlType() {
		return "longtext";
	}

	public function editorInx() {

		return array(
	        "type" => "inx.mod.TinyMCE.field",
	        "value" => $this->value(),
	    );

	}

	public function typeName() {
		return "TinyMce";
	}

	/**
	 * Возвращает все параметры конфигурации модуля TinyMCE
	 **/
	public static function configuration() {
	    return array(
	        array("id"=>"TinyMCE:active","title"=>"Включить TynyMCE"),
		);
	}

}
