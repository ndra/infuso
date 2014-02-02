<?

/**
 * Inx-компоненты для редактора элемента
 **/
class reflex_editor_behaviourInx extends mod_behaviour {

    /**
     * Ставим стандартному поведению низкий приоритет, чтобы можно было его переназначить
     **/
    public function behaviourPriority() {
        return -2;
    }

    /**
     * Включить ли панель с комментариями в редакторе элемента
     **/
    public function showComments() {
        return false;
    }

    /**
     * @return Основной компонент редактирования с вкладками
     **/
    public function inxEditor() {

        $ret = array(
            "type" => "inx.mod.reflex.editor.item.tabs",
            // Имя класса нужно для сохранения активного таба при редактировании объектов данного класса
            "className" => get_class($this->component()),
            "items" => array(),
        );

        if($fields = $this->component()->inxFieldsTab()) {
            $ret["items"][] = $fields;
        }

        foreach($this->component()->inxExtraTabs() as $tab) {
            $ret["items"][] = $tab;
		}

        if($this->item()->reflex_meta()) {
            $ret["items"][] = array(
                "type" => "inx.mod.reflex.meta",
                "title" => "Мета",
                "name" => "meta",
                "lazy" => true,
                "index" => $this->hash(),
            );
        }

        foreach($this->item()->childrenWithBehaviours() as $key=>$list) {
            $list->addBehaviour("reflex_editor_collection");
            $ret["items"][] = $list->editor()->inxList($list);
        }

        if(get_class($this->item())!="reflex_log" && $this->component()->log()) {
            $ret["items"][] = array(
                "type" => "inx.mod.reflex.log",
                "title" => "Журнал",
                "name" => "log",
                "lazy" => true,
                "index" => $this->hash(),
            );
        }

        return $ret;
    }

    public function inxBeforeForm() {
        return null;
    }

    public function inxFormCollections() {
        return array();
    }

    /**
     * @return inx-конструктор элемента, который выводится до формы редактирования
     **/
    public function inxFormCollectionsConstructor() {

        $items = array();

        foreach($this->component()->inxFormCollections() as $key=>$list) {
            $list->addBehaviour("reflex_editor_collection");
            $inx = $this->component()->inxList($list);
            $inx["style"]["border"] = 1;
            $inx["style"]["height"] = "content";
            $inx["title"] = "<div style='font-size:18px;font-weight:bold;' >".$inx["title"]."</div>";
            $inx["lazy"] = false;
            $items[] = $inx;
        }

        if(!sizeof($items))
            return null;

        return array (
            "type" => "inx.panel",
            "style" => array(
                "border" => 0,
                "spacing" => 20,
                "background" => "none",
                "height" => "content",
            ),
            "items" => $items,
        );
    }

    /**
     * @return inx-конструктор вкладки «Редактирования»
     * Если функция вернет null, вкладка «Редактирование» не создается
     **/
    public function inxFieldsTab() {

        $ret = array(
           "type" => "inx.mod.reflex.editor.item.fields",
           "index" => $this->hash(),
           "name" => "fields",
           "actions" => $this->component()->actions(),
           "keepLayout" => "y1bnpv0oce0q",
           "items" => array(),
        );

		// Дополнительный inx-компонент перед формой
        if($inx = $this->component()->inxBeforeForm()) {
            $ret["items"][] = $inx;
        }

		// Инлайн-коллекции
        if($inx = $this->component()->inxFormCollectionsConstructor()) {
            $ret["items"][] = $inx;
        }

        $ret["items"][] = $this->component()->inxForm();

		// Комментарии сразу на странице объекта
        if($this->component()->showComments()) {
            $ret["side"][] = array(
                "type" => "inx.mod.reflex.log",
                "index" => $this->hash(),
                "width" => 150,
                "resizable" => true,
                "region" => "right",
            );
        }

        $ret["toolbar"] = $this->component()->inxItemToolbar();

        return $ret;
    }

    public function inxItemToolbar() {

        $ret = array(
            "save",
            "actions",
            "|",
            "delete",
        );

        $ret2 = array();
        $disabled = $this->getDisableItems($list);

        foreach($ret as $button)
            if(!in_array($button,$disabled))
                $ret2[] = $button;

        return $ret2;

    }

