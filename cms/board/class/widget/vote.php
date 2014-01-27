<?

class board_widget_vote extends tmp_widget {

    public function name() {
        return "Виджет голосования";
    }

    public function execWidget() {
        tmp::exec("/board/widget/vote");
    }

}
