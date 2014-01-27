<?

tmp::add("center","eshop:group.description",$p1);

if($p1->subgroups()->count()) {
    $tmp->add("center","eshop:group.supergroup",$p1,$p2);
} else {
    $items = $p1->items();
    
    // Подключаем поведение-фильтр
    // Если вы хотите изменить сортировки и ограничения,
    // расширьте класс eshop_item_filter и подключите ваш класс
    // в качестве поведения
    $items->addBehaviour("eshop_item_filter");
    
    // Загружаем в фильтр параметры из запроса
    $items->applyQuery($p2);
    
    $tmp->add("center","eshop:group.items",$items);    
    $tmp->add("right","eshop:layout.subgroups",$p1->level0());

}

$tmp->exec("/eshop/layout");