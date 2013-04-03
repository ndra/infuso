<?

/**
 * Статус заказа "Новый"
 **/
class eshop_order_status_new extends eshop_order_status {

    public function _title() {
        return "Новый заказ";
    }

    public function _descr() {
        return "Ваш заказ отправлен менеджеру. Мы свяжемся с вами в ближайшее время.";
    }

    public function _priority() {
        return 100;
    }
    
    /**
     * Триггер, вызывающийся после изменения статуса в данный
     **/
    public function afterSet($order) {
    
        // Отправляем письмо пользователю
        $site = mod::conf("mod:site_title");

        $msg = "";
        $msg.= "Вы разместили заказ на сайте $site. В ближайшее время мы свяжемся с вами по указанной контактной информации.\n\n";
        $msg.= $order->asText();
        $mailer = $order->user()->mailer();
        $mailer->message($msg);
        $mailer->subject("Заказ с сайта $site");
        $mailer->param("orderID",$order->id());
        $mailer->code("eshop/newOrder");
        $mailer->send();

        $this->callBehaviours("afterSet",$order);
    }

}
