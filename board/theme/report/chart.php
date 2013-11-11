<? 

tmp::header();

<div class='dcyy6ydrbx' >

    $project = board_project::get($params["projectID"]);
    <h1>Отчет по проекту «{$project->title()}»</h1>
    
    $group = $params["group"];
    $current = util::date($params["from"])->date();
    $to = util::date($params["to"])->date();
    
    switch($group) {
        case "month":
            $current->day(1);
            break;
        case "week":
            $current->commercialWeekDay(1);
            break;
        case "day":
            break;
        default:
            throw new Exception("wrong request param 'group'");
    }
    
    $time = board_task_time::all()
        ->joinByField("taskID")
        ->eq("board_task.projectID",$params["projectID"]);
        
    $users = $time->distinct("userID");
        
    $data = array();
    
    $n = 0;
    while($current->stamp() <= $to->stamp()) {
    
        $row = array();
        $xdate = clone $current;
        $text = $xdate->num();
        
        if($group=="day") {
            $map = array(
                1 => "пн",
                2 => "вт",
                3 => "ср",
                4 => "чт",
                5 => "пт",
                6 => "сб",
                7 => "вс",
            );
            $text.= " ".$map[$xdate->commercialWeekDay()];
        }
        
        $row[] = $text;
    
        switch($group) {
            case "month":
                $next = $current->copy()->shiftMonth(1);
                break;
            case "week":
                $next = $current->copy()->shiftDay(7);
                break;
            case "day":
                $next = $current->copy()->shiftDay(1);
                break;
        }
        
        foreach($users as $userID) {
            
            $segmentTime = $time->copy()
                ->eq("userID",$userID)
                ->geq("date(begin)",$current)
                ->lt("date(begin)",$next)
                ->select("sum(unix_timestamp(`end`) - unix_timestamp(`begin`))");
                
            $segmentTime = end(end($segmentTime)) /3600;
            $row[] = $segmentTime;
        
        }
    
        $data[] = $row;
        
        $current = $next;
        $n++;
    }
    
    $chart = google_chart::create();
    $chart->param("hAxis",array(
        slantedTextAngle => 90,
        slantedText => true,
        maxAlternation => 4,
        showTextEvery => 1,
        textStyle => array(
            fontSize => 10
        ),
    ));
    $chart->param("chartArea",array(
        "height" => "50%",
    ));
    $chart->param("isStacked", true);
    $chart->columnChart();
    $chart->width("100%");
    $chart->height(300);
    $chart->col("Месяц","string");
    
    foreach($users as $userID) {
        $chart->col(user::get($userID)->title());
    }
    
    foreach($data as $row) {
        $chart->row($row);
    }
    
    $chart->exec();
    
</div>

tmp::footer();