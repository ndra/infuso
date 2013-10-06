<?

$h = tmp::helper("<tr>");

if($task->data("epicParentTask")) {
    $h->addClass("subtask");
}

$h->begin();

    <td class='id' >{$task->id()}</td>
    <td class='id' >{$task->pdata(changed)->num()}</td>
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
        tmp::widget("board_widget_tags",array(
            "task" => $task,
        ))->exec();
    </td>
    <td>
        tmp::exec("../notice",array(
            "task" => $task,
        ));
    </td>    
    <td>
        tmp::widget("board_widget_vote",array(
            "task" => $task,
        ))->exec();
    </td>
</tr>

$h->end();