<?

namespace infuso\ActiveRecord;
use \infuso\core\mod;
use \reflex;

/**
 * Модель коллекции элементов
 **/
class Collection extends \infuso\core\component implements \Iterator {

    private $itemsLoaded = false;
    
    private $perPage = 20;
    private $whereParts = array();
    protected $eqs = array();
    private $sort = array();
    private $groupBy = null;
    private $having = null;
    private $itemClass = null;
    private $listJoins = array();

    // @todo это временная переменная, для доступа из класса reflex_filter
    public $queryParams = array();

    public function _beforeQuery() {
    }
    
    /**
     * Вызывает триггер beforeQuery
     * Делает это один раз, чтобы избежать рекурсии
     **/
    private $beforeQueryCalled = false;
    private final function callBeforeQuery() {
        if(!$this->beforeQueryCalled) {
            $this->beforeQueryCalled = true;
            $this->beforeQuery();
        }
    }

    // Итераторская шняга
    protected $items = array();
    public function rewind() { $this->load(); reset($this->items); }
    public function current() { $this->load(); return current($this->items); }
    public function key() { $this->load(); return key($this->items); }
    public function next() { $this->load(); return next($this->items); }
    public function valid() { $this->load(); return $this->current() !== false; }

    public final function __construct($class=null) {
        $this->itemClass = $class;
        if($class) {
            $obj = new $class;
            $obj->reflex_beforeCollection($this);
        }
    }

    /**
     * Возвращает коллекцию элементов заданного класса
     **/
    public static function get($class) {
        return new collection($class);
    }

    /**
     * @return Массив со свойствами классов, которые будут сохранены
     * при сериализации
     */
    private static $serializeKeys = array(
        "perPage",
        "whereParts",
        "eqs",
        "sort",
        "groupBy",
        "having",
        "itemClass",
        "listJoins",
    );

    /**
     * @return Сериализует текущую коллекцию в строку
     **/
    public final function serialize() {

        $ret = array();

        // Сериализуем данные из массива "безопасных" ключей
        foreach(self::$serializeKeys as $key)
            $ret[$key] = $this->$key;

        // Сериализуем поведения
        $bb = array();
        foreach($this->behaviours() as $b) {
            $b = get_class($b);
            if(!in_array($b,array("reflex_collection_behaviour"))) {
                $bb[] = $b;
            }
        }

        $ret["params"] = $this->params();
        $ret["behaviours"] = $bb;

        return json_encode($ret);
    }

    /**
     * Восстанавливает коллекцию из строки
     **/
    public final function unserialize($data) {
    
        // Распаковываем данные в массив
        $data = json_decode($data,true);

        // Получаем коллекцию нужного класса
        $list = self::get($data["itemClass"]);

        // Настраиваем параметры у коллекции
        foreach(self::$serializeKeys as $key)
            $list->$key = $data[$key];

        // Подключаем поведения
        if($bb = $data["behaviours"]) {
            foreach($bb as $b) {
                $list->addBehaviour($b);
            }
        }

        // Разворачиваем параметры
        if($params = $data["params"]) {
            $list->params($params);
        }

        // Готово
        return $list;
    }

    /**
     * Применяет к этой колеекции отборы и фильтры из другой коллекции
     **/
    public function superposition($filter) {

        $this->where($filter->where());
        foreach($filter->eqs() as $key=>$val) {
            $this->def($key,$val);
		}

        if($filter->sort) {
            $this->sort = $filter->sort;
		}
    }

    /**
     * @return Возвращает копию коллекции
     **/
    public function copy() {
        return clone $this;
    }

    /**
     * Присоединяет к коллекции другую коллекцию или таблицу
     **/
    public function join($class,$a=null,$b=null) {

        if(is_string($class) && preg_match("/^field\:(.*)/",$class,$matches)) {
            $this->joinByField($matches[1]);
            return $this;
        }

        if(is_object($class)) {
            $class = $class->itemClass();
        }

        if(func_num_args()==2) {

            $this->listJoins[] = array (
                "class" => $class,
                "on" => $a,
                "type" => "inner join",
            );

        } elseif(func_num_args()==3) {

            $thisClass = $this->itemClass();
            $on = "`$class`.`$b` = `$thisClass`.`$a`";

            $this->listJoins[] = array (
                "class" => $class,
                "on" => $on,
                "type" => "inner join",
            );
        }

        return $this;
    }

