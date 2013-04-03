<?

/**
 * Контроллер управления пользователями из админки
 **/
class user_manager extends mod_controller {

	public function postTest() {
		return true;
	}
	
	/**
	 * Контроллер, возвращающий список ролей пользователя для админки
	 **/
	public function post_getRoles($p) {
	
	    if(!user::active()->checkAccess("user:viewRoles")) {
	        mod::msg("Вы не можете просматривать роли пользователей",1);
	        return array();
	    }
	
	    $ret = array();
	    foreach(user::get($p["userID"])->roles() as $role) {
	        $ret[] = array(
	            "id" => $role->code(),
	            "text" => $role->title(),
			);
		}
		
		return $ret;
	}
	
	/**
	 * Контроллер, возвращающий полный список ролей для админки
	 **/
	public function post_enumRoles($p) {

	    if(!user::active()->checkAccess("user:viewRoles")) {
	        mod::msg("Вы не можете просматривать роли",1);
	        return array();
	    }

	    $ret = array();
	    foreach(user_role::all() as $role) {
	        $ret[] = array(
	            "id" => $role->code(),
	            "text" => $role->title(),
			);
		}

		return $ret;
	}

	
	/**
	 * Контроллер, удаляющий ролдь в админке
	 **/
	public function post_deleteRoles($p) {

	    if(!user::active()->checkAccess("user:deleteRole")) {
	        mod::msg("Вы не можете удалять роли пользователей",1);
	        return array();
	    }
	    
	    $user = user::get($p["userID"]);
	    
	    foreach($p["roles"] as $role)
	    	$user->removeRole($role);

	}
	
	/**
	 * Контроллер, добавляющий роль в админке
	 **/
	public function post_addRole($p) {

	    if(!user::active()->checkAccess("user:addRole")) {
	        mod::msg("Вы не можете добавлять роли пользователей",1);
	        return array();
	    }

	    $user = user::get($p["userID"]);
	    
    	$user->addRole($p["role"]);

	}

	/**
	 * Контроллер изменения пароля
	 **/
	public function post_changePassword($p) {
	
	    if(!user::active()->checkAccess("user:editorChangePassword")) {
	        mod::msg("Вы не можете менять пароль пользователя",1);
	        return array();
	    }
	
	    $ret = user::get($p["userID"])->changePassword($p["password"]);
	    if($ret)
	        mod::msg("Пароль изменен");
	}

	/**
	 * Контроллер изменения электронной почты
	 **/
	public function post_changeEmail($p) {
	
	    if(!user::active()->checkAccess("user:editorChangePassword")) {
	        mod::msg("Вы не можете менять электронную почту пользователя",1);
	        return array();
	    }
	
	    $ret = user::get($p["userID"])->changeEmail($p["email"]);
	    if($ret) mod::msg("Электронная почта изменена");
	}

}
