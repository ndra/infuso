<?

/**
 * Класс-инициализатор
 **/
class mod_init_events extends mod_init {

	/**
	 * Метод, в котором реализуется бизнес-логика инициализации
	 **/
	public function init() {
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
