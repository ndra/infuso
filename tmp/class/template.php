<?

class tmp_template extends tmp_generic {

    private static $exists = array();
    private static $current = null;

    public $cache = null;

    public function __construct($name=null) {

        if($name) {
            $name = self::handleName($name);
            $this->param("*template", $name."");
            $this->lockParam("*template");
        }

        if($cache = $this->componentConf("cache")) {
            if(in_array($name,$cache)) {
                $this->cache();
            }
        }

        if($delayed = $this->componentConf("delayed")) {
            if(in_array($name,$delayed)) {
                $this->param("*delayed",true);
            }
        }

    }

    /**
     * Возвращает/меняет шаблон базовый шаблон
     **/
    public function base($base=null) {

        if(func_num_args()==0) {
            return $this->param("*base");
        }

        if(func_num_args()==1) {
            $this->param("*base",$base);
            return $this;
        }

        throw new Exception("tmp_template::base() wrong arguments count");

    }

    /**
     * Обрабатывает имя шаблона и преобразует относительный путь в абсолютный
     * Понимает ../ - вернуться на уровень назад
     **/
    public static function handleName($name) {

        $base = self::$current ? self::$current->template() : "/";

        $name = trim($name);
        $name = rtrim($name,"/");

        $axis = "root";
        $backStep = 1;

        // Весли в начале шаблона стоит два точки, ось - потомки родителя
        if(preg_match("/^(\.\.\/)+/",$name,$matches)) {
            $axis = "back";
            $backStep = round(strlen($matches[0]) / 3);
        }

        // старый способ указания абсолютного шаблона site:item
        // Новый способ - начать шаблон со слэша
        $name = preg_replace("/^([\da-zA-Z\_\-\.]+)\:/","/$1/",$name);

        // Избавляемся от точек и дублирующихся слэшей
        $name = preg_replace("/[\.\/]+/","/",$name);

        // Делаем абсолютные пути относительными
        if(!preg_match("/^\//",$name) && $axis!="back") {
            $axis = "children";
        }

        switch($axis) {
            case "children":
                $name = "/".$base."/".$name;
                $name = preg_replace("/[\.\/]+/","/",$name);
                break;
            case "back":
                $back = $base;
                for($i=0;$i<$backStep;$i++)
                    $back = preg_replace("/[\da-zA-Z\_\-\.]+\/?$/","",$back);
                $name = $back."/".$name;
                $name = preg_replace("/[\.\/]+/","/",$name);
                break;
            default:
                $name = "/".$name;
                $name = preg_replace("/[\.\/]+/","/",$name);
                break;

        }

        $name = "/".trim($name,"/");
        return $name;

    }

    /**
     * Выполняет шаблон
     * Первый параметр - шаблон.
     * Последующие параметры будут переданы внутрь шаблона как $p1,$p2 и т.д.
     **/
    public function exec() {

        // Если текущий шаблон отложенный - ставим маркер и выходим
        if($this->param("*delayed")) {
            $this->delayed();
            return;
        }

        $p = $this->params();

        tmp::css($this->fileCSS()."",1);
        tmp::js($this->fileJS()."",1);

        // Если включен режим кэширования
        if($this->cache) {

            // расчитываем хэш для сохранения в кэш :)
            $hash = $this->template().":".$this->cache.":".serialize($p);

            // Пробуем достать данные их кэша
            $cached = mod_cache::get($hash);

            // Если в кэше еще нет шаблона
            if(!$cached) {

                mod_profiler::beginOperation("tmp","cached miss",$this->template());

                tmp::pushConveyor();
                ob_start();
                $this->aexec($p);
                $cached = ob_get_flush();
                $conveyor = tmp::popConveyor();

                if(!$conveyor->preventCaching()) {
                    mod_cache::set($hash,$cached);
                    mod_cache::set($hash.":conveyor",$conveyor->serialize());
                }

                mod_profiler::endOperation();


            // Если шаблон в кэше
            } else {

                mod_profiler::beginOperation("tmp","cached hit",$this->template());

                // Выводим содержимое из кэша
                echo $cached;

                // Загружаем и выполняем конвеер из кэша (подключенные скрипты, тили и т.п.)
                $conveyorData = mod_cache::get($hash.":conveyor");
                $conveyor = tmp_conveyor::unserialize($conveyorData);

                tmp::conveyor()->mergeWith($conveyor);

                mod_profiler::endOperation();
            }
        }
        // Если кэширование выключено
        else {
            mod_profiler::beginOperation("tmp","exec",$this->template());
            $this->aexec($p);
            mod_profiler::endOperation();
        }
    }

