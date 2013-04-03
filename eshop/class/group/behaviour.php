<?

/**
 * Поведение по умолчанию для группы товаров
 **/
class eshop_group_behaviour extends mod_behaviour {

	/**
	 * @return Контент для поиска
	**/
	public function reflex_search() {
		if(!$this->published())
			return false;
		if(mod::conf("eshop:search_eshop"))
			return $this->title();
	}

	/**
	 * Поисковый вес
	 **/
	public function reflex_searchWeight() {
		return 10;
	}

	/**
	 * Маленький поисковый сниппет
	 * Выводится в подсказке к поиску
	 **/
	public function reflex_smallSearchSnippet() {
		ob_start();
		tmp::exec("eshop:search.groupSmallSnippet",$this);
		return ob_get_clean();
	}

	/**
	 * Большой поисковый сниппет
	 * Выводится в результатах поиска
	 **/
	public function reflex_bigSearchSnippet() {
		ob_start();
		tmp::exec("eshop:search.groupBigSnippet",$this);
		return ob_get_clean();
	}

}
