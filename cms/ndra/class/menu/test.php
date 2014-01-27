<?

/**
 * Тестовый контроллер для меню
 **/
class ndra_menu_test extends mod_controller {

/**
 * Видимость класса для браузеров
 **/
public function indexTest() {
	return true;
}

/**
 * Экшн теста
 **/
public function index() {

    tmp::header();
    
    echo "<div style='padding:20px;margin:30px 10%;'>";
    
    $n = 40;
	tmp::head("<style>.ndra-menu-active{font-weight:bold;}</style>");
    echo "<div class='menu' style='padding:10px;background:#ededed;' >";
    for($i=0;$i<$n;$i++)
        echo "<a href='#' menu:id='$i' style='margin-right:10px;white-space:nowrap;' >Пункт $i</a> ";
    echo "</div>";
    
    echo "<div class='submenu' style='position:relative;border:1px solid red;' >";
    for($i=0;$i<$n;$i+=3)
        echo "<div menu:id='$i' style='border:1px solid red;width:400px;padding:50px;' >$i - ".util_delirium::generate(10,50)."</div>";
    echo "</div>";
    
    echo "</div>";
    ndra_menu::create(".menu a",".submenu div")->offset(0)->exec();
    
    mod::coreJS();
    tmp::footer();
}

}
