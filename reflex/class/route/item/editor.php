<?

class reflex_route_item_editor extends reflex_editor {

	public function itemClass() {
	    return "reflex_route_item";
	}

    public function filters() {
        return array(
            reflex_route_item::all()->eq("hash","")->title("Пользовательские"),
            reflex_route_item::all()->neq("hash","")->title("Метаданные"),
        );
    }
    
    public function fields() {
        $ret = array();
        foreach(parent::__call("fields",array()) as $field)
            if(!$this->item()->data("hash") || !in_array($field->name(),array("params","controller","domain")))
                $ret[] = $field;
        return new mod_fieldset($ret);
    }
    
    public function beforeEdit() {
        return user::active()->checkAccess("admin");
    }
    
}
