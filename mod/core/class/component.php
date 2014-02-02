<?

namespace infuso\core;

class Component {

    /**
     * Параметры компонента
     *
     * @var array
    **/
    private $param = array();
    private $paramsLoaded = false;

    private $lockedParams = array();

    private static $conf = null;

    private $___behaviours = array();
    private $nextBehaviourPriority = 0;
    private $defaultBehavioursAdded = false;
    private $behavioursSorted = false;

    private $behavioursAdded = array();

    private static $reflections = array();

    /**
     * Статический массив для хранения списка отложенных функций
     **/
    private static $defer = array();

    private $componentID = null;

    /**
     * Добавляет поведение в класс
     * Аргументом - имя класса
     * @return $this
     **/
    public final function addBehaviour($behaviour) {

        profiler::beginOperation("mod","addbehaviour",$behaviour);

        if(!is_string($behaviour)) {
            throw new Exception("mod_component::addBehaviour() - аргумент должен быть строкой, содержащей имя класса");
        }

        if(!$this->defaultBehavioursAdded) {
            $this->addDefaultBehaviours();
        }

        // Добавляем поведение только если оно еще не было добавлено
        if(!$this->behavioursAdded[$behaviour]) {
            $this->behavioursAdded[$behaviour] = true;
            array_unshift($this->___behaviours,$behaviour);
            $this->behavioursSorted = false;
        }

        profiler::endOperation("mod","addbehaviour",$behaviour);

        return $this;
    }

    /**
     * @return array Возврвщает массив поведений объекта
     **/
    public final function behaviours() {
        $this->normalizeBehaviours();
        return $this->___behaviours;
    }

    /**
     * Вызывает метод $fn всех прикрепленных к объекту поведений (метод самого объекта не вызывается)
     * Поведения вызываются в порядке добавления: первым вызовется поведение, добавленное первым
     * @return array с объединением результатов вызванных методов (array_merge)
     **/
    public function callBehaviours($fn) {

        $args = func_get_args();
        array_shift($args);

        $ret = array();
        foreach(array_reverse($this->behaviours()) as $b)
            if(method_exists($b,$fn)) {
                $items = call_user_func_array(array($b,$fn),$args);
                if(is_array($items)) {
                    foreach($items as $item) {
                        $ret[] = $item;
                    }
                }
            }
        return $ret;
    }



    /**
     * Магический метод, который вызывается при обращении к несуществующему методу класса.
     * С помощью данного метода реализуется механизм поведений
     **/
    public final function __call($fn,$params) {

        // Сначала пытаемся вызвать метод у поведений
        foreach($this->behaviours() as $b) {
            if($fn2 = $b->routeBehaviourMethod($fn))
                return call_user_func_array(array($b,$fn2),$params);
        }

        // Пытаемся вызвать метод _fn
        $fn3 = "_".$fn;
        if(method_exists($this,$fn3)) {
            return call_user_func_array(array($this,$fn3),$params);
        }

        // Пытаемся вызвать дата-врапперы
        $wrappers = $this->dataWrappers();

        if(array_key_exists($fn,$wrappers)) {
        
        
			$split = function($str) {
		        $ret = array();
		        foreach(explode(",",$str) as $part) {
		            if(trim($part)!=="") {
		                $ret[] = $part;
		            }
		        }
		        return $ret;
		    };

            $wrappers = $split($wrappers[$fn]);
            foreach($wrappers as $wrapper) {

                if(preg_match("/^mixed(\/([a-z0-1\_]*))?/",$wrapper,$matches)) {

                    $wrapperMethod = $matches[2];
                    if(!$wrapperMethod)
                        $wrapperMethod = "param";

                    if(sizeof($params)==1) {
                        $this->$wrapperMethod($fn,$params[0]);
                        return $this;
                    } elseif(sizeof($params)==0) {
                        return $this->$wrapperMethod($fn);
                    } else {
                        throw new Exception("method $fn defined as wrapper and must have zero or one argument. ");
                    }

                    return;
                }
            }
        }

        // Вызываем метод componentCall()
        // Его можно переопределить и использовать как __call для компонента
        // (для обработки методов, которых нет в компоненте и в поведениях)
        // По умолчанию componentCall выбросить исключение.
        return $this->componentCall($fn,$params);

    }

