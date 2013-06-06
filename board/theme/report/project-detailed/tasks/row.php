<?

$h = tmp::helper("<tr>");

if($task->data("epicParentTask")) {
    $h->addClass("subtask");
}

$h->begin();

    <td class='id' >{$task->id()}</td>
    $userpick = $task->responsibleUser()->userpick()->preview(16,16)->crop();      
        
    <td style='background-image: url({$userpick});' class='text' >                    
        <a href='/board/#task/id/{$task->id()}' target='_top' >
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
        tmp::exec("../notice",array(
            "task" => $task,
        ));
    </td>
    <td>
        tmp::exec("../vote",array(
            "task" => $task,
        ));
    </td>
</tr>

$h->end();