<? 

tmp::header();
tmp::reset();

$tasks = $project->tasks()
    ->desc("changed")
    ->gt("changed",util::now()->shiftDay(-30))
    ->eq("epicParentTask",0);
    
<div style='padding:20px;' >
    <table>
        <tr>
            <td>
                tmp::exec("tasks",array(
                    "tasks" => $tasks,
                ));
            </td>
            <td style='padding-left:20px;' >
                tmp::exec("contributors",array(
                    "tasks" => $tasks,
                    "project" => $project,
                ));
            </td>
        <tr>        
    </table>
</div>

tmp::footer();