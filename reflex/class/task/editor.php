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
    
    public function disable() {
        return "list";
    }
    
	public function gridData() {
	    $data = parent::gridData();
	    
	    // Если были ошибки в течение часа, выводим строку коричневой
	    if(util::now()->stamp() - $this->item()->pdata("lastErrorDate")->stamp() <= 3600) {
		    $data["css"] = array(
		        "background-color" => "brown",
		        "color" => "white",
			);
		}
		
	    // Если были ошибки в течение 5 сек, выводим строку красной
	    if(util::now()->stamp() - $this->item()->pdata("lastErrorDate")->stamp() <= 5) {
		    $data["css"] = array(
		        "background-color" => "red",
		        "color" => "white",
			);
		}
		
	    return $data;
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

}
