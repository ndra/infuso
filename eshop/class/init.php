<?

/**
 * Обработчик событий модуля eshop
 **/
class eshop_init implements mod_handler {

	public function on_mod_init() {
	
	    // Создаем роль "Администратор интернет-магазина"
	    
	    $admin = user_role::create("eshop:admin","Администратор интернет-магазина");
	    $admin->appendTo("admin");
	    
	    user_operation::get("admin:showInterface")->appendTo("eshop:admin");
	    
	    user_operation::create("eshop:editGroup")
	    	->appendTo("eshop:admin");
	    
	    user_operation::create("eshop:editItem")
	    	->appendTo("eshop:admin");
	    
	    user_operation::create("eshop:editVendor")
	    	->appendTo("eshop:admin");
	    
	    user_operation::create("eshop:editOrder")
	    	->appendTo("eshop:admin");
	    	
	    user_operation::create("eshop:editOrderItem")
	    	->appendTo("eshop:admin");
	    	
		// Выгрузка в 1С
	    	
	    user_operation::create("eshop:1CExchange")
	    	->appendTo("eshop:admin");
	    	
		// Права на просмотр виджетов
	    	
	    user_operation::create("eshop:showOrdersWidget")
	    	->appendTo("eshop:admin");

	    user_operation::create("eshop:showMainWidget")
	    	->appendTo("eshop:admin");
	    	
	    // Статусы заказов
	    	
	    user_operation::create("eshop:getOrderStatusList")
	    	->appendTo("eshop:admin");
	    	
	    user_operation::create("eshop:changeOrderStatus")
	    	->appendTo("eshop:admin");
	    	
	    // Выгрузка в яндекс-маркет

	    user_operation::create("eshop:yandexMarket:viewExport","Просмотр состояния выгрузки в Яндекс.Маркет")
	    	->appendTo("eshop:admin");

	    user_operation::create("eshop:yandexMarket:doExport","Ручной запуск выгрузки в Яндекс.Маркет")
	    	->appendTo("eshop:admin");

		user_operation::create("eshop:yandexMarket:showReport","Просмотр отчета выгрузки в Яндекс.Маркет")
	    	->appendTo("eshop:admin");
	    	
        // Таб для каталога

        reflex_editor_rootTab::create(array(
            "title" => "Магазин",
            "name" => "eshop",
            "icon" => "/eshop/res/icons/48/eshop.png",
		));
	    
	}

}
