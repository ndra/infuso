<?

class mod_field_array extends mod_field {

    public function typeID() {
        return "puhj-w9sn-c10t-85bt-8e67";
    }

    public function typeName() {
        return "Массив";
    }

    public function mysqlType() {
        return "blob";
    }

    public function mysqlIndexFields() {
        return $this->name()."(1)";
    }

    public function editorInx() {
        return array(
            "type" => "inx.mod.reflex.fields.arr",
            "value" => $this->value(),
        );
    }

    public function pvalue() {
        $ret = json_decode($this->value(),1);
        if(!is_array($ret)) {
            $ret = array();
        }
        return $ret;
    }

    public function prepareValue($val) {
        if(is_array($val)) {
            $val = json_encode($val);
        }
        return $val;
    }

    public function tableRender() {
        $ret = array();
        foreach($this->pvalue() as $key=>$val) {
            $ret[] = $key.":".$val;
        }
        return implode(", ",$ret);
    }

}
