<?

/**
 * Модель позиции в заказе
 **/ 
class eshop_order_item extends reflex {

	/**
	 * @return Возвращает коллекцию все позиций во всех заказах
	 **/
	public static function all() {
	    return reflex::get(get_class());
	}

	/**
	 * @return Возвращает позицию заказа по id
	 **/
	public static function get($id) {
	    return reflex::get(get_class(),$id);
	}

	/**
	 * @return Возвращает родителя позиции заказа - объект заказа
	 **/
	public function reflex_parent() {
		return $this->order();
	}

	/**
	 * @return Возвращает товарную позицию, соответствующую позиции в заказе
	 **/
	public function item() {
		return eshop_item::get($this->data("itemID"));
	}

	/**
	 * @return Возвращает объект заказа с которым связана эта позиция
	 **/
	public function order() {
		return eshop_order::get($this->data("orderID"));
	}

	public function reflex_afterOperation() {
	    // Вызываем событие изменения позиции в заказе
	    // На это сообщение подписан него подписан заказ
		mod::fire("eshop_cartContentChanged",array(
			"item" => $this,
			"cart" => $this->order(),
			"deliverToClient" => true,
		));
	}

	public function fireError($txt) {
		mod::fire("eshop_cartItemError",array(
	    	"text" => $txt,
	    	"itemID" => $this->item()->id(),
	    	"orderItemID" => $this->id(),
	    	"deliverToClient" => true,
		));
	}

	/**
	 * Возвращает количество товара
	 **/
	public function quantity() {
		return $this->data("quantity");
	}

	/**
	 * Устанавливает количество товара
	 **/
	public function setQuantity($q) {

		$q = intval($q);
		if($q<=0) {
	     	$this->fireError("Недопустимое значение");
		    return false;
		}

		if(!$this->item()->tryBuy($q)) {
		    $this->fireError("Недостаточно товара в наличии. Максимум — {$this->item()->data(instock)}");
		    return false;
		}

		$this->data("quantity",$q);
		return true;
	}

	/**
	 * Возвращает цену за единицу товара
	 **/
	public function price() {
		if($this->order()->draft())
			return $this->item()->price();
		return $this->data("price");
	}

	/**
	 * Возвращает сумму по строке (цена*количество)
	 **/
	public function cost() {
		return $this->price() * $this->quantity();
	}

	/**
	 * Фиксирует цену и название товарной позиции
	 * После оформления товара цена и наименования товара могут изменитться
	 * Эта функция сохраняет состояние товарной позиции на момент заказа
	 **/
	public function fixItem() {
		$this->data("price",$this->item()->price());
		$this->data("title",$this->item()->title());
	}

	public function reflex_title() {
		if($title = $this->data("title"))
			return $title;
	    if(!$this->item()->exists())
			return "Несуществующая позиция";
	    return $this->item()->title();
	}

	/**
	 * @return url товарной позиции
	 **/
	public function reflex_url() {
		return $this->item()->url();
	}

	public function reflex_cleanup() {
		if(!$this->order()->exists())
	        return true;
	}

	public function extra($key,$val=null) {
	    $extra = $this->pdata("extra");
	    if(func_num_args()==1) {
	        return $extra[$key];
	    }
	    if(func_num_args()==2) {
	        $extra[$key] = $val;
	        $this->data("extra",json_encode($extra));
	    }
	}
	
}
