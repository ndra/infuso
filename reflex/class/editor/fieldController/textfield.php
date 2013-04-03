<?

/**
 * Контроллер для утилит текстового поля в админке
 **/
class reflex_editor_fieldController_textfield extends mod_controller {

	public static function postTest() {
	    return user::active()->checkAccess("admin:showInterface");
	}

	/**
	 * Экшн очистки от вава
	 **/
	public static function post_cleanup($p) {
		$ret = strip_tags($p["text"]);
		return $ret;
	}

	/**
	 * Экшн типографа для текстового поля
	 **/
	public static function post_typograph($p) {
		$remoteTypograf = new reflex_editor_fieldController_typograph();
		$remoteTypograf->noEntities();
	 	$remoteTypograf->br(false);
		$remoteTypograf->p(false);
		$remoteTypograf->nobr(3);
		$remoteTypograf->quotA('laquo raquo');
		$remoteTypograf->quotB('bdquo ldquo');
		return $remoteTypograf->processText($p["text"]);
	}

	/**
	 * Экшн возвращает список виджетов
	 * Используется в текстовом поле, в диалоге
	 **/
	public static function post_listWidgets($p) {
		$ret = array();
		foreach(tmp_widget::all() as $widget) {
		    $ret[] = array(
		        "id" => get_class($widget),
		        "text" => $widget->name(),
			);
		}
		return $ret;
	}

}
