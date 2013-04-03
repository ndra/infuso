<?

/**
 * Поведение для поля таблицы
 **/
class reflex_table_fieldBehaviour extends mod_behaviour {

    /**
     * Сообщаем рефлексу об изменении поля
     **/
    public function afterFieldChange() {
        if($r = $this->reflexItem()) {
            $r->markAsDirty();
        }
    }

    public function conf() {
        $conf = array(
            "indexEnabled" => $this->component()->conf["indexEnabled"] ? 1 : 0,
        );
        return $conf;
    }

    /**
     * Возвращает имя поля в формате tableName.fieldName
     **/
    public function fullName() {
        return get_class($this->component()->model()).".".$this->name();
    }

    /**
     * Индексировать ли это поле
     **/
    public function indexEnabled() {

        if($this->name()=="id")
            return 0;

        $ret = $this->component()->param("indexEnabled");
        if($ret===null)
            $ret = true;

        return $ret*1;
    }


    /**
     * inx.конструктор дополнительных настроек поля
     **/
    public function inxConf() {

        $ret = array(
            "type" => "inx.form",
            "items" => array(),
            "style" => array(
                "padding" => 0,
                "border" => 0,
            ),
        );

        foreach($this->component()->extraConf() as $conf)
            $ret["items"][] = array(
                "name" => $conf["name"],
                "width" => "auto",
                "label" => $conf["label"],
                "value" => $this->component()->conf($conf["name"]),
            );

        return $ret;
    }

    /**
     * Устанавливает таблицу поля
     **/
    public final function setTable($table) {
        $this->param("table",$table);
    }

    /**
     * Возвращает таблицу поля
     **/
    public final function table() {
        return $this->param("table");
    }

     /**
     * Возвращает объект reflex, связанный с данным полем
     **/
    public function reflexItem() {
        $model = $this->model();
        return $model;
    }

    /**
     * Возвращает редактор поля
     **/
    public function editorInx() {
        return array(
            "type" => "inx.textfield",
            "value" => "".$this->component()->value(),
        );
    }

    /**
     * Редактор поля в режиме "только чтение"
     **/
    public function editorInxDisabled() {
        return array(
            "type" => "inx.mod.reflex.fields.readonly",
            "value" => $this->rvalue(),
        );
    }

    /**
     * inx - конструктор
     * Полный редактор поля со всеми настройками
     **/
    public final function editorInxFull() {
        
        if($this->editable()) {
            $ret = $this->component()->editorInx();
			
			/**
			 * У таблицы есть возможность пробрасывать дополнительные параметры в Inx-конструктор 
			 * редактора поля  используя параметр editorInx(для редактируемых) и editorInxDisabled (для не редактируемых)
			 * в видееассоциативногогого массива 
			 * например 'editorInx' => array("width"=>450) для текстового поля установит его ширину в 450px
			**/
            $editorInxParams = $this->param("editorInx"); //получаем все параметры для редактируемого поля
            if( is_array($editorInxParams) ){
                foreach($editorInxParams as $key=>$value){ //делаем пуш в возвращаемый массив
                     $ret[$key] = $value;    
                }
            }
        } else {
            $ret = $this->component()->editorInxDisabled();
            $editorInxDisabledParams = $this->param("editorInxDisabled"); //получаем все параметры для нередактируемого поля
            if( is_array($editorInxDisabledParams) ){
                foreach($editorInxDisabledParams as $key=>$value){ //делаем пуш в возвращаемый массив
                     $ret[$key] = $value;    
                }
            }
        }
        
        $ret["name"] = $this->name();
        $ret["label"] = $this->label() ?
            $this->label() :
            $this->name();
        $ret["help"] = $this->help();

        $key = get_class($this->component()->reflexItem()).":".$this->component()->reflexItem()->id();
        $ret["storage"] = $key;
        $ret["index"] = $key;

        return $ret;

    }

    /**
     * inx-конструктор фильтра для админки
     **/
    public function filterInx() {

        switch($this->component()->filterType()) {
            case "checkbox";
                return array(
                    "label" => $this->label(),
                    "name" => $this->fullName(),
                    "checkbox" => true,
                    "type" => "inx.mod.reflex.fieldFilters.string"
                );
                break;
            case "string";
                return array(
                    "label" => $this->label(),
                    "name" => $this->fullName(),
                    "type" => "inx.mod.reflex.fieldFilters.string"
                );
                break;
            case "number";
                return array(
                    "label" => $this->label(),
                    "name" => $this->fullName(),
                    "type" => "inx.mod.reflex.fieldFilters.range"
                );
                break;
            case "date";
                return array(
                    "label" => $this->label(),
                    "name" => $this->fullName(),
                    "type" => "inx.mod.reflex.fieldFilters.range",
                    "filterType" => "date",
                );
            case "datetime";
                return array(
                    "label" => $this->label(),
                    "name" => $this->fullName(),
                    "type" => "inx.mod.reflex.fieldFilters.range",
                    "filterType" => "datetime",
                );
                break;
        }
    }

    /**
     * Метод, применяющий фильтр поля к коллекции
     **/
    public function filterApply($list,$data) {

        switch($this->component()->filterType()) {
            case "number":
            case "date":
                if($from = $data["from"]) {
                    $list->geq($this->fullName(),$from,"date");
				}
                if($to = $data["to"]) {
                    $list->leq($this->fullName(),$to,"date");
				}
                break;
            case "datetime":
                if($from = $data["from"]) {
                    $list->geq($this->fullName(),$from);
				}
                if($to = $data["to"]) {
                    $list->leq($this->fullName(),$to);
				}
                break;
            case "string":
            case "checkbox":
                switch($data["op"]."") {
                    case "yes":
                        $list->neq($this->fullName(),"");
                        break;
                    case "no":
                        $list->eq($this->fullName(),"");
                        break;
                    case "q":
                        $list->like($this->fullName(),$data["q"]);
                        break;
                    case "=":
                        $list->eq($this->fullName(),$data["q"]);
                        break;
                }
                break;
        }

    }

    public function filterType() {
        return "string";
    }

    public function tableCol() {
        return array(
            width=>100
        );
    }

    public function tableRender() {
        $ret = $this->rvalue();
        $max = 1000;
        if(mb_strlen($ret)>$max)
            $ret = mb_substr($ret,0,$max-3,"utf-8")."...";

        $ret = trim($ret);
        $ret = htmlspecialchars($ret);
        if(!$ret) $ret = "<span style='color:gray;'>&mdash;</span>";
        return $ret;
    }

}
