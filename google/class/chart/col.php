<?

class ndra_chart_col extends mod_controller {

	public function __construct($title=null,$type="number") {
		$this->title = $title;
		$this->type = $type;
	}

	// Название столбца
	private $title = "";
	public function title($p=null) {
		if(func_num_args()==0) {
		    return $this->title;
		} else {
		    $this->title = $p;
		    return $this;
		}
	}

	// Тип столбца
	private $type;
	public function type($p=null) {
		if(func_num_args()==0) {
		    return $this->type;
		} else {
		    $this->type = $p;
		    return $this;
		}
	}

}
