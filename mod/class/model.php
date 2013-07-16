<?

/**
 * Модель
 **/
abstract class mod_model extends mod_controller {

    private $fields = null;
    private $cfields = null;

    /**
     * Возвращает коллекцию полей модели
     **/
    public final function fields() {
    
        if(!$this->fields) {
            $this->fields = new mod_fieldset(array());
        }
        
        return clone $this->fields;
    }

    /**
     * @return Возвращает поле по id
     **/
    public final function field($name) {
    
        if(!$this->cfields[$name]) {
            $this->cfields[$name] = $this->fields()->name($name);
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
     **/
    public final function setInitialData($data=array()) {

        if(!$data) {
            $data = array();
		}
		
		$modelFields = $this->modelFields();

        $fields = array();
        foreach($modelFields as $field) {
            $field = clone $field;
            $field->setModel($this);
            $field->initialValue(array_key_exists($field->name(),$data) ?
                $data[$field->name()] :
                $field->defaultValue() );
            $fields[] = $field;
        }
        
        $this->fields = $modelFields->copyBehaviours($fields);
    }

    /**
     * Враппер для доступа к даным
     **/
    public final function data($key=null,$val=null) {

        // Если параметров 0 - возвращаем массив с данными
        if(func_num_args()==0) {
            $ret = array();
            foreach($this->fields() as $field)
                $ret[$field->name()] = $field->value();
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
