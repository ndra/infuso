<?

/*css:
.drto1s1gt0{border-radius:10px;background:#ededed;padding:20px;}
*/

$order = $p1;

if($order->items()->count()) {
    echo "<div class='drto1s1gt0' >";
    $n = number_format($order->total(),2,"."," ");
    echo "<div style='padding-bottom:10px;font-size:18px;' >Итого: <i>$n р.</i></div>";
    $url = $order->url(array("action"=>"form"));
    echo "<a class='sj2xmxcumc' href='$url' >Оформить заказ</a>";
    echo "</div>";
}

?>