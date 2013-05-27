<? 

tmp::header();
tmp::reset();

var_export(mod_url::current()."");

$from = util::date($params["from"]);
$to = util::date($params["to"]);

<div>
    echo "Отчет по проектам {$from->text()} &mdash; {$to->text()}";
</div>

$tasks = $project->tasks()
    ->desc("changed")
    ->geq("date(changed)",$from)
    ->leq("date(changed)",$to)
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