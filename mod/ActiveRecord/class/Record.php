<?

namespace infuso\ActiveRecord;
use \infuso\core\event;
use \infuso\core\mod;
use \reflex;
use \infuso\core\profiler;

/**
 * @todo вернуть register_shutdown_function или аналог
 **/
class Record extends \mod_model {

	/**
	 * первичный ключ элемента
	 **/
    protected $id = null;
    
	/**
	 * Коллекция полей
	 **/
    private $fields = null;
    
    private $justCreated;
    
    private static $created = 0;
    private static $deleted = 0;
    private static $stored = 0;
    
    private static $buffer = array();
    
	/**
     * Массив с объектами, которые нуждаются в сохранении
     **/
    public static $dirtyItems = array();
    
    /**
     * Признак того что объект виртуальный
     **/
    protected $isVirtual = false;
    
    // Триггеры
    public function reflex_beforeCreate() {
    }

    public function reflex_afterCreate() {
    }

    public function reflex_beforeStore() {
    }

    public function reflex_afterStore() {
    }

    public function reflex_beforeDelete() {
    }

    public function reflex_afterDelete() {}

    public function __sleep() {
        return array(
            "id",
        );
    }

    /**
     * @return Функция вызывается при создании коллекции
     * @param $items class reflex_list
     **/
    public function reflex_beforeCollection($items) {
        $this->callBehaviours("reflex_beforeCollection",$items);
    }

    public function defaultBehaviours() {
        $ret = parent::defaultBehaviours();
        $ret[] = "reflex_defaultBehaviour";
        return $ret;
    }

    /**
     * @return bool
     * true - объект опубликован
     * false - не опубликован
     **/
    public final function published() {
        return $this->reflex_published();
    }

    public static function classes() {
        $ret = array();
        foreach(mod::service("classmap")->classes("infuso\\ActiveRecord\\Record") as $class) {
            if($class!="reflex_none") {
                $ret[] = $class;
            }
        }
        return $ret;
    }

    /**
     * Возвращает объект домена, связанного с элементом
     **/
    public final function domain() {
        $ret = $this->reflex_domain();
        if(!is_object($ret)) {
            $ret = reflex_domain::get($ret);
        }
        return $ret;
    }

    private $metaObject = null;

    /**
     * Возвращает объект метаданных данного объекта.
     * На практике рекомендуется пользоваться ф-цией reflex::meta()
    **/
    public final function metaObject($lang=null) {

        if(!$this->metaObject) {

            if(!$lang) {
                $lang = \lang::active()->id();
            }

            if(get_class($this)=="reflex_meta_item") {
                return $this;
            }

            if(!$this->reflex_meta()) {
                return reflex::virtual("reflex_meta_item");
            }

            $this->metaObject = \reflex_meta_item::get(get_class($this).":".$this->id(),$lang);
        }

        return $this->metaObject;
    }

    /**
     * reflex::meta($key,$val)
     * При вызове с одним параметром вернет значение метаданных с этим ключем.
     * При вызове с двумя параметрами - изменит.
     **/
    public final function meta($key,$val=null) {

        if(func_num_args()==1) {

            $ret = $this->metaObject()->data($key);
            return $ret;

        } elseif (func_num_args()==2) {

            $obj = $this->metaObject();
            if(!$obj->exists()) {
                $hash = get_class($this).":".$this->id();
                $obj = reflex::create("reflex_meta_item",array(
					"hash" => $hash,
				));
				$this->metaObject = $obj;
            }
            $obj->data($key,$val);
        }
    }

    /**
     * Изменяет url-адрес объекта
     **/
    public final function setUrl($url) {
    
        if(!$this->reflex_route()) {
            return;
        }
        
        $hash = get_class($this).":".$this->id();
        $route = reflex_route_item::get($hash);
        
        if(!$route->exists()) {
            $route = reflex::create("reflex_route_item",array(
				"hash" => $hash,
			));
        }
        
        $route->data("url",$url);

        // Сохраняем мету. Это вызовет исправление url (русский в транслит, проблемы в тире и т.п.)
        $route->store();
    }

