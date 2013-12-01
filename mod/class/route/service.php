<?

class mod_route_service extends mod_service {

	public function defaultService() {
		return "route";
	}

	/**
	 * Очищает кэш url
	 **/
	public function clearCache() {
	    $ret = mod::service("cache")->clearByPrefix("action-url:");
	    if(!$ret) {
	        mod::service("cache")->clear();
	    }
	}

    public final function forwardTest($url) {

        mod_profiler::beginOperation("url","forward",$url);

        if(is_string($url)) {
            $url = mod_url::get($url);
        }

        if($url->path()=="/mod") {
            mod_profiler::endOperation();
            return mod::action("mod");
        }

        $routers = mod::service("classmap")->classmap("routes");

        foreach($routers as $router) {
            if($callback = call_user_func(array($router,"forward"),$url)) {
                mod_profiler::endOperation();
                return $callback;
            }
        }
    }

	/**
	 * @todo Включить буфферизацию
	 **/
    public function urlToAction($url) {

        $key = "action-to-url/".$url;
       // $serializedAction = mod_cache::get($key);

        if(!$serializedAction) {

            $action = $this->forwardTest($url);
            if($action) {
                $serializedAction = json_encode(array(
					$action->className(),
					$action->action(),
					$action->params()
				));
                mod_cache::set($key,$serializedAction);
            }
            return $action;

        } else {

            list($class,$method,$params) = json_decode($serializedAction,true);
            $action = mod::action($class,$method,$params);

            return $action;

        }

    }

    public function actionToUrl() {
    }

}
