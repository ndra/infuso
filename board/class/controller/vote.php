<?

class board_controller_vote extends mod_controller {

    public function postTest() {
        return user::active()->exists();
    }

    /**
     * Контроллер получения критериев голосования
     **/
    public function post_getCriterias($p) {

        $ret = array();

        $criterias = board_task_vote_criteria::all();
        $task = board_task::get($p["taskID"]);

        foreach($criterias as $criteria) {
        
			if(user::active()->checkAccess("board/vote",array(
			    "criteria" => $criteria,
			    "task" => $task,
			))) {

             $vote = board_task_vote::all()
	                ->eq("ownerID",user::active()->id())
	                ->eq("taskID",$task->id())
	                ->eq("criteriaID",$criteria->id())
	                ->one();

	            $score = $vote->exists() ? $vote->data("score") : null;

	            $ret[] = array(
	                "id" => $criteria->id(),
	                "type" => (int)$criteria->data("type"),
	                "title" => $criteria->title(),
	                "score" => $score,
	            );
            
            }
        }

        return $ret;
    }

    /**
     * Изменяет значение голосования по определенному критерию
     **/
    public static function post_vote($p) {

        $votes = board_task_vote::all()
            ->eq("ownerID",user::active()->id())
            ->eq("taskID",$p["taskID"])
            ->eq("criteriaID",$p["criteriaID"]);

        $vote = $votes->one();

        if($p["score"]) {
            if(!$vote->exists()) {
                $vote = $votes->create();
            }
            $vote->data("score",$p["score"]);
        } else {
            $vote->delete();
        }

    }

}
