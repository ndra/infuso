<?

/**
 * Поведение, добавляющее пункты в мею пользователя
 **/
class user_subscription_menu extends mod_behaviour {

	public function addToClass() {
	    return "user_menu";
	}

	public function items() {
	
	    $url = mod::action("user_subscription_action","list")->url();
	    $item = new tmp_menu_item($url,"Мои подписки");
	    $item->param("sup",user::active()->subscriptions()->count());
	
	    return array(
	        $item
		);
	}

}
