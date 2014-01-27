<?

/**
 * Поведение товара для яндекс-маркета
 **/
class eshop_yandexMarket_bidGrabberBehaviour extends mod_behaviour {

	public function addToClass() {
	  //  return mod_conf::get("eshop:yandex:market:grab-bids") ? "eshop_item" : null;
	}

	public function behaviourPriority() {
	    return -1;
	}

	public function fields() {
	    return array(
	        mod::field("decimal")->name("bid1")->label("bid1")->group("Ставки"),
	        mod::field("decimal")->name("bid2")->label("bid2")->group("Ставки"),
	        mod::field("decimal")->name("bid3")->label("bid3")->group("Ставки"),
	        mod::field("decimal")->name("bid4")->label("bid4")->group("Ставки"),
	        mod::field("decimal")->name("bid5")->label("bid5")->group("Ставки"),
	        mod::field("datetime")->name("bidGrabbed")->label("Ставки обновлены")->group("Ставки"),
	    );
	}

}
