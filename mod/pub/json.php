<?

ob_start();

if(!function_exists("__autoload")) include("../__autoload.inc.php");

header("Content-type: text/plain; charset=utf-8");
header("Content-Disposition: inline; filename=result.txt");

try {

	if($_POST["xcvb7c10q456ny1r74a6"] == "xcvb7c10q456ny1r74a6") {
		$data = $_POST;
	} else {
		$data = $_POST["data"];
		$data = json_decode($data,1);
	}
	$jret = mod_post::process(
		$data,
		$_FILES,
		$status
	);

	// Если скрипт вывел что-нибудь в поток, выводим это как сообщение
	$txt = ob_get_clean();
	if($txt)
		mod::msg($txt,1);

	reflex::storeAll();

} catch(Exception $ex) {

	mod::msg("<b>Exception:</b> ".$ex->getMessage(),1);

}

// Собираем массив сообщений
$messages = array();
foreach(mod_log::messages() as $msg)
	$messages[] = array(
		"text" => $msg->text(),
		"error" => $msg->error(),
	);

// Собираем массив событий
$events = array();
foreach(mod_event::all() as $event)
	$events[] = array(
		"name" => $event->name(),
		"params" => $event->params()
	);
	
$ret = array(
	"messages" => $messages,
	"events" => $events,
	"data" => $jret,
	"completed" => !!$status
);

// Если включен режим отладки, добавляем данные профайлера
if(mod::debug()) {
	/*
	ob_start();
	tmp::header();
	tmp::footer();
	util::profiler();
	$r = ob_get_clean();
	$ret["profiler"] = $r;
	*/
}

echo json_encode($ret);
