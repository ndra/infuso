<?

/**
 * Класс-инициализатор
 **/
abstract class mod_init {

	/**
	 * Метод, в котором реализуется бизнес-логика инициализации
	 **/
	abstract public function init();

	/**
	 * Приоритет инициализации
	 **/
	abstract public function priority();

}
