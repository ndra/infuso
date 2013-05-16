<? 

$userList = $tasks->distinct("responsibleUser");
$users = user::all()->eq("id",$userList);

$spentAllUsers = board_task_log::all()
    ->joinByField("taskID")
    ->eq("board_task.projectID",$project->id())
    ->gt("board_task.changed",util::now()->shiftDay(-30));
    
$spentSum = $spentAllUsers->sum("timeSpent");

<table class='p6gih670p2' >
    foreach($users as $user) {
    
        $userpick = $user->userpick()->preview(32,32)->crop();
        $spent = $spentAllUsers->copy()
            ->eq("userID",$user->id())
            ->sum("timeSpent");
    
        <tr>
            <td>                
                <img class='userpick' src='{$userpick}' />
            </td>
            <td>
                tmp::helper("<div>")
                    ->style("width",200 * $spent / $spentSum)
                    ->style("height",27)
                    ->style("padding-top",5)
                    ->style("background","red")
                    ->param("content",round($spent,2))
                    ->exec();
            </td>
        </tr>
    }
</table>