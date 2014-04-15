<? 

class eshop_handler implements mod_handler {
    
    public function on_mod_init() {
    
        reflex_task::add(array(
            "class" => "eshop_order_item",
            "method" => "deleteAllNonActiveOrderedItems",
            "crontab" => "0 0 * * *",
        ));
        
        reflex_task::add(array(
            "class" => "eshop_order",
            "method" => "deleteOldOrders",
            "crontab" => "0 0 * * *",
        ));
    
    }
}