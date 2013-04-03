<?

$order = eshop_order::cart();

if($order->items()->count()) {
    echo "<a href='{$order->url()}'> Моя корзина ({$order->items()->count()})</a>";
} else {
    echo "В вашей корзине пока нет товаров.";
}

?>
