<? class reflex_editor_fieldTypes extends mod_controller {

public function indexTest() { return mod_superadmin::check(); }
public function indexFailed() { admin::fuckoff(); }
public function indexTitle() { return "Типы данных"; }
public function index() {
	admin::header("Типы данных");
	
	echo "<div style='padding:40px;' >";
	
	foreach(mod_field::all() as $field) {
	    echo "<div style='margin-bottom:10px;' >";
	    
	    $url = mod_action::get("reflex_editor_fieldTypes","field",array("id"=>$field->typeID()))->url();
	    echo "<a href='{$url}'>{$field->typeName()}</a>";
	    echo "</div>";
	}
	
	echo "</div>";
	
	admin::footer();
}

public function index_field($p) {
	admin::header("");
	echo "<div style='padding:40px;' >";
	$field = mod_field::get(array(
	    "type" => $p["id"],
	));
	$inx = $field->editorInx();
	inx::add($inx);
	echo "</div>";
	admin::footer();
}

}
