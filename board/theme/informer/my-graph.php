<? 

$chart = google_chart::create();
$chart->col("день","string");
$chart->col("Потрачено времени");
//$chart->col("Выполнено задач");
for($i=13;$i>=0;$i--) {

    $date = util::now()->shiftDay(-$i)->date();

    $log = board_task_log::all()
        ->eq("userID",user::active()->id())
        ->eq("date(created)",$date);
    
    $chart->row($date->txt(),$log->sum("timeSpent"));
}
$chart->exec();