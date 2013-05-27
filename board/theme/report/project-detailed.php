<? 

tmp::header();
tmp::reset();
mod::coreJS();

$from = util::date($params["from"]);
$to = util::date($params["to"]);

<div style='padding:20px;' >

    <div style='position:absolute;right:20px;' >
        <input type='checkbox' id='display-subtasks' >
        <label for='display-subtasks' >Показать подзадачи</label>
    </div>
    
    <div style='margin-bottom:20px;' >
        echo "Отчет по проектам {$from->text()} &mdash; {$to->text()}";
    </div>

    $tasks = $project->tasks()
        ->desc("changed")
        ->geq("date(changed)",$from)
        ->leq("date(changed)",$to)
        ->eq("epicParentTask",0);
        
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