    public function leftJoin() {
        $args = func_get_args();
        $ret = call_user_func_array(array($this,"join"),$args);
        $this->setJoinType("left join");
        return $ret;
    }

    public function rightJoin() {
        $args = func_get_args();
        $ret = call_user_func_array(array($this,"join"),$args);
        $this->setJoinType("right join");
        return $ret;
    }

    /**
     * Алиас к join()
     **/
    public function joinList() {
        $args = func_get_args();
        return call_user_func_array(array($this,"join"),$args);
    }

    public function setJoinType($type) {
        $last = sizeof($this->listJoins)-1;
        $this->listJoins[$last]["type"] = $type;
        return $this;
    }

    /**
     * @return Возвращает список полей, включая поля в присоединенных таблицах
     **/
    public function joinedFields() {
        $ret = array();

        foreach($this->virtual()->fields() as $field)
            $ret[] = $field;

        foreach($this->listJoins as $join)
            foreach(reflex::virtual($join["class"])->fields() as $field)
                $ret[] = $field;

        return $ret;
    }

    /**
     * Присоединяет к коллекции коллекцию по внешнему ключу
     **/
    public function joinByField($name,$collection=null) {

        $field = $this->virtual()->field($name);

        if($field->typeID()!="pg03-cv07-y16t-kli7-fe6x") {
            return $this;
		}
		
		if($this->param("fieldJoin-".$name)) {
		    return $this;
		}
		
		$this->param("fieldJoin-".$name,true);

        $class = $field->itemClass();
        $list = reflex::get($class);
        $this->join($list,$field->name(), $field->foreignKey());

        if($collection) {

            foreach($collection->listJoins as $join) {
                $this->listJoins[] = $join;
            }

            $this->where($collection->whereQuery());
        }

        return $this;
    }

    /**
     * Присоединяет коллекции по всем внешним ключам модели
     **/
    public function joinAllFields() {
        foreach($this->virtual()->fields() as $field) {
            if($field->typeID()=="pg03-cv07-y16t-kli7-fe6x") {
                $this->joinByField($field->name());
            }
		}
        return $this;
    }

    /**
     * Возвращает FROM-часть mysql-запроса
     **/
    public final function from() {

        if(!$this->table()->exists()) {
            return null;
        }

        $alias = $this->itemClass();
        $ptable = $this->table()->prefixedName();
        $from = "`$ptable` as `$alias` ";

        // Присоединяем коллекции-джойны
        $aliases = array();
        foreach($this->listJoins as $join) {

            $joinClassName = $join["class"];
            $joinTable = reflex::get($joinClassName)->virtual()->table();
            $on = $join["on"];
            $joinType = $join["type"];

            $from.=" $joinType `{$joinTable->prefixedName()}` ";
            $from.= " as `{$joinClassName}` ";
            $from.=" on {$on} ";

            if($aliases[$joinClassName]) {
                throw new \Exception("Multiple joins with table `$joinClassName` ");
            }

            $aliases[$joinClassName] = true;
        }

        return $from;
    }

    /**
     * Возвращает FROM-часть mysql-запроса без учета джойнов
     **/
    public final function fromWithoutJoins() {
        $table = $this->table();
        if(!$table->exists())
            return;
        return "`{$table->prefixedName()}` as `{$this->itemClass()}` ";
    }

    /**
     * Возвращает select expression mysql-запроса
     **/
    public final function what() {
        return "`{$this->itemClass()}`.*";
    }

