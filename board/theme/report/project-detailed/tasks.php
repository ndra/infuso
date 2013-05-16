<?

<table class='uot58zssqv' style='width:800px;' >
    foreach($tasks->limit(0) as $task) {        
        <tr>
        
            $userpick = $task->responsibleUser()->userpick()->preview(16,16)->crop();
        
            <td class='text' style='background: url({$userpick}) 5px 5px no-repeat;padding-left:25px;' >
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
                
                $userpick = $subtask->responsibleUser()->userpick()->preview(16,16)->crop();            
                <td style='background: url({$userpick}) 55px 5px no-repeat;padding-left:75px;' class='text' >                    
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