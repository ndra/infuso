<?

class eshop_item_editorBehaviour extends mod_behaviour {

	public function filters() {
	    return array(
	        eshop_item::all()->title("<b>Активные товары</b>"),
	        eshop_item::all()->eq("starred",1)->title("Избранные товары"),
	        eshop_item::all()->inverse()->title("Неактивные товары"),
	    );
	}

	public function img() {
		return trim($this->item()->photo()->path()," /");
	}

}