    public final function reflex_updateSearch() {
    
        if(!$this->reflex_meta()) {
            return;
		}

        $search = $this->reflex_search();

        // Если объект не опубликован или запрещен для поиска, передаем пустую строку в данные для поиска
        if(!$this->published() || $search=="skip") {
            if($this->metaObject()->exists()) {
                $this->meta("search","");
            }
            return;
        }

        $this->meta("search",$search);
        $this->meta("searchWeight",$this->reflex_searchWeight());

    }

    /**
     * Возвращает название объекта.
     * По умолчанию название, это значение поле title, или строка вида class:id,
     * если поле title пустое или не существует.
     * Заголовок объекта отображается в редакторе каталога
     * Саму функцию вы не можете переопределить, т.к. она финальная. Нужно переопределить ф-цию reflex_title()
     **/
    public final function title() {
        return $this->reflex_title();
    }

    /**
     * Возвращает url объекта.
     * URL может быть задан явно ф-цией reflex_url()
     * В противном случае URL будет сгенерирован автоматически для экшна classname/item/id/$id
     * Вы можете передать первым аргументом функции массив с параметрами, которые будут учтены при генерации URL
     **/
    public final function url($params = array()) {

        if(!is_array($params)) {
            $params = array();
        }

        if($url = trim($this->reflex_url())) {

            // Если адрес передается в формате action::class_name/action/p1/123/p2/456..., преобразуем адрес в url
            if(preg_match("/action::/",$url)) {
                $url = strtr($url,array("action::"=>""));
                $action = mod_action::fromString($url);
                return mod::url($action->url());
            // В противном случае возвращаем адрес как есть
            } else {
                return mod::url($url);
            }

        } else {

            $params["id"] = $this->id();
            $action = \mod_action::get(get_class($this),"item",$params);
            return mod::url($action->url());

        }

    }

    public final function __construct($id=0) {
        $this->id = $id;
    }

    public static function get($class,$id=null,$data=null) {

        // Определяем класс элементов / последовательности
        $class = util::getItemClass($class);

        // Вызов функции с одним аргументом возвращает коллекцию
        if(func_num_args()==1) {
            return Collection::get($class);
        }

        // Если id <= 0, возвращаем несуществующий объект без запроса в базу
        if($id <= 0) {
            $ret = new $class(0);
            $ret->setInitialData();
            return $ret;
        }

        $item = self::$buffer[$class][$id];
        if(!$item) {
            if($data) {
                $item = new $class($id);
                $item->setInitialData($data);
                self::$buffer[$class][$id] = $item;
            } else {
                $item = reflex::get($class)->eq("id",$id)->one();
            }
        }

        return $item;
    }

    public function modelFields() {
        return $this->table()->fields();
    }

    /**
     * @return true/false Существует ли объект
     **/
    public final function exists() {
        return !!$this->id;
    }

    /**
     * Создает виртуальный объект
     * Если вызвана из контекста объекта, создает виртуальный объект - копию текущего
     **/
    public function virtual($class=null,$data=array()) {

        if(!$class && $this) {
            return reflex::virtual(get_class($this),$this->data());
        }

        $class = util::getItemClass($class);
        $item = new $class($data["id"]);
        $item->isVirtual = true;
        $item->setInitialData($data);
        return $item;
    }

    /**
     * @return true/false в зависимости от того виртуальный ли объект
     **/
    public function isVirtual() {
        return $this->isVirtual;
    }

    /**
     * Нормализует имя столбца, защищая от инъекций
     * Возможные варианты:
     * field
     * table.field
     * fn(field)
     **/
    public static function normalizeColName($name,$table=null) {

        $symbols = "[a-z0-9\_\-\:]+";

        if(preg_match("/^{$symbols}$/i",$name)) {
            return "`$table`.`".$name."`";
        }

        if(preg_match("/^({$symbols})\.({$symbols})$/i",$name,$matches)) {
            return "`".$matches[1]."`.`".$matches[2]."`";
        }

        // Функции
        if(preg_match("/^([a-z0-9\_]+)\(({$symbols})\)$/i",$name,$matches))
            return $matches[1]."(`$table`.`".$matches[2]."`)";

        // Функции
        if(preg_match("/^([a-z0-9\_]+)\(({$symbols})\.({$symbols})\)$/i",$name,$matches))
            return $matches[1]."(`".$matches[2]."`.`".$matches[3]."`)";

        return "";
    }

