<?

namespace Infuso\Board\Controller;

class Access extends \Infuso\Core\Controller {

    public function postTest() {
        return user::active()->exists();
    }

    /**
     * Контроллер получения списков доступа
     **/
    public function post_accessList($p) {

        // Параметры задачи
        user::active()->checkAccessThrowException("board/showAccessList",array(
            "access" => $access,
        ));

        $ret = array(
            "cols" => array(
                array(
                    "name" => "user",
                    "title" => "Пользователь",
                    "width" => 200,
                ), array(
                    "name" => "project",
                    "title" => "Проект",
                    "width" => 200,
                ), array(
                    "name" => "operations",
                    "title" => "Операции",
                    "width" => 400,
                ),
            ),
            "data" => array(),
        );

        foreach(board_access::all() as $access) {

            $operations = array();
            if($access->data("showSpentTime")) {
                $operations[] = "Просмотр потраченного времени";
            }
            if($access->data("showComments")) {
                $operations[] = "Просмотр комментариев";
            }
            $operations = implode(", ",$operations);

            $ret["data"][] = array(
                "id" => $access->id(),
                "user" => $access->user()->title(),
                "project" => $access->project()->title(),
                "operations" => $operations,
            );
        }

        return $ret;
    }

    public function post_getAccessData($p) {

        $access = board_access::get($p["accessID"]);

        // Параметры задачи
        user::active()->checkAccessThrowException("board/getAccessData",array(
            "access" => $access,
        ));

        return array(
            "userID" => $access->user()->id(),
            "userText" => $access->user()->title(),
            "projectID" => $access->project()->id(),
            "projectText" => $access->project()->title(),
            "showComments" => $access->data("showComments"),
            "showSpentTime" => $access->data("showSpentTime"),
            "editTasks" => $access->data("editTasks"),
            "editTags" => $access->data("editTags"),
        );


    }

    public function post_save($p) {

        $access = board_access::get($p["accessID"]);

        // Параметры задачи
        user::active()->checkAccessThrowException("board/updateAccessData",array(
            "access" => $access,
        ));

        $access->setData(array(
            "userID" => $p["data"]["userID"],
            "projectID" => $p["data"]["projectID"],
            "showComments" => $p["data"]["showComments"],
            "showSpentTime" => $p["data"]["showSpentTime"],
            "editTasks" => $p["data"]["editTasks"],
            "editTags" => $p["data"]["editTags"],
        ));

        return true;

    }

}
