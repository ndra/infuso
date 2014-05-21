<?

/**
 * Контроллер опроса
 **/
class vote_controller extends mod_controller {

    public static function postTest() {
        return true;
    }

    public static function post_vote($p) {

        $cookie = vote::getCookie();
        $vote = vote::get($p["voteID"]);
        switch($vote->data("mode")) {

            // Разрешен один ответ
            case 1:
                $option = end($p["options"]);
                $vote->addAnswer($option,$cookie);
                break;

            // Разрешено несколько ответов
            case 2:
                $options = array_unique($p["options"]);
                foreach($options as $option) {
                    $vote->addAnswer($option,$cookie);
                }
                break;
            case 3:
                $vote->addText($p["text"],$cookie);
                break;
        }
        if($vote->data("enableDraft") && $p["draft"]){
            $vote->addDraftOption($p["draft"],$cookie);    
        }
        ob_start();
        tmp::exec("vote:vote.ajax",$vote);
        return ob_get_clean();

    }

}
