<?

// Отбор цены
tmp::exec("range","price",$p2);

// Отбор по производителям
$vendors = array();
$ids = $p1->items()->distinct("vendor");
foreach(eshop::vendors()->eq("id",$ids) as $v) {
    $vendors[$v->id()] = $v->title();   
}
tmp::exec("options","vendor",$vendors);

echo "<a href='{$p2->url()}' >Показать</a>";