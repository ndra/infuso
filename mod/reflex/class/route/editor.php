<?

class reflex_route_editor extends mod_controller {

	public static function postTest() {
		return true;
	}

	/**
	 * Контроллер получения данных об адресе
	 **/
	public static function post_get($p) {
	
	    $editor = reflex_editor::byHash($p["index"]);
	    
	    if(!$editor->item()->reflex_route())
	        return;
	    
	    $obj = reflex_route_item::get(get_class($editor->item()).":".$editor->itemID());

	    if($obj->exists()) {
	    
	        $data = $obj->editor()->inxForm();
	        return array(
				"form" => $data,
			);
			
	    } else {
	    
	        $item = reflex_editor_controller::get($p["index"])->item();
	        return array(
	            "error" => "У данного объекта отсутствуют данные об адресе. Сейчас адрес объекта такой: <a target='_new' href='{$item->url()}'>{$item->url()}</a>"
	        );
	        
	    }
	    
	}

	/**
	 * Контроллер получения данных об адресе
	 **/
	public static function post_save($p) {
	
		$editor = reflex_editor::byHash($p["index"]);
		
		if(!$editor->beforeEdit()) {
		    mod::msg("Вы не можете редактировать метаданные этого объекта",1);
		    return;
		}
		
		$editor->setUrl($p["data"]["url"]);
		mod::msg("Роуты: данные сохранены");
	}

	/**
	 * Контроллер удаления адреса
	 **/
	public static function post_delete($p) {

		$editor = reflex_editor::byHash($p["index"]);
		
		if(!$editor->beforeEdit()) {
		    mod::msg("Вы не можете редактировать метаданные этого объекта",1);
		    return;
		}
		
		$obj = reflex_route_item::get(get_class($editor->item()).":".$editor->itemID());
		$obj->delete();
		
		mod::msg("Роут удален");
	}

}
