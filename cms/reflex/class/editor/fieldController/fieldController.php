<?

/**
 * Контроллер для получения полям своих данных
 * Вызывается полями из формы редактирвоание объекта в reflex_editor_controller
 **/
class reflex_editor_fieldController extends mod_controller {

    public function postTest() {
        return user::active()->checkAccess("admin:showInterface");
    }

    /**
     * Контроллер
     * Возвращает список опций для селекта
     **/
    public function post_getSelectOptions($p) {
        
        // Определяем последовательность
        $editor = Infuso\Cms\Reflex\Controller::byOldIndex($p["index"])->editor();

		// Для получения списка элементов достаточно права просматривать коллекцию
        if(!$editor->beforeCollectionView()) {
            return array();
        }

        $ret = array();
        foreach($editor->item()->fields() as $field) {
            if($field->name()==$p["name"]) {
                foreach($field->options() as $key=>$val) {
                    $ret[] = array("id"=>$key,"text"=>$val);
                }
            }
        }
        return $ret;

    }

    /**
     * Контроллер
     * Возвращает список типов
     **/
    public function post_getFieldTypes() {
        $ret = array();
        foreach(mod_field::all() as $field)
            $ret[] = array(
                "id" => $field->typeID(),
                "text" => $field->typeName(),
            );
        return $ret;
    }

    /**
     * Возвращает список объектов для полей типа "Внешний ключ" и "Список ссылок"
     **/
    public static function post_getListItems($p) {

        // Определяем последовательность
        $editor = reflex_editor_controller::byOldIndex($p["index"])->editor();
        $sourceItem = $editor->item();

        $field = $sourceItem->field($p["name"]);
        if(!$field->exists()) {
            mod::msg("reflex_type_link::post_getAll - sequence not found",1);
            return array();
        }
        
        $items = $field->items()->limit(100);
        
        if(!$items->editor()->beforeCollectionView()) {
            mod::msg("Ошибка доступа к просмосмотру списка ".get_class($items->editor()),1);
            return array();
        }

        // Учитываем поиск
        $quick = $items->editor()->quickSearch();
        if(!$quick) {
            $quick = "title";
        }
        if($quick && trim($p["search"])) {
            $items->like($quick,trim($p["search"]));
        }

        // Постраничная навигация
        $items->page($p["page"]);

        // Если передан список id
        if(array_key_exists("ids",$p)) {
            $items->eq("id",$p["ids"]);
            $items->setPrioritySequence($p["ids"]);
        }

        // Строим список объектов
        $ret = array();
        $fn = trim($field->itemTitleMethod());
        foreach($items as $item) {
        
            if($fn) {
                $title = $item->editor()->$fn();
            } else {
                $title = $item->title();
			}
                
            $ret["data"][] = array (
                "id" => $item->data($field->foreignKey()),
                "data" => array (
                    "text" => $title,
                    "url" => $item->editor()->url(),
                )
            );
        }

        $ret["pages"] = $items->pages();

        return $ret;
    }

    /**
     * @todo сделать по аналогии с post_getListItems()
     **/
    public function post_listEditURL($p) {

        // Определяем последовательность
        $editor = reflex_editor_controller::byOldIndex($p["index"])->editor();
        $sourceItem = $editor->item();

        if(!$sourceItem->editor()->beforeView()) {
            mod::msg("Ошибка доступа",1);
            return array();
        }

        $field = $sourceItem->field($p["name"]);
        if(!$field->exists()) {
            mod::msg("reflex_type_link::post_getAll - sequence not found",1);
            return array();
        }

        $item = $field->items()->eq("id",$p["itemID"])->one();

        return $item->editor()->url();

    }

    /**
     * Экшн возвращает список виджетов для типа поля "Виджет"
     **/
    public static function post_listWidgets($p) {

        $ret = array();

        $ret[] = array(
            "id" => "",
            "text" => "&mdash;"
        );

        foreach(tmp_widget::all() as $widget) {
            $ret[] = array(
                "id" => get_class($widget),
                "text" => $widget->name(),
            );
        }

        return $ret;
    }

}
