<?

<tr>

    <td class='date' >{$task->pdata(changed)->num()}</td>

    <td class='id' >{$task->id()}</td>
       
    <td>                    
        echo $task->project()->title();
    </td>
        
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
        tmp::widget("board_widget_vote")
            ->param("task",$task)
            ->exec();
    </td>
    
</tr>