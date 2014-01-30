<?

/**
 * Модель производителя
**/
class eshop_vendor extends reflex {

	/**
	 * Включаем экшны
	 **/
	public static function indexTest() {
		return true;
	}

	/**
	 * Экшн списка товаров
	 **/
	public static function index() {
	    tmp::exec("eshop:vendors");
	}

	/**
	 * Экшн страницы товара
	 **/
	public static function index_item($p) {
	    tmp::exec("eshop:vendor",self::get($p["id"]));
	}

	/**
	 * Вернет коллекцию всех активных производителей
	 **/
	public static function all() {
		return self::allEvenHidden()->eq("active",1)->gt("numberOfItems",0);
	}

	/**
	 * Вернет коллекцию всех производителей, даже тех что неактивны
	 **/
	public static function allEvenHidden() {
		return reflex::get(get_class())->asc("title");
	}

	/**
	 * @return Возвращает производителя по id
	 **/
	public static function get($id) {
		return reflex::get(get_class(),$id);
	}

	/**
	 * Возвращает коллекцию групп товаров данного производителя,
	 * Упорядоченную по количеству товаров в группе
	 **/
	public function groups() {
	    $items = eshop_group::all();
	    if($this->exists()) {
		    $items->join("eshop_item","`eshop_group`.`id`=`eshop_item`.`parent` and `eshop_item`.`vendor` = {$this->id()} and `eshop_item`.`active`");
		    $items->groupBy("eshop_group.id");
		    $items->orderByExpr("count(*) desc");
		    $items->limit(20);
	    } else {
	        $items->eq("id",-1);
	    }
	    return $items;
	}

	/**
	 * Возвращает коллекцию товаров данного производителя
	 **/
	public function items() {
		return eshop_item::all()->eq("vendor",$this->id());
	}

	public function reflex_children() {
	    return array(
	        $this->items()->title("Товары"),
	    );
	}

	public function updateItemsNumber() {
	    $this->data("numberOfItems",$this->items()->count());
	}

	public function reflex_repairSys() {
	    $this->updateItemsNumber();
	}

	public function reflex_search() {
	    if(!$this->published()) return false;
	    if(mod::conf("eshop:search_eshop")) return $this->title();
	}

	public function reflex_bigSearchSnippet() {
	    ob_start();
	    tmp::exec("eshop:search.vendorBigSnippet",$this);
	    return ob_get_clean();
	}

	public function reflex_searchWeight() {
		return 20;
	}

	public function reflex_meta() {
		return true;
	}

	public function reflex_classTitle() {
		return "Производитель";
	}

}
