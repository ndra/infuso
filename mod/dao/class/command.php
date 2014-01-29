<?

namespace infuso\dao;

class command extends \infuso\core\component {

	private $query;
	private $connection;

	public function __construct($connection, $query) {
	    $this->query = $query;
	    $this->connection = $connection;
	}
	
	public function connection() {
	    return $this->connection;
	}
	
	public function exec() {
	    $dbh = $this->connection()->dbh();
	    $result = $dbh->query($this->query);
	    
	    $error = $dbh->errorInfo();
	    if($error[0] != "00000") {
	        throw new \Exception($this->query." ".$error[2]);
	    }
	    
	    return new reader($result,$dbh->lastInsertId());
	}

}
