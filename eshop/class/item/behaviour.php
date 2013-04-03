<?

/**
 * Поведение по умолчанию для товара
 **/
class eshop_item_behaviour extends mod_behaviour {

	public function reflex_search() {
	    if(!$this->component()->published())
			return false;
	    if(mod::conf("eshop:search_eshop"))
			return $this->title();
	}

	public function fields() {
		$ret = array();
		for($i=1;$i<=6;$i++)
		    $ret[] = mod::field("bigint")->name("group-$i")->hide();
		return $ret;
	}

	public function indexes() {
		$ret = array();
		for($i=1;$i<=6;$i++)
		    $ret[] = reflex_table_index::create()->name("**group-$i")->fields("activeSys,group-$i,instock");
		return $ret;
	}

	public function reflex_beforeCollection($list) {
		$list->asc("priority");
	}

	public function reflex_smallSearchSnippet() {
	    ob_start();
	    tmp::exec("eshop:search.smallSnippet",$this->component());
	    return ob_get_clean();
	}

	public function reflex_bigSearchSnippet() {
	    ob_start();
	    tmp::exec("eshop:search.bigSnippet",$this->component());
	    return ob_get_clean();
	}

	/**
	 * Товары с фотографией имеют чуть более высокий приоритет
	 **/
	public function reflex_searchWeight() {
		return $this->data("photos") ? 2 : 1;
	}

	/**
	 * @return Возвращает коллекцию фотографий объекта
	 **/
	public function photos() {
		return $this->pdata("photos");
	}

	/**
	 * @return Возвращает фотографию товара
	 * Если у товара несколько фотографий, вернет первую
	 **/
	public function photo() {
		return $this->component()->photos()->first();
	}

	/**
	 * Возвращает цену товара
	 **/
	public function price() {
		return $this->data("price");
	}

}
