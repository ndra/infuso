<?

/**
 * Отвечает за то что мы видем в каталоге в разделе «Редиректы»
 **/

class reflex_redirect_route extends mod_route {

	/**
	 * url => action
	 **/
	public function forward($url) {
		$path = $url->relative();
		$redirect = reflex_redirect::all()->eq("source",$path)->one();
		if($redirect->exists()) {
			return mod::action("reflex_redirect","redirect",array(
			    "target" => $redirect->data("target"),
			));
		}
	}

	public function backward($controller) {}

}