    /**
     * Возвращает объект таблицы модели
     **/
    public final function table() {

        profiler::beginOperation("reflex","table",get_class($this));

        $ret = table::factoryTableForReflexClass(get_class($this));

        profiler::endOperation();

        return $ret;
    }

    /**
     * Запускает триггер
     * Выполняет метод $fn объекта и все методы $fn поведений, если такие есть.
     * Возвращает false, если хотя бы один из вызванных методов вернул false
     * Если false не был возвращен ни одним из методов, вернет true
     **/
    public final function callReflexTrigger($fn,$event) {

        if($this->$fn()===false) {
            return;
		}
            
        foreach($this->behaviours() as $b) {
            if(method_exists($b,$fn)) {
                if($b->$fn($event)===false) {
                    return false;
				}
			}
		}

        if(in_array($fn,array(
            "reflex_beforeCreate",
            "reflex_beforeStore",
            "reflex_beforeDelete",
        ))) {
            if(!$this->callReflexTrigger("reflex_beforeOperation",$event)) {
                return false;
			}
		}

        if(in_array($fn,array(
            "reflex_afterCreate",
            "reflex_afterStore",
            "reflex_afterDelete",
        ))) {
            $this->callReflexTrigger("reflex_afterOperation",$event);
		}
		
		$event->fire();
		
        return true;
    }

    /**
     * Удаляет объект
     * Очищает хранилище объекта
     **/
    public final function delete() {

        if(!$this->exists())
            return;
            
		$event = new event("reflex_beforeDelete",array(
		    "item" => $this,
		));

        // Вызываем пре-триггеры
        if(!$this->callReflexTrigger("reflex_beforeDelete",$event)) {
            return;
		}

        $prefixedTableName = $this->table()->prefixedName();
        $id = reflex_mysql::escape($this->id());
        reflex_mysql::query("delete `$tableName` from `$prefixedTableName` as `$tableName` where `id`='$id'");

        // Очищаем хранилище (Если не используется чужое хранилище)
        if($this->storage()->reflex()==$this) {
        	$this->storage()->clear();
        }
        
		$event = new event("reflex_afterDelete",array(
		    "item" => $this,
		));

        // Вызываем пост-триггеры
        if(!$this->callReflexTrigger("reflex_afterDelete",$event)) {
            return;
		}

        // Счетчик
        self::$deleted++;
    }

    /**
     * Добавляет новую запись в базу
     **/
    public static function create($class,$insert=array(),$keepID=false) {
    
        if(!is_string($class)) {
            throw new Exception ("reflex::create() first argument must be string, have ".gettype($class));
        }
    
        $class = util::getItemClass($class);
        $item = new $class(null);
        $item->setInitialData($insert);
        $item->createThis($keepID);
        return $item;
    }

    /**
     * Создает для данного объекта запись в базе
     **/
    private function createThis($keepID = false) {

        $this->justCreated = true;
        
		$event = new \infuso\core\event("reflex_beforeCreate",array(
		    "item" => $this,
		));

        if(!$this->callReflexTrigger("reflex_beforeCreate",$event)) {
            return false;
        }

		$this->storeCreated($keepID);
            
		$event = new \infuso\core\event("reflex_afterCreate",array(
		    "item" => $this,
		));
        
        $this->callReflexTrigger("reflex_afterCreate",$event);
        self::$created++;
        
        $this->justCreated = false;
    }

    /**
     * Сохраняет созданный объект в базу
     **/
    private final function storeCreated($keepID = false) {

        if(!$this->writeEnabled()) {
            return false;
		}

		$event = new event("reflex_beforeStore",array(
		    "item" => $this,
		));

        if(!$this->callReflexTrigger("reflex_beforeStore",$event)) {
            return false;
		}
		
        $table = $this->table()->prefixedName();

        // Вставляем в таблицу
        $data = array();
        foreach($this->fields() as $field) {
            if($field->name()!="id" || $keepID) {
                $data[$this->normalizeColName($field->name(),$table)] = $field->mysqlValue();
            }
        }
        $insert = " (".implode(",",array_keys($data)).") values (".implode(",",$data).") ";

        $query = "insert into `$table` $insert ";
        $id = mod::service("db")->query($query)->exec()->lastInsertId();

        // Заносим данные в объект
        // Объект заносим объект в буфер
        $this->field("id")->initialValue($id);

        $this->id = $id;
        self::$buffer[get_class($this)][$id] = $this;

        $this->reflex_updateSearch();
        
		$event = new event("reflex_afterStore",array(
		    "item" => $this,
		));
		
		$this->callReflexTrigger("reflex_afterStore",$event);

        return true;
    }

