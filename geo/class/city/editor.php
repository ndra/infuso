<?

/**
 * Модель города
 **/
class geo_city_editor extends reflex_editor {

	public function beforeEdit() {
	    return user::active()->checkAccess("geo:editCity",array(
	        "city" => $this->item(),
		));
	}

}
