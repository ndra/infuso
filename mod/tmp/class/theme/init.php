<?

/**
 * Сборщик описаний тем
 **/
class tmp_theme_init extends mod_init {

	public function sortThemes($a,$b) {
		return $a->priority() - $b->priority();
	}

	public function priority() {
		return 1;
	}

	/**
	 * Составляет список файлов в каждой из тем для быстрого поиска шаблона
	 **/
	public function init() {

		mod::msg("Init themes");

		// Очищаем карту тем
		file::get(tmp_theme::mapFolder())->delete(1);
		file::mkdir(tmp_theme::mapFolder());

		$themes = array();

    	// Собираем темы-классы
		foreach(mod::service("classmap")->classes("tmp_theme") as $class) {
	        $themes[] = new $class();
		}

		$autoload = array();

		foreach($themes as $theme) {
		    $theme->buildMap();
		    if($theme->autoload()) {
		        $autoload[] = $theme;
			}
		}

		usort($autoload,array("self","sortThemes"));

		foreach($autoload as $key=>$val) {
		    $autoload[$key] = $val->id();
		}

		util::save_for_inclusion(tmp_theme::mapFolder()."/"."_autoload.php",$autoload);

		$all = array();
		foreach($themes as $theme) {
		    $all[] = $theme->id();
		}
		util::save_for_inclusion(tmp_theme::mapFolder()."/"."_themes.php",$all);

	}

}
