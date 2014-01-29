<?

class reflex_defaultBehaviour extends mod_behaviour {

    public function behaviourPriority() {
        return - 1000;
    }

	/**
	 * @return Функция должна вернуть коллекцию или массив коллекций, которые будут
	 * выводиться на верхнем уровне каталога (в левом меню)
	 **/
	public function reflex_root() {
		return array();
	}

	/**
	 * @return Функция должна строку группы в левом меню каталога
	 **/
	public function reflex_rootGroup() {
		$class = get_class($this->component());
	    $mod = end(array_reverse(explode("_",$class)));
        $path = mod::service("bundle")->bundle($mod)->conf("title");
	    if(!$title) {
			$title = $mod;
		}
	    return $title;
	}

	/**
	 * @return Функция должна вернуть массив коллекций дочерних элементов данного объекта
	 **/
	public function reflex_children() {
		return array();
	}

	/**
	 * @return Функция должна вернуть url объекта
	 * По умолчанию, url объекта имеет вид /my_class_name/item/id/123
	 * Переопределите функцию, если у элемента должен быть другой url
	 **/
	public function reflex_url() {
		return null;
	}

	/**
	 * @return Функция должна вернуть таблицу mysql, связанную с классом
	 * Если функция возвращает символ @, то в качестве таблицы используется имя класса
	 **/
	public static function reflex_table() {
		return "@";
	}

	/**
	 * @return Функция должна вернуть родительский элемент
	 * Это должен быть существующий или не существующий объект reflex, либо null
	 * Родитель используется в каталоге для построения пути к объекту
	 **/
	public function reflex_parent() {
		return reflex::get("reflex_none",0);
	}

	public function reflex_title() {
        if(!$this->component()->exists()) return "";
        if($title = $this->component()->data($this->component()->reflex_titleField())){
            return $title;
        }    
        return get_class($this->component()).":".$this->component()->id();
    }

    public function reflex_titleField() {
        // перебираем поля до первого поля с именем title
        foreach($this->component()->fields() as $field) {
            if($field->name()=="title") {
                return $field->name();
            }
        }
        // перебираем поля до первого поля сторокогвого типа и возвращаем его имя
        foreach($this->component()->fields() as $field){
            if($field->typeID() == "v324-89xr-24nk-0z30-r243"){
                return $field->name();    
            }
        }
        
        return false;
    }

	public function reflex_cleanup() {
	}

	public function reflex_repair() {
	}

	public function reflex_repairClass() { return true; }

	// Быстрый подсчет элементов (считает приблизительно) для больших таблиц
	// По умолчанию — выключен
	public function reflex_fastCount() { return false; }

	/**
	 * Триггер, вызывающийся перед каждой поперацией создания, изменения или удаления
	 **/
	public function reflex_beforeOperation() {
	}

	/**
	 * Триггер, вызывающийся после каждой поперацией создания, изменения или удаления
	 **/
	public function reflex_afterOperation() {
	}

	public function reflex_classTitle() {
		return "";
	}

	public function reflex_published() {
		return $this->exists();
	}

	/**
	 * @return bool Есть ли у объекта метаданные? Работает автоматически, переопределять только в случае необъодимости.
	 **/
	public function reflex_meta() { return false; }

	/**
	 * @return bool Есть ли у объекта роут?
	 **/
	public function reflex_route() { return $this->component()->reflex_meta(); }

	public function reflex_search() {
		return "skip";
	}

	public function reflex_searchWeight() {
		return 1;
	}

	/**
	 * Возвращает код маленького поискового сниппета. Этот код будет использоваться в
	 * выводе поисковых подсказок при вводе запроса.
	 **/
	public function reflex_smallSearchSnippet() {
	    ob_start();
	    tmp::exec("reflex:search.smallSnippet",$this);
	    return ob_get_clean();
	}

	/**
	 * Возвращает код большого поискового сниппета.
	 * Этот код будет использоваться в
	 * выводе результатов поиска.
	 **/
	public function reflex_bigSearchSnippet() {
	    ob_start();
	    tmp::exec("reflex:searchResults.bigSnippet",$this);
	    return ob_get_clean();
	}

	/**
	 * Возвращает папку хранилища
	 **/
	public static function reflex_storageFolder() {
		return null;
	}

	/**
	 * @return Нужно ли использовать отдельную папку для каждого объекта
	 **/
	public static function reflex_storageUseMultipleFolders() {
		return true;
	}

	/**
	 * @return Какой объект использовать в качестве хранилища.
	 * Вы можете переопределить этот метод, чтобы, к примеру, все фотографии одной новости
	 * лежали в одной папке
	 **/
	public function reflex_storageSource() {
		return $this->component();
	}

	/**
	 * Триггер, вызывается перед просмотром хранилища из админки
	 **/
	public function reflex_beforeStorageView() {
		return $this->editor()->beforeView();
	}

	/**
	 * Триггер, вызывается перед изменением хранилища из админки
	 **/
	public function reflex_beforeStorageChange() {
		return $this->editor()->beforeEdit();
	}

	/**
	 * Триггер, вызывается после изменения файлов в хранилище
	 **/
	public function reflex_afterStorage() {
	}

	/**
	 * @return Возвращает домен, которому принадлежит этот объект, по умолчанию верент первый активный домен
	 **/
	public function reflex_domain() {
		return reflex_domain::get(0);
	}

}
