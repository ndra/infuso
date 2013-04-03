<?

/**
 * Статус заказа "Выполнен"
 **/
class eshop_order_status_completed extends eshop_order_status {

	/**
	 * Триггер, вызывающийся перед изменением статуса в данный
	 **/
	public function _beforeSet($order) {
	}

	/**
	 * Триггер, вызывающийся после изменения статуса в данный
	 **/
	public function _afterSet($order) {
	    $site = mod::conf("mod:site_title");
	    $msg = "Ваш заказ на сайте $site выполнен.\n\n";
	    $order->user()->mail($msg,"Заказ с сайта $site");
	}

	public function _title() {
		return "Выполнен";
	}

	public function _descr() {
		return "Ваш заказ выполнен";
	}

	public function _priority() {
		return 50;
	}

}
