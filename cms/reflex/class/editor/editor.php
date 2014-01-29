<?

abstract class reflex_editor extends mod_component {

    private $item;

    /**
     * Поведения по умолчанию
     **/
    public function defaultBehaviours() {
        return array(
            "reflex_editor_behaviourDefault",
            "reflex_editor_behaviourInx",
            "reflex_editor_behaviourViewModes",
        );
    }

    public function title() {
        return $this->item()->title();
    }

    /**
     * Конструктор
     **/
    public function __construct($itemID=null) {

        if(is_object($itemID)) {
            $this->item = $itemID;
        } else {
            $this->item = reflex::get($this->itemClass(),$itemID);
        }

    }

    /**
     * Возвращает хэш эдитора (строку, определяющую класс редактора и id редактируемого объекта)
     **/
    public function hash() {
        return get_class($this).":".$this->item()->id();
    }

    public static function byHash($hash) {

        //$hash = mod::base64URLDecode($hash);
        list($editorClass,$itemID) = explode(":",$hash);

        if(!mod::service("classmap")->testClass($editorClass,"reflex_editor")) {
            $editorClass = "reflex_none_editor";
        }

        $editor = new $editorClass($itemID);

        return $editor;

    }

    public function itemID() {
        return $this->item()->id();
    }

    public function root() {
        return array();
    }

    /**
     * Возвращает класс редактируемого элемента
     **/
    public function itemClass() {
        return preg_replace("/\_editor$/","",get_class($this));
    }

    /**
     * Возвращает редактируемый объект reflex
     **/
    public final function item() {
        return $this->item;
    }

    // Возвращает url для редактирвоания объяекта
    public function _url($p=array()) {
        $url = mod::action("reflex_editor_controller")->params($p)->url();
        return $url."#".$this->hash();
    }

    /**
     * Триггер, вызывающийся перед просмотром коллекции
     * Контекст в этом случае - виртуальный элемент коллекции
     **/
    public function beforeCollectionView() {
        return $this->component()->beforeEdit();
    }

    /**
     * Триггер, вызывающийся перед просмотром элемента
     **/
    public function beforeView() {

        if(!$this->item()->exists())
            return;

        return $this->component()->beforeEdit();
    }

    /**
     * Триггер, вызывающийся перед редактированием элемента
     * Редактирование элемента - любые изменения объекта через каталог
     **/
    public function beforeEdit() {
        return user::active()->checkAccess("reflex:editItem",array(
            "editor" => $this->component(),
        ));
    }
    
    public function _afterChange() {}

    /**
     * Триггер, вызывающийся перед удалением элемента через каталог
     **/
    public function beforeDelete() {
        return $this->component()->beforeEdit();
    }

    /**
     * Триггер, вызывающийся перед созданием элемента через каталог
     * Контекст - виртуальный объект
     **/
    public function beforeCreate($data) {
        return $this->component()->beforeEdit();
    }

    public function afterCreate() {}

    /**
     * Возвращает массив дополнительных вкладок для редактора элемента в каталоге
     * Каждый элемент массива - массив inx-конструктор
     * По умолчанию метожд вызывает такие же методы всех поведений и объединяет результат
     * Таким образом, вы можете определить эту функцию в классе-поведении и добавить
     * новые дополнительные вкладки в редактор элемента
     **/
    public function inxExtraTabs() {
        return $this->callBehaviours("inxExtraTabs");
    }

    /**
     * Возвращает массив действий с объектом
     **/
    public function actions() {
        return $this->callBehaviours("actions");
    }
    
    public function tab() {
        return "";
    }

    /**
     * Возвращает список дезактивированных функций
     **/
    public final function getDisableItems($list=null) {

        // Отключаем лишние функции
        $disable = $this->disable();
        if(!$disable)
            $disable = array();
        if(is_string($disable))
            $disable = util::splitAndTrim($disable,",");

        // Параметр "disable" коллекции
        if($list) {
            $disable2 = $list->param("disable");
            if(!$disable2) $disable2 = array();
            if(is_string($disable2))
                $disable2 = util::splitAndTrim($disable2,",");
            foreach($disable2 as $item)
                $disable[] = $item;
        }

        // Если выключена кнопка "Добавить", прячем кнопку "Закачать"
        if(in_array("add",$disable)) {
            $disable[] = "upload";
        }

        // Фильтруем уникальные значения
        $disable = array_unique($disable);

        return $disable;
    }

    /**
     * @return Возвращает массив фильтров
     * Пробегается по всем плведениям, вызываеит метод filters() и объединяет результаты
     **/
    public function filters() {
        return $this->callBehaviours("filters");
    }

    public function saveMeta($meta,$langID) {

        $metaObject = $this->item()->metaObject();

        if(!$metaObject->exists()) {
            $hash = get_class($this->item()).":".$this->itemID();
            $obj = reflex::create("reflex_meta_item",array("hash"=>$hash));
        }

        if($meta)
            foreach($meta as $key=>$val)
                $metaObject->data($key,$val);
    }

    public function group() {
        return $this->item()->data("group");
    }

    public function rootPriority() {
        return 0;
    }

    public function deleteMeta($langID) {

        $metaObject = $this->item()->metaObject();
        $metaObject->delete();
    }

    public function setURL($url) {
        $this->item()->setURL($url);
    }

    public function actionAfterCreate() {
        return "edit/".get_class($this)."/".$this->item()->id();
    }

}
