<?

namespace infuso\core;

class superadmin extends controller {

    private static $checked = false;
	private static $checkResult = false;
	private static $storedPassword = null;

    public static function changePassword($p) {

        $p1 = trim($p["p1"]);
        $p2 = trim($p["p2"]);
        if($p1!=$p2) {
            return;
        }

        $hash = mod_crypt::hash($p1);
        mod_file::get(mod::app()->confPath()."/__superadmin.txt")->put($hash);
    }

    /**
     * Проверяет является ли пользователь суперадмином
     **/
    public static function check() {

        if(!self::$checked) {

            self::$checked = true;

	        @session_start();
            $password = array_key_exists("mod:superadminPasswordHash", $_SESSION) ? trim($_SESSION["mod:superadminPasswordHash"]) : null;
	        $hash = self::getStoredPassword();

	        if($hash==="0000") {
	            self::$checkResult = ($hash===$password);
	        } else {
	            self::$checkResult = \mod_crypt::checkHash($hash,$password);
            }
		}

		return self::$checkResult;
    }

    public static function getStoredPassword() {

        if(self::$storedPassword===null) {
            self::$storedPassword = trim(file::get(mod::app()->confPath()."/__superadmin.txt")->data());
        }

        return self::$storedPassword;
    }

    public static function is0000() {
        return self::getStoredPassword()==="0000";
    }

	public static function postTest() {
		return true;
	}

    /**
     * Пробует авторизоваться в качестве суперадмина
     **/
    public static function post_logout() {
		self::logout();
	}

    public static function logout() {
        @session_start();
        unset($_SESSION["mod:superadminPasswordHash"]);
    }

    public static function post_login($p) {
		self::login($p["password"]);
	}

    public static function login($password) {
        @session_start();
        $_SESSION["mod:superadminPasswordHash"] = $password;
        self::$checked = false;
    }

}
