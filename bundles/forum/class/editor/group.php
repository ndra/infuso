<?

class forum_editor_group extends reflex_editor {

    public function itemClass() {
        return "forum_group";
    }
    
    /**
     * Настройка админки каталога
     *
     * @return array
     **/
     public function root() {
		return array(
			forum_group::root()
				->param("sort",true)
				->title("Группы"),
		);
     }
        
    public function beforeEdit() {
        return user::active()->checkAccess("forum:editGroup");
    }   
        
}
