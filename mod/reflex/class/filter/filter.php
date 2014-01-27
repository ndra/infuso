<?

/**
 * Класс-поведение для создения отборов и сортировок
 **/
class reflex_filter extends mod_behaviour {

    /**
     * Конфигуратор режимов просмотра
     * Переопределите эту функцию чтобы добавить режимы просмотра
     **/
    public function viewModes() {
    }

    /**
     * Конфигуратор режимов ограничений
     * Переопределите эту функцию если вам нужны другие ограничения на старницу
     **/
    public function limitModes() {
        return array(
            10,
            25,
            50,
            100
        );
    }

    /**
     * Конфигуратор режимов сортировки
     * Переопределите эту функцию чтобы настроить режимы просмотра
     **/
    public function sortModes() {
        return array(
            array(
                "title" => "По умолчанию",
                "method" => "sort_default",
            )
        );
    }

    public function sort_default() {
    }

    /**
     * Возвращает список разрешенных ключей для отбора
     **/
    public function filterKeys() {
        return array(
            "sort",
            "limit",
            "view",
            "page",
        );
    }

    public function filterRemember() {
        return true;
    }

    /**
     * Я хочу использовать одни и те же методы как для управления поведением,
     * так и для выдачи пользователю информации
     * Поэтому при обрадении к методу напрямую и при вызове метода через поведения
     * используются фактически разные методы
     * В данной функции определяются правила вызова методов
     **/
    public function routeBehaviourMethod($fn) {
        if($fn=="viewModes")
            return "getViewModes";
        if($fn=="limitModes")
            return "getLimitModes";
        if($fn=="sortModes")
            return "getSortModes";
        return parent::routeBehaviourMethod($fn);
    }

    public function beforeApplyQuery($q) {
        return $q;
    }

    public function afterApplyQuery($q) {
    }

    /**
     * Применяет к фильтру массив $_GET
     **/
    public function applyQuery($q) {

        if(!$q) {
            $q = array();
        }

        $q = $this->component()->beforeApplyQuery($q);

        $this->component()->queryParams = $q;

        // Устанавливаем текущую страницу
        $page = $this->component()->queryParams["page"];
        $this->component()->page($page);

        $queryParams = $this->component()->queryParams;
        if(!$queryParams)
            $queryParams = array();

        foreach($queryParams as $key=>$val) {
            if(in_array($key,$this->filterKeys()))
                if($val)
                    $this->applySingleFilter($key,$val);
        }

        // Учитываем ограничение количества на страницу
        $limit = $this->component()->limitMode()->val();
        $this->limit($limit);

        // Учитываем сортировку
        $sortfn = $this->component()->sortMode()->method();
        $this->component()->$sortfn();

        $this->afterApplyQuery($q);

        return $this->component();

    }

    /**
     * Возвращает массив параметров запроса, записанных в методе applyQuery
     **/
    public function queryParams() {
        return $this->component()->queryParams;
    }

    /**
     * Применяет к коллекции один элемент фильтра
     * Переопределите эту функцию, если вы хотите использовать нестандартные фильтры
     **/
    public function applySingleFilter($key,$val) {

        list($type,$name) = explode("_",$key);

        switch($type) {
            case "eq":
                $val = explode(",",$val);
                $this->eq($name,$val);
                break;
            case "to":
                $this->leq($name,$val);
                break;
            case "from":
                $this->geq($name,$val);
                break;
            case "like":
                $this->like($name,$val);
                break;
            case "match":
                $this->match($name,$val);
                break;

            // Специальная опция для поиска по полю типа "список ссылок"
            // Дополняет $value до пяти символов нулями слева и делает match against
            case "nmatch":
                $val = str_pad($val,5,"0",STR_PAD_LEFT);
                $this->match($name,$val);
                break;

            case "matchStrict":
                $this->match($name,'"'.$val.'"');
                break;
        }
    }

    /**
     * @return Возвращает адрес страницы с отбором
     **/
    public function url($params=null) {

        $query = $this->component()->queryParams;

        $q = array();
        foreach($this->filterKeys() as $key) {

            $val = $query[$key];

            if(trim($val)) {
                $q[$key] = $val;
            }
        }

        // Если аргумент - число, используем его как страницу при построении url
        if(is_numeric($params)) {
            $q["page"] = $params;
        }

        // Если аргумент - массив, добавляем его ключи в url
        if(is_array($params)) {
            foreach($params as $key=>$val) {
                if($key!==null && $key!=="") {
                    $q[$key] = $val;
                }
            }
        }

        // Писать что страница первая не имеест смысла
        if($q["page"]==1) {
            unset($q["page"]);
        }

        return "?".http_build_query($q);
    }

    /**
     * @return массив доступных режимов ограничения количества элементов на страницу
     **/
    public function getLimitModes() {
        return $this->getCustomModes("limit");
    }

    /**
     * @return Активный режим ограничения количества элементов на страницу
     **/
    public function limitMode() {
        foreach($this->component()->limitModes() as $mode)
            if($mode->active())
                return $mode;
    }

    /**
     * @return массив режимов
     * Используется через обертки
     **/
    private final function getCustomModes($qkey,$extraParams=array()) {

        $fn = $qkey."Modes";

        $ret = array();
        $confArray = $this->$fn();

        if(!is_array($confArray))
            $confArray = array();

        foreach($confArray as $modeConf) {

            if(!is_array($modeConf))
                $modeConf = array(
                    "val" => $modeConf,
                    "title" => $modeConf,
                );
            $modeConf["key"] = $qkey;
            $modeConf["collection"] = $this->component();

            foreach($extraParams as $key=>$val)
                $modeConf[$key] = $val;

            $mode = new reflex_filter_option($modeConf,$this->component());
            $ret[] = $mode;
        }
        // Выбираем активный элемент
        $activeFound = false;
        foreach($ret as $mode)
            if($mode->val()==$this->component()->queryParams[$qkey]) {
                $mode->setActive();
                $activeFound = true;
            }

        // Если не выбрано активного элемента, выбираем запомненный активный или просто первый
        if(!$activeFound)
            foreach($ret as $mode) {
                if($mode->wasActive()) {
                    $mode->setActive();
                    $activeFound = true;
                    break;
                }
            }

        if(!$activeFound)
            foreach($ret as $mode) {
                $mode->setActive();
                break;
            }

        return $ret;
    }

    public function getViewModes() {
        return $this->getCustomModes("view",array(
            "keepPage" => true,
        ));
    }

    /**
     * @return Возвращает активный режим просмотра
     * Если активного режима нет, возвращает первый попавшийся
     **/
    public function viewMode() {

        foreach($this->component()->viewModes() as $mode)
            if($mode->active())
                return $mode;

    }

    /**
     * @return Возвращает активный режим просмотра
     **/
    public function sortMode() {
        foreach($this->component()->sortModes() as $mode)
            if($mode->active())
                return $mode;
    }

    public function getSortModes() {
        return $this->getCustomModes("sort");
    }

}
