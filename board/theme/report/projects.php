<? 

tmp::header();

tmp::reset();

$items = board_task_log::all()
    ->groupBy("board_task.projectID")
    ->joinByField("taskID")
    ->orderByExpr("`spent` desc ")
    ->limit(0)
    ->gt("created",util::now()->shiftDay(-30))
    ->select("sum(`board_task_log`.`timeSpent`) as `spent`, `projectID` ");

<table class='x8367pvuga0' >

    <tr>
        <td>Проект</td>
        <td>Потрачено времени</td>
        <td>Поставлено задач</td>
        <td>Закрыто задач</td>
    </tr>
    
        foreach($items as $row) {
        
            $scheduled = board_task::all()
                ->eq("projectID",$row["projectID"])
                ->gt("created",util::now()->shiftDay(-30))
                ->neq("status",board_task_status::STATUS_CANCELLED)
                ->neq("status",board_task_status::STATUS_DRAFT)
                ->sum("timeScheduled");
                
            $completed = board_task::all()
                ->eq("projectID",$row["projectID"])
                ->gt("changed",util::now()->shiftDay(-30))
                ->eq("status",array(board_task_status::STATUS_COMPLETED,board_task_status::STATUS_CHECKOUT))
                ->sum("timeScheduled");
        
            <tr>        
                $project = board_project::get($row["projectID"]);            
                <td>
                    echo $project->title();
                </td>
                <td>
                    echo round($row["spent"],2);
                </td>
                
                <td>
                    echo $scheduled;
                </td>
                <td>
                    echo $completed;
                </td>
                
            </tr>
        }

</table>

tmp::footer();