    /**
     * Сохраняет виртуальный объект в базу
     * Если объект не виртуальный, просто сохраняет его
     * Метод отличается от reflex::store, который игнорирует виртуальные объекты
     **/
    public function storeVirtual() {

        if($this->isVirtual()) {
            $this->isVirtual = false;
            $this->createThis();
        } else {
            $this->store();
        }

    }

    /**
     * Возвращает первичный ключ объекта
     **/
    public final function id() {
        return $this->id;
    }

    /**
     * Помечает данные объект как изменный
     * Все измененные объекты сохраняются при вызове reflex::storeAll();
     **/
    public final function markAsDirty() {
        if(!$this->exists())
            return false;
        self::$dirtyItems[get_class($this).":".$this->id()] = true;
    }

    /**
     * Убирает объект из списка измененных
     * Убирает у полей объекта отметку о том что они изменились
     **/
    public final function markAsClean() {

        // Убираем объект из списка измененных
        $key = get_class($this).":".$this->id();
        unset(self::$dirtyItems[$key]);

        // Убираем у полей отметку об изменении
        foreach($this->fields()->changed() as $field) {
            $field->applyChanges();
        }

    }

    /**
     * Сохраняет в базу все изменения
     * Вызывается автоматически в конце работы скрипта
     **/
    public static function storeAll() {

        profiler::addMilestone("reflex before store");

        $items = array_keys(self::$dirtyItems);

        $b = 0;
        
        while(sizeof($items)) {

            self::$dirtyItems = array();

            foreach($items as $key) {

                list($class,$id) = explode(":",$key);
                $item = self::get($class,$id);
                $item->store();

            }

            $items = array_keys(self::$dirtyItems);
            $n++;

            if($n>100) {
				throw new Exception("reflex_storeAll() - recursion");
			}

        }

        profiler::addMilestone("reflex stored");
    }

    private final function from() {
        return "`".$this->table()->prefixedName()."`";
    }

    private final function writeEnabled() {

        if($this->isVirtual())
            return false;

        return true;
    }

    /**
     * Сохраняет изменения в базу
     **/
    public final function store() {

        if(!$this->writeEnabled()) {
            $this->markAsClean();
            return false;
        }

        if(!$this->fields()->changed()->count()) {
            $this->markAsClean();
            return false;
        }
        
		$event = new event("reflex_beforeStore",array(
		    "item" => $this,
		));

        // Триггер
        if(!$this->callReflexTrigger("reflex_beforeStore",$event)) {
            $this->markAsClean();
            return false;
        }

        // После вызова reflex_beforeCreate() поля объекта могуть стать такими же как в были до изменений
        // В этом случае нет смысла сохранять объект в базу
        $changedFields = $this->fields()->changed();
        if(!$changedFields->count()) {
            $this->markAsClean();
            return true;
        }

        $set = array();
        foreach($changedFields as $field) {
            $set[] = "`".$field->name()."`=".$field->mysqlValue();
        }
        $set = "set ".implode(",",$set)." ";

        $id = $this->id();
        self::$stored++;

        $from = $this->from();
        mod::service("db")->query("update $from $set where `id`='$id' ")->exec();

        // Сразу после сохранения, помечаем объект как чистый
        // Таким образом, если в reflex_afterStore() будут изменены поля объекта,
        // Метод store может быть вызванповторно
        $this->markAsClean();
        
		$event = new event("reflex_afterStore",array(
		    "item" => $this,
		    "changedFields" => $changedFields,
		));

        $this->callReflexTrigger("reflex_afterStore",$event);

        $this->reflex_updateSearch();

        return true;
    }

    /**
     * Возвращает объект в исходное состояние
     * Отменяя все изменения, сделанные после загрузки
     **/
    public function revert() {
        foreach($this->fields() as $field)
            $field->revert();
        $this->markAsClean();
    }

