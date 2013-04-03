<?

/**
 * Контроллер истории заказов
 **/
class eshop_order_collection extends mod_behaviour {

	public function viewModes() {
	
	    $ret = array();
	
	    $ret[] = new eshop_order_collection_full($this->component());
	    
		$disabled = $this->editor()->getDisableItems();

	    if(!in_array("list",$disabled))
	        $ret[] = new reflex_editor_collection_list($this->component());
	    
	    return $ret;
	    
	}
	
}
