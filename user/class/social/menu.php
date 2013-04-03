<?

/**
 * Поведение, добавляющее пункты в мею пользователя
 **/
class user_social_menu extends mod_behaviour {

	public function addToClass() {
	    return "user_menu";
	}

	public function items() {
	
	    $url = mod::action("user_social_action","list")->url();
	    $item = new tmp_menu_item($url,"Социальные профили");
		$item->param("sup",user::active()->socialLinks()->count());
	    return array(
	        $item,
		);
	}

}