    /**
     * Выполняет запрос в БД и загружает элементы коллекции
     **/
    public function load() {
        if($this->itemsLoaded)
            return;
        $this->itemsLoaded = true;

        $items = $this->select($this->what());

        foreach($items as $data)
            $this->items[] = reflex::get($this->itemClass(),$data["id"],$data);

        if($this->priorityArray)
            usort($this->items,array($this,"sortItemsUsingArray"));
    }

    public function select($select) {

        // Делаем запрос в БД только в том случае, если класс связан с таблицей.
        if($this->from()) {

            $this->callBeforeQuery();

            // Расчитываем ограничения
            $from = intval(($this->page-1)*$this->perPage);
            $perPage = intval($this->perPage);
            $query = "select {$select} from {$this->from()} where {$this->whereQuery()} ";

            if($g = $this->groupBy()) {
                $query.= " group by $g ";
            }

            if($h = $this->having()) {
                $query.= " having $h ";
            }

            $query.= " order by {$this->orderBy()} ";

            if($perPage) {
                $query.= "limit $from,$perPage";
            }

            $ret = mod::service("db")->query($query)->exec()->fetchAll();
            
            return $ret;

        } else {

            return array();

        }

    }

    /**
     * Указывает последовательность id для принудительной сортировки
     **/
    private $priorityArray = null;
    public function setPrioritySequence($ids) {
        $a = array_flip(array_values($ids));
        $this->priorityArray = $a;
        return $this;
    }

    private function sortItemsUsingArray($a,$b) {
        return $this->priorityArray[$a->id()] - $this->priorityArray[$b->id()];
    }

    /**
     * @return Возвращает класс элемента коллекции
     **/
    public function itemClass() {
        return $this->itemClass;
    }

    /**
     * @return Возвращает объект таблицы
     **/
    public function table() {
        return table::factoryTableForReflexClass($this->itemClass());
    }

    /**
     * Возвращает true/false в зависимости от того пуста ли коллекция
     **/
    public function void() {
        $this->load();
        return sizeof($this->items)==0;
    }

    /**
     * Возвращает количество элементов в коллекции
     **/
    public function count($cached=false) {

        $this->callBeforeQuery();

        // Если у коллекции нет таблицы, возвращаем 0 не выполняя запрос
        // @todo - это проверка временная. Переписать более аккуратно
        if(!$this->table()->id()) {
            return 0;
        }

        $groupBy = $this->normalizeColName($this->groupBy());
        $what = $this->groupBy() ? "distinct {$groupBy} " : "*";

        $q = "select count($what) from {$this->from()} where {$this->whereQuery()} ";
        
        if($cached) {

            $count = mod_cache::get($q);
            if($count) {
                return $count;
            }

        }

        $count = mod::service("db")->query($q)->exec()->fetchScalar();

        if($cached) {
            mod_cache::set($q,$count);
        }

        return $count;

    }

    public function countCached() {
        return $this->count(true);
    }

    /**
     * @return Возвращает количество страниц в коллекции
     **/
    public function pages() {
        if(!$this->perPage)
            return 1;
        return ceil($this->count()/$this->perPage);
    }

    private $page = 1;

    /**
     * Устанавливает / возвращает текущую страницу
     **/
    public function page($page=null) {
        if(func_num_args()==0) {
            return $this->page;
        } else {
            $page = intval($page);
            if($page<1) $page=1;
            $this->page = $page;
            return $this;
        }
    }

    public function perPage($perPage=null) {
        if(func_num_args()==0) {
            return $this->perPage;
        } else {
            $this->perPage = $perPage;
            return $this;
        }
    }

    public function limit($perPage) {
        $this->perPage = $perPage;
        return $this;
    }

    public function normalizeColName($key) {
        return Record::normalizeColName($key,$this->itemClass());
    }

