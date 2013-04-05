<?

class reflex_mysql {

    private static $connection = false;
    private static $result = false;

    /**
     * Массив для быстрого кэша в пределах запроса
     **/
    private static $cache = array();

	/**
	 * Массив, куда будут сложены переменные сессии (для отладки)
	 **/
    private static $variables = array();

	/**
	 * Возвращает переменные сессии
	 **/
    private static function getVariables() {
        reflex_mysql::query("SHOW SESSION STATUS");
        $ret = array();
        foreach(reflex_mysql::get_array() as $row) {
            $ret[$row["Variable_name"]] = $row["Value"];
        }
        return $ret;
    }

	/**
	 * Сохраняет переменные сессии
	 * Вызывается при установке соединения с mysql
	 **/
    private static function keepVariables() {
        self::$variables = self::getVariables();
    }

	/**
	 * Возвращает разницу между переменными сесси в начале работы приложения и в конце.
	 * Используется в профайлере
	 **/
    public function getDiffVariables() {
        $ret = array();

        $a = self::getVariables();
        $b = self::$variables;
        foreach($a as $key=>$val) {
            $ret[$key] = $a[$key] - $b[$key];
        }
        
        return $ret;
    }

	/**
	 * Устанавливает соединение с mysql
	 **/
    public static function connect() {
		
        if(self::$connection) {
			return;
		}
		
        self::$connection = mysql_connect(
            mod::conf("reflex:mysql_host"),
            mod::conf("reflex:mysql_user"),
            mod::conf("reflex:mysql_password"));
            
		if(!self::$connection) {
		    throw new Exception("Cannot connect mysql");
		}
            
        mysql_select_db(mod::conf("reflex:mysql_db"),self::$connection);
        mysql_query("set CHARACTER SET utf8",self::$connection);
        mysql_query("set NAMES utf8",self::$connection);
        mysql_query("SET SESSION sql_mode = ''",self::$connection);

        if(mod::debug()) {
            self::keepVariables();
        }
    }

    public static function getPrefixedTableName($table) {
        return mod::conf("reflex:mysql_table_prefix").$table;
    }

    public static function escape($str) {
        self::connect();
        return mysql_real_escape_string($str,self::$connection);
    }

    public static function insertID() {
        return mysql_insert_id(self::$connection);
    }

    public static function quote_array($a,$char) {
        foreach($a as $key=>$val) {
            $a[$key] = @$char.self::escape($val).$char;
        }
        return implode(",",$a);
    }

    public static function prefix() {
        return mod::conf("reflex:mysql_table_prefix");
    }

    public static function clearCache() {
        self::$cache = array();
    }
    
    public static function query($query) {

        $query = trim($query);
        if(!preg_match("/^select/i",$query)) {
            self::clearCache();
		}

        $params = array();
        for($i=1;$i<func_num_args();$i++)
            $params[$i] = func_get_arg($i);

        $hash = $query.":".serialize($params);
        if(self::$cache[$hash]) {
            self::$result = self::$cache[$hash];
            return true;
        }

        $ret = self::aquery($query,$params);
        self::$cache[$hash] = self::$result;
        return $ret;
    }

    /**
     * Выполняет SQL-запрос
     **/
    private static function aquery($query)    {
    
        preg_match("/\w+/",$query,$matches);
        mod_profiler::beginOperation("mysql",$matches[0],$query);
        
        self::connect();
        
        $t = time();

        $result = @mysql_query($query,self::$connection);

        $d = time() - $t;

        if($d>10) {
            mod::trace("slow query: $query ($d s.)");
        }

        // Сохраняем результат в self::$result
        self::$result = array();
        if(is_resource($result)) {
            while($item = mysql_fetch_assoc($result)) {
                array_push(self::$result,$item);
            }
        }

        if(mysql_error()) {
            throw new Exception("Error in query ".$query.": ".mysql_error(self::$connection));
        }

        mod_profiler::endOperation();
        return true;
    }

    /**
     * Возвращает результат запроса ввиде массива
     * Если задано $index_field, то оно используется в качестве индекса в возвращаемом массиве
     **/
    public static function get_array($index_field=null)    {
    
        if(!self::$result) {
            return array();
		}
        $ret = array();
        foreach(self::$result as $row) {
            if($index_field) {
				$ret[$row[$index_field]] = $row;
			} else {
				array_push($ret,$row);
			}
        }

        return $ret;
    }

    // Возвращает столбец запроса
    public static function get_col($field=null,$index_field=null) {
        $ret = array();
        foreach(self::$result as $row)
            if(!$index_field)
                array_push($ret,@($field ? $row[$field] : end($row)));
            else
                $ret[$row[$index_field]] = $row[$field];
        return $ret;
    }

    // Возвращает первую строку результата
    public static function get_row() {
        return end(self::$result);
    }

    // Возвращает значение первого столбца первой строки
    public static function get_scalar() {
        return @end(end(self::$result));
    }

    public static function scalar() {
        return @end(end(self::$result));
    }

}
