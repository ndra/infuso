<?

namespace Infuso\Board;
use \User, \ndra_menu;

<div class='x55qv4lhb8m' >

    foreach(taskStatus::all() as $status) {
        if($status->id() != taskStatus::STATUS_DRAFT) {
            <a class='item' href='#task-list/status/{$status->id()}' >
                echo $status->title();
                $n = $status->visibleTasks()->count();
                <span class='count' >{$n}</span>
            </a>
        }
    }
    <i>
        <a href='#' menu:id='reports' class='item' >Отчеты</a>
        <a href='#' menu:id='conf' class='item' >Настройки</a>
    </i>
</div>
    
// Субменю
<div class='x55qv4lhb8m-submenu' style='position:absolute;z-index:100' >  
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
        
        <a class='item' href='#conf-profile' >Профиль</a>
        
    </div>
</div>

ndra_menu::create(".x55qv4lhb8m .item",".x55qv4lhb8m-submenu .submenu")->exec();
