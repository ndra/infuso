<?

/**
 * роли пользователя
 * @todo выпилить этот класс т.к. вместо него есть user_operation
 **/
class user_role {

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
	 * Конструктор роли
	 **/
	public static function create($code,$title=null) {
	    return reflex::create("user_operation",array(
	        "role" => true,
	        "code" => $code,
	        "title" => $title,
		));
	}
	
}