    public function eq($key,$val=null,$fn=null) {

        $type = (is_array($key)?"a":"s").":".(is_array($val)?"a":"s");

        // Если оба параметра скаляры и не задана ф-ция
        if($type=="s:s" && !$fn) {
			$this->eqs[$key] = $val;
		}

        switch($type) {

            case "s:s":
                $key = $this->normalizeColName($key);
                $val = mod::service("db")->quote($val);
                if($fn) {
					$key = "$fn($key)";
				}
                $this->where("$key=$val",$key);
                break;

            case "a:s":
                if(func_num_args()==1) {
                    foreach($key as $k=>$v) {
                        $this->eq($k,$v);
					}
				}
                break;

            // Если второй параметр массив, производится выборка p1 in p2
            case "s:a":

                $r = array();
                foreach($val as $v) {
                    $r[] = mod::service("db")->quote($v);
				}

                switch(sizeof($r)) {

                    case 0:
                        $this->where("0=1",$key);
                        break;

                    case 1:
                        $this->eq($key,end($val));
                        break;

                    default:
                        $r = join(",",$r);
                        $key = $this->normalizeColName($key);
                        $this->where("$key in ($r)",$key);
                        break;
                }
                break;

        }
        return $this;
    }

    public function def($key,$val) {
        $this->eqs[$key] = $val;
        return $this;
    }

    /**
     * Возврашает массив ключ => значение условий сравнения
     * Т.е., если вы написали $items->eq("a","b"),
     * то в возвращаемом методом массиве появится элемент "a" => "b",
     **/
    public final function eqs() {
        return $this->eqs;
    }

    /**
     * Проверяет, есть ди в этой коллекциир условие $key = $val
     **/
    public final function isEq($key,$val) {
        $eqs = $this->eqs();
        return $eqs[$key] == $val;
    }

    public function neq($key,$val) {
    
        $type = (is_array($key)?"a":"s").":".(is_array($val)?"a":"s");
        
        switch($type) {
    
		    case "s:s":
		        $key = $this->normalizeColName($key);
		        $val = mod::service("db")->quote($val);
		        $this->where("{$key}<>{$val}",$key);
		        break;
		        
			case "s:a":
			$r = array();
                foreach($val as $v) {
                    $r[] = "'".reflex_mysql::escape($v)."'";
                }
                switch(sizeof($r)) {

                    case 0:
                        break;

                    case 1:
                        $this->neq($key,end($val));
                        break;

                    default:
                        $r = join(",",$r);
                        $key = $this->normalizeColName($key);
                        $this->where("$key not in ($r)",$key);
                        break;
                }
                break;
		}
        
        return $this;
    }

    /**
     * Добавляет условие $key like %val%
     * Регистр не учитывается
     **/
    public function like($key,$val) {
        $key = $this->normalizeColName($key);
        $val = reflex_mysql::escape($val);
        $val = mb_strtolower($val,"utf-8");
        $this->where("lower($key) LIKE '%$val%'");
        return $this;
    }

    public function match($key,$val) {
        $key = $this->normalizeColName($key);
        if(is_array($val)) {
            $val = implode(" ", $val);
        } 
        $str = reflex_mysql::escape($val);
        $this->where("match($key) against ('$str' in boolean mode) ");
        return $this;
    }
    
    public function eqMaterializedPath($key, $val) {
        if(is_array($val)) {
            $mas = array();
            foreach($val as $item){
                $item = str_pad($item,5,"0",STR_PAD_LEFT);   
            }
        }else{
            $val = str_pad($val,5,"0",STR_PAD_LEFT);   
        }
        $this->match($key,$val);
        return $this;    
    } 
    
    public function isnull($key) {
        $key = $this->normalizeColName($key);
        $this->where("$key is null");
        return $this;
    }

    public function notnull($key) {
        $key = $this->normalizeColName($key);
        $this->where("$key is not null");
        return $this;
    }

    public function gt($key,$val) {
        $key = $this->normalizeColName($key);
        $val = mod::service("db")->quote($val);
        $this->where("{$key} > {$val}",$key);
        return $this;
    }

    public function geq($key,$val) {
        $key = $this->normalizeColName($key);
        $val = reflex_mysql::escape($val);
        $this->where("$key>='$val'",$key);
        return $this;
    }

    public function lt($key,$val) {
        $key = $this->normalizeColName($key);
        $val = reflex_mysql::escape($val);
        $this->where("$key<'$val'",$key);
        return $this;
    }

