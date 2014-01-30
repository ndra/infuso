<?

class eshop_order_action extends mod_controller {

    public static function indexTest() {
        return true;
    }
    
    public static function postTest() {
        return true;
    }
    
    public function index_history() {
        tmp::exec("eshop:myOrders");
    }
    
    /**
    * Экшн добавления позиции в заказ
    * @param array("itemID"=>123)
    **/
    public static function post_addItem($p) {
        $order = eshop_order::cart();
        if(!$order->exists())
            $order = eshop_order::createOrderForActiveUser();
        if(!$order->editable()) {
            mod::msg("You can't change items in this order",1);
            return;
        }
        $quantity = 1;
        if($p["quantity"]){
            $quantity = $p["quantity"];
        }
        $order->addItem($p["itemID"], $quantity, $p["itemSku"]);
    }
    
    /**
    * Экшн удаления позиции из заказа
    * @param array("itemID"=>123)
    **/
    public static function post_deleteItem($p) {
        $item = eshop_order_item::get($p["itemID"]);
        $order = $item->order();
        if(!$order->editable()) {
            mod::msg("You can't change items in this order",1);
            return;
        }
        $item->delete();
    }
    
    /**
    * Изменяет количество товаров в заказе
    **/
    public static function post_setQuantity($p) {
    
        $n = $p["n"]; // Количество товаров
    
        if($p["itemID"]) {
            $item = eshop::item($p["itemID"]);
            $order = eshop_order::cart();
            if(!$order->exists())
                $order = eshop_order::createOrderForActiveUser();
            $order->setQuantity($p["itemID"],$n);
            $success = true;
        }
    
        if($p["orderItemID"]) {
            $item = eshop_order_item::get($p["orderItemID"]);
            $order = $item->order();
            if(!$order->editable()) {
                mod::msg("You can't change items in this order",1);
                return;
            }
            $item->setQuantity($n);
        }
    
    }
    
    /**
    * Экшн вызывающийся при заполнении пользователем анкеты
    **/
    public static function post_fillInForm($p) {
        $order = eshop_order::get($p["orderID"]);
        if(!$order->editable()) {
            mod::msg("You can't change items in this order",1);
            return;
        }
        $order->fillInForm($p);
    }
    
    /**
    * Возвращает контент виджета маленькой корзины
    **/
    public static function post_getCart($p) {
        $order = eshop_order::cart();
        ob_start();
        tmp::exec("eshop:order.content.ajax",$order);
        return ob_get_clean();
    }
    
    /**
    * Возвращает контент виджета большой корзины
    **/
    public static function post_getCartSmall($p) {
        $order = eshop_order::cart();
        ob_start();
        tmp::exec("eshop:layout.cart.ajax",$order);
        return ob_get_clean();
    }
    
}
