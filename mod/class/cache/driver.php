<? abstract class mod_cache_driver extends mod_component {

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

}
