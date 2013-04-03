<?

class reflex_mysql {

    private static $connection = false;
    private static $result = false;
    private static $vars = array();
    private static $log_enabled = false;
    private static $log = array();
    private static $queries = 0;
    private static $cached = 0;
    private static $trace = 0;

    private static $variables = array();

    private static function getVariables() {
        reflex_mysql::query("SHOW SESSION STATUS");
        $ret = array();
        foreach(reflex_mysql::get_array() as $row) {
            $ret[$row["Variable_name"]] = $row["Value"];
        }
        return $ret;
    }

    private static function keepVariables() {
        self::$variables = self::getVariables();
    }

    public function getDiffVariables() {
        $ret = array();

        $a = self::getVariables();
        $b = self::$variables;
        foreach($a as $key=>$val) {
            $ret[$key] = $a[$key] - $b[$key];
        }

        return $ret;

    }

    public static function connect() {
        if(self::$connection) return;
        self::$connection = @mysql_connect(
            mod::conf("reflex:mysql_host"),
            mod::conf("reflex:mysql_user"),
            mod::conf("reflex:mysql_password"));
        @mysql_select_db(mod::conf("reflex:mysql_db"),self::$connection);
        @mysql_query("set CHARACTER SET utf8",self::$connection);
        @mysql_query("set NAMES utf8",self::$connection);
        @mysql_query("SET SESSION sql_mode = ''",self::$connection);

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
        foreach($a as $key=>$val)
            $a[$key] = @$char.self::escape($val).$char;
        return implode(",",$a);
    }

    public static function prefix() {
        return mod::conf("reflex:mysql_table_prefix");
    }

    public static function clearCache() {
        self::$cache = array();
    }

    private static $cache = array();
    public static function query($query) {

        $query = trim($query);
        if(!preg_match("/^select/i",$query))
            self::clearCache();

        $params = array();
        for($i=1;$i<func_num_args();$i++)
            $params[$i] = func_get_arg($i);

        $hash = $query.":".serialize($params);
        if(self::$cache[$hash]) {
            self::$result = self::$cache[$hash];
            self::$cached++;
            return true;
        }

        self::$queries++;
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


        if(self::$trace) {
            $str = $query."\n";
            $str.= "Executed ".number_format(microtime(1)-$time,4)." с";
            mod::trace($str);
        }

        // Сохраняем результат в self::$result
        self::$result = array();
        if(is_resource($result)) {
            while($item = mysql_fetch_assoc($result)) {
                array_push(self::$result,$item);
            }
        }

        if(mysql_error()) {
            if(mod_superadmin::check()) mod::msg(mysql_error(self::$connection),1);
            mod::trace("Error in query ".$query.": ".mysql_error(self::$connection));
            mod_profiler::endOperation();
            return false;
        }

        mod_profiler::endOperation();
        return true;
    }

    // Возвращает результат запроса ввиде массива
    // Если задано $index_field, то оно используется в качестве индекса в возвращаемом массиве
    public static function get_array($index_field=null)    {
        if(!self::$result)
            return array();
        $ret = array();
        foreach(self::$result as $row)
            if($index_field) $ret[$row[$index_field]] = $row;
            else array_push($ret,$row);

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

    public static function enable_log() {
        self::$log_enabled=true;
    }

    public static function print_log() {

        echo "<div style='background:#ededed;border:1px solid #cccccc;padding:10px;'>";
        foreach(self::$log as $item)
        {
            echo "<div>$item[query]</div>";
            echo "<div>error:$item[error]</div>";
            echo "<div>result:$item[result]</div>";
        }
        echo "</div>";
    }

    // Возвращает количество запросов
    public static function queries() {
        return self::$queries;
    }

    // Возвращает самый длинный запрос
    public static function cached() {
        return self::$cached;
    }

    // Включает трассировку запросов в лог
    public static function trace() {
        self::$trace = true;
    }

}