    /**
     * Записывает объект и удаляет его из буфера
     **/
    public final function free() {
        $this->store();
        $this->markAsClean();
    }

    /**
     * Записывает все объекты и очищает буффер
     **/
    public static function freeAll() {
        foreach(self::$buffer as $class)
            foreach($class as $item)
                $item->free();
        self::$buffer = array();
        self::$dirtyItems = array();
    }

    /**
     * Возвращает цепочку родителей
     * @return Array
     **/
    public final function parents() {
        $parents = array();
        $parent = $this;
        $n=0;
        while(1) {
            $parent = $parent->reflex_parent();
            if(!$parent)
                break;
            if(!$parent->exists())
                break;
            $parents[] = $parent;
            $n++;

            if($n>100)
                break;
        }
        return array_reverse($parents);
    }

    /**
     * Проверяет цепочку родителей на рекурсию
     * Проходит по родителям, и, если встретит одного из них дважды - возвращает true
     * @return Bool
     **/
    public final function testForParentsRecursion() {
        $parents = array($this);
        $parent = $this;
        while(1) {
            $parent = $parent->reflex_parent();
            if(!$parent) break;
            if(!$parent->exists()) break;
            if(in_array($parent,$parents)) return true;
            $parents[] = $parent;
        }
        return false;
    }

    /**
     * Возвращает родителя элемента
     **/
    public final function parent() {
        return $this->reflex_parent();
    }

    /**
     * Возвращает объект файлового хранилища, связанного с данным объектом
     **/
    public final function storage() {
        $source = $this->reflex_storageSource();
        return new storage(get_class($source),$source->id());
    }

    public function log($text,$params=array()) {
    
        if(!$this->editor()->log()) {
			return;
		}
        
        if(get_class($this)=="reflex_log") {
			return;
		}
		
		$source = $this->reflex_logSource();

        $log = reflex::create("reflex_log",array(
            "user" => \user::active()->id(),
            "index" => get_class($source).":".$source->id(),
            "text" => $text,
            "comment" => $params["comment"],
        ));
    }
    
    public function reflex_logSource() {
        return $this;
    }

    public function getLog() {
        $index = get_class($this).":".$this->id();
        return reflex_log::all()->eq("index",$index);
    }

    /**
     * Возвращает количество удаленных объектов
     **/
    public static function deleted() {
        return self::$deleted;
    }

    /**
     * Возвращает количество сохраненных объектов
     **/
    public static function stored() {
        return self::$stored;
    }

    /**
     * Возвращает количество созданных объектов
     **/
    public static function created() {
        return self::$created;
    }

    /**
     * Сбрасывает счетчики статистики
     **/
    public static function clearStatistics() {
        self::$created = 0;
        self::$deleted = 0;
        self::$stored = 0;
    }

    /**
     * Возвращает редактор элемента
     **/
    public final function editor() {

        $map = \infuso\core\file::get(mod::app()->varPath()."/reflex/editors.php")->inc();

        $class = $this->reflex_editor();

        if(!$class) {

            $classes = $map[get_class($this)];
            if(!$classes) {
                $classes = array();
            }

            $class = end($classes);

        }

        if(!$class) {
            return reflex::get(0,0)->editor();
        }

        return new $class($this->id());

    }

    public function reflex_editor() {
        return null;
    }

    /**
     * Метод должен вернуть Массив коллекций потомков
     * Переопределите его в своем классе для реализации иерархии
     **/
    public function reflex_children() {
        return array();
    }

    public final function childrenWithBehaviours() {
        $ret = $this->reflex_children();
        $ret2 = $this->callBehaviours("reflex_children");
        return array_merge($ret,$ret2);
    }

    // Возвращает все параметры конфигурации
    public static function configuration() {
        return array(
            array("id"=>"reflex:mysql_host","title"=>"mysql host"),
            array("id"=>"reflex:mysql_user","title"=>"mysql user"),
            array("id"=>"reflex:mysql_password","title"=>"Пароль к БД"),
            array("id"=>"reflex:mysql_db","title"=>"База даных"),
            array("id"=>"reflex:mysql_table_prefix","title"=>"Префикc таблиц"),
            array("id"=>"reflex:content_processor","title"=>"Класс процессора контента"),
        );
    }

}
