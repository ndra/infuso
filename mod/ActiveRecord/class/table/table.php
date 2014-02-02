<?

namespace infuso\ActiveRecord;
use \infuso\core\mod;
use \infuso\core\file;

/**
 * Класс описывающий таблицу
 **/
class table extends \infuso\core\component {

    /**
     * Уникальный код таблицы
     * Должен строиться по правилу module:hash
     * module - имя модуля, например mod или site
     * hash - Уникальная произвольная последовательность символов
     **/
     protected $id = null;

    /**
     * Список полей определенных в этой таблице
     **/
    protected $conf = null;
    
    /**
     * Массив с полями таблицы
     **/
    protected $fields = array();
    
    /**
     * Массив с индексами таблицы
     **/
    protected $indexes = array();
    
    /**
     * Буфер для фабрики таблиц forReclexClass()
     **/
    private static $bufferReflex = array();
    
	protected $fieldsWokenUp;
	protected $indexesWokenUp;
	protected $fieldsReady;
	protected $fieldGroupsWokenUp;
	protected $fieldGroups;
    
    /**
     * Массив tableID => tableName
     **/
    protected static $names = null;

    public function __construct($id,$conf=null) {

        $this->id = $id;
        
        // Если при создании таблицы не передаются данные, они будут загружены из файла таблицы
        if(!$conf) {
            $conf = $this->loadConf();
        }
        
        $this->conf = $conf;
    }
    
    public function serialize() {

        $ret = array(
            "name" => $this->name(),
            "fields" => $this->serializeFields(),
            "indexes" => $this->serializeIndexes(),
            "fieldGroups" => $this->serializeFieldGroups(),
        );

        return $ret;
    }

    protected function serializeFields() {
    
         $ret = array();
        foreach($this->fields() as $field) {
        
            $params = array();
            foreach($field->params() as $key=>$val) {
                if(is_scalar($val)) {
                    $params[$key] = $val;
                }
            }
                
            $ret[] = $params;
            
        }
        return $ret;
    }
    
    /**
     * @return Возвращает массив сериализованных индексов
     **/
    private function serializeIndexes() {
        $ret = array();
        foreach($this->indexes() as $index)
            if(!$index->automatic())
            	$ret[] = $index->serialize();
        return $ret;
    }
    
    /**
     * @return Возвращает массив сериализованных групп полей
     **/
    private function serializeFieldGroups() {
        $ret = array();
        foreach($this->fieldGroups() as $group)
            $ret[] = $group->serialize();
        return $ret;
    }
    
    /**
     * Фабрика таблиц
     * Возвращает таблицу по id
     **/
    public function factory($id,$data=null) {
    
        if(!self::$bufferReflex[$id]) {
            self::$bufferReflex[$id] = new self($id,$data);
        }
        
        return self::$bufferReflex[$id];
    }
    
    /**
     * Возвращает все таблицы модуля $mod
     **/
    public static function factoryModuleTables($mod) {

        $path = mod::service("bundle")->bundle($mod)->conf("mysql","path");
        
        if(!$path) {
			return array();
		}	
			
        $path = "/$mod/$path";
        $ret = array();
        foreach(file::get($path)->dir() as $file) {
            $ret[] = self::factory($mod.":".$file->baseName());
		}
            
        usort($ret,array(self,"sortByName"));
        return $ret;
    }
    
    private static function sortByName($a,$b) {
        return strcmp($a->name(),$b->name());
    }
    
    /**
     * Фабрика объектов
     * @return Возвращает таблицу по ее имени
     **/
    public function factoryByName($name) {

        if(!self::$names) {
            self::$names = @file::get(mod::app()->varPath()."/reflex/names.php")->inc();
        }

        $id = self::$names[$name];

        return self::factory($id);
    }
    
