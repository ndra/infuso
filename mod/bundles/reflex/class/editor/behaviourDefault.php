<?

/**
 * Стандартное поведение для редактора элемента
 **/
class reflex_editor_behaviourDefault extends mod_behaviour {

    /**
     * Ставим стандартному поведению низкий приоритет, чтобы можно было его переназначить
     **/
    public function behaviourPriority() {
        return -2;
    }

    /**
     * @return Возвращает html для нижнего колонтитула в списке товаров
     **/
    public function bbar($list) {
        return "Найдено элементов: ".$list->count();
    }

    public function icon() {
        $img = trim($this->img(),"/ ");
        return $img ? file::get($img)->preview(16,16)."" : "";
    }

    /**
     * Возвращает изображение элемента
     * По умолчанию система ищет поля с файлами и пытается взять картинку оттуда
     **/
    public function img() {

        // Тип поля файл
        $field = $this->item()->fields()->type("knh9-0kgy-csg9-1nv8-7go9");
        if($field->exists()) {
            return $this->item()->data($field->name());
        }

        // Тип поля список файлов
        $field = $this->item()->fields()->type("f927-wl0n-410x-4grx-pg0o");
        if($field->exists()) {
            return $file = $this->item()->pdata($field->name())->first()."";
        }

    }

    /**
     * Включить ли лог в редакторе элемента
     **/
    public function log() {
        return true;
    }

    public function fields() {
        return $this->item()->fields();
    }

    /**
     * @return Строка со списком функций, которые надо выключить (через запятую)
     **/
    public function disable() {
        return array();
    }

    public function titleField() {
        return $this->item()->reflex_titleField();
    }

    /**
     * @return Функция возвращает имя поля, которое будет использоваться для быстрого поиска в каталоге
     **/
    public function quickSearch() {
        return $this->component()->titleField();
    }

    /**
     * @return Функция возвращает имя поля, которое будет использоваться для закачивания файлов в каталоге
     * Кнопка Функции->Закачать
     * По умолчанию функция вернет первое поле типа «Файл» в таблице
     **/
    public function uploadToField() {
        foreach($this->item()->fields() as $field) {
            if($field->typeID()=="knh9-0kgy-csg9-1nv8-7go9" || $field->typeID()=="f927-wl0n-410x-4grx-pg0o") {
                return $field->name();
            }
        }
    }

    /**
     * Включить / выключить закачку файлов
     **/
    public function uploadsEnabled() {
        return $this->item()->fields()->type("knh9-0kgy-csg9-1nv8-7go9")->exists();
    }

    public function beforeUpload($p) {

        $name = explode(".",$p["name"]);
        $name = $name[0];

        if($title = $this->titleField()) {
            $this->item()->data("title",$name);
        }
    }

    /**
     * Закачать загруженный файл в объект
     **/
    public function uploadFileIntoItem($p) {

        $tmpName = $p["tmpName"];
        $name = $p["name"];

        $item = $this->item();

        $field = $this->uploadToField();
        if(!$field) {
            return false;
        }

        $this->beforeUpload($p);

        $item->log("Объект создан из файла");
        $storage = $item->storage();
        $path = $storage->addUploaded($tmpName,$name);
        $item->data($field,$path);

        return true;

    }

    /**
     * Возвращает массив редакторов элементов
     * Этот массив меняется в левом меню каталога
     **/
    public function editorChildren() {
        $ret = array();
        foreach($this->item()->childrenWithBehaviours() as $list) {
            if($list->param("menu")!==false)
                   foreach($list->limit(200)->editors() as $editor)
                       $ret[] = $editor;
        }
        return $ret;
    }

    /**
     * Возвращает список инлайновых потомков, т.е. тех, которые будут выводится
     * сразу в первой вкладке редактирования
     **/
    public function childrenInline() {
        return $this->component()->editorChildren();
    }

    /**
     * @return Возвращает количество дочерних элементов
     * Выводится в дереве в каталоге
     **/
    public function numberOfChildren() {
        $n = 0;
        foreach($this->item()->childrenWithBehaviours() as $list) {
            if($list->param("menu")!==false) {
                $list->useFilter(0);
                $n+= $list->count();
            }
        }
        return $n;
    }

    /**
     * Применяет фильтр к коллекции
     **/
    public static function applyFilter($list,$data) {
        if(!$data) return;
        foreach($list->joinedFields() as $field) {
            $filter = $data[$field->fullName()];
            $field->filterApply($list,$filter);
        }
    }

    public function applyQuickSearch($list,$query) {

        $name = $this->component()->quickSearch();

        if($name) {
            $list->like($name,$query);
        }
    }

    public function placeWidget() {

        tmp_widget::get("reflex_editor_widget")
            ->param("class",get_class($this->item()))
            ->param("id",$this->item()->id())
            ->delayed();

    }

}
