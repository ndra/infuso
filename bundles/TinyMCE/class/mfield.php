<?

class TinyMCE_mfield extends mod_field_textarea {

public function typeID() {
	return "kgh3yezx";
}

public function mysqlType() {
	return "longtext";
}

public function mysqlIndexFields() {
	return $this->name()."(1)";
}

public function editorInx() {
	return array(
	    "editor" => "inx.mod.TinyMCE.field",
	    "type" => "inx.mod.lang.fields.textarea",
	    "value" => $this->value(),
	);
}

public function typeName() {
	return "TinyMce (мультияз.)";
}

public function pvalue() {
	$ret = json_decode($this->value(),true);
	$key = lang::active()->name();
	$html = $ret[$key];
	return $html;
}

}
