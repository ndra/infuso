<?

/**
 * Стандартное поведение для заказа
 **/
class eshop_order_behaviour extends mod_behaviour {

	/**
	 * Цепляем поведение к классу заказа
	 **/
	public function addToClass() {
		return "eshop_order";
	}

	public function behaviourPriority() {
		return -1;
	}

	/**
	 * Метод, вызывающийся после заполнения пользователем формы
	 * @param $p Массив с полями формы, отправленной заполненной пользователем
	 **/
	public function fillInForm($p) {

	    $this->data("name",$p["name"]);
	    $this->data("email",$p["email"]);
	    $this->data("phone",$p["phone"]);
	    $this->data("comments",$p["comments"]);
	    $this->setStatus("eshop_order_status_new");

	}

	/**
	 * @return Возвращает информацию о заказе ввиде текста
	 * Используется при отправке писем пользователю
	 **/
	public function asText() {
	    $ret = "";

	    $ret.= "Информация о заказе:\n\n";
	    foreach($this->fields() as $field)
	        if($field->visible())
	            if($this->data($field->name()))
	                $ret.= "{$field->label()}: {$this->rdata($field->name())}\n";
	    $ret.= "\n\n";

	    $ret.= "Состав заказа:\n\n";
	    foreach($this->items() as $item) {
	        if($art = $item->item()->data("article")) $ret.= "Арт. $art / ";
	        $ret.= $item->item()->group()->title()." / ".$item->title()."\n";
	        $ret.= $item->price()."р. x ".$item->quantity()." шт. = ".$item->cost()." р.\n";
	        $ret.= "\n";
	    }

	    return $ret;
	}

	/**
	 * Возвращает цвет плашки со статусом заказа. Используется в админке.
	 **/
	public final function labelColor() {
	    $index = 0;
	    foreach(eshop_order_status::all() as $n=>$status)
	        if(get_class($status)==get_class($this->status()))
	            $index = $n;
	    $colors = array(
	        "#3366cc",
	        "#dc3912",
	        "#109618",
	        "#990099",
	        "#0099c6",
	        "#dd4477",
	        "#66aa00",
	    );
	    $color = $colors[$index];
	    return $color;
	}

	/**
	 * Название заказа для каталога
	 **/
	public function reflex_title() {
	    $date = $this->draft() ? $this->pdata("changed") : $this->pdata("sent");
	    return "Заказ {$this->id()} от ".$date->num();
	}

}
