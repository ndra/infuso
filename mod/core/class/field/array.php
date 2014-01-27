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

	/**
	 * Редактор в режиме «Только чтение»
	 **/
    public function editorInxDisabled() {

        $html = "";
        $html.= "<table>";
        foreach($this->pvalue() as $key=>$val) {
            $html.= "<tr>";
            $val = util::str($val)->esc()->ellipsis(200);
            $html.= "<td><b>{$key}</b></td>";
            $html.= "<td>{$val}</td>";
            $html.= "</tr>";
        }
        $html.= "</table>";

        return array(
            "type" => "inx.panel",
            "style" => array("padding" => 10),
            "html" => $html,
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
