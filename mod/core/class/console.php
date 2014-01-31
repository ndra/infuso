<?

namespace infuso\core;
use \infuso\core\log;

class console extends controller {

	public static function indexTest() {
		return mod_superadmin::check();
	}

	public static function indexTitle() {
		return "Консоль";
	}

	public static function index() {
		header("Location:/mod/");
	}

	public static function indexFailed() {
		admin::fuckoff();
	}

	public static function xindex() {
	
	    try {
	
		// Пробуем залогиниться
	    if($_GET["cmd"]=="login") {
	        superadmin::post_login($_POST);
	        console::redirect("/mod/");
	    }

	    // Проверяем наличие административного пароля
	    if(!superadmin::check()) {
			self::fuckoff();
		}

	    // Пробуем установить бесконечное время выполнения скрипта
		if(function_exists("set_time_limit")) {
			set_time_limit(0);
		}
		
	    switch($_GET["cmd"]) {

	        case "logout":
	            mod_superadmin::post_logout();
	            self::redirect("/mod/");
	            break;

	        case "phpinfo":
	            phpinfo();
	            break;

	        case "change":
	        
	            if($_POST["p1"]) {
	                mod_superadmin::changePassword($_POST);
	            }

	            self::header();
	            echo "<a href='/mod/'>&larr;Back</a><br/><br/>";
	            echo "<form action='/mod/?cmd=change' method='post'>";
	            echo "<small >Password</small><br/>";
	            echo "<input name='p1' /><br/><br/>";
	            echo "<small >And again</small><br/>";
	            echo "<input name='p2' /><br/><br/>";
	            echo "<input type='submit' value='Change password' />";
	            echo "</form>";
	            self::footer();
	            
	            break;

	        case "relink":

				$step = $_POST["step"];

                $done = mod::app()->deployStep($step);

	            $messages = array();
	            foreach(log::messages() as $msg) {
	            	$messages[] = array(
						"text" => $msg->text(),
						"error" => $msg->error()
					);
				}
					
	            $ret = array(
					"messages" => $messages,
					"next" => !$done,
				);

				echo json_encode($ret);

	            break;

	        case "update":
	            $mods = mod::all();
	            $ret = array();
	            if($mod = $mods[$_POST["mod"]]) {
					mod_update::update($mod);
		            $messages = array();
		            foreach(mod_log::messages() as $msg)
		            	$messages[] = array("text"=>$msg->text(),"error"=>$msg->error());
		            $ret = array(
						"messages"=>$messages,
						"next" => true,
					);
	            } else {
	            	mod::app()->generateHtaccess();
	            }
	            echo json_encode($ret);
	            break;

	        default:

	            mod::app()->generateHtaccess();
	            self::header();
				
	            // Выводим предостережение в случае пароля 0000
	            if(superadmin::is0000()) {
	                echo "<div style='background:red;margin-bottom:20px;padding:10px;color:white;border:1px solid brown;' >";
	                echo "Infuso works with superadmin password <b>0000</b>. You must <a style='color:white;border-bottom:1px solid white;' href='?cmd=change'>change</a> superadmin password as soon as possible.";
	                echo "</div>";
	            }
	            
	            echo "<div style='text-align:right;margin-bottom:20px;' >";
	            // Изменить пароль
	            echo "<a href='?cmd=phpinfo' style='margin-right:20px;' >phpinfo</a>";
	            // Изменить пароль
	            echo "<a href='?cmd=change' style='margin-right:20px;'>Change password</a>";
	            // Выйти
	            echo "<a href='?cmd=logout'>Logout</a>";
	            echo "</div>";

	            // Окошко лога
	            echo "<div id='log' style='border:1px solid gray;height:200x;background:#ededed;'>";
	            echo "</div>";

	            echo "<br/><br/>";
	            echo "<input style='margin-right:20px;' type='button' onclick='cs.clearLog();cs.linkStep(0)' value='Relink' />";
	            echo "<input type='button' onclick='cs.clearLog();cs.updateStep(0)' value='Update' >";
	            echo "<script>cs.log('Standby');</script>";
	            
	            self::footer();
	            break;
	    }
	    
	    } catch (\Exception $ex) {
	        echo $ex;
	    }
	}
	
	public static function resPath() {
	    return mod::service("classmap")->getClassBundle(get_class())->path()."/res/";
	}

	public static function header() {
	
	    $resPath = self::resPath();
	
	    echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0//EN" "http://www.w3.org/TR/html4/strict.dtd">';
	    echo "<html>";
	    echo "<head>";
	    echo "<title>Console</title>";
	    echo "<style>";
	    
	    echo "html,body{padding:100px;font-family:arial;width:700px;font-size:14px;height:100%;}";
	    echo "input{font-size:19px;padding:3px;}";
	    echo ".log-message{padding:3px;}";
	    echo "</style>";
	    echo "<script type='text/javascript' src='{$resPath}jquery-1.2.6.pack.js'></script>";
	    echo "<script type='text/javascript' src='{$resPath}console.js'></script>";
	    echo "</head>";
	    echo "<body>";
	}

	public static function footer() {
	    echo "</body></html>";
	}

	/**
	 * Показывает пользователю форму входа и останавливает скрипт
	 **/
	public static function fuckoff() {

	    self::header(); ?>

	    <div style='text-align:center;'>
	    <div style='margin:0px auto 0px auto;width:300px;'>
	    <form method='post' action='/mod/?cmd=login'>
	    <input type='password' id='password' name='password' style='width:200px;padding:10px;' />
	    <input type='submit' value='&rarr;' style='padding:10px;' />
	    <input type='hidden' name='cmd' value='login' />
	    </form>
	    </div>
	    </div>

	    <script>$("#password").focus();</script>

	    <?
		self::footer();
	    die();
	}

}
