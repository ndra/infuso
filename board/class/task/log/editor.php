<?

class board_task_log_editor extends reflex_editor {
    public function render($task=false) {

        $item = $this->item();
           $log = "<table style='font-size:11px;word-wrap:break-word;' >";
        $log.= "<tr>";
        $log.= "<td style='white-space:nowrap;opacity:.5;' ><div style='width:140px;' >";
        $log.= "<div style='font-size:9px;' >{$item->pdata(created)->txt()}</div>";
        $log.= "<div>{$item->user()->title()}</div>";
        $log.= "</div></td>";
        $log.= "<td><div style='width:250px;' >{$item->text()}</div></td>";

        if($task)
            $log.= "<td><div style='width:150px;' >{$item->task()->project()->title()} / ".util::str($this->item()->task()->text())->ellipsis(30)."</div></td>";

        if($t = $item->timeSpent())
            $log.= "<td>$t ч.</td>";

        $log.= "</tr>";

        $log.= "</table>";
        return $log;
    }
}
