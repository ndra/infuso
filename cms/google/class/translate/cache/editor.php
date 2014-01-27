<?

class google_translate_cache_editor extends reflex_editor {

	public function beforeEdit() {
	    return mod_superadmin::check();
	}
	
}
