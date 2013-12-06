<? 

tmp::header();
tmp::reset();

<div style='padding:20px;' >

    $from = util::date($params["from"])->date();
    $to = util::date($params["to"])->date();
    
    <div style='margin-bottom:20px;' >
        echo "Отчет по проектам {$from->text()} &mdash; {$to->text()}";
    </div>

    $items = board_task_log::visible()
        ->groupBy("board_task.projectID")
        ->joinByField("taskID")
        ->orderByExpr("`spent` desc ")
        ->limit(0)
        ->geq("date(created)",$from)
        ->leq("date(created)",$to)
        ->having("`spent` > 0")
        ->select("sum(`board_task_log`.`timeSpent`) as `spent`, `projectID` ");
        
    $spentSum = 0;
    $scheduledSum = 0;
    $completedHoursSum = 0;
    $completedNumberSum = 0;
    
    <table class='x8367pvuga0' >
    
            foreach($items as $row) {
            
                // Поставлено задач (ч.)
                $scheduled = board_task::all()
                    ->eq("projectID",$row["projectID"])
                    ->geq("date(created)",$from)
                    ->leq("date(created)",$to)
                    ->neq("status",board_task_status::STATUS_CANCELLED)
                    ->neq("status",board_task_status::STATUS_DRAFT)
                    ->sum("timeScheduled");
                
                // Закрыто задач (ч.)  
                $completedTasks = board_task::all()
                    ->eq("projectID",$row["projectID"])
                    ->geq("date(changed)",$from)
                    ->leq("date(changed)",$to)
                    ->eq("status",array(board_task_status::STATUS_COMPLETED,board_task_status::STATUS_CHECKOUT));
                    
                $completed = $completedTasks->sum("timeScheduled");
                $number = $completedTasks->count();
            
                <tr>        
                    $project = board_project::get($row["projectID"]);            
                    <td>
                        $url = "/board/#report-project/id/{$project->id()}";
                        <a href='{$url}' target='_parent' >
                            echo $project->title();
                        </a>
                    </td>
                    <td>
                        <a href='/board/#report-chart/id/{$project->id()}' target='_top' ><img src='/board/res/img/icons16/chart.png' /></a>
                    </td>
                    <td>
                        $spentSum+= $row["spent"];
                        echo round($row["spent"],2);
                    </td>
                    
                    <td>
                        echo $scheduled;
                        $scheduledSum+= $scheduled;
                    </td>
                    
                    <td>
                        echo $completed;
                        $completedHoursSum += $completed;
                    </td>                
    
                    <td>
                        echo $number;
                        $completedNumberSum += $number;
                    </td>
                    
                </tr>
            }
            
            <thead>    
                <tr>
                    <td>Проект</td>
                    <td></td>
                    <td>Потрачено</td>
                    <td>Поставлено</td>
                    <td>Закрыто, ч.</td>
                    <td>Закрыто, шт.</td>
                </tr>
                <tr class='sum' >
                    <td>Все проекты</td>
                    <td><a href='/board/#report-chart/' target='_top' ><img src='/board/res/img/icons16/chart.png' /></a></td>
                    <td>
                        echo round($spentSum,2);
                    </td>
                    <td>
                        echo round($scheduledSum,2);
                    </td>
                    <td>
                        echo round($completedHoursSum,2);
                    </td>
                    <td>
                        echo round($completedNumberSum,2);
                    </td>
                </tr>
            </thead>
    
    </table>
    
    /*<br/><br/>
    tmp::exec("../contributors",array(
        "from" => $from,
        "to" => $to,
    )); */
    
</div>

tmp::footer();