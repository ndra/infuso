<? 


$interval = function($start,$duration,$params) {

    $height = 32;
    $width = 400;

    $w = tmp::helper("<div>");
    $w->style("height",$height);
    $w->style("top",0);
    $w->style("left",$start/3600*32 + 33);
    $w->style("width",$duration/3600*32);
   // $w->style("background","rgba(0,0,0,.5)");
    $w->style("position","absolute");
    
    $w->attr("title",$params["title"]);
    
    if($params["stripped"]) {
        $w->addClass("strip");
    } else {
        $w->style("background","rgba(0,0,0,.5)");
    }
    
    $w->exec();
};

<div class='fi91bj9l9l' >

$userpick = $user->userpick()->preview(32,32);
<img src='$userpick' />

// Выводим отчет по времени
foreach(board_task_log::all()->eq("userID",$user->id())->gt("timeSpent",0)->desc("created")->limit(0)->eq("date(created)",util::now()->date()) as $log) {
    $time = $log->pdata("created");
    $duration = $log->data("timeSpent") * 3600;
    $start = $time->stamp() - $time->date()->stamp() - $duration;
    $interval($start, $duration, array(
        "title" => $log->task()->title(),
    ));
}

// Выводим задачи в работе
foreach(board_task::all()->eq("responsibleUser",$user->id())->eq("status",board_task_status::STATUS_IN_PROGRESS)->limit(0) as $task) {
    $time = $task->pdata("changed")->stamp() - $task->pdata("changed")->date()->stamp();
    $duration = util::now()->stamp() - $task->pdata("changed")->stamp();
    $interval($time,$duration,array(
        "title" => $task->title(),
        "stripped" => true,
    ));
}


</div>