    public function leq($key,$val) {
        $key = $this->normalizeColName($key);
        $val = mod::service("db")->quote($val);
        $this->where("{$key} <= {$val}",$key);
        return $this;
    }



    protected $orMode = false;
    
    /**
     * Добавляет условие принодлежности точки в поле $key многоугольнику
     **/
    public final function inPolygon($key,$a) {
        $txt = "";
        $txt.= "MBRContains(GeomFromText('Polygon((";
        $points = array();
        foreach($a as $point)
            $points[] = implode(" ",$point);
        $txt.= implode(",",$points);
        $txt.= "))'),";
        $txt.= $this->normalizeColName($key);
        $txt.= ")=1";
        $this->where($txt);
        return $this;
    }

    /**
     * Добавляет условие чтобы поле $key лежало в прямоугольнике, образованном четырьмя координатами
     **/
    public final function inRect($key,$x1,$y1,$x2,$y2) {
        $this->inPolygon($key,array(
            array($x1,$y1),
            array($x2,$y1),
            array($x2,$y2),
            array($x1,$y2),
            array($x1,$y1),
        ));
        return $this;
    }

    /**
     * Добавляет условие чтобы поле $key лежало в прямоугольнике, образованном четырьмя координатами
     * В отличие от метода inRect этот метод правильно работает с географическими координатами
     * (ситуацию осложняет то что при переходе через долготу 180 или -180 край прямоугольника должен появиться с противоположной стороны карты)
     **/
    public final function inGeographicalRect($key,$x1,$y1,$x2,$y2) {
        if($x1>$x2) {
            $this->inRect($key,$x1,$y1,180,$y2)
                ->orr()
                ->inRect($key,-180,$y1,$x2,$y2);
        } else {
            $this->inRect($key,$x1,$y1,$x2,$y2);
        }
        return $this;
    }

    /**
     * Сортирует объекты по расстоянию до указанной точки
     * @todo метод считает дистанцию неправильно если используются сферические (использую сферические координаты как плоские). Надо добавить отдельный метод для географический сферических координат
     **/
    public function orderByDistance($key,$point) {

        $coords = mod::field("point")->value($point)->mysqlValue();

        $this->orderByExpr("GLength(
            LineStringFromWKB(
            LineString(
                `coords`,
                $coords
            )))");

        return $this;

    }

    /**
     * Устанавливает условие запроса
     * Данные никак не экранируются, вся ответственность на программисте
     **/
    public function where($where=null,$key=null) {

        if(func_num_args()==0) {
            return $this->whereQuery();
        }

        if(func_num_args()==1 || func_num_args()==2) {

            $w = array(
                "w" => $where,
                "key" => $key,
            );

            if($this->orMode)
                $w["or"] = true;

            $this->whereParts[] = $w;

            $this->orMode = false;
            return $this;

        }
    }

    public function having($having=0) {

        if(func_num_args()==0) {
            return $this->having;
        }

        if(func_num_args()==1) {
            $this->having = $having;
            return $this;
        }

    }

    /**
     * Убирает из ограничений на коллекцию ограничения на заданный ключ
     **/
    public function removeRestrictions($keyToRemove) {

        $keyToRemove = $this->normalizeColName($keyToRemove);

        $where = array();

        foreach($this->whereParts as $key=>$part)
            if($part["key"]!=$keyToRemove)
                $where[$key] = $part;

        $this->whereParts = $where;
        return $this;

    }

    /**
     * Возвращает WHERE-часть mysql-запроса
     **/
    public function whereQuery() {

        $parts = array(
            array(),
        );

        foreach($this->whereParts as $part) {

            if(!$part["or"])
               $parts[] = array();

            $parts[sizeof($parts)-1][] = "(".$part["w"].")";
        }

        $where = array();

        foreach($parts as $part) {
            if(count($part))
                $where[] = "(".implode(" or ",$part).")";
        }

        $where = implode(" and ",$where);

        if(!$where)
            $where = 1;

        return $where;

    }

