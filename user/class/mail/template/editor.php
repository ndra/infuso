<?

/**
 * Модель шаблона письма
 **/
class user_mail_template_editor extends reflex_editor {

    public function renderListData() {
    
        $html = "";
        $html.= $this->item()->code();
        $html.= " &mdash; ".$this->item()->subject();
        
        return array(
            "data" => array(
                "text" => $html,
            ),
            "css" => array(
                "text-decoration" => $this->item()->disable() ? "line-through" : "none",
                "color" => $this->item()->enable() ? "black" : "gray",
            )
        );
    
    }


}
