<?

/**
 * Класс для задач-итераторов рефлекса
 **/
class reflex_task_reflex {

    public static function add($p) {
        reflex_task::add(array(
            "class" => get_class(),
            "method" => "execReflex",
            "params" => array(
                "class" => $p["class"],
                "method" => $p["method"],
                "query" => $p["query"],
                "params" => $p["params"],
            ),
        ));
    }

	/**
	 * Статический метод для задач-рефлексов
	 **/
    public static function execReflex($p,$task) {

		$query = 1;
		
        if($q = trim($p["query"])) {
        
	        if(($q*1).""==$q."") {
	            $q = " `id`='{$q}' ";
			}
			
			$query = $q;
        }
        
        $item = reflex::get($p["class"])
            ->asc("id")
            ->gt("id",$task->data("iterator"))
            ->where($query)
            ->one();
            
        if(!$item->exists()) {
            $task->data("completed",true);
            $task->store();
            return;
        }

        $task->data("iterator",$item->id());
        $task->store();

        $method = $p["method"];
        $params = $p["params"];
        
        $item->$method($params);

    }

}
