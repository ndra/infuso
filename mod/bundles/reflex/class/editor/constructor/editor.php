<?

class reflex_editor_constructor_editor extends reflex_editor {

    public function inxEditor() {
        return array(
            "type" => "inx.mod.reflex.construct",
            "constructorID" => $this->item()->id(),
            "form" => $this->item()->getList()->editor()->inxConstructorForm(),
        );
	}
	
	public function beforeEdit() {
		return $this->item()->data("userID") == user::active()->id();
	}

}
