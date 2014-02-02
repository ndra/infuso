<?

namespace Infuso\Board;

class ProjectEditor extends \reflex_editor {

	public function itemClass() {
	    return "Infuso\\Board\\Project";
	}

	public function beforeEdit() {
	    return \Infuso\Core\Superadmin::check();
	}

}
