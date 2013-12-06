<? 

$logs = board_task_log::visible()
    ->gt("timeSpent",0)
    //->joinByField("taskID")
    ->geq("date(created)",$params["params"]["from"])
    ->lt("date(created)",$params["params"]["to"]);
    
<table class='zcr5cl03je' >
    foreach($logs->limit(500) as $log) {
        <tr>
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
</table>