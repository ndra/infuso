<?

class reflex_editor_root_editor extends reflex_editor {

	public function inxFieldsTab() {
		return false;
	}

	public function numberOfChildren() {
		return $this->item()->data("data") ? parent::__call("numberOfChildren",array()) : 0;
	}

	public function editorChildren() {

	    if($this->item()->id()==0) {
	        $ret = array();
	        foreach(mod::service("reflexEditor")->level0() as $editor) {
	            $ret[] = $editor;
            }
	        return $ret;
	    }

	    return parent::__call("editorChildren",array());
	}

	/**
	 * Выключаем лог у рута
	 **/
	public function log() {
		return false;
	}

	public function beforeEdit() {
		return true;
	}

	/**
	 * В качестве иконки используем иконку коллекции
	 **/
	public function icon() {
		if($this->item()->id()=="0")
		    return null;
		return $this->item()->getList()->icon();
	}
	
	public function tab() {
	    return $this->item()->data("tab");
	}
	
    public function rootPriority() {
    	return $this->item()->data("priority");
    }

}
