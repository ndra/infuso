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
            );
        }

        return $ret;

    }
    
    /**
     * Возвращает простой список проектов (для выбиралки)
     **/    
    public static function post_listProjectsSimple($p) {

        $ret = array();

        $projects = board_project::visible()->limit(0);
        if($search = trim($p["search"])) {
            $projects->like("title",$search)
                ->orr()->like("title",util::str($search)->switchLayout());
        }

        foreach($projects as $project) {
            $ret[] = array(
                "id" => $project->id(),
                "text" => $project->title(),
            );
        }

        return $ret;

    }
    
    /*public static function post_getProject($p) {
        $project = board_project::get($p["projectID"]);
        return array(
            "title" => $project->data("title"),
            "priority" => $project->data("priority"),
            "customerEmail" => $project->pdata("customerUserID")->data("email"),
        );
    }  */

   /* public static function post_saveProject($p) {

        // Название проекта
        $data = array();
        if($title = trim($p["data"]["title"])) {
            $data["title"] = $title;
        } else {
            mod::msg("Название проекта обязательно для заполнения",1);
            return;
        }

        // Приоритет
        $data["priority"] = $p["data"]["priority"];

        $data["customerUserID"] = 0;
        if($u = trim($p["data"]["customerEmail"])) {
            $user = user::byEmail($u);
            if(!$user->exists()) {
                mod::msg("Пользователь с электронной почтой $u не существует",1);
                return;
            }
            $data["customerUserID"] = $user->id();
        }

        if($p["projectID"]=="new") {

            if(!user::active()->checkAccess("board:createProject")) {
                mod::msg(user::active()->errorText(),1);
                return;
            }

            $project = reflex::create("board_project");

        } else {

            $project = board_project::get($p["projectID"]);

            if(!user::active()->checkAccess("board:updateProject")) {
                mod::msg(user::active()->errorText(),1);
                return;
            }
        }

        foreach($data as $key=>$val) {
            $project->data($key,$val);
        }

        return true;
    }  */

    /**
     * Экшн удаления проекта
     **/
   /* public static function post_deleteProject($p) {
    
        mod::msg($p);
        return;

        if(!user::active()->check("board:access")) 
            return;

        $project = board_project::get($p["projectID"]);
        $project->delete();
        return true;
    }

    private static function sortProjects($a,$b) {
        if(!sizeof($b["tasks"]))
            return -1;
        if(!sizeof($a["tasks"]))
            return 1;
        return board_project::get($b["id"])->data("priority") - board_project::get($a["id"])->data("priority");
    }  */

}
