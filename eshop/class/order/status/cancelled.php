<?

/**
 * Статус заказа "Новый"
 **/
class eshop_order_status_cancelled extends eshop_order_status {

	public function _title() {
		return "Отменен";
	}

	public function _descr() {
		return "Заказ отменен";
	}

	/**
	 * Триггер, вызывающийся после изменения статуса в данный
	 **/
	public function _afterSet($order) {
	    $site = mod::conf("mod:site_title");
	    $msg = "Ваш заказ на сайте $site отменен.\n\n";
	    $order->user()->mail($msg,"Заказ с сайта $site");
	}

	public function _priority() {
		return -100;
	}

}
