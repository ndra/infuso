<?

class board_collectionBehaviour extends mod_behaviour {

    public function useTag($tag) {

        $this->join("board_task_tag","board_task_tag.taskID = board_task.id")->eq("board_task_tag.tagID",$tag);
        return $this->component();

    }

}
