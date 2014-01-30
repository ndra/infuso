<?

namespace infuso\dao;

/**
 * Класс для чтения результата запроса
 **/
class reader {

	private $statement;
	private $lastInsertId;

	public function __construct(\PDOStatement $statement,$lastInsertId) {
	    $this->statement = $statement;
	    $this->lastInsertId = $lastInsertId;
	}
	
	public function fetchAll() {
	    $this->statement->setFetchMode(\PDO::FETCH_ASSOC );
	    return $this->statement->fetchAll();
	}
	
	public function fetch() {
		return $this->statement->fetch();
	}
	
	public function fetchCol($col) {
	    $ret = array();
		foreach ($this->fetchAll() as $row) {
		    $ret[] = $row[$col];
		}
		return $ret;
	}
	
	public function fetchScalar($col = null) {
	    $row = $this->fetch();
	    if( $col === null ) {
			$col = key($row);
	    }
	    return $row[$col];
	}
	
	public function lastInsertId() {
	    return $this->lastInsertId;
	}

}
