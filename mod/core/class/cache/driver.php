<?

namespace infuso\core\cache;

abstract class driver extends \infuso\core\component {

	/**
	 * Возвращает значение переменной
	 **/
	abstract public function get($key);

	/**
	 * Устанавливает значение переменной
	 **/
	abstract public function set($key,$val);

	/**
	 * Очищает кэш
	 **/
	abstract public function clear();
	
	/**
	 * Очищает кэш c определенным префиксом
	 **/
	abstract public function clearByPrefix($prefix);

}
