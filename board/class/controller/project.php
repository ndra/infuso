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
            $ret[] = array(
                "id" => $project->id(),
                "text" => $project->title(),
                "icon" => $project->icon()->preview(16,16)->transparent(),
            );
        }

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
            return $a["priority"] - $b["priority"];
		});

        return $ret;

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
