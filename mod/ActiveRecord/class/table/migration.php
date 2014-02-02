<?

namespace infuso\ActiveRecord;

use infuso\core\mod;
use infuso\core\file;

/**
 * Класс миграции mysql
 **/
class tableMigration {

    private $table = null;

    /**
     * сюда будут складываться кусочки запросов по изменению таблицы
     **/
    private $q = array();

    public function __construct(table $table) {
        $this->table = $table;
    }

    /**
     * Миграция таблицы до актуального состояния
     **/
    public function migrateUp() {

        if(!$this->table()->name())
            return;

        $this->createTable();

        $this->updateEngine();

        // Проверяем таблицу на наличие дублирующихся полей
        $names = array();
        foreach($this->table()->fields() as $field) {
            if(in_array($field->name(),$names)) {
                mod::msg("Duplicate field name <b>{$field->name()}</b> in table <b>{$this->table()->name()}</b>",1);
            }
            $names[] = $field->name();
        }

        // Добавляем / восстанавливаем нужные поля
        foreach($this->table()->fields() as $field)
            $this->updateField($field);

        // Удаляем лишние поля
        foreach($this->realFields() as $field) {
            if(!$this->table()->field($field)->exists()) {
                mod::msg("Field ".$this->table()->name().".".$field." not exists in model. You need to remove it manually. ");
                //$this->deleteField($field);
            }
        }

        $this->updateIndex();

        if(sizeof($this->q)) {
            $q = implode(", ",$this->q);
            $q = "alter table `{$this->table()->prefixedName()}` $q";
            mod::trace($q);
            return mod::service("db")->query($q)->exec();
        }

    }

    public function updateEngine() {
		$query = "SHOW TABLE STATUS like '{$this->table()->prefixedName()}' ";
        $status = mod::service("db")->query($query)->exec()->fetch();
        $engine = $status["Engine"];
        if($engine!="MyISAM") {
            $this->q[] = "ENGINE=myisam";
		}
    }

    /*public function updateRowFormat() {
        reflex_mysql::query("SHOW TABLE STATUS like '{$this->table()->prefixedName()}' ");
        $status = reflex_mysql::get_row();
        $format = $status["Row_format"];
        if($format!="Fixed") {
            $this->q[] = "ROW_FORMAT = FIXED";
            mod::msg("change row format");
        }
    }*/

    /**
     * Возвращает таблицу (она передавалась в конструктор)
     **/
    public function table() {
        return $this->table;
    }

    public function needType($field) {

        $type = $field->mysqlType()." ";

        if(preg_match("/(varchar)|(longtext)/i",$type))
            $type.= "COLLATE utf8_general_ci ";

        if(!$field->mysqlNull())
            $type.="NOT NULL ";

        if($field->mysqlAutoIncrement())
            $type.= "auto_increment ";

        return strtolower($type);

    }

    public function existsType($field) {
        $descr = $this->describeField($field);
        $ret = $descr["Type"]." ";

        if($c = $descr["Collation"])
            $ret.= "collate ".$c." ";

        if($descr["Null"]=="NO")
            $ret.= "NOT NULL ";
        if($descr["Extra"]=="auto_increment")
            $ret.= "auto_increment ";
        return strtolower($ret);
    }

    public function updateField($field) {

        $a = $this->needType($field);
        $b = $this->existsType($field);
        $descr = $this->describeField($field);

        if(!$descr) {
            $this->createField($field,$a);
        } else {

            $alter = trim($a)!=trim($b);
            $type = $a;

            // Если да, меняем поле
            if($alter) {
                $this->q[] = "MODIFY `{$field->name()}` $type ";
                mod::msg("alter {$this->table()->name()}.{$field->name()}");
            }
        }
    }

    /**
     * Создает таблицу, если ее еще нет
     * Вызывается в начале миграции
     **/
    public function createTable() {
        $table = $this->table()->prefixedName();
        $query = "create table if not exists `$table` (`id` bigint(20) primary key) ";
        mod::service("db")->query($query)->exec();
    }

    /**
     * Возвращает описание поля
     **/
    public function describeField($field) {
        $query = "show full columns from `{$this->table()->prefixedName()}` like '{$field->name()}' ";
        return mod::service("db")->query($query)->exec()->fetch();
    }

    /**
     * Возвращает список полей в реальной таблице
     **/
    public function realFields() {
        $query = "show full columns from `{$this->table()->prefixedName()}` ";
        return mod::service("db")->query($query)->exec()->fetchCol("Field");
    }

    /**
     * Добавляет поле в таблиу
     **/
    public function createField($field,$descr) {
        $this->q[] = "add `{$field->name()}` $descr";
    }

    /**
     * Удаляет поле из таблицы
     **/
    public function deleteField($field) {
        mod::msg("delete {$this->table()->name()}.{$field}");
        $this->q[] = "drop `{$field}`";
    }

    /**
     * Обновляет индекс до требуемого состояния
     **/
    public function updateIndex() {

        $q = array();

        // Индексы, которые должны быть
        $a = array();
        foreach($this->table()->indexes() as $index) {
            $fields = \infuso\util\util::splitAndTrim($index->fields(),",");
            sort($fields);
            $a[$index->name()]["fields"] = $fields;
            $a[$index->name()]["type"] = $index->type();
        }
        $a["PRIMARY"] = array(
            "fields" => array("id"),
            "type" => "index",
        );

        // Индексы, которые реально есть
        $b = array();
        $query = "show index from `{$this->table()->prefixedName()}` ";
        $items = mod::service("db")->query($query)->exec()->fetchAll();
        
        foreach($items as $index) {
            $name = $index["Key_name"];
            $indexDescr = $index["Column_name"];
            if($n=$index["Sub_part"])
                $indexDescr.= "(".$n.")";
            $b[$name]["fields"][] = $indexDescr;
            $b[$name]["type"] = $index["Index_type"]=="BTREE" ? "index" : "fulltext";
        }

        // Сортируем поля
        foreach($b as $key=>$val) {
            $fields = $val["fields"];
            sort($fields);
            $b[$key]["fields"] = $fields;
        }

        // Добавляем/изменяем индексы
        foreach($a as $name=>$index) {

            $hash1 = serialize($index);
            $hash2 = serialize($b[$name]);

            $fields = array();
            foreach($index["fields"] as $field) {
                preg_match("/^([^()]*)(\(\d+\))?$/i",$field,$matches);
                $fields[] = "`".$matches[1]."`".($matches[2] ? " {$matches[2]}" : "");
            }

            $fields = implode(",",$fields);

            if($hash1!=$hash2) {

                mod::msg("alter index {$this->table()->name()}.$name ");

                if(array_key_exists($name,$b))
                    $this->q[] = "drop index `$name`";

                $type = $index["type"];

                if($name=="PRIMARY")
                    $this->q[] = "add primary key($fields)";
                else
                    $this->q[] = "add $type `$name` ($fields) ";
            }

        }

        // Убираем ненужные индексы
        foreach($b as $name=>$fuck) {
            if(!array_key_exists($name,$a)) {
                $this->q[] = "drop index `$name`";
			}
		}

    }

    // Удаляет таблицу
    /*public static function deleteTable()    {
        $table = reflex_mysql::getPrefixedTableName($table);
        reflex_mysql::query("drop table `$table` ");
    } */

}
