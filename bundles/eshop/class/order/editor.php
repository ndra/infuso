<?

class eshop_order_editor extends reflex_editor {

	public function itemClass() {
	    return "eshop_order";
	}

    public function filters() {
    
        $orders = array();
        foreach(eshop_order_status::all() as $status)
            $orders[] = eshop_order::all()->eq("status",get_class($status))->desc("sent")->title($status->name());

        $orders[] = eshop_order::all()->title("<b>Все заказы</b>");

        //$active = eshop_order_status::all()->eq("sense",1)->distinct("code");
       // $orders[] = eshop_order::all()->eq("status",$active)->title("<b>Активные</b>");

        $orders[] = eshop_order::all()->eq("status",$active)->leq("changed",util::now()->shift(-3600*24*3))->title("Три дня без изменений");
        $orders[] = eshop_order::all()->eq("status",$active)->leq("changed",util::now()->shift(-3600*24*7))->title("Неделю без изменений");
        $orders[] = eshop_order::all()->eq("status",$active)->leq("changed",util::now()->shift(-3600*24*30))->title("Месяц без изменений");

        $orders[] = eshop_order::drafts()->title("<span style='color:gray;' >Неоформленные заказы</span>");

        return $orders;
    }
    
    public function beforeEdit() {
        return user::active()->checkAccess("eshop:editOrder",array(
            "order" => $this->item(),
		));
    }

    public function disable() {
        return array("add","delete");
    }

	/**
	 * Включаем отображение комментариев
	 **/
    public function showComments() {
		return true;
	}

}
