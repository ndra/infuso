<?

class eshop_order_item_editor extends reflex_editor {

	public function itemClass() {
	    return "eshop_order_item";
	}

	public function disable() {
	    return "list";
	}
	
    public function beforeEdit() {
        return user::active()->checkAccess("eshop:editOrderItem",array(
            "order" => $this->item(),
		));
    }

}
