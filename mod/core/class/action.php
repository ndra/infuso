<?

namespace infuso\core;

class action extends component {

    private $className = "";
    private $action = "";
    private $ar = "";

    public function __construct($className=null,$action=null,$params = array()) {
    
        if(!trim($action)) {
            $action = "index";
        }
        $this->action = $action;
        $this->className = $className;
        
        if($params) {
        	$this->params($params);
        }
    }

    public function get($class,$action=null,$params=array()) {
        return new action($class,$action,$params);
    }

    /**
     * Преобразует строку в экшн
     * myclass/action/a/123/b/xxx
     * return @class mod_action
     **/
    public function fromString($str) {

        $path = util::splitAndTrim($str,"/");
        $class = array_shift($path);
        $action = array_shift($path);

        $key = null;
        $params = array();
        $n = 0;
        foreach($path as $item) {
            if($n%2==0) {
                $key=$item;
            } else {
                $params[$key] = $item;
            }
            $n++;
        }
        return mod_action::get($class,$action,$params);
    }

    public function canonical() {
        $ret = "";
        $ret.= $this->className();
        $ret.= "/".$this->action();
        foreach($this->params() as $key => $val) {
            $ret.= "/$key/$val";
        }
        return $ret;
    }
    
    /**
     * Возвращает информацию о маршруте
     **/
    public function ar($ar = null) {
    
        if(func_num_args()==0) {
        	return $this->ar;
        } elseif(func_num_args()==1) {
            $this->ar = $ar;
            return $ar;
        }
    }

    /**
     * Возвращает хэш экшна, состоящий из класса, метода и параметров
     * Хэш используется в таблице роутов для быстрого поиска
     * */
    public function hash() {

        $controller = $this;
        $seek = $controller->className();
        if($a = $controller->action()) {
            $seek.= "/".$controller->action();
        }
            
        $params = $controller->params();

        foreach($params as $key=>$val) {
            $params[$key] = $val."";
        }

        sort($params);
        $seek.= serialize($params);
        return $seek;
    }

    /**
     * Может ли активный пользователь выполнить этот экшн?
     **/
    public function test() {

        if(!mod::app()->service("classmap")->testClass($this->className(),"infuso\\core\\controller")) {
            return false;
		}

        $class = "\\".$this->className();
        $obj = new $class;

        if(!call_user_func($this->testCallback(),$this->params())) {
            return false;
		}

        if($obj->methodExists($this->method())) {
            return true;
        }

        return false;
    }

    /**
     * Возвращает экшн, который выполняется в данное время
     **/
    public static function current() {
		return mod::app()->action();
    }

    /**
     * Выполняет этот экшн
     * Предварительно проверяет возможность его выполнения, вызывая метод test
     **/
    public function exec() {

        if(!$this->test()) {
			call_user_func($this->failCallback(),$this->params());
        } else {
            // Если события не заблокированы - вызываем событие
            if(!$suspendEvent) {
                mod::fire("mod_beforeAction",array(
                    "action" => $this,
                ));
            }
            call_user_func($this->callback(),$this->params());
        }

    }

    public function className() {
        return $this->className;
    }

    public function action() {
        return $this->action;
    }

    public function callback() {
        $method = $this->method();
        $class = $this->className();
        $obj = new $class;
        return array($obj,$method);
    }

    public function method() {
        return $this->action()=="index" ? "index" : "index_".$this->action();
    }

    public function testCallback() {
        $class = $this->className();
        $obj = new $class;
        return array($obj,"indexTest");
    }

    public function failCallback() {
        $class = $this->className();
        $obj = new $class;
        return array($obj,"indexFailed");
    }

    public function all() {
        $map = mod::service("classmap")->classmap("routes");
        return $map;
    }

    /**
     * @return Возвращает url экшна
     * url Кэшируется на сутки
     * @todo сделать настройки кэширвоания url
     **/
    public final function url() {

        profiler::beginOperation("url","build",$this->canonical());

        if(true) {

            // Урл кэшируются на день
            $hash = "action-url:".$this->hash().ceil(time()/3600/24);

            if($url = mod::service("cache")->get($hash)) {
                profiler::endOperation();
                return $url;
            }
        }


        $url = $this->urlWithoutCache();

        //if(conf::get("mod:cacheURL")) {
        if(true) {
            mod::service("cache")->set($hash,$url);
        }

        profiler::endOperation();

        return $url;

    }

    public function __toString() {
        return $this->url();
    }

    /**
     * Возвращает url экшна
     * результат не кэшируется
     **/
    private final function urlWithoutCache() {
        foreach(self::all() as $router) {
            if($url = call_user_func(array($router,"backward"),$this)) {
                return $url;
            }
        }
    }

    public function redirect() {
        header("location:{$this->url()}");
        die();
    }

}
