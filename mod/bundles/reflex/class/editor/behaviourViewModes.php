<?

/**
 * Стандартное поведение для редактора элемента
 * В этом поведении описываются режимы просмотра (список, таблица и т.п.)
 **/
class reflex_editor_behaviourViewModes extends mod_behaviour {

    /**
     * Ставим стандартному поведению низкий приоритет, чтобы можно было его переназначить
     **/
    public function behaviourPriority() {
        return -2;
    }

    /**
     * @return bool Включен ли режим "Превью"
     **/
    public function previewModeEnabled() {

        if($this->component()->fields()->type("knh9-0kgy-csg9-1nv8-7go9")->exists())
            return true;

        if($this->component()->fields()->type("f927-wl0n-410x-4grx-pg0o")->exists())
            return true;

        return false;
    }

    /**
     * Данные списка
     **/
    public function renderListData() {
        return array(
            "data" => array(
                "text" => $this->item()->reflex_title(),
            ),
            "dblclick" => "edit/".get_class($this->item())."/".$this->item()->id(),
        );
    }

    public function render() {
        return $this->item()->title();
    }

    /**
     * Функция возвращает список полей для табличного режима в таблице
     **/
    public function gridFields() {
    
        $ret = array();
        $fields = $this->item()->fields();
        foreach($fields as $field) {
            if($field->editable() || $field->readonly()) {
                $ret[] = $field;
            }
		}

        return $fields->copyBehaviours($ret);
    }

    public function gridColWidth() {
        return array();
    }

    public function gridColCss() {
        return array(
            "id" => array(
                "color" => "gray",
                "font-style" => "italic",
            )
        );
    }

    /**
     * Возвращает настройки колонок
     **/
    public function gridCols() {

        $ret = array();

        $colWidth = $this->component()->gridColWidth();

        foreach($this->component()->gridFields() as $field) {

            $col = $field->tableCol();
            $col["name"] = $field->name();
            $col["title"] = $field->label() ? $field->label() : $field->name();

            if($width = $colWidth[$field->name()]) {
                $col["width"] = $width;
            }

            $ret[] = $col;
        }

        return $ret;
    }

    /**
     * Возвращает данные строки для табличного вида
     **/
    public function gridData() {

        $colCss = $this->component()->gridColCss();

        $row = array();
        foreach($this->component()->gridFields() as $field) {
            $row[$field->name()] = array(
                "text" => $field->tableRender(),
                "dblclick" => "editcell/{$this->hash()}/{$field->name()}",
            );

            if($css = $colCss[$field->name()])
                foreach($css as $key=>$val)
                    $row[$field->name()]["css"][$key] = $val;

        }

        return $row;

    }

}
