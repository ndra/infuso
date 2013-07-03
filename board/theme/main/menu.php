<?

// Меню
<div class='sr3yrzht3j' >
    foreach(board_task_status::all() as $status) {
        <a class='item' href='#task-list/status/{$status->id()}' >
            echo $status->title();
            $n = board_task::all()->eq("status",$status->id())->count();
            <span class='count' >{$n}</span>
        </a>
    }
    <a href='#' menu:id='reports' class='item' >Отчеты</a>
</div>

// Субменю
<div class='sr3yrzht3j-submenu' style='position:absolute;z-index:100' >  
    <div class='submenu' menu:id='reports' >
        <a class='item' href='#report-done' >Сделано сегодня</a>
        <a class='item' href='#report-projects' >Проекты</a>
        <a class='item' href='#report-workers' >Пользователи</a>
        <a class='item' href='#report-vote' >Голоса</a>
        <a class='item' href='#report-gallery' >Галерея</a>
    </div>
</div>

ndra_menu::create(".sr3yrzht3j .item",".sr3yrzht3j-submenu .submenu")->exec();