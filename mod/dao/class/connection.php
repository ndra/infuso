<?

namespace infuso\dao;

class connection extends \infuso\core\service {

	/**
	 * Объект класса PDO, создающийся при соедниении
	 **/
	private $dbh;
	
	/**
	 * Флаг того, что соединение с БД установлено
	 **/
	private $connected = false;

	public function defaultService() {
	    return "db";
	}
	
	public function query($query) {
	    return new command($this,$query);
	}
	
	/**
	 * Устанавливает соединение с базой данных
	 **/
	public function connect() {
		$dsn = $this->param("dsn");
		$user = $this->param("user");
		$password = $this->param("password");
	    $this->dbh = new \PDO($dsn, $user, $password);
	}
	
	public function quote($str) {
	    return $this->dbh()->quote($str);
	}
	
	public function tablePrefix() {
	    return "infuso_";
	}
	
	/**
	 * Создает соединение (если оно еще не было создано)
	 * Возвращает объект класса PDO, создающийся при соедниении
	 **/
	public function dbh() {
	
	    if(!$this->connected) {
		    $this->connect();
		    return $this->dbh;
	    }
	}

}
