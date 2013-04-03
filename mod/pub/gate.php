<?

Header("HTTP/1.0 200 OK");


// Подключаем функцию автозагрузки
if(!function_exists("__autoload"))
    include("../__autoload.inc.php");
    
try {

    mod_post::process($_POST,$FILES);
    
    mod_profiler::addMilestone("post command executed");
    
    $url = mod_url::current();
    $action = $url->action();
    
    mod_profiler::addMilestone("url transformed to action");
    
    if($action) {
        $action->exec();
    } else {
        mod_cmd::error(404);
    }

} catch(Exception $exception) {

    while(ob_get_level()) {
        ob_end_clean();
    }
    
    // Трейсим ошибки
    mod::trace($_SERVER["REMOTE_ADDR"]." at ".$_SERVER["REQUEST_URI"]." got exception: ".$exception->getMessage());

    try {
        
        tmp::destroyConveyors();

        $action = mod::action("mod_cmd","exception")
            ->param("exception",$exception)
            ->exec();
    
    } catch(Exception $ex2) {
        throw $exception;
    }
    
}
