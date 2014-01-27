<?

/**
 * Контроллер авторизации в социальной сети
 **/
class user_social_action extends mod_controller {

	public function indexTest() {
		return true;
	}

	public function index_list() {
		tmp::exec("user:social.list");
	}

	/**
	 * Экшн возврата на наш сайт с сайта ulogin
	 **/
	public function index_back() {

		$s = file_get_contents('http://ulogin.ru/token.php?token=' . $_POST['token'] . '&host=' . $_SERVER['HTTP_HOST']);
		$data = json_decode($s, true);

		if(!$data["identity"]) {
		    tmp::header();
		    echo $data["error"];
		    tmp::footer();
		    return;
		}

		//Редирект на главную страницу
		header("location:/");
		
		$link = user_social::all()->eq("identity",$data["identity"])->one();
		$user = $link->pdata("userID");

		// Если можно авторизоваться по социальному профилю
		if($user->exists() && !user::active()->exists()) {

		    $user->activate();

		// Если нельзя авторизоваться по социальному профилю
		} else {

			// Удаляем все старые профили с такой же идентификацией
			user_social::all()->eq("identity",$data["identity"])->delete();

	        // Создаем новый профиль
			$social = reflex::create("user_social",array(
			    "identity" => $data["identity"],
			    "userID" => user::active()->id(),
			    "data" => $data,
			));

			if(!user::active()->exists()) {
				user_social::addActive($social);
				
				$url = mod::action("user_social_action","registerOrLogin")->url();
				header("location:$url");
				
			}

		}
		
        // Выбрасываем событие редиректа при социальной авторизации
        // Вы можете среагировать на это сообщение и сделать редирект
        mod::fire("user_socialAfterLoginRedirect", array(
        	"social" => $link,
        ));
	}

	public function index_registerOrLogin() {
		tmp::exec("/user/social/registerOrLogin");
	}

	public function postTest() {
		return true;
	}

	public function post_register($p) {
		switch($p["action"]) {
		    case "register":
		        $url = mod::action("user_action","register")->url();
		        header("location:$url");
		        break;
		    default:
		        $url = mod::action("user_action","login")->url();
		        header("location:$url");
		        break;
		}
	}
	
	public function post_unlink($p) {
	
	    $social = user_social::get($p["socialID"]);
	
	    if(!user::active()->checkAccess("user:unlinkSocial",array(
	        "social" => $social
		))) {
		    mod::msg("Не удалось удалить социальный профиль",1);
	        return false;
		}
	    $social->delete();
	    return true;
	}

}