    /**
     * Возвращает форму редактирования объекта
     * @param array Конфигурация
     * @return array Масив с конструктором inx
     **/
    public function inxForm($p = array()) {

        $ret = array(
            "type" => "inx.panel",
            "items" => array(),
            "style" => array(
                "titleMargin" => 10,
                "padding" => 0,
                "border" => 0,
                "spacing" => 20,
                "background" => "none",
            )
        );

        $groups = array();

        $usedFields = array();

        $hidden = false;

        $n = 0;

        foreach($this->item()->table()->fieldGroups() as $group) {

            //Если в настройках указана группа, то пропускаем другие группы
            if (array_key_exists("group", $p) && $p["group"] != $group->name()) {
                continue;
            }
            
            $items = array();

            foreach($this->fields() as $field) {
                if($field->group()==$group->name() && !in_array($field->name(),$usedFields)) {
                    if($field->editable() || ($field->readonly() && $this->item()->exists()) ) {
                        $items[] = $field->editorInxFull();
                        $usedFields[] = $field->name();
                    }
                }
            }

            if(!sizeof($items)) {
                continue;
            }
            
            $groupTitle = $group->title();

            $group = array(
                "type" => "inx.form",
                "title" => "<b style='font-size:16px;' >{$groupTitle}</b>",
                "items" => $items,
                "hidden" => $hidden,
                "style" => array(
                    "border" => 0,
                    "padding" => 00,
                    "spacing" => 15,
                    "background" => "none",
                ),
                "lazy" => $hidden
            );

            $groups[] = $group;
            $hidden = true;
            $n++;

        }

        foreach($groups as $group)
            $ret["items"][] = $group;

        return $ret;
    }
    
    public function inxConstructorForm() {
		return $this->component()->inxForm();
    }

    /**
     * @return bool
     * Можно ли выделять текст в списке элементов
     **/
    public function inxListSelectable() {
        return false;
    }

    /**
     * @return inx-конструктор списка элементов
     **/
    public function inxList($list) {

        $viewModes = array();
        foreach($list->viewModes() as $mode)
            $viewModes[] = array(
                "id" => get_class($mode),
                "title" => $mode->title(),
                "icon" => $mode->icon(),
            );

        $ret = array(
            "type" => "inx.mod.reflex.editor.list",
            "enableTextSelection" => $list->editor()->inxListSelectable(),
            "listData" => $list->serialize(),
            "itemClass" => $list->itemClass(),
            "title" => $list->title()." ({$list->count()})",
            "lazy" => true,
            "actions" => $list->editor()->actions(),
            "toolbar" => $list->editor()->inxListToolbar($list),
            "viewModes" => $viewModes,
            "height" => "parent"
        );

        $ret["side"] = array();
        foreach($this->component()->inxListSide() as $panel)
            $ret["side"][] = $panel;


        return $ret;
    }

    public function inxListSide() {
        return array();
    }

    /**
     * Возвращает массив параметров тулбара
     * Используется в методе inxList()
     **/
    public final function inxListToolbar($list) {

        $ret = array();

        $ret[] = "add";
        if($this->component()->uploadsEnabled()) {
            $ret[] = "upload";
        }
        $ret[] = "edit";
        $ret[] = "actions";
        if($list->param("sort")) {
            $ret[] = "up";
            $ret[] = "down";
        }
        $ret[] = "|";
        if($this->component()->quickSearch()) {
            $ret[] = "search";
        }
        $ret[] = "filter";
        $ret[] = "pager";
        if(sizeof($this->component()->filters()) > 0) {
            $ret[] = "filters";
        }
        $ret[] = "|";
        if(sizeof($list->component()->viewModes())>1) {
            $ret[] = "view";
        }
        $ret[] = "refresh";
        $ret[] = "|";
        $ret[] = "delete";


        $ret2 = array();
        $disabled = $this->getDisableItems($list);

        foreach($ret as $button)
            if(!in_array($button,$disabled))
                $ret2[] = $button;

        return $ret2;

    }

    /**
     * Возвращает inx-конструктор фильтра (выводится в админке справа от списка)
     **/
    public function inxFilter($list) {

        $groups = array();
        foreach($this->component()->fields() as $field)
            if($field->visible()) {
                $groupName = $field->group();
                if(!$groups[$groupName]) {

                    $hidden = count($groups)>0 && $groupName;

                    $group = array(
                        "type" => "inx.form",
                        "title" => $groupName,
                        "items" => array(),
                        "hidden" => $hidden,
                        "style" => array(
                            "border" => 0,
                            "background" => "none",
                        ),
                        "lazy" => $hidden
                    );
                    $groups[$groupName] = $group;
                }

                $f = $field->filterInx();
                $f["labelAlign"] = "left";
                $f["labelWidth"] = 100;
                $groups[$groupName]["items"][] = $f;
            }

        $items = array();
        foreach($groups as $group)
            $items[] = $group;

        $ret = array(
            "type" => "inx.mod.reflex.editor.list.filter",
            "region" => "right",
            "items" => $items
        );
        return $ret;
    }

}
