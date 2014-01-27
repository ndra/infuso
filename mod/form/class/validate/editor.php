<?

/**
 * Класс для сохранения модели формы в базу
 **/
class form_validate_editor extends reflex_editor {

	public function beforeEdit() {
		return mod_superadmin::check();
	}

}
