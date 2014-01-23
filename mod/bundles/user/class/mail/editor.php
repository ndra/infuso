<?

/**
 * Редактор для писем в каталоге
 **/
class user_mail_editor extends reflex_editor {

    public function root() {
        return array(
            user_mail::all()->param("tab","user")->title("Письма"),
        );
    }
    
    public function disable() {
        return "list";
    }
    
    public function gridCols() {    
        return array(
            array(
                "title" => "Отправлено",
                "name" => "datetime",
                "width" => 120,
            ),
            array(
                "title" => "Кому",
                "name" => "to",
                "width" => 150,
            ),
            array(
                "title" => "Тема",                
                "name" => "subject",
                "width" => 300,
            ),
        );    
    }
    
    public function gridData() {    
    
        $text = $this->item()->subject();
        $text.= " <span style='color:gray' >— ".util::str($this->item()->message())->text()->ellipsis(120)."</span>";
    
        return array(
            "data" => array (
                "subject" => $text,
                "to" => $this->item()->to(),
                "datetime" => $this->item()->pdata("sent")->num(),
            ),
            "css" => array(
                "font-size" => $this->item()->glue() ? "10px" : "",
            )
        );    
    }

}
