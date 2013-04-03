<?

/**
 * Виджет меню для интернет-магазина
 **/
class eshop_widget extends admin_widget {

	public function test() {
		if(user::active()->checkAccess("eshop:showMainWidget"))
		    return true;
	}

	public function exec() {

		echo "<h2>Интернет-магазин</h2>";

		if(mod_conf::get("eshop:yandex:market")) {
	        echo "Яндекс.Маркет: ";
	        $url = mod_action::get("eshop_yandexMarket")->url();
			echo "<a href='{$url}' >выгрузка</a>, ";
	        $url = mod_action::get("eshop_yandexMarket_report")->url();
	        echo "<a href='{$url}' >отчет</a><br/>";
		}

		if(mod_conf::get("eshop:1c")) {
			$url = mod_action::get("eshop_1c_import")->url();
			echo "<a href='{$url}' >Ручная загрузка CommerceML</a><br/>";
		}

	}

}
