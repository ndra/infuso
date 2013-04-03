<?

/**
 * Контроллер истории заказов
 **/
class eshop_order_history extends mod_controller {

public static function indexTest() {
	return true;
}

public static function index() {
    $orders = eshop_order::myOrders()->page($_REQUEST["p"]);
	tmp::exec("eshop:myOrders",$orders);
}

}
