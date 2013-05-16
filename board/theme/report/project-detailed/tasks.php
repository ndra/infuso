<?

<table class='uot58zssqv' style='width:800px;' >
    foreach($tasks->limit(0) as $task) {        
        <tr>
            <td class='text' >
                echo util::str($task->text())->ellipsis(100);
            </td>
            <td>
                echo $task->timeScheduled();
            </td>            
            <td>
                echo round($task->timeSpent(),2);
            </td>
        </tr>
        
        foreach($task->subtasks()->limit(0) as $subtask) {        
            <tr>
                <td style='padding-left:50px;' class='text' >
                    echo util::str($subtask->text())->ellipsis(100);
                </td>
                <td>
                    echo $subtask->timeScheduled();
                </td>            
                <td>
                    echo round($subtask->timeSpent(),2);
                </td>
            </tr>
        }  
        
    }
</table>