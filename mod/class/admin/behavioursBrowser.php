<?

class mod_admin_behavioursBrowser extends mod_controller {

	public function indexTest() {
		return mod_superadmin::check();
	}

	public function index() {

		admin::header("Просмотр поведений");

		echo "<div style='padding:40px;' >";

		echo "<table><tr>";

		$behaviours = array();

		echo "<td>";

		echo "<h1 style='font-size:18px;margin-bottom:20px;' >Компоненты</h1>";

		foreach(mod::classes("mod_component") as $class) {

		    $doc = doc_class::get($class);
			echo "<b style='font-size:16px;' ><a href='{$doc->url()}' >{$class}</a></b><br/>";

			$obj = new $class;
			foreach($obj->behaviours() as $b) {
			    $bclass = get_class($b);
			    $doc = doc_class::get($bclass);
			    echo "<a href='{$doc->url()}' >{$bclass}</a><br/>";
			    $behaviours[$bclass][] = $class;
			}

			echo "<br/>";
		}
		echo "</td>";

		echo "<td>";

		echo "<h1 style='font-size:18px;margin-bottom:20px;' >Поведения</h1>";

		foreach($behaviours as $bclass=>$classes) {

		    $doc = doc_class::get($bclass);
			echo "<b style='font-size:16px;' ><a href='{$doc->url()}' >{$bclass}</a></b><br/>";

			foreach($classes as $class) {
			    $doc = doc_class::get($class);
			    echo "<a href='{$doc->url()}' >{$class}</a><br/>";
			}

			echo "<br/>";
		}


		echo "</td>";
		echo "</tr></table>";

		echo "</div>";

		admin::footer();

	}

}
