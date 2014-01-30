<?

/**
 * Обработчик событий модуля admin
 **/
class admin_init implements mod_handler {

    public function on_mod_init() {
    
        $op = user_operation::create("admin:showInterface");
        $op->appendTo("reflex:contentManager");
    
    }

}
