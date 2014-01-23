<?

/**
 * Режим просмотра - список
 **/ 
class reflex_editor_collection_list extends reflex_editor_collection_view {


	public function title() {
	    return "Список";
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
		    $data = $editor->renderListData();

		    // Если данные - строка, преобразуем в массив
		    if(!is_array($data))
		        $data = array(
		            "data" => array(
		            	"text" => $data
		            ),
				);
				
            $data["id"] = $editor->hash();
			$data["dblclick"] = "edit/".$editor->hash();
			
			// Складываем элемент
		    $ret[] = $data;
		}
		
		return $ret;
	}

}
