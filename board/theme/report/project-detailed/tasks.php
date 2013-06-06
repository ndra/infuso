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
        
        tmp::exec("row",array (
            "task" => $task,
        ));
        
        foreach($task->subtasks()->limit(0) as $subtask) {        
            tmp::exec("row",array (
                "task" => $subtask,
            ));
        }  
        
    }
</table>