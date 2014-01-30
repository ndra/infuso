<?

/**
 * Модель комментария
 **/

class comment_controller extends mod_controller {

    public function postTest() {
        return true;
    }
    
    public function post_comment($p) {
    
        $form = form::byCode("comment");
        if(!$form->validate($p)) {
            mod::msg("При отправке комментария произошла ошибка, попробуйте еще раз.");
            return;
        }   
            
        $comment = reflex::create("comment",array(
            "text" => $p["text"],
            "plus" => $p["plus"],
            "minus" => $p["minus"],
            "mark" => $p["mark"],
            "for" => $p["for"],
        ));
        
    }

}
