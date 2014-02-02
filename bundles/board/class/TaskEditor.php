<?

namespace Infuso\Board;

class TaskEditor extends \reflex_editor {

	public function root() {
	    return array(
	        Task::all()->title("Задачи"),
		);
	}

	public function itemClass() {
	    return "Infuso\\Board\\Task";
	}

	public function beforeEdit() {
	    return \Infuso\Core\Superadmin::check();
	}

}
