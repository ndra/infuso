<?

class tmp implements mod_handler {

    private static $conveyor;

    private static $global = array();
    private static $defaultParams = false;

    private static $regions = array();
    private static $templateMap = array();
    private static $obj = null;
    private static $bodyClass = null;

    /**
     * @return Возващает объект шаблона
     **/
    public function get($name,$params=array()) {
        $tmp = new tmp_template($name);
        $tmp->params($params);
        return $tmp;
    }

    /**
     * Выполняет шаболон
     **/
    public static function exec($name) {
        $template = self::get($name);
        $args = func_get_args();
        array_shift($args);
        $args = self::normalizeArguments($args);
        foreach($args as $key=>$val)
            $template->param($key,$val);
        $template->exec();
    }

    /**
     * Возвращает текущую область видимости
     **/
    public function conveyor() {
        if(!count(self::$conveyor))
            self::$conveyor[] = new tmp_conveyor();
        return end(self::$conveyor);
    }

    /**
     * Создает новую область видимости
     **/
    public function pushConveyor() {
        self::$conveyor[] = new tmp_conveyor();
    }

    /**
     * Уничтожает текущую область видимости, применяя ее свойства к предыдущей
     **/
    public function mergeConveyorDown() {
        $conveyor = self::popConveyor();
        self::conveyor()->mergeWith($conveyor);
        return $conveyor;
    }

    public function popConveyor() {
        $conveyor = array_pop(self::$conveyor);
        return $conveyor;
    }

    public function destroyConveyors() {
        self::$conveyor = array();
    }

    /**
     * Добавляет css автоматически
     **/
    public static function css($path,$priority = 0){
        if($path{0}=="/")
            self::packCSS($path,$priority);
        else
            self::singleCSS($path,$priority);
    }

    /**
     * Добавляет css без упаковки
     **/
    public static function singleCSS($path,$priority=null) {
        self::conveyor()->add(array(
            "t" => "sc",
            "c" => $path,
            "p" => $priority,
        ));
    }

    /**
     * Добавляет упакованный css
     **/
    public static function packCSS($path,$priority=null) {
        self::conveyor()->add(array(
            "t" => "c",
            "c" => $path,
            "p" => $priority,
        ));
    }

    /**
     * Добавляет js автоматически
     **/
    public static function js($path,$priority=null) {
        if($path{0}=="/")
            self::packJS($path,$priority);
        else
            self::singleJS($path,$priority);
    }

    /**
     * Добавляет js без упаковки
     **/
    public static function singleJS($path,$priority=null) {
        self::conveyor()->add(array(
            "t" => "sj",
            "c" => $path,
            "p" => $priority,
        ));
    }

    /**
     * Добавляет упакованный js с упаковкой
     **/
    public static function packJS($path,$priority=null) {
        self::conveyor()->add(array(
            "t" => "j",
            "c" => $path,
            "p" => $priority,
        ));
    }

    /**
     * Добавляет строку в хэд
     **/
    public static function head($str,$priority=null) {
        self::conveyor()->add(array(
            "t" => "h",
            "c" => $str,
            "p" => $priority,
        ));
    }

    /**
     * Добавляет в хэдей скрипт (js-код)
     **/
    public static function script($str,$priority=null) {
        self::conveyor()->add(array(
            "t" => "s",
            "c" => $str,
            "p" => $priority,
        ));
    }

    public static function bodyClass($class) {
        self::$bodyClass = $class;
    }

    public function headInsert() {

        $head = "";

        $obj = tmp::obj();

        // Добавляем <title>
        $title = $obj->meta("title");
        $title = strtr($title,array("<"=>"&lt;",">"=>"&gt;"));
        $head.= "<title>$title</title>\n";

        // Добавляем noindex
        if($obj->meta("noindex") || tmp::param("meta:noindex")) {
            $head.= "<meta name='ROBOTS' content='NOINDEX,NOFOLLOW' >\n";
        }

        // Добавляем меты
        foreach(array("keywords","description") as $name) {
            if($val = trim($obj->meta($name))) {
                $head.= "<meta name='{$name}' content='{$val}' />\n";
            }
        }

        $head.= tmp::conveyor()->exec();

        echo $head;

    }

    public function header($p1=null) {

        if(!$p1["html"]) {
            $html = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">'."\n";
            $html.= "<html xmlns='http://www.w3.org/1999/xhtml'>\n<head>\n";
            $html.= "<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />\n";

            $html.= tmp_delayed::add(array(
                "class" => "tmp",
                "method" => "headInsert",
                "priority" => 1000,
            ));

            $html.= "</head>\n";
            $html.=  "<body".(self::$bodyClass ? ' class="' . self::$bodyClass . '"' : '') . ">\n";
            $p1["html"] = $html;
        }
        echo $p1["html"];

        if(mod::conf("admin:showMenu")) {
            admin::menu();
        }

        mod_profiler::addMilestone("tmp::header()");

    }

