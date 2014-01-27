<?

class board_task_tag_description_editor extends reflex_editor {

	public function root() {
	    return array (
            board_task_tag_description::all()->title("Тэги"),
		);
	}

}
