<?

class pay_behaviour_userEditor extends mod_behaviour {

    public function addToClass() {
        return "user_editor";
    }
    
    public function actions() {
        
        return array(
            array(
                "text" => "Пополнить счет",
                "icon"=> "money",
                "dlg" => array(
                    "type" => "inx.mod.pay.addFunds",
                    "userID" => $this->item()->id(),
                ),
            ),
        );
        
    }

}