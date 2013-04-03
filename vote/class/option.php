<?

/**
 * Модель варианта ответа на вопрос
 **/
class vote_option extends reflex implements mod_handler {

	/**
	 * Возвращает коллекцию всех вариантов ответа
	 **/
	public static function all() {
		return reflex::get(get_class())->asc("priority");
	}

	/**
	 * Возвращает вариант ответа по ID
	 **/
	public static function get($id) {
		return reflex::get(get_class(),$id);
	}

	/**
	 * Возвращает родительский объект голосования
	 **/
	public function reflex_parent() {
		return $this->vote();
	}

	/**
	 * Возвращает родительский объект голосования
	 **/
	public function vote() {
		return $this->pdata("voteID");
	}

	/**
	 * Возвращает коллекцию ответов пользователей, выбравших этот вариант
	 **/
	public function answers() {
		return $this->vote()->answers()->eq("optionID",$this->id());
	}

	/**
	 * Возвращает количество ответов пользователей, выбравших этот вариант
	 **/
	public function count() {
		return $this->answers()->count();
	}

	/**
	 * Возвращает процент ответов пользователей, выбравших этот вариант
	 * Результат округляется до сотых
	 **/
	public function percent() {
		$ret = $this->count() / $this->vote()->answers()->count();
		$ret*=100;
		$ret = number_format($ret,2,".","");
		return $ret;
	}

	/**
	 * При изменении ответов, обновляем количество ответов в базе
	 **/
	public function on_vote_answersChanged($p) {
		$p->param("option")->updateCount();
	}

	/**
	 * Обновляет количество ответов с данным вариантом, которое хранится в поле таблицы
	 **/
	public function updateCount() {
		$count = $this->answers()->count();
		$this->data("count",$count);
	}

	public function reflex_repair() {
		$this->updateCount();
	}

}
