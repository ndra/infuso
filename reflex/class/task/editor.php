<?

/**
 * редактор задачи
 **/
class reflex_task_editor extends reflex_editor {

    /**
     * Коллекция для каталога
     **/
    public function root() {
    
        if(mod_superadmin::check()) {
            return array(
                reflex_task::all()->title("Задачи")->param("tab","system"),
            );
		}
		
        return array();
    }

    public function filters() {
        return array(
            reflex_task::all()->eq("completed",0)->title("Активные"),
            reflex_task::all()->eq("completed",1)->title("Выполненные"),
        );
    }

    public function actions() {
        return array(
            array(
				"text" => "Выполнить",
				"action" => "exec",
			),
        );
    }

    public function action_exec() {
        $this->item()->exec();
    }

    public function renderListData() {
        $ret = "";
        $ret.= $this->item()->data("class");
        $ret.= "::";
        $ret.= $this->item()->data("method");
        $ret.= "() ";
        $ret.= "<i style='opacity:.5;' > where ";
        $ret.= $this->item()->data("query");
        $ret.= "</i>";
        $ret.= " — ".$this->item()->data("fromID");
        return $ret;
    }

}
