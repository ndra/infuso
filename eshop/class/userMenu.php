<?

/**
 * Поведение, добавляющее пункты в мею пользователя
 **/
class eshop_userMenu extends mod_behaviour {

	public function addToClass() {
	    return "user_menu";
	}

	public function items() {
	
	    $url = mod::action("eshop_order_history")->url();
	
	    return array(
	        new tmp_menu_item($url,"Мои заказы"),
		);
	}

}
