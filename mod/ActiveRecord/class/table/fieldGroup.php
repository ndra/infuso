<?

namespace infuso\ActiveRecord;

/**
 * Класс описывающий таблицу
 **/
class fieldGroup extends \infuso\core\component {

    private $table = null;

    public function __construct($p = array()) {
        $this->params($p);
    }

    public function serialize() {
        return $this->params();
    }
    
    public function setTable($table) {
        $this->table = $table;
    }
    
    public function table() {
        return $this->table;
    }
    
    public function fields() {
    
        $fields = array();
        
        foreach($this->table()->fields() as $field)
            if($field->group()==$this->name())
                $fields[] = $field;
        
        return $fields;
    }
    
    public function dataWrappers() {
        return array(
            "title" => "mixed",
            "id" => "mixed",
            "name" => "mixed",
        );
    }
    
}
