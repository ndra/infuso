<?

namespace infuso\core;

class controller extends component {

	private $redirectUrl = null;

	public function defaultBehaviours() {
		return array(
		    "mod_controller_behaviour"
		);
	}
	
	public final function redirect($url) {
	
	    // Выполняем редирект только если есть экшн
		if(action::current()) {
		    $this->redirectUrl = $url;
			$this->defer("processRedirect");
		}
	}
	
	public final function processRedirect() {
	    header("Location:{$this->redirectUrl}");
	    die();
	}
    
}
