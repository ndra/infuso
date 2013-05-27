<?

<table class='uot58zssqv' style='width:800px;' >

    <thead>
        <tr>
            <td>id</td>
            <td>Задача</td>
            <td>План</td>
            <td>Потрачено</td>
            <td>Статус</td>
            <td>Заметка</td>
        </tr>
    </thead>

    foreach($tasks->limit(0) as $task) {        
        <tr>
        
            $userpick = $task->responsibleUser()->userpick()->preview(16,16)->crop();
            <td class='id' >{$task->id()}</td>
            <td class='text' style='background: url({$userpick}) 5px 5px no-repeat;padding-left:25px;' >
                <a href='{$task->url()}' >
                    echo util::str($task->text())->ellipsis(100);
                </a>
            </td>
            <td>
                echo $task->timeScheduled();
            </td>            
            <td>
                echo round($task->timeSpent(),2);
            </td>
            <td class='status' >{$task->status()->title()}</td>
            <td>
                tmp::exec("notice",array(
                    "task" => $task,
                ));
            </td>
        </tr>
        
        foreach($task->subtasks()->limit(0) as $subtask) {        
            <tr class='subtask' >
                <td class='id' >{$subtask->id()}</td>
                $userpick = $subtask->responsibleUser()->userpick()->preview(16,16)->crop();      
                      
                <td style='background: url({$userpick}) 55px 5px no-repeat;padding-left:75px;' class='text' >                    
                    <a href='{$task->url()}' >
                        echo util::str($subtask->text())->ellipsis(100);
                    </a>
                </td>
                
                <td>
                    echo $subtask->timeScheduled();
                </td>            
                <td>
                    echo round($subtask->timeSpent(),2);
                </td>
                <td class='status' >{$task->status()->title()}</td>
            </tr>
        }  
        
    }
</table>