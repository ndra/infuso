<?

$order = $p1;

if(!$order->items()->count()) {
    echo "Ваша корзина пуста";
} else {

    $n=1;
    echo "<table class='a3zfuls1et' >";
    foreach($order->items() as $item) {
        echo "<tr>";
        $preview = file::get($item->item()->photo())->preview(40,40);
        echo "<td><a href='{$item->url()}' ><img src='$preview' class='a3zfuls1et-preview' /></a></td>";
        echo "<td><a href='{$item->url()}' >{$item->title()}</a></td>";
        
        // Цена
        echo "<td style='text-align:right;white-space:nowrap;' >{$item->price()} р.</td>";
        
        // Количество
        echo "<td style='color:gray;' >&times;</td>";
        echo "<td style='padding-left:20px;padding-right:35px;' ><input type='button' class='eshop-change' value='{$item->data(quantity)}' eshop:id='{$item->id()}' id='eshop-edit-{$item->id()}' style='width:40px;' /></td>";
        
        // Стоимость
        echo "<td><span> = {$item->cost()}  р.</span></td>";
        
        echo "<td>";
        echo "<input type='button' value='Удалить' eshop:id='{$item->id()}' class='eshop-delete' />";
        echo "</td>";        
        echo "</tr>";
      
        $n++;
    }
    echo "</table>";
    
    tmp::exec("total",$p1);
}