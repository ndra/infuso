<?

class mod_field_link extends mod_field {


    public function typeID() {
        return "pg03-cv07-y16t-kli7-fe6x";
    }

    public function typeName() {
        return "Внешний ключ";
    }

    public function mysqlType() {
        return "bigint(20)";
    }

    public function mysqlIndexType() {
        return "index";
    }

    public function pvalue() {
        if(trim($this->conf("foreignKey"))) {
            return reflex::get($this->itemClass())->eq(trim($this->conf("foreignKey")),$this->value())->one();    
        } else {
            return reflex::get($this->itemClass(),$this->value());
        }
    }

    // возвращает имя поля внешнего ключа для связи, если оно не задано используется id
    public function foreignKey() {
        if( trim($this->conf("foreignKey")) ){
            return trim($this->conf("foreignKey"));
        }else{
            return "id";
        }
    }

    public function rvalue() {
        return $this->pvalue()->title();
    }

    public function prepareValue($val) {
        return intval($val);
    }

    public function tableRender() {
    
        $item = $this->pvalue();
        if($item->exists())
            return $item->title();
            
        return "";
            
    }

    public function items() {

        $fn = trim($this->conf("collection"));

        if($fn)
            return $this->reflexItem()->$fn();

        $items = reflex::get($this->itemClass())->limit(100);
        return $items;
    }

    public function editorInx() {
        return array(
            "type" => "inx.mod.reflex.fields.link",
            "value" => $this->value(),
            "text" => $this->pvalue()->exists() ? $this->pvalue()->title() : "",
            "className" => $this->itemClass(),
        );
    }

    // Редактор поля в режиме "только чтение"
    public function editorInxDisabled() {
        $txt = $this->rvalue();
        $item = $this->pvalue();
        if($item->exists())
            $txt = "<a href='{$item->editor()->url()}' >$txt</a>";
        return array(
            "type" => "inx.mod.reflex.fields.readonly",
            "value" => $txt,
        );
    }

    public function filterInx() {
        return array(
            "label" => $this->label(),
            "name" => $this->fullName(),
            "type" => "inx.combo",
            "labelAlign" => "top",
            "width" => 194,
            "loader" => array(
                "name" => $this->name(),
                "cmd" => "reflex/editor/fieldController/getListItems",
                "index" => get_class($this->reflexItem()).":0"
            )
        );
    }

    public function filterApply($list,$data) {
        if($data)
            $list->eq($this->fullName(),$data);
    }

    /**
     * Возвращает / устанавливает имя класса объектов
     **/
    public function itemClass($class=null) {

        if(func_num_args()==0) {
            return $this->conf("class");
        }

        if(func_num_args()==1) {
            $this->conf("class",$class);
            return $this;
        }
    }

    public function className() {
        return call_user_func_array(array($this,"itemClass"),func_get_args());
    }

    /**
     * Возвращает / устанавливает метод, возвращающий заголовок элементов
     **/
    public function itemTitleMethod($method=null) {

        if(func_num_args()==0) {
            return $this->conf("titleMethod");
        }

        if(func_num_args()==1) {
            $this->conf("titleMethod",$method);
            return $this;
        }
    }

    public function extraConf() {
        return array(
            array(
                "name" => "class",
                "label" => "Класс объектов",
                "itWasParam" => true,
            ),array(
                "name" => "foreignKey",
                "label"=> "Имя внешнего ключа для связи"
            ),array(
                "name" => "collection",
                "label" => "Метод обьекта, возвращающий коллекцию",
            ),array(
                "name" => "titleMethod",
                "label" => "Метод редактора элемента, возвращающий название",
            ),
        );
    }

}
