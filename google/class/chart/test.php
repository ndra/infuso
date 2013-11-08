<?

/**
 * Текстовый контроллер для гуглчарта
 **/ 
class google_chart_test extends mod_controller {

	public static function indexTest() {
	    return true;
	}

	public static function index() {
	    tmp::header();

	    $chart = google_chart::create();
	    $chart->col("день","string");
	    $chart->col("Посещения");
	    $chart->col("Просмотры");
	    $chart->width(500);
	    for($i=0;$i<100;$i++) {
	        $chart->row($i."",rand(),rand());
		}

	    $chart->exec();
	    $chart->columnChart()->exec();
	    $chart->pieChart()->exec();


	    $chart = google_chart::create();
	    $chart->col("x");
	    $chart->col("y");
	    for($i=0;$i<100;$i++)
	        $chart->row($i,$i+rand()%10);
	    $chart->scatterChart()->exec();

	    tmp::footer();
	}

}
