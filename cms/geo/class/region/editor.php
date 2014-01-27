<?

/**
 * Модель города
 **/
class geo_region_editor extends reflex_editor {

	public function beforeEdit() {
	    return user::active()->checkAccess("geo:editRegion",array(
	        "region" => $this->item(),
		));
	}

}
