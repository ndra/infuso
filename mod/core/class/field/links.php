<?

class mod_field_links extends mod_field_link {

    public function typeID() {
        return "car3-mlid-mabj-mgi3-8aro";
    }

    public function typeName() {
        return "Список ссылок";
    }

    public function mysqlType() {
        return "longtext";
    }

    public function mysqlIndexType() {
        return "";
    }

    public function mysqlIndexFields() {
        return $this->name()."(1)";
    }

    public function editorInx() {
        return array(
            "type" => "inx.mod.reflex.fields.links",
            "value" => $this->value(),
        );
    }

    public function pvalue() {

        $ids = array();
        foreach(explode(" ",$this->value()) as $id)
            $ids[] = $id*1;

        $ret = $this->items()->eq("id",$ids);
        $ret->setPrioritySequence($ids);
        return $ret;
    }

    // Редактор поля в режиме "только чтение"
    public function editorInxDisabled() {
        /*$txt = $this->rvalue();
        $item = $this->pvalue();
        if($item->exists())
            $txt = "<a href='{$item->editor()->url()}' >$txt</a>"; */

        $txt = $this->value();
        return array(
            "type" => "inx.mod.reflex.fields.readonly",
            "value" => $txt,
        );
    }

    /**
     * Человекопонятное значение поле: заголовки элементов, разделенные запятыми
     **/
    public function rvalue() {
        $ret = array();
        foreach($this->component()->pvalue() as $item)
            $ret[] = $item->title();
        return implode(", ",$ret);
    }

    public function tableRender() {
        /*$ret = array();
        foreach($this->pvalue() as $a)
            $ret[] = $a->title();
        return implode(",",$ret); */

        return $this->value();
    }

    public function prepareValue($val) {

        // Если передана строка с json, преобразуем ее в массив (Для совместимости со старыми версиями)
        if(is_string($val))
            if(preg_match("/(\[)|(\{)/",$val)) {
                $val = @json_decode($val,1);
            }

        // Строку преобразуем в массив
        if(is_string($val)) {
            $val = explode(" ",$val);
        }

        // Массив преобразуем в строку
        if(is_array($val)) {
            $ret = array();
            foreach($val as $b) {
                if(!is_scalar($b))
                    continue;
                $b*=1;
                if($b>0) {
                    $b = str_pad($b,5,"0",STR_PAD_LEFT);
                    $ret[] = $b;
                }
            }
            $ret = array_unique($ret);
            $val = implode(" ",$ret);
        }

        return $val;
    }

}
