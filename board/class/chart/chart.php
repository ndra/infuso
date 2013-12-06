<?

class board_chart extends tmp_generic {

	private static $colors = array(
	    "#ff0000",
	    "#ffc600",
	    "#0c5cce",
	    "#ff6000",
	    "#bae806",
	    "#1e0cce",
	    "#ff9900",
	    "#2ece0c",
	);

	private $colGroups = array();
	
	private $rows = array();

	/**
	 * Добавляет группу колонок
	 **/
	public function addColGroup($group) {
	    $this->colGroups[] = $group;
	}
	
	public function addRow($data) {
		$this->rows[] = $data;
	}
	
	public function rows() {
	    return $this->rows;
	}
	
	public function colGroups() {
	    return $this->colGroups;
	}
	
	public function exec() {
		$this->sortCols();
	    $this->autoSetColors();
	    tmp::exec("/board/chart",array(
	        "chart" => $this,
		));
	}
	
	public function autoSetColors() {
	    $colorIndex = 0;
	    foreach($this->colGroups as $key1 => $colGroup) {
	        foreach($colGroup["cols"] as $key2 => $col) {
	            if(!$col["color"]) {
	                $this->colGroups[$key1]["cols"][$key2]["color"] = self::$colors[$colorIndex%sizeof(self::$colors)];
	                $colorIndex++;
	            }
	        }
	    }
	}
	
	public function sortCols() {
	
	    $sum = array();
	    foreach($this->colGroups as $colGroup) {
	        foreach($colGroup["cols"] as $col) {
	            $sum[$col["name"]] = $this->colSum($col["name"]);
	        }
	    }
	
	    foreach($this->colGroups as $key => $colGroup) {
	        usort($this->colGroups[$key]["cols"],function($a,$b) use ($sum) {
	            return $sum[$b["name"]] - $sum[$a["name"]];
	        });
	    }
	}
	
	private $max = null;
	
	public function max() {
	
	    if(!$this->max) {
	    
		    $max = 0;

		    foreach($this->rows() as $row) {
		        $colSum = array();
		        foreach($this->colGroups() as $colKey => $colGroup) {
		            foreach($colGroup["cols"] as $col) {
		                $colSum[$colKey] += $row[$col["name"]]["value"];
		            }
		        }
		        $max = max($max,max($colSum));
		    }

		    $this->max = $max;
		}
		
		return $this->max;
	
	}
	
	public function colSum($col) {
	    $sum = 0;
		foreach($this->rows() as $row) {
		    $sum += (float) $row[$col]["value"];
		}
		return $sum;
	}
	
}
