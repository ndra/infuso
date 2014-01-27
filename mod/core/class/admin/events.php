<?

/**
 * Контроллер для отображение классов, подписанных на события
 **/
class mod_admin_events extends mod_controller {

public function indexTest() {
	return mod_superadmin::check();
}

public function index() {

	admin::header("События");
	
	echo "<div style='padding:40px;' >";
	
	echo "<h1 style='font-size:18px;margin-bottom:20px;' >События</h1>";
	
	echo "Жирным отображается название события, а ссылки под ним — имена классов, которые на это событие подписаны.";
	echo "<br/><br/>";

	
	foreach(mod::classmap("handlers") as $event=>$classes) {
	
	    echo "<b style='font-size:16px;' >$event</b><br/>";
	    foreach($classes as $class) {
	        $doc = doc_class::get($class);
			echo "<a href='{$doc->url()}' >{$class}</a><br/>";
	    }
	    echo "<br/>";

	}
	
	echo "</div>";
	
	admin::footer();
	
}

}
