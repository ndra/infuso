<?

/**
 * Модель языка
 **/
class lang extends reflex {

	/**
	 * Возвращает список языков
	 **/
	public static function all() {
		return reflex::get(get_class())->asc("priority")->param("sort",true);
	}

	/**
	 * Возвращает язык по его id
	 **/
	public static function get($id) {
		return reflex::get(get_class(),$id);
	}

	public function reflex_title() {
	    $ret = $this->data("title");
	    if(!$ret)
			$ret = $this->data("name");
	    return $ret;
	}

	/**
	 * Мета-данные у языков выключены
	 **/
	public function reflex_meta() {
		return false;
	}

	/**
	 * Возвращает фразу с ключем $key, переведенную на данный язык.
	 **/
	public function tr($key) {
		$ph = lang_phrase::search($key);
		return $ph->pdata("replace");
	}

	/**
	 * Возвращает активный язык.
	 **/
	public function active() {
		$lang = mod::session("lang");
		$item = self::get($lang);
		if(!$item->exists())
			$item = self::all()->one();
		return $item;
	}

	/**
	 * Делает текущий язык активным
	 **/
	public function activate() {
		if($this->exists())
			mod::session("lang",$this->id());
	}

	/**
	 * @return Возвращает сокращенное название языка, например, 'en'
	 * Это название редактируется в каталоге
	 **/
	public function name() {
		return $this->data("name");
	}

	public static function reflex_root() {
	
		if(mod_superadmin::check()) {
			return array(
				self::all()->title("Языки")->param("tab","system"),
			);
		}
			
		return array();
	}

	public function reflex_url() {
		return mod_action::get("lang_controller","set",array("lang"=>$this->id()))->url();
	}

	/**
	 * Возвращает файл иконки языка
	 **/
	public function img() {
		return $this->pdata("img");
	}

}
