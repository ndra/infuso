<?

class reflex_conf_editor extends reflex_editor {

	public function icon() {
		return $this->item()->subconf()->count() ? "folder" : "gear";
	}
	
	public function inxFieldsTab() {
	    if(mod_superadmin::check())
	        return parent::__call("inxFieldsTab",array());
		return false;
	}
	
	public function inxExtraTabs() {
		return array(
			array(
			    "title" => "Значение",
			    "lazy" => true,
			    "type" => "inx.mod.reflex.conf",
			    "confID"=>$this->item()->id(),
			),
		);
	}
	
	public function beforeCollectionView() {
	    return user::active()->checkAccess("reflex:viewConf");
	}
	
	public function beforeView() {
	    return user::active()->checkAccess("reflex:viewConf");
	}
	
	public function beforeEdit() {
	    return mod_superadmin::check();
	}

}
