<?

class forum_editor_post extends reflex_editor {
    
    public function itemClass() {
        return "forum_post";
    }
    
    /**
     * @todo Petr Grishin Мне не нравиться как реализован метод, плюс ко всему что он зафиксирован в svn 
     */
    public function _renderListData() {
    
        $topic = $this->item()->topic();
    
        $html = "";
        $html.= "<div style='font-weight:bold;' >"
                . "<a href='{$topic->url()}' target='_blank'>{$topic->title()}</a>"
                . "<a href='{$topic->editor()->url()}'><img src='/forum/res/img/topic_edit.png' width='16' height='16' title='Редактировать тему форума' style='margin-left:6px; vertical-align:bottom;' /></a>"
                . "</div>";
        
        $user = $this->item()->user();
        $html.= "<a href='{$user->url()}' target='_blank'>{$user->title()}</a>";
        $html.= "<a href='{$user->editor()->url()}'><img src='/forum/res/img/user_edit.png' width='16' height='16' title='Редактировать автора сообщения' style='margin-left:6px; vertical-align:bottom;' /></a>";
        $html.= " <i>".$this->item()->actualDate()->num()."</i>";
        
        $html.= "<div>";
        $html.= util::str($this->item()->message())->ellipsis(500);
        $html.= "</div>";
        
        return $html;
    }
    
    public function beforeEdit() {
        return user::active()->checkAccess("forum:editPost");
    }
    
    
}