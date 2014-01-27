<?

/**
 * Модель
 **/
abstract class mod_model extends mod_controller {

    private $initialData = array();
    private $fields = null;
    protected $cfields = array();

    /**
     * Статический кэш для полей модели
     * Поля кэшируются для каждого класса
     **/
    private static $modelFields = array();

    /**
     * Возвращает коллекцию полей модели
     **/
    public final function fields() {

        mod_profiler::beginOperation("reflex","fields",get_class($this));

        if(!$this->fields) {

            $modelFields = $this->modelFieldBuffered();
            foreach($modelFields as $name => $field) {
                $this->field($name);
            }

            $fields = new mod_fieldset($this->cfields);
            $this->fields = $fields;
            $this->fields = $this->modelFields()->copyBehaviours($this->fields);
        }
        
        $ret = clone $this->fields;

        mod_profiler::endOperation();

        return $ret;
    }

    private function modelFieldBuffered() {

        $class = get_class($this);

        if(!array_key_exists($class, self::$modelFields)) {
            self::$modelFields[$class] = array();
            foreach($this->modelFields() as $field) {
                self::$modelFields[$class][$field->name()] = $field;
            }
        }

        return self::$modelFields[$class];
    }

    /**
     * @return Возвращает поле по id
     **/
    public final function field($name) {

        $modelFields = $this->modelFields();

        if(!$this->cfields[$name]) {

            $fields = $this->modelFieldBuffered($class);
            $field = $fields[$name];

			if($field) {
			
	            $field = clone $field;
	            $field->setModel($this);

	            $initialValue = array_key_exists($field->name(),$this->initialData) ?
	                $this->initialData[$field->name()] :
	                $field->defaultValue();

	            $field->initialValue($initialValue);
	            $this->cfields[$name] = $field;
	            
            } else {
            
                return mod_field::getNonExistent();
            
            }

        }
        
        return $this->cfields[$name];
		
    }

    /**
     * Метод, который должен вернуть коллекцию полей модели
     **/
     abstract function modelFields();

    /**
     * Устанавливает начальные данные модели
     * Вызывается при создании модели
     * @todo рефакторинг скорости
     **/
    public final function setInitialData($initialData=array()) {

        if(!is_array($initialData)) {
            $initialData = array();
        }

        $this->initialData = $initialData;

    }

    /**
     * Враппер для доступа к даным
     **/
    public final function data($key=null,$val=null) {

        // Если параметров 0 - возвращаем массив с данными
        if(func_num_args()==0) {
            $ret = array();
            foreach($this->fields() as $field) {
                $ret[$field->name()] = $field->value();
            }
            return $ret;
        }

        // Если параметров 1 - возвращаем значение поля
        if(func_num_args()==1) {
            return $this->field($key)->value();
        }

        // Если два параметра - меняем значение
        elseif(func_num_args()==2) {
            $this->field($key)->value($val);
        }
    }

    /**
     * Передает в модель массив данных
     * Аргументом может быть какже экземпляр класса mod_fieldset
     **/
    public function setData($data) {

        if(is_array($data)) {
            foreach($data as $key=>$val) {
                $this->data($key,$val);
            }
        }

        if(is_object($data) && get_class($data)=="mod_fieldset") {
            foreach($data as $field) {
                $this->data($field->name(),$field->value());
            }
        }

    }

    /**
     * Возвращает данные в формате, зависящем от типа поля.
     * Для файлов - объект файла, для внешнего ключа - объект reflex и т.д.
     **/
    public final function pdata($key) {
        return $this->field($key)->pvalue();
    }

    /**
     * Возвращает данные в человекопонятной форме
     **/
    public final function rdata($key) {
        return $this->field($key)->rvalue();
    }

}
