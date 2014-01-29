<?

namespace infuso\core;
use infuso\util\util;

abstract class field extends component {

	/**
	 * Конфигурация поля
	 **/
	protected $conf = array();

    private $value = null;
    
    private $changedValue = null;
    
    protected $model = null;
    
    /**
     * Пометка о том что поле изменилось
     **/
    private $changed = false;
    
	private static function path() {
		return mod::app()->varPath()."/field.php";
	}
	
    private static $descr = array();
    
    public static function get($arg) {
        return self::factory($arg);
    }

    /**
     * Фабрика-конструктор полей
     * Если передана строка, то она трактуется как имя класса или тип поля
     * Если передан массив, он используется как конфигурация поля
     **/
    public static function factory($conf="mod_field_textfield") {
    
		if(is_object($conf)) {
		    return $conf;
		}

        self::loadDescription();

        if(is_string($conf)) {
        
            $class = $conf;
            
            if(!mod::service("classmap")->testClass($class,"\\infuso\\core\\field")) {
                $class = self::$descr[$conf];
            }
            
            $conf = array(
                "editable" => 1,
            );
            
        } else {
            $class = self::$descr[$conf["type"]];
            if(!$class) {
                $class = "mod_field_textarea";
            }
        }

        return new $class($conf);
    }

    protected $exists = true;

    /**
     * Создает несуществующее поле
     * Используется в mod_fieldset::field() если запрашиваемого поля не существует
     **/
    public function getNonExistent() {
        $field = self::get(null);
        $field->exists = false;
        return $field;
    }

    /**
     * Устанавливает объект модели, связанный с данным полем
     **/
    public function setModel($model) {
        $this->model = $model;
        return $this;
    }

    /**
     * Возвращает модель, с которой связано это поле
     **/
    public function model() {
        return $this->model;
    }

    /**
     * Конструктор поля
     **/
    public function __construct($conf=null) {
    
        if(!$conf["id"]) {
            $conf["id"] = util::id();
        }
            
        $this->params($conf);
    }

    /**
     * @return true/false существует ли поле
     **/
    public final function exists() {
        return $this->exists;
    }

    /**
     * @return Возвращает массив полей - по одному каждого типа
     **/
    public static function all() {
        $ret = array();
        self::loadDescription();
        foreach(array_unique(self::$descr) as $typeID=>$className)
            $ret[] = self::get(array("type"=>$typeID));
        return $ret;
    }

    /**
     * Загружает описание типов полей
     **/
    private function loadDescription() {
        if(!self::$descr) {
            self::$descr = file::get(self::path())->inc();
        }
    }

    /**
     * Собирает описание типов полей
     **/
    public static function collect() {
        file::mkdir(file::get(self::path())->up());
        $ret = array();
        foreach(mod::service("classmap")->classes("infuso\\core\\field") as $class) {

            $obj = new $class;

            $ret[call_user_func(array($class,"typeID"))] = $class;

            $a = $obj->typeAlias();
            if($a)
                $ret[$a] = $class;
        }
        util::save_for_inclusion(self::path(),$ret);
        mod::msg("Описания типов собраны");
    }

    /**
     * @return Должна вернуть уникальный тип поля
     **/
    public abstract function typeID();

    /**
     * @return Возвращает алиас типа поля
     * Алиас используется при создании поля, чтобы не запоминвать громоздкий ID или имя класса
     **/
    public function typeAlias() {
        $class = get_class($this);
        if(preg_match("/^mod_field_(.*)/",$class,$matches)) {
            return $matches[1];
		}
    }

    /**
     * @return Должна вернуть имя типа поля
     **/
    public abstract function typeName();
    
    public function dataWrappers() {
        return array(
            "name" => "mixed",
            "label" => "mixed",
            "help" => "mixed",
            "group" => "mixed",
		);
    }

    /**
     * Возвращает / устанавливает начальное значение поля
     **/
    public final function initialValue($val=null) {

        if(func_num_args()==0) {
            return $this->value;
        }
        
        if(func_num_args()==1) {
            $this->value = $this->prepareValue($val);
        }
    }

