<?

/**
 * Модель города
 **/
class geo_country_editor extends reflex_editor {

	public function beforeEdit() {
	    return user::active()->checkAccess("geo:editCountry",array(
	        "country" => $this->item(),
		));
	}

}