    public function __get($key) {
        $class = get_class($this);
        throw new \Exception("access to undefined property $class::$key");
    }

    public function __set($key,$val) {
        $class = get_class($this);
        throw new \Exception("access to undefined property $class::$key");
    }

    public function componentCall($fn) {
        $class = get_class($this);

        $b = debug_backtrace(false);
        $line = $b[2]["line"];
        $file = $b[2]["file"];

        throw new \Exception("Call undefined method $class::$fn in $file on line $line");
    }

    /**
     * Проверяет наличие метода у компонента
     * Поиск производится в самом компоненте и в прикрепленных поведениях
     **/
    public final function methodExists($fn) {

        if(method_exists($this,$fn)) {
            return true;
		}

        if(method_exists($this,"_".$fn)) {
            return true;
		}

        foreach($this->behaviours() as $b) {
            if($b->routeBehaviourMethod($fn)) {
                return true;
			}
		}
    }

    private final function normalizeBehaviours() {


        $this->addDefaultBehaviours();
        if(!$this->behavioursSorted) {

            profiler::beginOperation("mod","normalizeBehaviours",get_class($this));

            foreach($this->___behaviours as $key => $behaviour) {
                if(is_string($behaviour)) {
                    $behaviour = new $behaviour;
                    $behaviour->registerComponent($this,$this->nextBehaviourPriority);
                    $this->___behaviours[$key] = $behaviour;
                    $this->nextBehaviourPriority--;
                }
            }

            $this->sortBehaviours();

            profiler::endOperation();
        }

    }

    private final function sortBehaviours() {

        profiler::beginOperation("mod","sortBehaviours",get_class($this));

        $this->behavioursSorted = true;
        usort($this->___behaviours,array("self","sortBehavioursCallback"));

        profiler::endOperation();
    }

    private static function sortBehavioursCallback($a,$b) {

        $d = $b->behaviourPriority() - $a->behaviourPriority();
        if($d!=0) {
            return $d;
        }

        $d = $b->behaviourSequenceNumber() - $a->behaviourSequenceNumber();
        return $d;
    }

    /**
     * Добавляет в объект поведения по умолчанию
     * Вызывается автоматически при вызове метода behaviours()
     * Если вызывать второй раз - ничего не сделает
     **/
    private final function addDefaultBehaviours() {

        // Второй раз поведения по умолчанию не добавляем
        if($this->defaultBehavioursAdded) {
            return;
		}

        profiler::beginOperation("mod","addDefaultBehaviours",get_class($this));

        $this->defaultBehavioursAdded = true;

        foreach($this->defaultBehaviours() as $b) {
            $this->addBehaviour($b);
		}

        $bb = mod::service("classmap")->classmap("behaviours");
        $bb = $bb[get_class($this)];
        if($bb) {
            foreach($bb as $b) {
                $this->addBehaviour($b);
			}
		}

        profiler::endOperation("mod","addDefaultBehaviours",get_class($this));

    }
    
    /**
     * @return Массив поведений, который дорбавляются объекту по умолчанию
     **/
    public function defaultBehaviours() {
        return array();
    }

    /**
     * Магическая функция __clone клонирует поведения
     **/
    public final function __clone() {
        foreach($this->behaviours() as $key=>$b) {
            $b = clone $b;
            $b->registerComponent($this,$this->nextBehaviourPriority);
            $this->nextBehaviourPriority++;
            $this->___behaviours[$key] = $b;
        }
    }

    /**
     * Загрузка параметров из конфигурации YAML
     **/
    private function loadParams() {
    
        if($this->paramsLoaded) {
            return;
        }

        $this->paramsLoaded = true;

        // Загружаем параметры по умолчанию
        $this->params($this->initialParams());

        $params = $this->componentConf("params");
        
        if(is_array($params)) {
            foreach($params as $key=>$val) {
                $this->param($key,$val);
            }
        }

    }

