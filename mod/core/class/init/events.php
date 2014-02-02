<?

/**
 * Класс-инициализатор
 **/
class mod_init_events extends mod_init {

	/**
	 * Метод, в котором реализуется бизнес-логика инициализации
	 * @todo убрать отсюда \infuso\core\field::collect()
	 **/
	public function init() {
	
		// Собираем типы полей
	    \infuso\core\field::collect();
	    
	    mod::msg("Firing init events");
	    mod::fire("mod_beforeInit");
		mod::fire("mod_init");
		mod::fire("mod_afterInit");
	}

	/**
	 * Приоритет инициализации
	 **/
	public function priority() {
		return -1;
	}

}
