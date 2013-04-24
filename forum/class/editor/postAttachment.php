<?

class forum_editor_postAttachment extends reflex_editor {
    
    public function itemClass() {
        return "forum_postAttachments";
    }
    
    public function beforeEdit() {
        return user::active()->checkAccess("forum:editPost");
    }
    
    
}
