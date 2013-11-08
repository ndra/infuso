<?

$log = board_task_log::all()
    ->joinByField("taskID")
    ->eq("board_task.projectID",$params["id"]);
    
$tasks = board_task::all()
    ->eq("status",array(board_task_status::STATUS_CHECKOUT, board_task_status::STATUS_COMPLETED))
    ->eq("projectID",$params["id"])
    ->eq("epicParentTask",0);
    
$data = array();

for($i=0;$i<10;$i++) {

    $date = util::now()
        ->shiftDay(-$i);
        
    $log2 = $log->copy()
        ->eq("date(created)",$date->date());
        
    $tasks2 = $tasks->copy()
        ->eq("date(changed)",$date->date());

    $row = array();
    $row[] = $date->num();
    $row[] = $log2->sum("timeSpent");
    $row[] = $tasks2->sum("timeScheduled");
    
    /*foreach(board_task_tag_description::all() as $tag) {
        $row[] = $tasks2->copy()->useTag($tag->id())->sum("timeScheduled");
    } */
    
    $data[] = $row;
        
}

$chart = google_chart::create();
$chart->columnChart();
$chart->width(1000);
$chart->height(200);
$chart->col("Месяц","string");
$chart->col("Потрачено");
$chart->col("Планировалось");

/*foreach(board_task_tag_description::all() as $tag) {
    $chart->col($tag->title());
} */

foreach(array_reverse($data) as $row) {
    $chart->row($row);
}

$chart->exec();