<?

class eshop_order_editorBehaviour extends mod_behaviour {

	public function addToClass() {
		return "eshop_order_editor";
	}

	public function behaviourPriority() {
		return -1;
	}

	/**
	 * @return Добавляем дополнительную вкладку «Управление заказом»
	 **/
	public function inxBeforeForm() {
	    return array(
            "type" => "inx.mod.eshop.order.manageStatus",
            "status" => $this->item()->data("status"),
            "orderID" => $this->item()->id(),
            "name" => "order_contents",
        );
	}

	/**
	 * @return Возвращает данные подробного режима просмотра
	 **/
	public function renderListData() {

	    $ret = "";
	    $ret.= "<div style='display:inline-block;width:50px;font-weight:bold;' >{$this->item()->id()}.</div>";
	    $ret.= "<div style='display:inline-block;width:200px;opacity:.5;font-style:italic;' >{$this->item()->pdata(created)->txt()}</div>";
	    $ret.= "<div style='display:inline-block;width:100px;text-align:right;padding-right:20px;' >{$this->item()->total()} р.</div>";

	    // Статус заказа
	    if($this->item()->status()->exists())
	        $ret.= "<div style='display:inline-block;width:150px;' ><span style='padding:2px;border-radius:3px;color:white;background:{$this->item()->labelColor()};' >{$this->item()->status()->title()}</span></div>";

	    return $ret;
	}
	
	public function inxFormCollections() {
	    return array(
	        $this->item()->items()->title("Товары в заказе"),
		);
	}

	/**
	 * @return Возвращает данные подробного режима просмотра
	 **/
	public function renderFullData() {

	    $ret = "<div style='padding:20px;position:relative;' >";

	    $ret.= "<style>";
	    $ret.= ".lrnk90dme-head td{opacity:.5;padding:4px;white-space:nowrap;font-style:italic;}";
	    $ret.= ".lrnk90dme-row td{border-top:1px solid rgba(0,0,0,.05);padding:4px;vertical-align:middle;}";
	    $ret.= ".lrnk90dme-footer td{border-top:1px solid rgba(0,0,0,.05);padding:4px;}";
	    $ret.= "</style>";

	    // Заголовок заказа
	    $ret.= "<table style='width:100%;margin-bottom:10px;' ><tr>";
	    $ret.= "<td>";
	    $ret.= "<b style='font-size:1.4em;margin-right:10px;' >";
	    $ret.= $this->item()->id()."&nbsp;/ ";
	    $ret.= $this->item()->date()->txt()."&nbsp;/ ";
	    $ret.= $this->item()->data("name")."&nbsp;/ ";
	    $ret.= "<a href='mailto:{$this->item()->data(email)}' >{$this->item()->data(email)}</a>";
	    $ret.= "</td>";

	    // Статус заказа
	    if($this->item()->status()->exists())
	        $ret.= "<td style='text-align:right;' ><span style='padding:4px;border-radius:3px;color:white;background:{$this->item()->labelColor()};' >{$this->item()->status()->title()}</span></td>";

	    $ret.= "</tr></table>";

	    if($this->item()->items()->count()) {
	        $ret.= "<table style='width:100%;' >";
	        $ret.= "<tr class='lrnk90dme-head'  >";
	        $ret.= "<td>Фото</td>";
	        $ret.= "<td>Артикул</td>";
	        $ret.= "<td>Наименование</td>";
	        $ret.= "<td>Кол-во</td>";
	        $ret.= "<td>В наличии</td>";
	        $ret.= "<td>Цена</td>";
	        $ret.= "<td>Сумма по строке</td>";
	        $ret.= "</tr>";
	        foreach($this->item()->items() as $item) {
	            $ret.="<tr class='lrnk90dme-row' >";
	            $preview = $item->item()->photo()->preview(32,32)->fit()."";
	            $ret.= "<td><img src='$preview' style='opacity:.5;' /></td>";
	            $ret.= "<td>{$item->item()->data(article)}</td>";
	            $ret.= "<td><a href='{$item->item()->editor()->url()}' >{$item->title()}</a></td>";
	            $ret.= "<td>{$item->quantity()} шт.</td>";

	            $ret.= "<td>{$item->item()->data(instock)}</td>";

	            // Цена
	            $price = number_format($item->price(),2,".",$item->price()>9999 ? " " : "");
	            $ret.= "<td style='white-space:nowrap;'>{$price} р.</td>";

	            // Сумма по строке
	            $price = $item->cost();
	            $price = number_format($price,2,".",$price ? " " : "");
	            $ret.= "<td style='white-space:nowrap;'>{$price} р.</td>";

	            $ret.="</tr>";
	        }

	        $ret.= "<tr class='lrnk90dme-footer' >";
	        $ret.= "<td style='text-align:right;' colspan='3' >Итого </td>";
	        $ret.= "<td>{$this->item()->totalNumber()} шт.</td>";
	        $ret.= "<td colspan='2' ></td>";
	        $price = number_format($this->item()->total(),2,".",$item->price()>9999 ? " " : "");
	        $ret.= "<td style='white-space:nowrap;font-weight:bold;font-size:1.4em;' >{$price}&nbsp;р.</td>";
	        $ret.= "</tr>";

	        $ret.= "</table>";
	        $ret.= "<br/>";
	    }

	    // Информация о поисковом запросе
	    /*$visit = $this->item()->session()->visits()->one();
	    $a = array();
	    if($visit->searchQuery())
	        $a[] = "поисковый запрос: <a href='{$visit->data(referer)}' target='_blank' >{$visit->searchQuery()}</a>";
	    if($visit->data(referer))
	        $a[] = "источник: <a href='{$visit->data(referer)}' target='_blank' >".$visit->data("refererDomain")."</a>";
	    if(sizeof($a))
	        $ret.= "<div style='opacity:.5;margin-bottom:10px;' >".implode(", ",$a)."</div>";*/

	    // Комментарий клиента
	    if($comments = $this->item()->data("comments")) {
	        $ret.= "<div><b>Комментарий:</b> <i>";
	        $ret.= util::str($comments)->ellipsis(150);
	        $ret.= "</i></div>";
	    }

	    // Последнее сообщение из лога
	    $logItem = $this->item()->getLog()->one();
	    if($logItem->exists()) {
	        $ret.= "<div>";
	        $ret.= $logItem->user()->title().": ";
	        $ret.= "<span style='opacity:.5' >".$logItem->pdata("datetime")->txt()."</span> ";
	        $ret.= $logItem->msg();
	        $ret.= "</div>";
	    }

	    $ret.= "</div>";

	    return $ret;
	}

}
