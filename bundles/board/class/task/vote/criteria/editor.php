<?

class board_task_vote_criteria_editor extends reflex_editor {

    public function root() {
        return array (
            board_task_vote_criteria::all()->title("Критерии оценки"),
        );
    }

}
