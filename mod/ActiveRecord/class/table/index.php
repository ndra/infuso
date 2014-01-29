<?

use \infuso\util\util;

class reflex_table_index extends mod_component {

	private $table = null;

	public static function get($data) {
		if(is_object($data) && get_class($data)=="reflex_table_index") {
		    return $data;
		}
		return new self($data);
	}

	public function __construct($data=array()) {
	    if(!$data["id"]) {
	        $data["id"] = util::id();
		}
		$this->params($data);
	}

	public final function setData($data) {
	    $this->params($data);
	}

	/**
	 * Возвращает id индекса
	 **/
	public function id() {
		return $this->param("id");
	}
	
	/**
	 * Возвращает массив с данными индекса
	 **/
	public function serialize() {
		return array(
		    "id" => $this->id(),
		    "name" => $this->name(),
		    "fields" => $this->fields(),
		    "type" => $this->type(),
		);
	}

	public function dataWrappers() {
	    return array(
	        "name" => "mixed",
	        "fields" => "mixed",
	        "automatic" => "mixed",
		);
	}

	public static function create() {
		return new self();
	}

	/**
	 * Возвращает / устанавливает тип индекса BTREE или FULLTEXT
	 **/
	public function type($type=null) {

		if(func_num_args()==0) {
			if($this->param("type")=="fulltext")
				return "fulltext";
		    return "index";
	    }

	    if(func_num_args()==1) {
	        $this->param("type",$type);
	        return $this;
		}
	}

	public function fulltext() {
		return $this->type("fulltext");
	}

	public final function table() {
		return $this->table;
	}

	public final function setTable($table) {
	    $this->table = $table;
	}

}
