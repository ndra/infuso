<?

/**
 * Класс-огбертка для работы с сессией
 **/
class mod_session {

	private static $started;
	public static function start() {
		if(self::$started)
		    return;
		session_start();
		self::$started = true;
	}

	public static function get($key) {
		self::start();
		return $_SESSION[$key];
	}

	public static function set($key,$val) {
		self::start();
		$_SESSION[$key] = $val;
	}

}