    /**
     * Блокирует параметр $key для изменения
     **/
    public function lockParam($key) {
        $this->lockedParams[] = $key;
    }

    /**
     * Возвращает параметр конфигурации компонента
     **/
    public function componentConf() {
        $args = func_get_args();
        array_unshift($args,get_class($this));
        array_unshift($args,"components");
        return call_user_func_array(array("\infuso\core\conf","general"),$args);
    }

    /**
     * Возвращает набор начальныйх параметров компонента.
     * начальные параметры перекрываются параметрами из components.yml
     **/
    public function initialParams() {
        return array();
    }

    /**
     * Получить параметр, задать параметр
     **/
    public final function &param($key=null,$val=null) {

        $this->loadParams();

        if(func_num_args()==0) {
            return $this->param;
        }

        if(func_num_args()==1) {

            if(is_array($key)) {
                foreach($key as $a=>$b)
                    $this->param($a,$b);
                return $this;
            }

            // Мы возвращаем значение по ссылке
            // Если возвращать по ссылке несуществующие элементы массива, php создает их на лету
            // и записывает в них нули
            // Чтобы этого не произошло - проверяем наличие ключа у массива
            if(array_key_exists($key,$this->param)) {
                return $this->param[$key];
            } else {
                return null;
            }

        }

        if(func_num_args()==2) {

            if(!in_array($key,$this->lockedParams)) {
                $this->param[$key] = $val;
            }
            return $this;
        }

    }

    /**
     * При вызове без параметров выозвращает
     **/
    public final function params($params=null) {

        $this->loadParams();

        if(func_num_args()==0) {
            return $this->param;
        }

        if(func_num_args()==1) {

            if(!is_array($params)) {
                throw new Exception("mod_component::params() called with single argument of type ".gettype($params).": '$string', expecting array" );
            }

            foreach($params as $key=>$val)
                $this->param($key,$val);
            return $this;
        }

    }

    /**
     * Ставит задачу отложенного вызова метода $method
     **/
    public function defer($method) {

        if(!self::$defer[$this->getComponentID()]) {
            self::$defer[$this->getComponentID()] = array(
                "component" => $this,
                "methods" => array(),
            );
        }

        self::$defer[$this->getComponentID()]["methods"][$method] = true;

    }

    /**
     * Выполняет все отложенные функции
     * Вы не должны вызывать этот метод напрямую, его вызывает система
     **/
    public static function callDeferedFunctions() {

        $n = 0;

        while(sizeof(self::$defer)) {

            $defer = self::$defer;
            self::$defer = array();

            foreach($defer as $component) {
                foreach($component["methods"] as $method => $none) {
                    $component["component"]->$method();
                }
            }

            $n++;

            if($n>500) {
                throw new Exception("Defered function recursion");
            }
        }
    }

    public function getComponentID() {
        if(!$this->componentID) {
            $this->componentID = util::id();
        }
        return $this->componentID;
    }

    /**
     * Возвращает массив дата-врапперов
     * Переопределите этот метод для создания собственных врапперов
     * return array(
     *   "myparam" => "mixed", // Враппер к параметру
     *   "myvalue" => "mixed/data" // Враппер к ->data() для reflex
     * )
     **/
    public function dataWrappers() {
        return array();
    }

    public function factoryReflectionMethod($class,$method) {

        if(!self::$reflections[$class.":".$method]) {
            self::$reflections[$class.":".$method] = new \ReflectionMethod($class,$method);
        }
        return self::$reflections[$class.":".$method];

        return $r;
    }
    
    public static final function inspector() {
        return new \infuso\core\inspector(get_called_class());
    }

    /**
     * Выполняет код в контексте объенкта
     * Разворачивает в область видимости кода массив переменных $params
     **/
    public function evalCode() {
        if(func_num_args()==2 && is_array(func_get_arg(1))) {
            extract(func_get_arg(1));
        }
        return eval(func_get_arg(0));
    }
    
	public function confDescription() {
		return array();
	}

}

register_shutdown_function(array("\infuso\core\component","callDeferedFunctions"));
