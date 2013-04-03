<?

class user_operation_editor extends reflex_editor {

	public function root() {
	    $ret = array();
	    if(mod_superadmin::check()) {
	        $ret[] = user_operation::all()->param("tab","user")->title("Операции")->limit(20);
		}
		return $ret;
	}

}
