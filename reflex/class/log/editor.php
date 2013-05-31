<?

/**
 * Редактор лога
 **/
class reflex_log_editor extends reflex_editor {

    public function root() {
        return array(
			reflex_log::all()->title("Журнал")->param("tab","system")->icon("log")
		);
    }

    public function renderListData() {
        return array(
            "text" => tmp::get("/reflex/admin/log/item")->param("log",$this->item())->getContentForAjax(),
        );
    }
    
	/**
     * Редактировать записи в логе могут только суперадмины
     **/
    public function beforeEdit() {
        return user::active()->checkAccess("reflex:editLog");
    }
    
    /**
     * Редактировать записи в логе могут только суперадмины
     **/
    public function beforeView() {
        return user::active()->checkAccess("reflex:viewLog");
    }
    
	public function beforeCollectionView() {
        return user::active()->checkAccess("reflex:viewLog");
    }
    
    public function disable() {
        return "grid";
    }
    
}
