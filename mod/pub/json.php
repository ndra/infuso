<?

ob_start();

if(!function_exists("__autoload")) {
    include("../__autoload.inc.php");
}

header("Content-type: text/plain; charset=utf-8");
header("Content-Disposition: inline; filename=result.txt");
$cmds = array(); //стэк всех команд ан выполнение
if($_POST["xcvb7c10q456ny1r74a6"] == "xcvb7c10q456ny1r74a6") {
    $data = $_POST;
}else{
    $data = $_POST["data"];
    $data = json_decode($data,1);    
}
//если у нас пулл команд, то разбираем его для того что бы получить массив вида array('cmd'=>'комнада', 'p1'=>'парметр 1') - совместимость
if(count($data["cmdPull"])){
    $isMultiCMD = true;
    foreach($data["cmdPull"] as $cmd => $params){
        $params["cmd"] = $cmd;
        $cmds[] = $params;        
    }
}else{
    $cmds[] = $data;    
}


$jret = array();

foreach($cmds as $data){
    try {

        $cmd = $data["cmd"];
        $result = mod_post::process(
            $data,
            $_FILES,
            $status
        );
        //в случае мультикоманды складываем все в аосцииатвный массив array("коммнда"=>"рузльтат")
        if($isMultiCMD){
            $jret[$cmd] = $result;
        }else{
            $jret = $result;    
        }
        
        // Если скрипт вывел что-нибудь в поток, выводим это как сообщение
        $txt = ob_get_clean();
        if($txt) {
            mod::msg($txt,1);
        }

        reflex::storeAll();

    } catch(Exception $ex) {
    
        mod::msg("<b>Exception:</b> ".$ex->getMessage(),1);
    
    }        
}


// Собираем массив сообщений
$messages = array();
foreach(mod_log::messages() as $msg) {
    $messages[] = array(
        "text" => $msg->text(),
        "error" => $msg->error(),
    );
}

// Собираем массив событий
$events = array();
foreach(mod_event::all() as $event) {
    $events[] = array(
        "name" => $event->name(),
        "params" => $event->params()
    );
}
    
$ret = array(
    "messages" => $messages,
    "events" => $events,
    "data" => $jret,
    "completed" => !!$status,
);


$json = new mod_confLoader_json();
echo $json->write($ret);
