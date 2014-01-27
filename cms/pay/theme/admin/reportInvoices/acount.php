<?

echo "Количество операций с внутреним счетом пользователя";

$chart = google_chart::create();
$chart->lineChart();
$chart->width(800);
$chart->height(250);
$chart->col("день","string");
$chart->col("Списание");
$chart->col("Пополнение");


for($i=30;$i>=0;$i--) {

    $date = util::now()->shiftMonth(-$i);
    $row = array($date->month().".".$date->year());

    $items = pay_operationLog::all()
        ->eq("month(date)",$date->month())
        ->eq("year(date)",$date->year());

    $row[] = $items->copy()->gt("amount",0)->count()*1;
    $row[] = $items->copy()->lt("amount",0)->count()*1;
    
    $chart->row($row);        
}

$chart->exec();