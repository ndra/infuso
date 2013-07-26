<?

// Меню
<div class='sr3yrzht3j' >
    foreach(board_task_status::all() as $status) {
        <a class='item' href='#task-list/status/{$status->id()}' >
            echo $status->title();
            $n = board_task::visible()->eq("status",$status->id())->count();
            <span class='count' >{$n}</span>
        </a>
    }
    <a href='#' menu:id='reports' class='item' >Отчеты</a>
    <a href='#' menu:id='conf' class='item' >Настройки</a>
</div>

// Субменю
<div class='sr3yrzht3j-submenu' style='position:absolute;z-index:100' >  
    <div class='submenu' menu:id='reports' >
        <a class='item' href='#report-done' >Сделано сегодня</a>
        <a class='item' href='#report-projects' >Проекты</a>
        
        if(user::active()->checkAccess("board/showReportUsers")) {
            <a class='item' href='#report-workers' >Пользователи</a>
        }

        if(user::active()->checkAccess("board/showReportVote")) {
            <a class='item' href='#report-vote' >Голоса</a>
        }
        
        if(user::active()->checkAccess("board/showReportGallery")) {
            <a class='item' href='#report-gallery' >Галерея</a>
        }
        
    </div>
    <div class='submenu' menu:id='conf' >
        <a class='item' href='#conf-projects' >Проекты</a>
        
        if(user::active()->checkAccess("board/showAccessList")) {
            <a class='item' href='#conf-access' >Доступ</a>
        }
        
    </div>
</div>

ndra_menu::create(".sr3yrzht3j .item",".sr3yrzht3j-submenu .submenu")->exec();