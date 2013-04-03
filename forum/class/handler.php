<?

class forum_handler implements mod_handler {

    public function on_mod_init() {
    
        $forumAdmin = user_role::create("forum:admin", "Администратор форума")
            ->appendTo("admin");
        
        user_operation::get("admin:showInterface")
            ->appendTo("forum:admin");
        
        user_operation::get("user:editorCollectionView")
            ->appendTo("forum:admin");
            
        user_operation::create("admin:editTopic", "Операция редактирование топиков")
            ->appendTo("forum:admin"); 
            
        user_operation::create("admin:editPost", "Операция редактирование постов")
            ->appendTo("forum:admin");
        
        user_operation::create("admin:editGroup", "Операция редактирование групп")
            ->appendTo("forum:admin");            
        
    }    

}
