<?

/**
 * Модель роли пользователя
 **/
class user_role extends reflex {

	/**
	 * Возвращает коллекцию всех ролей
	 **/
	public static function all() {
	    return user_operation::all()->eq("role",true);
	}

	/**
	 * @return Возвращает роль по коду
	 **/
	public static function get($code) {
	    return self::all()->eq("code",$code)->one();
	}
	
	/**
	 * Раздлы для каталога
	 **/
	public static function reflex_root() {
	    $ret = array();
	    if(mod_superadmin::check())
	        $ret[] = self::all()->param("tab","user")->title("Роли");
		return $ret;
	}
	

	/**
	 * Конструктор роли
	 **/
	public static function create($code,$title=null) {
	    return reflex::create("user_operation",array(
	        "role" => true,
	        "code" => $code,
	        "title" => $title,
		));
	}
	
	public function parentRole() {
	    return user_role::get($this->data("parentRole"));
	}
	
}
