<?

tmp::header();
tmp::reset();

$from = util::date($params["from"])->date();
$to = util::date($params["to"])->date();

<div style='padding:20px;' >

    $tasks = board_task::visible()
        ->desc("changed")
        ->eq("status",array(board_task_status::STATUS_CHECKOUT,board_task_status::STATUS_COMPLETED))
        ->geq("date(changed)",$from)
        ->leq("date(changed)",$to);
        
    <table class='tl7dg1n4f2' style='width:800px;' >
    
        <thead>
            <tr>
                <td>Измененение статуса</td>
                <td>id</td>
                <td>Проект</td>
                <td>Задача</td>
                <td>План</td>
                <td>Потрачено</td>
                <td>Статус</td>
                <td></td>
            </tr>
        </thead>
    
        foreach($tasks->limit(0) as $task) {        
            
            tmp::exec("row",array (
                "task" => $task,
            ));
            
        }
    </table>

</div>

tmp::footer();