<?

// Сообщение о статусе заказа
echo "<div style='background:#ffeebb;padding:10px;border:1px solid #df775d;' >";
echo $p1->message();
echo "</div>";

echo "<br/><br/>";

// Заголовок таблицы
echo "<table class='mb5nics729' >";    
echo "<thead><tr>";
echo "<td></td>";
echo "<td>Название</td>";
echo "<td>Количество</td>";
echo "<td>Цена</td>";
echo "<td>Сумма по строке</td>";
echo "</tr></thead>";

// Элементы
foreach($p1->items() as $item) {
    echo "<tr>";

    $preview = file::get($item->item()->photo())->preview(40,40);
    echo "<td><a href='{$item->url()}' ><img src='$preview' class='a3zfuls1et-preview' /></a></td>";
    
    echo "<td><a href='{$item->url()}' target='_new' >{$item->title()}</a></td>";
    echo "<td>{$item->quantity()}</td>";
    
    $price = util::price($item->price());
    echo "<td style='white-space:nowrap;' >{$price} р.</td>";
    
    $price = util::price($item->cost());
    echo "<td style='white-space:nowrap;' >{$price} р.</td>";
    echo "</tr>";
}
echo "<tr></tr>";


// Подвал
echo "</tr>";
echo "<td></td>";
echo "<td></td>";
echo "<td></td>";
echo "<td style='text-align:right;font-weight:bold;' >Итого</td>";
$total = util::price($p1->total());
echo "<td style='font-weight:bold;' >{$total} р.</td>";
echo "</tr></thead>";

echo "</table>";