<?

namespace infuso\core;

/**
 * Обработчик пост-запросов
 **/
class post {

	/**
	 * Обрабатывает POST-запрос
	 **/
	public function process($p,$files,&$status=null) {

		$status = false;

	    if(!$cmd = trim($p["cmd"])) {
			return;
	    }

	    $d = strtr($cmd,array(
			"::"=>":",
			"/"=>":"
		));
		
	    $d = explode(":",$d);
	    $method = array_pop($d);
	    $class = join("_",$d);

	    // Проверяем теоретическую возможность обработать пост-запрос
	    if(mod::service("classmap")->testClass($class,"mod_controller")) {

	        $obj = new $class;
		    if(call_user_func(array($obj,"postTest"),$p)) {
			    if($obj->methodExists("post_".$method)) {
			        $status = true;
			        try {
			        
			            // Вызываем сообщение
			            mod::fire("mod_beforecmd",array(
			                "params" => $p,
						));

						// Выполняем
			        	$ret = call_user_func_array(array($obj,"post_".$method),array($p,$files));
			        	
			        } catch(mod_userLevelException $ex) {
			            mod::msg($ex->getMessage(),1);
			        }
			        mod_component::callDeferedFunctions();
			        return $ret;
			    }
			}
			
		}

	    $cmd = mod_superadmin::check() ? $cmd : "";
	    mod_log::msg("Команда $cmd отклонена",1);
	}

}
