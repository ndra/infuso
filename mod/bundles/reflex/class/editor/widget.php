<?

class reflex_editor_widget extends \mod\template\widget {

	public function name() {
	    return "Редактор";
	}
	
	public function execWidget() {
	
	    $item = reflex::get($this->param("class"),$this->param("id"));

		if(!$item->editor()->beforeEdit()) {
		    return;
		}
    
		tmp::exec("/reflex/admin/editWidget",array(
		    "item" => $item,
		));
	}

}
