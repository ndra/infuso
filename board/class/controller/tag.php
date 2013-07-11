<?

class board_controller_tag extends mod_controller {

    public function postTest() {
        return user::active()->exists();
    }

    /**
     * Контроллер получения тэгов задачи
     **/
    public function post_getTaskTags($p) {
    
		$task = board_task::get($p["taskID"]);

        $ret = array(
            "tags" => array(),
		);
        
        foreach(board_task_tag_description::all() as $tag) {
            $ret["tags"][] = array(
                "tagID" => $tag->id(),
                "value" => $task->tagExists($tag->id()),
                "tagTitle" => $tag->title(),
			);
        }

        return $ret;
    }
    
    /**
     * Контроллер изменения тэга
     **/
    public function post_updateTag($p) {

		$task = board_task::get($p["taskID"]);
		$task->updateTag($p["tagID"],$p["value"]);
		mod::msg("Тэг изменен");
        return $ret;
    }
    
    public function post_getWidgetContent($p) {
    
        $task = board_task::get($p["taskID"]);
        $content = tmp::get("/board/widget/tags/ajax",array(
            "task" => $task,
		))->getContentForAjax();
		
		return array(
		    "content" => $content,
		);
    
    }

}