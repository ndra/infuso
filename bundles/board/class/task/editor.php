<?

class board_task_editor extends reflex_editor {

	public function itemClass() {
	    return "board_task";
	}

	public function beforeEdit() {
	    return mod_superadmin::check();
	}

}
