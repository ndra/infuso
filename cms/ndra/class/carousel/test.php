<?

/**
 * Тестовый контроллер для карусели
 **/
class ndra_carousel_test extends mod_controller {

	public static function indexTest() {
		return true;
	}
	
	public static function index() {
	
	    tmp::header();
	    tmp::head("<style>.prevDisabled,.nextDisabled{color:#ededed;} .xxx {font-weight:bold}</style>");

		for($n=1;$n<=5;$n++) {
			echo "<div class='c$n' style='position:relative;' >";

			echo "<table style='width:100%;' ><tr>";
	  		echo "<td class='prev' >Назад</td>";
			echo "<td style='width:100%;height:200px;'>";
			echo "<div class='container'> ";
			for($i=0;$i<=20;$i++)
			    echo "<div style='background:#ededed;padding:30px;' ><h1>$i</h1>".util_delirium::generate()."</div>";
			echo "</div>";
			echo "</td>";
			echo "<td class='next'>Вперед</td>";
			echo "</tr></table>";
			
			if($n==4) {
			    echo "<div style='position:absolute;left:60px;top:20px;border:1px solid red;' class='nav' >";
			        for($k=0;$k<10;$k++)
			    		echo "<span>[{$k}]</span> ";
			    echo "</div>";
			}

			echo "</div>";
		}

		ndra_carousel::create(".c1")
			->spacing(100)
			->prev(".prev","prevDisabled")
			->next(".next","nextDisabled")
			->container(".container")
			->exec();
			
		ndra_carousel::create(".c2")
			->minWidth(400)
			->height(150)
			->spacing(1)
			->offset(1)
			->cycle()
			->delay(2)
			->prev(".prev","prevDisabled")
			->next(".next","nextDisabled")
			->container(".container")
			->exec();
		
		ndra_carousel::create(".c3")
			->minWidth(3000)
			->prev(".prev")
			->next(".next")
			->container(".container")
			->exec();
			
		ndra_carousel::create(".c4")
			->minWidth(3000)
			->prev(".prev","prevDisabled")
			->next(".next","nextDisabled")
			->container(".container")
			->navigation(".nav","xxx")
			->delay(2)
			->exec();
        
        ndra_carousel::create(".c5")
            ->vertical()
            ->spacing(100)
            ->prev(".prev","prevDisabled")
            ->next(".next","nextDisabled")
            ->container(".container")
            ->exec();
        
			
	    tmp::footer();
	}
}
