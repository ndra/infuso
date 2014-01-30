<?

/**
 * Статус заказа "Неопределен"
 **/
class eshop_order_status_none extends eshop_order_status {

	public function title() {
		return "Статус не определен";
	}

	public function exists() {
		return false;
	}

}
