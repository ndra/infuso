<?

namespace Infuso\Board\Controller;

use Infuso\Board\TaskStatus;
use \user;

/**
 * Контроллер для операций с вложениями в задачи
 **/
class Attachment extends \Infuso\Core\Controller {

    public function postTest() {
        return user::active()->exists();
    }

    /**
     * Возвращает список файлов к задаче
     **/
    public function post_listFiles($p) {

        $task = Task::get($p["taskID"]);

        // Параметры задачи
        user::active()->checkAccessThrowException("board/listTaskAttachments",array(
            "task" => $task
        ));

        $ret = array();

        $path = $p["sessionHash"] ? "/log/".$p["sessionHash"] : "/";
        foreach($task->storage()->setPath($path)->files() as $file) {
            $ret[] = array(
                "text" => $file->name(),
                "preview" => $file->preview(100,100),
                "name" => $file->name(),
                "url" => $file->url(),
            );
        }

        return $ret;

    }

	/**
	 * Закачивает файл в задачу
	 **/
    public function post_uploadFile($p) {

        $task = Task::get($p["taskID"]);

        // Параметры задачи
        if(!user::active()->checkAccess("board/uploadFile",array(
            "task" => $task
        ))) {
            mod::msg(user::active()->errorText(),1);
            return;
        }

        $file = $_FILES["file"];
        $path = $p["sessionHash"] ? "/log/".$p["sessionHash"] : "/";
        $task->storage()->setPath($path)->addUploaded($file["tmp_name"],$file["name"]);
        $task->uploadFilesCount();

    }

}