    public function factoryTableForReflexClass($class) {

        if(!self::$bufferReflex[$class]) {

            $iClass = util::getItemClass($class);

            $obj = new $iClass(0);
            $ret = $obj->reflex_table();
            
            // Если не указаны таблицы, используем имя класса в качестве таблицы.
            if($ret=="@")
                $ret = $class;

            if(is_string($ret)) {
                $table = self::factoryByName($ret);
            } elseif (is_array($ret)) {
                $table = new self(\infuso\util\util::id(),$ret);
            } else {
                $table = new self(null);
            }

            // Добавляем к таблице поля из поведений
            foreach($obj->callBehaviours("fields") as $field) {
                $table->addField($field);
            }

            // Добавляем к таблице индексы из поведений
            foreach($obj->callBehaviours("indexes") as $index) {
                $table->addIndex($index);
            }

            self::$bufferReflex[$class] = $table;

        }

        return self::$bufferReflex[$class];

    }
    
    /**
     * Возвращает id таблицы
     **/
    public function id() {
        return $this->id;
    }
    
    /**
     * Существует ли таблица
     * @todo Сейчас метод проверяет по id. Переделать.
     **/
    public function exists() {
        return !!$this->id();
    }
    
    /**
     * Возвращает имя таблицы, например site_page
     **/
    public function name() {
        return $this->conf["name"];
    }
    
    /**
     * Устанавливает имя таблицы
     **/
    public function setName($name) {
        $this->conf["name"] = $name;
    }
    
    /**
     * Возвращает имя таблицы с префиксом (такое, которое реально используется в mysql)
     **/
    public function prefixedName() {
        return mod::service("db")->tablePrefix().$this->name();
    }

    /**
     * Возвращает путь к файлу таблицы
     **/
    public function confPath() {
        list($mod,$id) = explode(":",$this->id());
        $path = mod::service("bundle")->bundle($mod)->conf("mysql","path");
        if(!$path) {
            return;
        }
        $path = "/$mod/$path/$id.php";
        return $path;
    }

    /**
     * Загружает описание таблицы из файла
     **/
    public function loadConf() {

        // Если у таблицы нет id - не загружаем ее
        if(!$this->id()) {
            return;
        }

        $data = file::get($this->confPath())->inc();
        return $data;
    }
    
    /**
     * Сохраняет описание таблицы в файл
     **/
    public function saveConf() {
        if(!$path = $this->confPath())
            return;
        file::mkdir(file::get($path)->up());
        $data = $this->serialize();
          \infuso\core\util::save_for_inclusion($path."",$data);
    }
    
    /**
     * Разворачивает массив полей из настроек (ленивая инициалищзация)
     **/
    public function fieldsLazyInit() {

        if($this->fieldsWokenUp) {
            return;
        }

        $this->fieldsWokenUp = true;

        if($conf = $this->conf["fields"]) {
            foreach($conf as $fieldData) {
                $this->fields[] = \infuso\core\field::get($fieldData);
            }
        }
    }
    
    /**
     * Возвращает коллекцию полей таблицы
     **/
    public function fields() {
    
        $this->fieldsLazyInit();
        
        if(!$this->fieldsReady) {
            $this->fieldsReady = true;
            foreach($this->fields as $field) {
                $field->addBehaviour("reflex_table_fieldBehaviour");
                $field->setTable($this);
            }
        }
        
		$fieldset = new \mod_fieldset($this->fields);
        $fieldset->addBehaviour("reflex_table_fieldBehaviour");
        return $fieldset;
    }
    
    public function field($name) {
        return $this->fields()->name($name);
    }
    
    /**
     * Добавляет поле в описание таблицы
     **/
    public function addField($field) {
        $this->fieldsLazyInit();
        $field = \infuso\core\field::get($field);
        $this->fields[] = $field;
        $this->fieldsReady = false;
    }
    
    /**
     * Удаляет поле из таблицы
     **/
    public function deleteField($fieldID) {
        $this->fieldsLazyInit();
        $fields = array();
        foreach($this->fields as $field)
            if($field->id()!=$fieldID)
                $fields[] = $field;
        $this->fields = $fields;
    }
    
    /**
     * Поднимает поле на одну позицию выше
     **/
    public function moveFieldUp($id) {
        $this->fieldsLazyInit();
        $this->fields = array_values($this->fields);
        foreach($this->fields as $pos=>$field)
            if($field->id()==$id)
                break;
        if($pos==0) return;
        array_splice($this->fields,$pos-1,2,array($this->fields[$pos],$this->fields[$pos-1]));
    }

