<?

namespace Infuso\Cms\Reflex;

class init extends \mod_init {

	public function priority() {
	    return -1;
	}

	public function init() {
	
	    \mod::msg("removing root tabs");
        rootTab::removeAll();
	}
	
}
