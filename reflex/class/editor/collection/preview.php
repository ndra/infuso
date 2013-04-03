<?

/**
 * Режим просмотра - превьюшки
 **/ 
class reflex_editor_collection_preview extends reflex_editor_collection_view {

	public function title() {
	    return "Превью";
	}
	
	public function icon() {
	    return "images";
	}
	
	public function layout() {
		return "inx.layout.column";
	}
	
	/**
	 * Данные таблицы
	 **/
	public function inxData() {

	    // Сюда складываем данные
	    $ret = array();

		// Перебираем коллекцию и складываем данные
		foreach($this->collection()->editors() as $editor) {

		    $html = "";
		    $preview = file::get($editor->img())->preview(90,90)->crop();
			$html.= "<div style='text-align:center;background:url($preview) center top no-repeat;height:100px;' >";
		    $html.= "</div>";
		    
		    $html.= "<div style='text-align:center;font-size:11px;opacity:.7;' >";
		    $html.= $editor->item()->title();
			$html.= "</div>";

			$ret[] = array(
			    "id" => $editor->hash(),
			    "dblclick" => "edit/".$editor->hash(),
			    "width" => 100,
				"data" => array(
				    "text" => $html,
				),
			);

		}

		return $ret;
	}

}
