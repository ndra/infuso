<?

echo "<h2 style='margin-bottom:20px;' >С этим товаром часто покупают</h2>";

foreach($p1->alsoBuy() as $item) {
    echo "<div class='ib vpluvjawuk' >";
    echo "<a href='{$item->url()}'><img style='border:1px solid #cccccc;' src='{$item->photo()->preview()}' /></a><br/>";
    echo "<a href='{$item->url()}'>{$item->title()}</a>";
    tmp::exec("eshop:layout.buy",$item);
    echo "</div>";
}

?>