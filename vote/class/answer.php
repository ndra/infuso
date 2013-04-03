<?

/**
 * Модель ответа на вопрос
 **/
class vote_answer extends reflex {

	public function reflex_beforeCreate() {
	    $this->data("date",util::now());
	}

	public static function all() {
	    return reflex::get(get_class())->desc("date");
	}

	public static function get($id) {
	    return reflex::get(get_class(),$id);
	}

	public function reflex_parent() {
		return vote::get($this->data("voteID"));
	}

	public function reflex_cleanup() {
		return !$this->reflex_parent()->exists();
	}

	/**
	 * @return Вариант ответа
	 **/
	public function option() {
		return $this->pdata("optionID");
	}

	/**
	 * @return Объект голосования
	 **/
	public function vote() {
		return $this->pdata("voteID");
	}

	public function reflex_afterOperation() {
		mod::fire("vote_answersChanged",array(
		    "vote" => $this->vote(),
		    "answer" => $this,
		    "option" => $this->option(),
		));
	}

}
