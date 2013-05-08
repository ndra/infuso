<? 

tmp::header();
tmp::reset();

tmp::exec("my-graph");

<div style='padding:10px;' >

    <h2 style='margin-bottom:10px;' >Я делаю:</h2>

    $tasks = board_task::visible()
        ->eq("responsibleUser",user::active()->id())
        ->eq("status",board_task_status::STATUS_IN_PROGRESS);
    
    <div class='f5zez5yt78' >
        foreach($tasks as $task) {
            <div class='item' >
                <a href='/board/#{$task->id()}' target='_top' >{$task->title()}</a>
            </div>
        }
    </div>
    
    tmp::exec("log");

</div>

tmp::footer();