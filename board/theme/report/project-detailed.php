<? 

tmp::header();
tmp::reset();
mod::coreJS();

$from = util::date($params["from"])->date();
$to = util::date($params["to"])->date();

<div style='padding:20px;' >

    <div style='position:absolute;right:20px;' >
        <input type='checkbox' id='display-subtasks' >
        <label for='display-subtasks' >Показать подзадачи</label>
    </div>
    
    <div style='margin-bottom:20px;' >
        echo "Отчет по проекту «{$project->title()}» {$from->text()} &mdash; {$to->text()}";
        <div style='opacity:.5;font-style:italic;font-size:.8em;' >
            echo "Показаны задачи, изменившие статус в указанный интервал. Подзадачи выводятся без ограничения по дате.";
        </div>
    </div>

    $tasks = $project->tasks()
        ->desc("changed")
        ->geq("date(changed)",$from)
        ->leq("date(changed)",$to)
        ->eq("epicParentTask",0);

    if(($tag = trim($params["tag"])) && $tag!="*") {
        $tasks->useTag($tag);
    }
        
    <table>
        <tr>
            <td>
                tmp::exec("tasks",array(
                    "tasks" => $tasks,
                ));
            </td>
            <td style='padding-left:20px;' >
                tmp::exec("../contributors",array(
                    "from" => $from,
                    "to" => $to,
                    "project" => $project,
                ));
            </td>
        <tr>        
    </table>
</div>

tmp::footer();