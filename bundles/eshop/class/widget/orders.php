<?

/**
 * Виджет для вывода заказов на главной странице админки /admin/
 **/
class eshop_widget_orders extends admin_widget {

    public function test() {
        if(user::active()->checkAccess("eshop:showOrdersWidget")) {
            return true;
		}
    }
    
    public function inStartPage() {
        return true;
    }
    
    public function inMenu() {
        return false;
    }
    
    public function width() {
        return 400;
    }
    
    public function exec() {    
        tmp::exec("/eshop/widgets/orders");
    }

}
