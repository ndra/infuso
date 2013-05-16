<? 

tmp::header();

$tasks = $project->tasks()
    ->desc("changed")
    ->gt("changed",util::now()->shiftDay(-30))
    ->limit(0);

<table class='uot58zssqv' >
    foreach($tasks as $task) {        
        <tr>
            <td>
                echo $task->text();
            </td>
            <td>
                echo $task->timeScheduled();
            </td>            
            <td>
                echo $task->timeSpent();
            </td>
            <td>
                echo $task->data("epicParentTask");
            </td>
        </tr>
    }
</table>

tmp::footer();