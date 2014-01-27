<?

/**
 * Режим просмотра - список
 **/ 
abstract class reflex_editor_collection_view {

	public function __construct($list) {
	    $this->collection = $list;
	}
	
	public function collection() {
	    return $this->collection;
	}
	
	abstract public function title();
	
	abstract public function icon();
	
	public function layout() {
		return "inx.layout.default";
	}

	/**
	 * Возвращает массив данных списка, который будет передан компоненту на основе inx.list
	 * Вызывается методом reflex_editor_collection::inxData();
	 **/
	public function inxDataFull() {
	
	    // Сюда складываем данные
	    $ret = array(
	        "data" => array(),
			"layout" => $this->layout(),
			"cols" => $this->cols(),
		);
		
		foreach($this->inxData() as $item)
		    $ret["data"][] = $item;

		return $ret;
	}
	
	/**
	 * Возвращает данные для колонок таблицы
	 **/
	public function cols() {
	    return false;
	}
	
	abstract public function inxData();

}
