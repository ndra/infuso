<?

$orders = eshop_order::myOrders();
if(!$orders->count()) return;

$url = mod::action("eshop_order_action","history")->url();
<b><a href='$url' >Мои заказы:</a></b><br/>
foreach($orders as $order) {
    <div><a href='{$order->url()}' >{$order->title()}</a></div>
    <div style='font-size:.7em;' >{$order->status()->title()}</div>
}