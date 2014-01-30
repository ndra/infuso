<?

class eshop_vendor_editor extends reflex_editor {

	public function beforeEdit() {
	    return user::active()->checkAccess("eshop:editVendor",array(
	        "vendor" => $this->item(),
		));
	}

    public function filters() {
        return array(
            eshop_vendor::all()->title("<b>Активные</b>"),
            eshop_vendor::all()->inverse()->title("Неактивные"),
        );
    }

}
