<?

class ndra_menu extends mod_component {

	private $menu,$submenu;

	public function create($a,$b) {
		return new self($a,$b);
	}

	public function __construct($menu=null,$submenu=null) {
	    $this->menu = $menu;
	    $this->submenu = $submenu;
	}

	/**
	 * Устанавливает смещение субменю относительно пункта меню
	 **/
	public function offset($o) {
		$this->param("offset",$o);
			return $this;
	}

	/**
	 * Подключает меню
	 **/
	public function exec() {
	    tmp::jq();
	    tmp::js("/ndra/res/menu/menu.js");
	    tmp::head("<style>{$this->submenu} {display:none}</style>");
	    $p = json_encode($this->params());
	    tmp::head("<script>$(function() { ndra.menu('{$this->menu}','{$this->submenu}',$p) })</script>");
	}

}
