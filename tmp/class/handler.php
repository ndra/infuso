<?

class tmp_handler implements mod_handler {

	public function on_mod_init() {
	    mod::msg("clear css and js render");
		tmp_render::clearRender();
	}

}
