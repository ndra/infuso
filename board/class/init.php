<?

class board_init implements mod_handler {

    public function on_mod_init() {
        
        // Создаем роль
        
        $role = user_role::create("boardUser");
        $role->data("title","Пользователь доски");
        
        // Операции в доске
        
        $o = user_operation::create("board:viewAllProjects");
        $o->appendTo("boardUser");
        
        // Операции с проектами
        
        $o = user_operation::create("board:createProject","Создание проекта в доске");
        $o->appendTo("boardUser");
        
        // Операции с задачами
        
        $o = user_operation::create("board:updateTaskText");
        $o->appendTo("boardUser");
        
        $o = user_operation::create("board:updateTaskParams");
        $o->appendTo("boardUser");
        
        
        
    }

}