    public function orr() {
        $this->orMode = true;
        return $this;
    }

    public function inverse() {
        $where = "not(".$this->whereQuery().")";
        $this->whereParts = array();
        $this->where($where);
        return $this;
    }

    public function groupBy($groupBY=null) {
        if(func_num_args()==0) {
            return $this->groupBy;
        }
        if(func_num_args()==1) {
            $this->groupBy = $groupBY;
            return $this;
        }
    }

    public function resetSort() {
        $this->sort = array();
        $this->param("*priority",false);
        return $this;
    }

	/**
	 * Устанавливает коллекции сортировку по возрастанию
	 **/
    public function asc($field, $keep = false) {
        if(!$keep) {
            $this->resetSort();
            $this->param("*priority",$field);
        }
        $this->sort[] = $this->normalizeColName($field)." asc";
        return $this;
    }

    public function desc($field, $keep = false) {
        if(!$keep) $this->resetSort();
        $this->sort[] = $this->normalizeColName($field)." desc";
        return $this;
    }

    public function orderByExpr($expr,$keep=false) {
        if(!$keep) $this->resetSort();
        $this->sort[] = $expr;
        return $this;
    }

    public function orderBy() {
        $ret = implode(",",$this->sort);
        if(!$ret) $ret = 1;
        return $ret;
    }

    public function one($index=0) {
        $this->callBeforeQuery();
        $this->limit(1);
        if(func_num_args()>0)
            $this->page($index+1);
        $this->load();
        $ret = $this->items[0];
        if(!$ret)
            return reflex::get($this->itemClass(),0);
        return $ret;
    }

    public function first() {
        $this->callBeforeQuery();
        $items = $this->copy();
        $items->limit(1);
        $items->load();
        $ret = $this->items[0];
        if(!$ret) {
            return Record::get($items->itemClass(),0);
		}
        return $ret;
    }

    public function rand() {
        $this->callBeforeQuery();
        $n = rand(1,$this->count());
        $this->limit(1)->page($n);
        $this->load();
        $ret = $this->items[0];
        if(!$ret) return reflex::get($this->itemClass(),0);
        return $ret;
    }

    /**
     * Возвразает сумму по колонке
     **/
    public function sum($key) {
        $this->callBeforeQuery();
        $key = $this->normalizeColName($key);
        $q = "select sum($key) from {$this->from()} where {$this->whereQuery()} ";
        return mod::service("db")->query($q)->exec()->fetchScalar();
    }

    /**
     * Возвразает максимальное значение по колонке
     **/
    public function max($key) {
        $this->callBeforeQuery();
        $key = $this->normalizeColName($key);
        reflex_mysql::query("select max($key) from {$this->from()} where {$this->whereQuery()} ");
        return reflex_mysql::get_scalar();
    }

    /**
     * Возвразает минимальное значение по колонке
     **/
    public function min($key) {
        $this->callBeforeQuery();
        $key = $this->normalizeColName($key);
        reflex_mysql::query("select min($key) from {$this->from()} where {$this->whereQuery()} ");
        return reflex_mysql::get_scalar();
    }

    /**
     * Возвразает среднее значение по колонке
     **/
    public function avg($key) {
        $this->callBeforeQuery();
        $key = $this->normalizeColName($key);
        reflex_mysql::query("select avg($key) from {$this->from()} where {$this->whereQuery()} ");
        return reflex_mysql::get_scalar();
    }

    /**
     * Возвразает массив уникальных значений по колонке
     **/
    public function distinct($key,$fn=null) {
        $this->callBeforeQuery();
        $key = $this->normalizeColName($key);
        if($fn) $key = "$fn($key)";
        reflex_mysql::query("select distinct $key from {$this->from()} where {$this->whereQuery()} order by {$this->orderBy()} ");
        return reflex_mysql::get_col();
    }

