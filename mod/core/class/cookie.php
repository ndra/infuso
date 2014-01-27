<?

/**
 * Класс-обертка для рабты с куками
 **/
class mod_cookie {

	public static function get($key) {
		return $_COOKIE[$key];
	}

	public static function set($key,$val,$keepDays=30) {
		$expire = $keepDays ? time()+60*60*24*$keepDays : null;
		setcookie($key,$val,$expire,"/");
		$_COOKIE[$key] = $val;
	}

}
