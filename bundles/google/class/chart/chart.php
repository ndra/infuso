<?

/**
 * Билдер для гугл-диаграмм
 **/ 
class google_chart extends mod_component {

    private $cols = array();
    
	private $data = array();
	
	private static $id = 1;

	public function initialParams() {
	    return array(
		    "width" => 300,
		    "height" => 150,
		);
	}

	private $type = "LineChart";

	private $firstColumnString = true;
	
	/**
	 * Конструктор
	 **/
	public static function create() {
	    return new self();
	}

	public function lineChart() {
	    $this->type = "LineChart";
	    return $this;
	}

	public function pieChart() {
	    $this->type = "PieChart";
	    return $this;
	}

	public function columnChart() {
	    $this->type = "ColumnChart";
	    return $this;
	}

	public function scatterChart() {
	    $this->type = "ScatterChart";
	    $this->firstColumnString = false;
	    return $this;
	}

	/**
	 * Добавляет колонку
	 **/
	public function col($title,$type="number") {
	    $col = new ndra_chart_col($title,$type);
	    $this->cols[] = $col;
	    return $col;
	}

	/**
	 * Добавляет строку к массиву данных
	 **/
	public function row($row=null) {

	    if(is_array($row)) {
	        $this->addRow($row);
	        return $this;
	    }

	    $row = array();
	    for($i=0;$i<func_num_args();$i++)
	        $row[] = func_get_arg($i);
	    $this->addRow($row);
	    return $row;
	}

	/**
	 * Добавляет строку к массиву данных
	 **/
	private function addRow($row) {
	    // Приводим первый элемент строки к массиву
	    foreach($row as $key=>$val) {
	        $row[$key] = $val;
	        break;
	    }
	    $this->data[] = $row;
	}

	/**
	 * Задает ширину диаграммы
	 **/
	public function width($w) {
	    $this->param("width", $w);
	    return $this;
	}

	/**
	 * Задает высоту диаграммы
	 **/
	public function height($h) {
	    $this->param("height", $h);
	    return $this;
	}

	/**
	 * Задает заголовок диаграммы
	 **/
	public function title($t) {
	    $this->param("title", $t);
	    return $this;
	}

	/**
	 * Возвращает тип диаграммы (имя js-конструктора)
	 **/
	public function scriptChartType() {
	    return $this->type;
	}

	/**
	 * Выполняет диаграмму
	 **/
	public function exec() {
	
	    tmp::js("https://www.google.com/jsapi");
	    $script = "";

	    $id = "ndra-chart-".self::$id;
	    self::$id++;

	    $script.= "<script type='text/javascript'>";
	    $script.= "google.load('visualization', '1', {'packages':['corechart']});";
	    $script.= "google.setOnLoadCallback(function() {";
	    $script.= "var data = new google.visualization.DataTable();";
	    foreach($this->cols as $col) {
	        $script.= "data.addColumn('{$col->type()}', '{$col->title()}');";
	    }

	    $script.= "data.addRows(".json_encode($this->data).");";

	    $script.= "var chart = new google.visualization.{$this->scriptChartType()}(document.getElementById('$id'));";
	    $script.= "chart.draw(data,".json_encode($this->params()).");";
	    $script.= "});";
	    $script.= "</script>";
	    tmp::head($script);

	    echo "<div id='$id'></div>";
	}

}
