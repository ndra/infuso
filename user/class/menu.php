<?

/**
 * Виджет меню личного кабинета пользователя
 **/
class user_menu extends tmp_menu {

	public function execWidget() {
	    $params = $this->params();
	    $params["items"] = $this->items();
	    tmp::exec("user:widgets.user-menu",$params);
	}
	
	public function items() {
	
	    $items = parent::items();
	    $url = mod::action("user_action","update")->url();
		$item = new tmp_menu_item($url,"Изменение данных!");
		array_unshift($items,$item);
		return $items;
		
	}

}