    /**
     * Возвращает список первичных ключей коллекции
     **/
    public function idList() {
        $this->callBeforeQuery();
        $from = intval(($this->page-1)*$this->perPage);
        $perPage = intval($this->perPage);
        $key = $this->normalizeColName("id");
        $query = "select $key from {$this->from()} where {$this->whereQuery()} ";
        if($g=$this->groupBy())
            $query.= " group by {$g} ";
        $query.= " order by {$this->orderBy()} ";
        if($perPage) {
            $query.= "limit $from,$perPage";
		}
        reflex_mysql::query($query);
        return reflex_mysql::get_col();

    }

    /**
     * Удаляет все элементы в последовательности.
     * Триггеры игнорируются.
     **/
    public function delete() {
        $this->callBeforeQuery();
        $query = "delete `{$this->itemClass()}` from {$this->fromWithoutJoins()} where {$this->whereQuery()} ";
        return \mod::service("db")->query($query)->exec();
    }

    /**
     * Устанавливает значение поля $key равным $val для всей коллекции.
     * Функция использует быстрые операции mysql, поэтому триггеры игнорируются.
     **/
    public function data($key,$val) {
        if(func_num_args()==2) {
            //$key = reflex_mysql::escape($key);
            $val = mod::service("db")->quote($val);
            $query = "update {$this->from()} set `$key`=$val where {$this->whereQuery()} ";
            mod::service("db")->query($query)->exec();
            return $this;
        }
    }

    /**
     * Устанавливает / возвращает заголовок коллекции
     * Это равносильно вызову метода reflex_collection::param("title",$myTitle)
     **/
    public final function title($title=null) {
        if(func_num_args()==0) {
            return $this->param("title");
        } else {
            $this->param("title",$title);
            return $this;
        }
    }

    /**
     * Устанавливает / возвращает иконку коллекции
     * Это равносильно вызову метода reflex_collection::param("icon",$myTitle)
     **/
    public final function icon($title=null) {
        if(func_num_args()==0) {
            $ret = $this->param("icon");
            if(!$ret)
                $ret = $this->editor()->icon();
            return $ret;
        } else { $this->param("icon",$title); return $this; }
    }

    /**
     * Создает элемент в коллекции
     * Если на коллекцию были наложены ограничения типа ->eq("field","value")
     * они будут использоваться как значения полей для создаваемого объекта
     **/
    public function create($p=null) {
        $this->callBeforeQuery();
        foreach($this->eqs as $key=>$val)
            $new[$key] = $val;
        if(is_array($p))
        foreach($p as $key=>$val)
            $new[$key] = $val;
        return reflex::create($this->itemClass(),$new);
    }

    /**
     * Создает виртуальный объект коллекции
     * Если на коллекцию были наложены ограничения сравнения ->eq(),
     * то они будут учтены при заполнении полей объекта
     **/
    public function virtual($p=null) {

        $this->callBeforeQuery();
        foreach($this->eqs as $key=>$val) {
            $new[$key] = $val;
        }

        if(is_array($p)) {
            foreach($p as $key=>$val) {
                $new[$key] = $val;
            }
        }

        return reflex::virtual($this->itemClass(),$new);
    }

    public final function useFilter($n) {
        if($ff = $this->editor()->filters()) {
            foreach($ff as $key=>$filter) {
                if($key==$n) {
                    $this->superposition($filter);
                }
            }
        }
        return $this;
    }

    /**
     * Возвращает редактор для виртуального элемента коллекции
     **/
    public final function editor() {

        $editorClass = $this->param("editor");
        $virtual = $this->virtual();

        if(!$editorClass)
            $editorClass = get_class($virtual->editor());

        $editor = new $editorClass($virtual);

        return $editor;

    }

    /**
     * Устанавливает у коллекции класс-редактор
     **/
    public final function setEditor($class) {
        $this->param("editor",$class);
        return $this;
    }

    /**
     * Возвращает массив редакторов элементов
     **/
    public function editors() {

        $class = get_class($this->editor());

        $ret = array();
        foreach($this as $item) {
            $ret[] = new $class($item->id());
        }

        return $ret;

    }

}
