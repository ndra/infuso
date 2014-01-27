<?

/**
 * Класс коллекции полей
 **/
class mod_fieldset implements Iterator{

    // Итераторская шняга
    protected $fields = array();

    private $behaviours = array();

    public function __construct($fields) {

        if(is_array($fields)) {
            $fields = array_values($fields);
        }

        if(is_object($fields) && get_class($fields)=="mod_fieldset") {
            $fields = $fields->fields;
        }

        $this->fields = $fields;
    }

    public function rewind() { reset($this->fields); }

    public function current() {
        $item = current($this->fields);
        if(is_object($item)) {
            foreach($this->behaviours as $b) {
                $item->addBehaviour($b);
            }
        }
        return $item;
    }
    public function key() { return key($this->fields); }

    public function next() { return next($this->fields); }

    public function valid() { return $this->current() !== false; }

    public static function zero() {
        return mod_field::getNonExistent();
    }

    public function get($n) {
        return $this->fields[$n];
    }

    /**
     * Возвращает поле по имени
     **/
    public function name($name) {

        foreach($this->fields as $field) {
            if($field->name()==$name) {
                return $field;
            }
		}

        return self::zero();
    }

    /**
     * Возвращает поле по типу
     **/
    public function type($typeID) {
    
        foreach($this->fields as $field) {
            if($field->typeID()==$typeID) {
                return $field;
			}
		}
		
        return self::zero();
    }

    /**
     * Возвращает поле по id
     **/
    public function id($id) {
    
        foreach($this->fields as $field) {
            if($field->id()==$id) {
                return $field;
			}
		}
		
        return self::zero();
    }

    /**
     * Возвращает измененные поля
     **/
    public function changed() {
    
        $ret = array();
        foreach($this->fields as $field) {
            if($field->changed()) {
                $ret[] = $field;
			}
		}
		
        return $this->copyBehaviours($ret);
    }

    /**
     * Возвращает видимые поля
     **/
    public function visible() {
        $ret = array();
        foreach($this->fields as $field) {
            if($field->visible()) {
                $ret[] = $field;
			}
		}
        return $this->copyBehaviours($ret);
    }

    /**
     * Фильтрует поля, принадлежащие заданной группы
     **/
    public function group($group) {
        $ret = array();
        foreach($this->fields as $field) {
            if($field->group()==$group) {
                $ret[] = $field;
            }
		}
        return $this->copyBehaviours($ret);
    }

	/**
	 * Создает набор полей на основе данного,
	 * При этом сами поля передаются аргументом, а поведения берутся из данного филдсета
	 **/
    public function copyBehaviours($fields) {
        $fieldset = new self($fields);
		foreach($this->behaviours() as $b) {
		    $fieldset->addBehaviour($b);
		}
		return $fieldset;
    }

    /**
     * Возвращает количество полей в наборе
     **/
    public function count() {
        return sizeof($this->fields);
    }

    public function textfield() {
        $field = mod_field::get(array("type"=>"v324-89xr-24nk-0z30-r243"));
        $this->fields[] = $field;
        return $field;
    }

    public function cost() {
        $field = mod_field::get(array("type"=>"nmu2-78a6-tcl6-owus-t4vb"));
        $this->fields[] = $field;
        return $field;
    }

    public function file() {
        $field = mod_field::get(array("type"=>"knh9-0kgy-csg9-1nv8-7go9"));
        $this->fields[] = $field;
        return $field;
    }

    /**
     * Добавляет поле в коллекцию
     * Аргумент — имя класса, аналогично mod_field::get()
     **/
    public function addField($type) {
        $field = mod_field::get($type);
        $this->fields[] = $field;
        return $field;
    }

    public function setData($p) {
        foreach($this->fields as $field) {
            if(array_key_exists($field->name(),$p)) {
                $field->value($p[$field->name()]);
            }
        }
    }

    /**
     * Добпавляет поведение ко все полям в сете
     * Поведение добавится все полям, даже тем, которые будет добавлены в сет после поведения
     **/
    public function addBehaviour($class) {
        $this->behaviours[] = $class;
    }
    
    public function behaviours() {
        return $this->behaviours;
    }

}
