<?

/**
 * Класс-поведение для создения отборов и сортировок в каталоге
 **/ 
class reflex_editor_collection extends mod_behaviour {

	private $viewMode = null;
	
	public function behaviourPriority() {
	    return -1;
	}

	public function applyParams($p) {

	    // Чит
		// @todo Убрать при рефакторинге
	    $p["view"] = $p["viewMode"];
	    
	    $this->setViewMode($p["view"]);
	    
        // Учитываем поиск
        $s = trim(mb_strtolower($p["quickSearch"],"utf-8"));
        if($s) {
            $this->editor()->applyQuickSearch($this->component(),$s);
        }
            
		// Запоминаем коллекцию до наложения фильтров
		// Это потребуется при расчете количества элементов в разных фильтрах
		$this->param("collectionBeforeFilter",$this->copy());

        // Фильтры
        $this->useFilter($p["filters"]);

        // Учитываем фильтр
        $this->editor()->applyFilter($this->component(),$p["filter"]);
        
		// Выбираем страницу
	    $this->page($p["page"]);
	
	}
	
	/**
	 * Возвращает массив режимов просмотра
	 * Элемент массива - объект
	 **/
	public function viewModes() {
	
	    $disabled = $this->editor()->getDisableItems();
	    
	    if(!in_array("list",$disabled))
	        $ret[] = new reflex_editor_collection_list($this->component());

	    if(!in_array("grid",$disabled))
	        $ret[] = new reflex_editor_collection_grid($this->component());
	        
	    if(!in_array("preview",$disabled))
	        if($this->editor()->previewModeEnabled())
	        	$ret[] = new reflex_editor_collection_preview($this->component());
	        
		return $ret;
	    
	}
	
	/**
	 * Устанавливает режим прсмотра
	 * @param $string $class
	 **/
	public function setViewMode($class) {
	    $this->viewMode = $class;
	}
	
	/**
	 * Возвращает активный режим просмотра коллекции
	 **/
	public function viewMode() {
	
	    foreach($this->component()->viewModes() as $mode)
	        if(get_class($mode)==$this->viewMode)
	            return $mode;
	
	    $mode = end(array_reverse($this->component()->viewModes()));
	    return $mode;
	}
	
	/**
	 * Возвращает данные списка для inx-компонента
	 **/
	public function inxData() {
	
	    $mode = $this->viewMode();
	    $ret = $mode->inxDataFull();
	    
		// Количество страниц
	    $ret["pages"] = $this->pages();
	    
	    // Нижний текст
		$ret["bbar"] = $this->editor()->bbar($this->component());
		
		$ret["serialized"] = $this->serialize();
		
	    // Формируем список быстрых фильтров
	    if($filters = $this->editor()->filters()) {
	        foreach($filters as $key=>$filter) {
	        
				// Определяем количество элементов под фильтром
				$n = $this->param("collectionBeforeFilter")->copy()->useFilter($key)->count();
	            
	            $ret["filters"][] = array(
					"id" => $key,
					"data" => array (
						"text" => $filter->title()."&nbsp;($n)",
					)
				);
	        }
	    }
	    
	    $ret["sortable"] = $this->param("sort");
	    
	    return $ret;
	}

}
