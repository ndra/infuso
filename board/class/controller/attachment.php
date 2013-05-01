<?

/**
 * Контроллер для операций с вложениями в задачи
 **/
class board_controller_attachment extends mod_controller {

    public function postTest() {
        return user::active()->exists();
    }

    /**
     * Возвращает список файлов к задаче
     **/
    public function post_listFiles($p) {

        $task = board_task::get($p["taskID"]);

        // Параметры задачи
        if(!user::active()->checkAccess("board/listTaskAttachments",array(
            "task" => $task
        ))) {
            mod::msg(user::active()->errorText(),1);
            return;
        }

        $ret = array();

        foreach($task->storage()->files() as $file) {
            $ret[] = array(
                "text" => $file->name(),
            );
        }

        return $ret;

    }

	/**
	 * Закачивает файл в задачу
	 **/
    public function post_uploadFile($p) {

        $task = board_task::get($p["taskID"]);

        // Параметры задачи
        if(!user::active()->checkAccess("board/uploadFile",array(
            "task" => $task
        ))) {
            mod::msg(user::active()->errorText(),1);
            return;
        }

        $file = $_FILES["file"];
        $task->storage()->addUploaded($file["tmp_name"],$file["name"]);

    }

}
