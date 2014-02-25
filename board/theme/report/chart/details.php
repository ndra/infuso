<? 

$logs = board_task_log::visible()
    ->gt("timeSpent",0)
    ->geq("date(created)",$params["params"]["from"])
    ->lt("date(created)",$params["params"]["to"]);
    
if($projectID = $params["params"]["projectID"]) {
    $logs->joinByField("taskID")
        ->eq("board_task.projectID", $projectID);
}
    
<table class='zcr5cl03je' >
    foreach($logs->limit(500) as $log) {
        <tr>
            <td>
                echo $log->id();
            </td>
            <td>
                echo $log->pdata("created")->text();
            </td>
            <td>
                $userpick = $log->user()->userpick()->preview(16,16)->crop();
                <img src='{$userpick}' align='absmiddle' style='margin-right:4px;' />
                echo $log->user()->nickname();
            </td>
            <td>
                echo round($log->timeSpent(),2);
            </td>
            <td>
                echo $log->task()->project()->title();
            </td>
            <td>
                echo util::str($log->task()->text())->ellipsis(100);
            </td>
        </tr>
    }
    
    $sum = $logs->sum("timeSpent");
    <tr>   
        <td></td>
        <td></td>
        <td></td>
        <td>{$sum}</td>
    </tr>
    
</table>