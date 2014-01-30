<?

/**
 * Тест slideshow.js
 **/
class ndra_slideshow_test extends mod_controller {

	public function indexTest() {
		return true;
	}

	public function index() {
		tmp::header();
		
		tmp::reset();

		echo "<a href='#' id='a1'>Галерея1</a><br/>";

		for($i=0;$i<10;$i++)
			echo util_delirium::generate(100000);

		echo "<a href='#' id='a2' >Галерея2</a> (Выбрана 30-я фотография)";

		echo "<div>";
		foreach(file::get("/ndra/res/example/")->dir() as $file)
		    echo "<a class='slideshow' href='{$file->path()}' ><img src='{$file->preview()}' /></a>";
	    echo "</div>";

		ndra_slideshow::create(array("cmd"=>"ndra:slideshow:test:test"))->version(2)->bind("#a1");
		ndra_slideshow::create(array("cmd"=>"ndra:slideshow:test:test"))->version(2)->select(30)->bind("#a2");
		ndra_slideshow::create()->version(2)->bind(".slideshow");

		tmp::footer();
	}

	public function postTest() {
		return true;
	}

	public function post_test() {

		//sleep(2); // Имитируем загрузку

		$data = array();
		for($i=0;$i<1;$i++) {

			$r = "?".rand();

		    $data[] = array(
		        "small" => "http://farm8.staticflickr.com/7131/7051136579_74261c7025_q.jpg".$r,
		        "big" => "http://farm8.staticflickr.com/7131/7051136579_cd02a0a5b5_h.jpg".$r,
			);
			$data[] = array(
		        "small" => "http://farm2.staticflickr.com/1364/4595818049_2303101bab_q.jpg".$r,
		        "big" => "http://farm2.staticflickr.com/1364/4595818049_8fca1479a2_o.jpg".$r,
			);

			

		}

		foreach($data as $key=>$val)
	    	$data[$key]["html"] = util_delirium::generate();

		//$data = array($data[0]);

		return $data;
	}

}
