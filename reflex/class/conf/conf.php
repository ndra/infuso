<?

/**
 * Модель конфигурационной переменной
 **/

class reflex_conf extends reflex{

	public static function all() {
		return reflex::get(get_class())->asc("priority")->param("sort",true);
	}

	/**
	 * Возвращает параметр конфигурации
	  **/
	public static function get($name) {
		$item = self::all()->eq("name",$name)->one();
		return $item->data("value");
	}

	/**
	 * Возвращает параметр конфигурации
	 * Типа значения зависит от типа поля
	 **/
	public static function pget($name) {
		$item = self::all()->eq("name",$name)->one();
		$val = $item->data("value");
		$field = $item->pdata("type");
		$field->addBehaviour("reflex_table_fieldBehaviour");
		$field->value($val);
		return $field->pvalue();
	}

	/**
	 * Только пользователи с правом reflex:conf могут видеть список настроек в каталоге
	 **/
	public function reflex_root() {
		return array(
			self::all()->eq("parent",0)->title("Настройки")->param("group","Настройки")->param("tab","system"),
		);
	}

	public function reflex_children() {
		return array(
		    self::all()->eq("parent",$this->id())->title("Подразделы"),
		);
	}

	/**
	 * Возвращает коллекцию дочерних настроек
	 **/
	public function subconf() {
		return self::all()->eq("parent",$this->id());
	}

	public function reflex_parent() {
		return $this->pdata("parent");
	}

	public function reflex_beforeStorageView() {
		return user::active()->checkAccess("reflex:editConfValue");
	}

	public function reflex_beforeStorageChange() {
		return user::active()->checkAccess("reflex:editConfValue");
	}

	public static function postTest() {
		return user::active()->checkAccess("reflex:editConfValue");
	}

	public static function post_get($p) {

		$item = reflex::get("reflex_conf",$p["confID"]);
		$field = $item->pdata("type");
		$field->addBehaviour("reflex_table_fieldBehaviour");
		$field->setModel($item);
		$field->label($item->title());
		$field->value($item->data("value"));
		$editor = $field->editorInxFull();

		return $editor;
	}

	public static function post_save($p) {
		$item = reflex::get("reflex_conf",$p["confID"]);
		$item->data("value",$p["value"]);
		$item->log("Изменение значения");
		mod::msg("Данные сохранены");
	}

}
