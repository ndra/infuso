<?

class forum_editor_topic extends reflex_editor {

    public function itemClass() {
        return "forum_topic";
    }
    
     public function beforeEdit() {
        return user::active()->checkAccess("forum:editTopic");
    }
}