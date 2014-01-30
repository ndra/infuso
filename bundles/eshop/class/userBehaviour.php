<?

class eshop_userBehaviour extends mod_behaviour {

	public function addToClass() {
		return "user";
	}

    /**
	 * Добавляем пользователю метод, возвращающий заказы
	 **/
	public function eshopOrders() {
		$uid = $this->id();
		if(!$uid)
		    $uid = -1;
		return reflex::get("eshop_order")->eq("userID",$uid);
	}

	/**
	 * Добавляем вкладку «Заказы» у пользователя
	 **/
	public function reflex_children() {
		return array(
		    $this->eshopOrders()->title("Заказы"),
		);
	}
	
}
