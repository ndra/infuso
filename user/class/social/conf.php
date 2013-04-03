<?

class user_social_conf extends mod_conf {

	public function name() {
		return "user";
	}

	public function conf() {
		return array(
		    array(
				"title" => "Вход через соцсети",
				"id" => "user:social",
				"type" => "checkbox",
			)
		);
	}

}
