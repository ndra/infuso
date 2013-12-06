<? 

$project = board_project::get($params["projectID"]);
$group = $params["group"];
$current = util::date($params["from"])->date();
$to = util::date($params["to"])->date();

$mode = "user";
$split = $params["split"] = $params["split"] ?: "user";

$time = board_task_time::visible()
    ->joinByField("taskID")
    ->geq("date(begin)",$current)
    ->lt("date(begin)",$to);
    //->eq("board_task.projectID",$params["projectID"]);
    
$log = board_task_log::all()
    ->gt("timeSpent",0)
    ->joinByField("taskID");
    
if($params["projectID"]) {
    $log->eq("board_task.projectID",$params["projectID"]);
}

tmp::header();

mod::coreJS();

<div class='dcyy6ydrbx' >

    $d = ($to->stamp() - $current->stamp()) / 3600 / 24;
    
    if($project->exists()) {
        <h1>Отчет по проекту «{$project->title()}» {$current->num()} — {$to->num()} ({$d} д.)</h1>
    } else {
        <h1>Отчет по проектам {$current->num()} — {$to->num()} ({$d} д.)</h1>
    }
    
    tmp::exec("menu",array(
        "params" => $params,
    ));
    
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
    
    $users = $time->distinct("userID");
    $projects = $time->distinct("board_task.projectID");
        
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
        
        $row["date"] = array(
            "value" => $text,
        );
    
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
        
        if($mode=="auto") {
            foreach($users as $userID) {            
                $segmentTime = $time->copy()
                    ->eq("userID",$userID)
                    ->geq("date(begin)",$current)
                    ->lt("date(begin)",$next)
                    ->where("unix_timestamp(`end`) - unix_timestamp(`begin`) < 3600 * 24 * 3")
                    ->select("sum(unix_timestamp(`end`) - unix_timestamp(`begin`)) as `time`");
                    
                $segmentTime = end(end($segmentTime)) /3600;
                $row["a-".$userID] = array(
                    "value" => round($segmentTime,2),
                );
            }
        }
        
        if($mode=="user") {
        
            $onclick = "mod.fire('board/showLog',{from:'$current',to:'$next'})";
        
            if($split == "user") {
            
                $timeSpent = $log->copy()
                    ->geq("date(created)",$current)
                    ->lt("date(created)",$next)
                    ->groupBy("userID")
                    ->select("sum(board_task_log.timeSpent) as `sum`,userID");
                    
                foreach($timeSpent as $userTime) {
                    $row[$userTime["userID"]] = array(
                        "value" => round($userTime["sum"],2),
                        "onclick" => $onclick,
                    );
                }
                
            } elseif($split == "project") {
            
                $timeSpent = $log->copy()
                    ->geq("date(created)",$current)
                    ->lt("date(created)",$next)
                    ->groupBy("board_task.projectID")
                    ->select("sum(board_task_log.timeSpent) as `sum`,projectID");
                    
                foreach($timeSpent as $groupTime) {
                    $row[$groupTime["projectID"]] = array(
                        "value" => round($groupTime["sum"],2),
                        "onclick" => $onclick,
                    );
                }
            
            }
            
        }
    
        $data[] = $row;
        
        $current = $next;
        $n++;
    }
    
    $chart = new board_chart;    
    
    if($mode == "auto") {
        $colGroup = array(
            "label" => "Потрачено времени (авто)",
            "cols" => array(),
        );
        foreach($users as $userID) {    
            $colGroup["cols"][] = array(
                "name" => "a-".$userID,
                "label" => user::get($userID)->title(),
            );
        }    
        $chart->addColGroup($colGroup);
    }
    
    if($mode == "user") {
    
        if($split == "user") {
        
            $colGroup = array(
                "label" => "Потрачено времени (указал)",
                "cols" => array(),
            );
            foreach($users as $userID) {    
                $colGroup["cols"][] = array(
                    "name" => $userID,
                    "label" => user::get($userID)->title(),
                );
            }    
            $chart->addColGroup($colGroup);
            
        } elseif($split == "project") {
        
            $colGroup = array(
                "label" => "Потрачено времени",
                "cols" => array(),
            );
            foreach($projects as $projectID) {    
                $colGroup["cols"][] = array(
                    "name" => $projectID,
                    "label" => board_project::get($projectID)->title(),
                );
            }    
            $chart->addColGroup($colGroup);
        
        }
    

        
        foreach($data as $row) {
            $chart->addRow($row);
        }
    }
    
    $chart->exec();
    
    <div class='details' ></div>
    
</div>

tmp::footer();