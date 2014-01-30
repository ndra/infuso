<?

// Хлебные крошки
$a = array();
$a["/"] = "Главная";
$a["/eshop/"] = "Магазин";
foreach(tmp::obj()->parents() as $parent)
    $a[$parent->url()] = $parent->title();

foreach($a as $key=>$val)
    $a[$key] = "<a href='$key' >$val</a>";
    
echo implode(" / ",$a);