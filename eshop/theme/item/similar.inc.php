<?

echo "<h2 style='margin-bottom:20px;' >Похожие товары</h2>";

foreach($p1->similar()->limit(4) as $item) {
    echo "<div class='ib khylsrt9lu' >";
    echo "<a href='{$item->url()}'><img style='border:1px solid #cccccc;' src='{$item->photo()->preview()}' /></a><br/>";
    echo "<a href='{$item->url()}'>{$item->title()}</a>";
    tmp::exec("eshop:layout.buy",$item);
    echo "</div>";
}

?>