    public static function footer() {

        echo "</body></html>";

        mod_profiler::addMilestone("tmp::footer() end");
    }

    public function insertMessages($str) {
        return preg_replace_callback("/\<body[^>]*>/",array("self","insertMessagesCallback"),$str);
    }

    public function insertMessagesCallback($str) {

        $tmp = tmp::get("mod:messages");
        return $str[0].$tmp->rexec();
    }

    /**
     * Запрещает текущую страницу к индексации
     * (На практике устанавливает специальный параметр, который учитывается при построеннии шапки)
     **/
    public static function noindex() {
        tmp::param("meta:noindex",true);
    }

    public static function nocache() {
        tmp::conveyor()->preventCaching(true);
    }

    public static function param($key,$val=null) {

        // Загружаем объект tmp::obj(), т.к. в этом методе устанавливаются некоторые базовые парамеры
        if(!self::$defaultParams) {
            self::$global["title"] = tmp::obj()->meta("title");
            self::$global["pageTitle"] = tmp::obj()->meta("pageTitle");
            self::$defaultParams = true;
        }

        if(func_num_args()==1) {
            return self::$global[$key];
        }
        if(func_num_args()==2) {
            self::$global[$key] = $val;
        }
    }

    /**
     * Возвращает / устанавливает "текущий" объект reflex
     **/
    public function obj($obj=null) {

        // Если метод tmp::obj() вызван без параметра - возвращаем текущий объект
        // Если объект еще не установлен, то возвращаем объект, соответствующий активному статическому разделу
        if(func_num_args()==0) {

            if(!self::$obj) {
                self::obj(reflex::get("reflex_none",0));
            }
            return self::$obj;

        } elseif(func_num_args()==1) {
            self::$obj = $obj;
        }
    }

    /**
     * Возвращает заголовок H1 текущей страницы
     **/
    public static function h1() {
        $ret = tmp::obj()->meta("pageTitle");
        if(!$ret) {
            $ret = tmp::obj()->title();
        }
        return $ret;
    }

    /**
     * $params - массив аргументов функции
     * Если в этом массиве один элемент и он - массив, возвращаем этот массив
     * Если элементов больше чем один, складываем их в массив с ключами p1,p2,p3...
     **/
    public static function normalizeArguments($arguments) {

        if(sizeof($arguments)==0) {
            $ret = tmp_template::currentParams();
            return $ret;
        }

        $ret = array();
        foreach(array_values($arguments) as $key=>$val) {
            $ret["p".($key+1)] = $val;
        }

        if(sizeof($arguments)==1) {
            $a = end($arguments);
            if(is_array($a))
                foreach($a as $key=>$val)
                    $ret[$key] = $val;
        }

        return $ret;
    }
    /**
     * Добавляет в регион шаблон
     **/
    public static function add($block,$name) {

        if(is_object($name)) {
            tmp_block::get($block)->add($name);
        } else {

            $p = func_get_args();
            $name = tmp_template::handleName($name);
            $template = self::get($name);
            array_shift($p);
            array_shift($p);
            $params = self::normalizeArguments($p);

            foreach($params as $key=>$val)
                $template->param($key,$val);

            tmp_block::get($block)->add($template);
        }

    }

    /**
     * Добавляет в регион вызов метода
     **/
    public static function fn($region,$class,$method) {
        $tmp = tmp::get("tmp:fn");
        $tmp->param("class",$class);
        $tmp->param("method",$method);
        $args = func_get_args();
        array_shift($args);
        array_shift($args);
        array_shift($args);
        $tmp->param("args",$args);
        tmp_block::get($region)->add($tmp);
    }

    /**
     * Выводит содержимое региона добавленное при помощи метода add
     **/
    public static function region($block,$prefix="",$suffix="") {
        tmp_block::get($block)->exec($prefix,$suffix);
    }

    /**
     * @return Возвращает объект блока
     **/
    public function block($name) {
        return tmp_block::get($name);
    }

    public static function reset() {
        tmp_lib::reset();
    }

    public static function jq() {
        tmp_lib::jq();
    }

    public function templateMap() {
        return self::$templateMap;
    }

    /**
     * Подключает тему
     * @param $class php-класс или объект темы
     * Если такая тема уже была подключена, то она «всплывет» на самый верх списка
     **/
    public function theme($id) {
        tmp_theme::loadDefaults();
        $theme = tmp_theme::get($id);
        foreach($theme->templatesArray() as $key=>$tmp)
            self::$templateMap[$key] = $tmp;
    }

    public function filePath($template,$ext) {

        tmp_theme::loadDefaults();

        $template = trim($template,"/");
        $ret = self::$templateMap[$template][$ext];

        if($ret)
            return file::get($ret);
        else
            return file::nonExistent();
    }

    public static function helper($html) {
        return tmp_helper::fromHTML($html);
    }

    public static function widget($name) {
        return tmp_widget::get($name);
    }

}
