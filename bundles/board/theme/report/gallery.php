<? 

tmp::header();

$log = board_task_log::all()->neq("files","")->limit(100);
foreach($log as $item) {

    foreach($item->task()->storage()->setPath("/log/".$item->data("files"))->files() as $file) {
        $preview = $file->preview(150,150);
        
        <a class='slideshow' href='{$file}' style='box-shadow:0 0 10px rgba(0,0,0,.3);display:inline-block;width:150px;height:170px;' >
            <img src='{$preview}' style='display:block;' />
            
            // Голосовалка
            // tmp::widget("board_widget_vote")->param("task",$item->task())->exec();
        </a>         
    }
    
}

ndra_slideshow::create()->version(2)->bind(".slideshow");

tmp::footer();