    /**
     * Возвращает / изменяет значение этого поля
     **/
    public final function value($value=null) {

        // Если функция вызвана без параметров, возвращаем значение поля
        // Если значение было изменено, возвращаем измененое значение
        if(func_num_args()==0) {
            return $this->changed ?
                $this->changedValue :
                $this->value;
        }

        if(func_num_args()==1) {

            $this->changedValue = $this->prepareValue($value);
            $this->changed = true;

            // Вызываем триггер
            // Он будет определен в поведении
            $this->afterFieldChange();

            return $this;
        }
    }

    /**
     * Триггер, вызывающийся при изменении поля
     **/
    public function _afterFieldChange() {}

    /**
     * Откатывает изменения значения поля
     **/
    public final function revert() {
        $this->changed = false;
    }
    
    public function applyChanges() {
        $this->changed = false;
        $this->value = $this->changedValue;
    }

    /**
     * @return Возвращает true, если поле было изменено
     **/
    public final function changed() {

        if($this->name()=="id")
            return false;

        if(!$this->changed)
            return false;
            
        return $this->prepareValue($this->value) !== $this->prepareValue($this->changedValue);
    }

    /**
     * @return Возвращает значение поля по умолчанию
     **/
    public function defaultValue() {
        return $this->prepareValue($this->conf["default"]);
    }

    /**
     * @return Возвращает обработанное значение поля. Тип возвращаемого значения
     * и способ обработки зависит от типа поля
     **/
    public function pvalue() {
        return $this->value();
    }

    /**
     * @return Возвращает человекопонятное значение поля (строку)
     **/
    public function rvalue() {
        return util::str($this->value())->esc()->ellipsis(1000)."";
    }

    /**
     * Подготовака поля для сохранения в модель
     * Поведение зависит от типа поля
     * Например, для числовых полей строка конвертируется в число, и выполняется
     * преобразование "," => "."
     * Для полей типа файл - файл преобразуется в стрку и т.п.
     **/
    public function prepareValue($val) {
        return $val;
    }

    public function mysqlValue() {
        return mod::service("db")->quote($this->value());
    }

    /**
     * Возвращает id поля
     * id поля уникально среди всех полей, в отличие от name
     **/
    public final function id() {
        return $this->param("id");
    }

    /**
     * Дополнительные параметры конфигурации
     **/
    public function descr() {
        return "Описание поля";
    }

    /**
     * Дополнительные параметры конфигурации
     **/
    public function extraConf() {
        return array();
    }

    /**
     * Возвращает ключи дополнительных параметров конфигурации
     **/
    public function extraConfKeys() {
        $ret = array();
        foreach($this->extraConf() as $conf)
            $ret[] = $conf["name"];
        return $ret;
    }

    /**
     * 0 аргументов - возвращает массив конфигурации
     * 1 аргумент (ключ) - возвращает значение конфигурации по ключу
     * 2 аргумента (ключ,значение) - изменяет переметр конфигурации
     **/
    public function conf() {
        $args = func_get_args();
        return call_user_func_array(array($this,"param"),$args);
    }


    public function mysqlType() {
    }

    public function mysqlAutoincrement() {
        return false;
    }

    public function mysqlNull() {
    }

    public function mysqlIndexFields() {
        return $this->name();
    }

    /**
     * Возвращает режим редактирования:
     * 0 - поле скрыто
     * 1 - Редактируемое
     * 2 - Только чтение
     **/
    public final function editMode() {
    
        if($this->editable()) {
            return 1;
        }
        
        if($this->readonly()) {
            return 2;
		}
		
        return 0;
    }

    /**
     * @return Видимо ли данное поле?
     **/
    public final function visible() {
        return !!$this->conf("editable");
    }

    /**
     * @return Можно ли редактировать данное поле
     **/
    public final function editable() {
        if($this->name()=="id") {
            return false;
		}
        return $this->conf("editable")==1;
    }

    /**
     * @return Поле только для чтения?
     **/
    public final function readonly() {
        return $this->conf("editable")==2;
    }

    /**
     * Делает поле видимым
     **/
    public function show() {
        $this->conf("editable",1);
        return $this;
    }

    /**
     * Скрывает поле
     **/
    public function hide() {
        $this->conf("editable",0);
        return $this;
    }

    /**
     * Делает поле «Только для чтения»
     **/
    public function disable() {
        $this->conf("editable",2);
        return $this;
    }

}