    /**
     * Возвращает параметры текущего шаблона
     **/
    public static function currentParams() {
        if(self::$current) {
            return self::$current->params();
        }
        return array();
    }

    private function aexec($params) {

        // Запоминаем предыдущий шаблон
        $last = self::$current;

        self::$current = $this->base() ? tmp::get($this->base()) : $this;

        foreach($params as $key=>$val) {
            $$key = $val;
        }

        // Проверяем наличие шаблона если мы в режиме отладки
        // Если пользователь не суперадмин - сразу выполняем шаблон
        if(mod::debug()) {
            if(!$this->file()->exists()) {
                throw new Exception("Шаблон '{$this->template()}' не найден.");
            }
        }

        if(mod::debug()) {
            echo "<!-- ".$this->template()." -->";
        }

        include $this->file()->native();

        if(mod::debug()) {
            echo "<!-- end of ".$this->template()." -->";
        }

        self::$current = $last;

    }

    /**
     * Возвращает контент шаблона для отправки через ajax
     * В этом случае все скрипты и стили будут добавлены к html-коду, возаращаемому шаблоном
     * Также будет выполнена обработка отложенных функций
     **/
    public function getContentForAjax() {
        tmp::pushConveyor();
        $html = $this->rexec();
        $conveyor = tmp::popConveyor();
        $html = $conveyor->processDelayed($html);
        $html.= $conveyor->getContentForAjax();
        return $html;
    }

    /**
     * Включает кэширование этого шаблона
     **/
    public function cache($hash=-1) {
        $this->cache = $hash;
        return $this;
    }

    /**
     * @return Возвращает полное имя шаблона
     **/
    public function template() {
        return trim($this->param("*template"),".:/");
    }

    /**
     * @return Возвращает имя шаблона (последнюю часть)
     **/
    public function name() {
        return end(explode("/",$this->path()));
    }

    /**
     * @return Возвращает имя модуля шаблона
     **/
    public function mod() {
        list($mod,$path) = explode(":",$this->template());
        return $mod;
    }

    public function path() {
        list($mod,$path) = explode(":",$this->template());
        return strtr($path,".","/");
    }

    /**
     * Возвращает файл с php-кодом этого шаблона
     **/
    public function file() {
        return tmp::filePath($this->mod()."/".$this->path(),"php");
    }

    /**
     * Возвращает файл с js-кодом этого шаблона
     **/
    public function fileJS() {
        return tmp::filePath($this->mod()."/".$this->path(),"js");
    }

    /**
     * Возвращает файл с css-кодом этого шаблона
     **/
    public function fileCSS() {
        return tmp::filePath($this->mod()."/".$this->path(),"css");
    }

    public function includeScriptsAndStyles() {
        tmp::css($this->fileCSS()."");
        tmp::js($this->fileJS()."");
    }

    public function inc() {
        $this->includeScriptsAndStyles();
    }

    /**
     * Рекурсивно подключает все стили и скрипты шаблона и шаблонов внутри него
     **/
    public function incr() {
        tmp_theme::loadDefaults();
        foreach(tmp::templateMap() as $key=>$tmp)
            if(strpos($key,$this->template())===0) {
                if($tmp["css"])
                    tmp::css($tmp["css"]);
                if($tmp["js"])
                    tmp::js($tmp["js"]);
            }
    }

    /**
     * Возвращает php-код шаблона
     **/
    public function code() {
        return $this->file()->contents();
    }

    /**
     * Возвращает js-код шаблона
     **/
    public function js() {
        return $this->fileJS()->contents();
    }

    /**
     * Возвращает css-код шаблона
     **/
    public function css() {
        return $this->fileCSS()->contents();
    }

}
