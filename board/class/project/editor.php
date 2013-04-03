<?

class board_project_editor extends reflex_editor {

	public function itemClass() {
	    return "board_project";
	}

	public function beforeEdit() {
	    return mod_superadmin::check();
	}

}
