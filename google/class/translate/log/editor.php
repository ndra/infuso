<?

class google_translate_log_editor extends reflex_editor {

    public function beforeEdit() {
        return mod_superadmin::check();
    }
    
    public function root() {
        return array(
            google_translate_log::all()->title("Лог перевода Google")->param("tab","system"),
        );
    }
    
    public function disable() {
        return "list";
    }
    
}
