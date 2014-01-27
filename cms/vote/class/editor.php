<?


class vote_editor extends reflex_editor {

	/**
	 * @return array данные для отобрадения списка опросов в каталоге
	 **/
	public function renderListData() {

	    $txt = "";
	    $txt.= "<div style='padding:10px;' >";
	    if($this->item()->data("active"))
	        $txt.= "<span style='background:#3333ff;color:white;padding:4px;border-radius:4px;margin-right:7px;' >Автивно</span>";
	    $txt.= $this->item()->title();
	    $txt.= "<div>";

	    $txt.= "<style>.ab38vho8v td {border:1px solid #ccc;padding:4px;}</style>";

		$txt.= "<table class='ab38vho8v' >";
		foreach($this->item()->options()->desc("count") as $option) {
		    $txt.= "<tr>";
		    $txt.= "<td>{$option->title()}</td>";
		    $txt.= "<td>{$option->count()}</td>";
		    $txt.= "<td>{$option->percent()}%</td>";
		    $txt.= "</tr>";
		}
		$txt.= "</table>";

	    return array(
	        "text" => $txt,
	    );
	}
	
	public function root() {
        return array(
			vote::allEvenHidden()->title("Опросы")->param("tab","system"),
		);
	}
	
}
