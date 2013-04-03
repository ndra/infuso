<?

/**
 * Виджет опроса
 **/
class vote_widget extends tmp_widget {

	public function name() {
		return "Опрос";
	}

	public function execWidget() {
		$vote = vote::last();
		tmp::exec("vote:vote", $vote);
	}

}
