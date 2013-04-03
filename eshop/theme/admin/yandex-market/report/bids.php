<? 

$items = eshop_item::all()->eq("yandexMarket",1);
$max = $items->copy()->max("bid1");

$chart = google_chart::create()->columnChart()->width("100%")->height(400);
$chart->title("Распределение цен, установленных роботом для позиций, у которых найдены рекомендации");
$chart->col("Ставка","string");
$chart->col("Количество");
for($i=0.1;$i<=$max+.1;$i+=.01) {

    $n1 = $items->copy()->eq("cbid",$i)->count();
    $chart->row($i."$",$n1*1);
    
}
$chart->exec();