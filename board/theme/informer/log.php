<?

<h2 style='margin-bottom:10px;' >Активность:</h2>

$log = board_task_log::all();

<div class='pjs63jttpu' >
    foreach($log as $item) {
    
        $userpick = $item->user()->userpick()->preview(16,16);
    
        <div class='item' style='background:url($userpick) no-repeat;padding-left:20px;' >
            <div style='position:relative;height:16px;' >
                <div class='user' >{$item->user()->title()}</div>
                <div class='date' >{$item->pdata(created)->left()}</div>
            </div>
            echo $item->task()->title();
            
            echo $item->data("text");
        </div>
    }
</div>