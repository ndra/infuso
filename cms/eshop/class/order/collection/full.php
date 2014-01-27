<?

/**
 * Контроллер истории заказов
 **/
class eshop_order_collection_full extends reflex_editor_collection_view {

	public function title() {
	    return "Подробно";
	}
	
	public function icon() {
	    return "list";
	}
	
	/**
	 * Данные списка
	 **/
	public function inxData() {
	
	    // Сюда складываем данные
	    $ret = array();

		// Перебираем коллекцию и складываем данные
		foreach($this->collection()->editors() as $editor) {
		
			// Вызываем метод редактора для получения данных
		    $data = $editor->renderFullData();
		    
		    // Если данные - строка, преобразуем в массив
		    if(!is_array($data))
		        $data = array(
		            "text" => $data
				);

			// Складываем элемент
		    $ret[] = array(
		        "id" => $editor->hash(),
		        "dblclick" => "edit/".$editor->hash(),
		        "data" => $data,
			);
			
		}
		
		return $ret;
	}

}
