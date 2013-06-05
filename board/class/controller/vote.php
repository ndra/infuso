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

        foreach($criterias as $criteria) {

            $vote = board_task_vote::all()
                ->eq("ownerID",user::active()->id())
                ->eq("taskID",$p["taskID"])
                ->eq("criteriaID",$criteria->id())
                ->one();

            $score = $vote->exists() ? $vote->data("score") : null;

            $ret[] = array(
                "id" => $criteria->id(),
                "title" => $criteria->title(),
                "score" => $score,
            );
        }

        return $ret;
    }

    public static function post_vote($p) {
        $votes = board_task_vote::all()
            ->eq("ownerID",user::active()->id())
            ->eq("taskID",$p["taskID"])
            ->eq("criteriaID",$p["criteriaID"]);

        $vote = $votes->one();
        if(!$vote->exists()) {
            $vote = $votes->create();
        }

        $vote->data("score",$p["score"]);

    }

}
