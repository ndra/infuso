<?

// Шаблон кнопки добавления в корзину

$inject = $p1->inCart() ? "tiv5mvln5q-in" : "";
echo "<div class='tiv5mvln5q-{$p1->id()} $inject' >";

echo "<div class='pejhat-price' >{$p1->price()} р.</div>";
echo "<input type='button' value='Купить' class='eshop-buy' eshop:id='{$p1->id()}' />";

echo "<div class='eshop-goToCart' >";
echo "Товар в корзине<br/>";
$url = mod_action::get("eshop_order")->url();
echo "<a href='$url' >Оформить заказ</a>";
echo "</div>";

echo "</div>";

?>
