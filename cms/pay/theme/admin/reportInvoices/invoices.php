<?

echo "Количество инвойсов (счетов):";

$invoices = pay_invoice::all();

$chart = google_chart::create();
$chart->lineChart();
$chart->width(800);
$chart->height(250);
$chart->col("день","string");
foreach(pay_invoice::statusAll() as $statusTitle) {
    $chart->col($statusTitle);
}

for($i=30;$i>=0;$i--) {

    $date = util::now()->shiftMonth(-$i);
    $row = array($date->month().".".$date->year());
    
    foreach(pay_invoice::statusAll() as $statusID => $statusTitle) {
        $row[] = $invoices->copy()
            ->eq("month(date)",$date->month())
            ->eq("year(date)",$date->year())
            ->eq("status",$statusID)
            ->count()*1;
    }
    
    $chart->row($row);        
}

$chart->exec();