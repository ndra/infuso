<?

/**
 * Клонтроллер тестовых данных для inxdev
 **/ 
class inxdev_example extends mod_controller {

	public static function postTest() {
	    return true;
	}

	public static function post_treeLoader($p) {

	    $data = file::get("/inxdev/data/tree.inc.php")->inc();
	    $ret = array();
	    foreach($data as $node)
	        if($node["parent"].""==$p["id"]."") {
	            //$node["folder"] = true;
	            $ret[] = $node;
			}
		return $ret;
	}

	/**
	 * Контроллер, возвращающий тестовые данные для списков и комбобоксов
	 **/
	public static function post_listLoader($p) {
	
		$data = explode("\n",file::get("/inxdev/data/list.txt")->data());
		$ret = array();
		
		/**
		 * Количество элементов на страницу
		 * По умолчанию - 20
		 **/
		$n = $p["n"];
		if(!$n) {
			$n = 20;
		}
			
		$k = 1;
			
		for($i=0;$i<=$n;$i++) {
		
			$text = trim($data[$i]);
			if(!$text) {
				continue;
			}
				
			if(trim($p["search"]) && !mb_substr_count($text,trim($p["search"]))) {
				continue;
			}
		
		    $ret[] = array(
		        "id" => $i,
				data => array (
			        "text" => $text,
			        "icon" => rand()%2 ? "star" : "",
		        )
			);
			
			$k++;
		}
		
		return array (
		    "cols" => false,
			"data" => $ret,
		);
	}

	public static function post_listLoaderWithComponents($p) {
		$data = explode("\n",file::get("/inxdev/data/list.txt")->data());
		$ret = array();
		$n = $p["n"];
		if(!$n) $n= 20;
		for($i=0;$i<=$n;$i++)
		    $ret[] = array(
		        "text" => $data[$i],
		        "icon" => rand()%2 ? "star" : "",
		        "@inx" => array(
		            "type" => "inx.panel",
		            "width" => "100",
		            "height" => "20",
				),
			);
		return $ret;
	}


	public static function post_gridLoader($p) {
	
		$ret = array(
		    "data" => array(),
		);
		
	    for($i=0;$i<20;$i++)
	        $ret["data"][] = array(
	            "css" => array(
	                "background" => "red",
				),
	            "data" => array(
		            "id" => $i,
		            "a" => rand(),
		            "b" => rand(),
		            "c" => util::now(rand())->num(),
		            "d" => rand()%2 ? "folder" : "file",
		            "e" => rand(),
		            "f" => rand()%2 ? "folder" : "file",
		            "g" => rand(),
	            ),
			);
			
		$ret["cols"] = array(
			array(
			    "name" => "d",
				"type" => "image",
				"title" => "*",
			),
			array(
			    "name" => "f",
				"type" => "image",
			),
		    array(
		        "name" => "a",
				"title" => "первая колонка",
			),
		    array(
		        "name" => "b",
				"width"=>50
			),
			array(
			    "name" => "c",
				"width"=>100
			),
			array(
			    name => e,
				"width"=>100
			),
		);
		
		return $ret;
	}
	
	public static function randomInxComponent() {
	
		$ret = array();
	
		$ret[] = array(
			"type" => "inx.date",
		);
		
		$ret[] = array(
			"type" => "inx.textarea",
			"onchange" => "inx.msg(this.info('value'))",
			"style" => array(
				width => "parent"
			),
		);
		
		$ret[] = array(
			"type" => "inx.select",
			"data" => array(
				array(
					"text" => "Один",
				),
				array(
					"text" => "Два",
				),
				array(
					"text" => "Три",
				),
			),
		);	
		
		$ret[] = array(
			"type" => "inx.textfield",
			"width" => "parent",
		);	
		
		return $ret[array_rand($ret)];
	
	}
	
	public function post_gridLoaderInx() {
	
		$ret  = array();
	
		$ret["cols"] = array(
		    array(
		        "name" => "a",
				"title" => "первая колонка",
			),
		    array(
		        "name" => "b",
				"width"=>150
			),
			array(
			    "name" => "c",
				"width"=>100
			),
			array(
			    name => "d",
				"width"=>100
			),
		);
		
		for($i=0;$i<10;$i++)
			$ret["data"][] = array(
				"a" => array(
					"text" => rand(),
					css => array(
						"color" => "red",
					),
				),
				"b" => array(
					"inx" => self::randomInxComponent(),
					css => array(
						"color" => "red",
					),
				),
				"c" => rand(),
				"d" => rand(),
			);
		
		return $ret;
	
	}

	public static function post_galleryLoader($p) {
	    $data = file::get("/inxdev/data/gallery.inc.php")->inc();
		return $data;
	}

	public static function post_uploadTest($p,$f) {
	    mod::msg($f);
	}

	public static function post_sandbox() {
		return array(
		    array(
		        "text" => "Показать сообщение",
		        "code" => "inx.msg('hello world :)')",
			),
		    array(
		        "text" => "Создать редактор кода",
		        "code" => "inx({type:'inx.code',id:'code'}).cmd('render','#sandbox')",
			),
		    array(
		        "text" => "Создать флоатер",
				"code" => "inx({type:'inx.floater',width:200,height:100,id:'floater'})"
			),
		    array(
		        "text" => "Изменить размер флоатера",
				"code" => "inx('floater').cmd('width',50).cmd('height',50)"
			),
		    array(
		        "text" => "Добавить во флоатер дерево",
				"code" => "inx('floater').cmd('add',{type:'inx.tree',autoHeight:true,loader:{cmd:'inxdev:example:treeLoader'}})"
			),
		);
	}

	public static function post_combo($p) {
		$data = explode("\n",file::get("/inxdev/data/combo.txt")->data());
		$ret = array();
		foreach($data as $key=>$val)
		    if(!$p["search"] || substr_count($val,$p["search"]))
		        $ret[] = array("id"=>$key,"text"=>trim($val));
		return $ret;
	}

}
