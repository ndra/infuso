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
	
	public function query() {
	    return $this->query;
	}
	
	public function exec() {
	    $dbh = $this->connection()->dbh();
	    $result = $dbh->query($this->query());
	    return $result;
	}

}
