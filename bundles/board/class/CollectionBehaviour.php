<?

namespace Infuso\Board;

class CollectionBehaviour extends \Infuso\Core\Behaviour {

    public function useTag($tag) {

        $this->join("board_task_tag","board_task_tag.taskID = board_task.id")->eq("board_task_tag.tagID",$tag);
        return $this->component();

    }

}