    /**
     * Опускает поле на одну позицию ниже
     **/
    public function moveFieldDown($id) {
        $this->fieldsLazyInit();
        $this->fields = array_values($this->fields);
        foreach($this->fields as $pos=>$field)
            if($field->id()==$id)
                break;
        if($pos>=sizeof($this->fields)-1) return;
        array_splice($this->fields,$pos,2,array($this->fields[$pos+1],$this->fields[$pos]));
    }
    
    /**
     * Возвращает коллекцию индексов таблицы
     **/
    public function indexes() {
        $this->indexesLazyInit();
        return $this->indexes;
    }
    
    /**
     * Возвращает индекс по id
     **/
    public function index($indexID) {
        foreach($this->indexes() as $index) {
            if($index->id()==$indexID) {
                return $index;
            }
		}
    }

    /**
     * Разворачивает массив индексов полей из настроек (ленивая инициалищзация)
     **/
    public function indexesLazyInit() {

        if($this->indexesWokenUp) {
            return;
        }

        $this->indexesWokenUp = true;

        // Автоматические индексы
        foreach($this->fields() as $field) {
            if($field->indexEnabled()) {
                $this->indexes[] = \reflex_table_index::get(array(
                    "name" => "+".$field->name(),
                    "fields" => $field->mysqlIndexFields(),
                    "automatic" => true,
                ));
            }
        }
        
        if($conf = $this->conf["indexes"]) {
            foreach($conf as $indexData) {
                $this->indexes[] = new \reflex_table_index($indexData);
            }
        }
        
    }
    
    /**
     * Добавляет индекс в таблицу
     **/
    public function addIndex($index=null) {
    
        if(!$index)
            $index = reflex_table_index::create();
    
        $this->indexesLazyInit();
        $index = \reflex_table_index::get($index);
        $this->indexes[] = $index;
        return $index;
    }
    
    /**
     * Удаляет индекс по id
     **/
    public function deleteIndex($id) {
    
        $this->indexesLazyInit();
        $indexes = array();
        foreach($this->indexes() as $index)
            if($index->id()!=$id)
                $indexes[] = $index;
        $this->indexes = $indexes;
    }
    
    /**
     * Разворачивает массив групп полей из настроек (ленивая инициалищзация)
     **/
    public function fieldGroupsLazyInit() {

        if($this->fieldGroupsWokenUp) {
            return;
        }

        $this->fieldGroupsWokenUp = true;

        $names = array();

        if($conf = $this->conf["fieldGroups"]) {
        
            foreach($conf as $groupData) {
                $group = new fieldGroup($groupData);
                $group->setTable($this);
                $this->fieldGroups[] = $group;
                $names[] = $group->name();
            }
        }
        
        foreach($this->fields() as $field) {
            if(!in_array($field->group(),$names)) {
                $group = new fieldGroup(array(
                    "name" => $field->group(),
                    "title" => $field->group(),
                ));
                $this->fieldGroups[] = $group;
                $group->setTable($this);
                $names[] = $group->name();
            }
        }
        
    }
    
    /**
     * Возвращает массив групп полей в этой таблице
     **/
    public function fieldGroups() {
        $this->fieldGroupsLazyInit();
        return $this->fieldGroups;
    }
    
    /**
     * Добавляет группу полей в таблицу
     **/
    public function addFieldGroup() {
        $this->fieldGroupsLazyInit();
        $group = new reflex_table_fieldGroup();
        $this->fieldGroups[] = $group;
        return $group;
    }
    
    /**
     * Запускат миграцию иаблицы
     **/
    public function migrateUp() {
        $migration = new tableMigration($this);
        $migration->migrateUp();
    }

    /**
     * Создает новую таблицу в модуле $mod
     **/
    public static function create($mod) {

        $id = "$mod:".\infuso\core\util::id();
        $data = array("name"=>"{$mod}_new");
        $table = new self($id,$data);

        // Добавляем в таблицу поля по умолчанию
        $field = mod::field("jft7-kef8-ccd6-kg85-iueh")->name("id");
        $table->addField($field);

        $table->saveConf();
    }
    
    /**
     * Удаляет описание таблицы
     * Этот метод не имеет отношения к удалению таблицы из базы (drop table)
     **/
    public function delete() {
        if(!$path = $this->confPath()) {
            return;
        }
        file::get($path)->delete();;
    }
    
}
