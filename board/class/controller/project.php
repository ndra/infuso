<?

/**
 * Контроллер для работы с проектами
 **/
class board_controller_project extends mod_controller {

    public function postTest() {
        return true;
    }

    /**
     * Возвращает список проектов
     **/
    public function post_listProjects($p) {

        $ret = array();

        $projects = board_project::visible()->limit(0);

        foreach($projects as $project) {
            $ret["data"][] = array(
                "id" => $project->id(),
                "title" => $project->title(),
                "icon" => $project->icon()->preview(16,16)->transparent(),
                "subscribe" => $project->isActiveUserHaveSubscription() ? "mail" : "/board/res/img/icons16/message-void.png",
                "completeAfter" => $project->data("completeAfter"),
            );
        }
        
        $ret["cols"] = array(
			array(
		       "name" => "icon",
		       "type" => "image",
			), array(
		       "name" => "title",
		       "title" => "Проект",
		       "width" => 200,
			), array(
		       "name" => "subscribe",
		       "type" => "image",
		    ), array(
		       "name" => "completeAfter",
		       "width" => 30,
		    )
		);

        return $ret;

    }
    
    /**
     * Возвращает простой список проектов (для выбиралки)
     **/    
    public static function post_listProjectsSimple($p) {

        $ret = array();
        
		$priority = board_task::all()
            ->eq("creator",user::active()->id())
            ->groupBy("projectID")
            ->orderByExpr("max(created) desc")
			->select("projectID");
			
		$priority = array_map(function($e) {
		    return $e["projectID"];
		},$priority);
		
		$priority = array_flip($priority);
			
        $projects = board_project::visible()->limit(0);
        if($search = trim($p["search"])) {
            $projects->like("title",$search)
                ->orr()->like("title",util::str($search)->switchLayout());
        }

        foreach($projects as $project) {
            $ret[] = array(
                "id" => $project->id(),
                "text" => $project->title(),
                "priority" => $priority[$project->id()],
            );
        }
        
        usort($ret,function($a,$b) {

            if($a["priority"] === null) {
                return 1;
            }
            if($b["priority"] === null) {
                return -1;
            }

            return $a["priority"] - $b["priority"];
		});

        return $ret;

    }
    
    public function post_subscribeProject($p) {
    
        $project = board_project::get($p["projectID"]);
	    $subscriptionKey = "board/project-{$project->id()}/taskCompleted";
	    $subscriptions = user::active()->subscriptions()->eq("key",$subscriptionKey);
	    
	    if($subscriptions->void()) {
	        $subscriptions->create();
	    } else {
	        $subscriptions->one()->delete();
	    }
    
    }

    public function post_deleteProjects($p) {
        foreach($p["idList"] as $projectID) {
            $project = board_project::get($projectID);
            $project->delete();
        }
    }
    
    public static function post_getProject($p) {
        $project = board_project::get($p["projectID"]);
        return array(
            "title" => $project->data("title"),
            "url" => $project->data("url"),
            "completeAfter" => $project->data("completeAfter"),
        );
    }

   public static function post_saveProject($p) {

        // Название проекта
        $data = array();
        if($title = trim($p["data"]["title"])) {
            $data["title"] = $title;
        } else {
            mod::msg("Название проекта обязательно для заполнения",1);
            return;
        }

        $data["url"] = $p["data"]["url"];
        $data["completeAfter"] = $p["data"]["completeAfter"];

        if($p["projectID"]=="new") {

            if(!user::active()->checkAccess("board/createProject")) {
                mod::msg(user::active()->errorText(),1);
                return;
            }

            $project = reflex::create("board_project");

        } else {

            $project = board_project::get($p["projectID"]);

            if(!user::active()->checkAccess("board/updateProject")) {
                mod::msg(user::active()->errorText(),1);
                return;
            }
        }

        foreach($data as $key=>$val) {
            $project->data($key,$val);
        }

        $project->loadFavicon();

        return true;
    }

}
