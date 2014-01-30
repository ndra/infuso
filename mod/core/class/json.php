<?

/**
 * Контроллер для json-запросов
 **/
class mod_json extends \infuso\core\controller {

	public function indexTest() {
	    return true;
	}
	
	public function index() {

		ob_start();

		header("Content-type: text/plain; charset=utf-8");
		header("Content-Disposition: inline; filename=result.txt");

		try {

			$data = $_POST["data"];
			$data = json_decode($data,1);

			$jret = \infuso\core\post::process(
				$data,
				$_FILES,
				$status
			);

			// Если скрипт вывел что-нибудь в поток, выводим это как сообщение
			$txt = ob_get_clean();
			if($txt) {
				mod::msg($txt,1);
			}

			reflex::storeAll();

		} catch(Exception $ex) {

			mod::msg("<b>Exception:</b> ".$ex->getMessage(),1);

		}

		// Собираем массив сообщений
		$messages = array();
		foreach(\infuso\core\log::messages() as $msg) {
			$messages[] = array(
				"text" => $msg->text(),
				"error" => $msg->error(),
			);
		}

		// Собираем массив событий
		$events = array();
		foreach(\infuso\core\event::all() as $event) {
			$events[] = array(
				"name" => $event->name(),
				"params" => $event->params()
			);
		}

		$ret = array(
			"messages" => $messages,
			"events" => $events,
			"data" => $jret,
			"completed" => !!$status,
		);


		$json = new mod_confLoader_json();
		echo $json->write($ret);
		
	}

}
