<? 


$interval = function($day,$start,$duration,$log) {

    $height = 10;
    $width = 400;

    $w = tmp::helper("<div>");
    $w->style("height",$height);
    $w->style("top",$height*$day);
    $w->style("left",($start-$duration)/24*$width);
    $w->style("width",$duration/24*$width);
    $w->style("background","rgba(0,0,0,.5)");
    $w->style("position","absolute");
    
    $w->attr("title",$log->task()->title());
    
    $w->exec();
};

<div style='width:400px;border: 1px solid #ededed;height:400px;position:relative;' >
foreach(board_task_log::all()->eq("userID",$user->id())->gt("timeSpent",0)->desc("created")->limit(300) as $log) {
    
    $time = $log->pdata("created");
    
    $day = floor(util::now()->stamp()/3600/24) - floor($time->stamp()/3600/24);
    $interval($day,$time->hours() + $time->minutes() /60,$log->data("timeSpent"),$log);

}
</div>
