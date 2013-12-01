<?

/**
 * Модель объекта метаданных
 **/
class reflex_meta_item extends reflex {

	/**
	 * Возвращает коллекцию всех объектов метаданных
	 **/
	public static function all() {
		return reflex::get(get_class());
	}

	/**
	 * Возвращает объект метаданных по хэшу и id языка (по умолчанию - активный язык)
	 **/
	public static function get($hash,$lang=null) {
		if(!$lang)
		    $lang = lang::active()->id();
		return self::all()->eq("hash",$hash)->eq("lang",$lang)->one();
	}

	/**
	 * Возвращает элемент reflex, связанный с этим объектом метаданных
	 **/
	public function item() {
	    list($class,$id) = explode(":",$this->data("hash"));
	    return reflex::get($class,$id);
	}

	/**
	 * Подготовка объекта метаданных к сохранению
	 **/
	public function reflex_beforeStore() {

	    // Ссылки
	    $links = preg_replace("/\s*\,\s*/",",",$this->data("links"));
	    $links = preg_replace("/\s+/"," ",$links);
	    $links = mb_strtolower($links,"utf-8");
	    $links = util::splitAndTrim($links,",");
	    $this->data("links",",".implode(",",$links).",");

	    // Ставим язык, если он не был установлен
		if(!$this->data("lang")) {
		    $this->data("lang",lang::all()->one()->id());
		}

		// Удаляем избыточные меты
	    $this->unnecessary()->delete();

	    // На всякий случай, восстанавливаем хэш
		$this->data("hash",get_class($this->item()).":".$this->item()->id());
	}

	/**
	 * Возвращает список "лишних" метаданных.
	 * Лишние метаданные - те для которых хэш и язык совпадают
	 * Этот список используется для удаления лишних метаданных
	 **/
	public function unnecessary() {
	    return self::all()->eq("hash",$this->data("hash"))->eq("lang",$this->data("lang"))->neq("id",$this->id());
	}

	/**
	 * Нужно ли удалять объект при очистке каталога
	 **/
	public function reflex_cleanup() {
	    // Убираем оторванные меты
	    if(!$this->item()->exists()) {
	        return true;
	    }
	}

}
