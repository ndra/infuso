<?

/**
 * Модель объекта метаданных
 **/
class reflex_meta_item extends reflex {

	public function reflex_table() {
		return array (
		  'name' => get_class(),
		  'fields' =>
		  array (
		    array (
				'name' => 'id',
				'type' => 'jft7-kef8-ccd6-kg85-iueh',
				'editable' => '0',
		    ), array (
				'name' => 'hash',
				'type' => 'v324-89xr-24nk-0z30-r243',
				'editable' => '0',
				'label' => 'Хэш (class:ID)',
		    ), array (
				'name' => 'lang',
				'type' => 'pg03-cv07-y16t-kli7-fe6x',
				'editable' => '2',
				'label' => 'Язык',
				'class' => 'lang',
		    ), array (
				'name' => 'url',
				'type' => 'v324-89xr-24nk-0z30-r243',
				'editable' => '0',
				'label' => 'Адрес url',
		    ), array (
				'name' => 'controller',
				'type' => 'v324-89xr-24nk-0z30-r243',
				'editable' => '0',
				'label' => 'Контроллер',
		    ), array (
				'name' => 'params',
				'type' => 'puhj-w9sn-c10t-85bt-8e67',
				'editable' => '0',
				'label' => 'Параметры контроллера',
		    ), array (
				'name' => 'title',
				'type' => 'kbd4-xo34-tnb3-4nxl-cmhu',
				'editable' => '1',
				'label' => 'Заголовок браузера &lt;title&gt;',
		    ), array (
				'name' => 'pageTitle',
				'type' => 'kbd4-xo34-tnb3-4nxl-cmhu',
				'editable' => '1',
				'label' => 'Заголовок на странице &lt;h1&gt;',
		    ), array (
				'name' => 'keywords',
				'type' => 'kbd4-xo34-tnb3-4nxl-cmhu',
				'editable' => '1',
				'label' => 'Keywords',
		    ), array (
				'name' => 'description',
				'type' => 'kbd4-xo34-tnb3-4nxl-cmhu',
				'editable' => '1',
				'label' => 'Description',
		    ), array (
				'name' => 'links',
				'type' => 'kbd4-xo34-tnb3-4nxl-cmhu',
				'editable' => '1',
				'label' => 'Ссылки (через запятую)',
		    ), array (
				'name' => 'search',
				'type' => 'kbd4-xo34-tnb3-4nxl-cmhu',
				'editable' => '2',
				'label' => 'Контент для поиска',
		    ), array (
				'name' => 'searchWeight',
				'type' => 'gklv-0ijh-uh7g-7fhu-4jtg',
				'editable' => '1',
				'label' => 'Важность для поиска',
		    ), array (
				'name' => 'noindex',
				'type' => 'fsxp-lhdw-ghof-1rnk-5bqp',
				'editable' => '1',
				'label' => 'Не индексировать',
				'help' => 'Добавляет мета-тэг NOINDEX в шапку страницы и запрещает ее к индексации в поисковых системах.',
		    ), array (
				'name' => 'beforeAction',
				'type' => 'kbd4-xo34-tnb3-4nxl-cmhu',
				'editable' => '1',
				'label' => 'Бизнес-логика (php)',
				'indexEnabled' => '0',
				'help' => 'PHP код, который выполнится до начала работы контроллера',
		    ),
		  ), 'indexes' => array (
		    array (
				'id' => '3fu0tfe07pu23kgitmlb7fg07fvs7k',
				'name' => 'hash-lang',
				'fields' => 'hash,lang',
				'type' => 'index',
		    ),
		  ),
		);
	}

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
		if(!$lang) {
		    $lang = lang::active()->id();
		}
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
	    return self::all()
			->eq("hash",$this->data("hash"))
			->eq("lang",$this->data("lang"))
			->neq("id",$this->id());
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
