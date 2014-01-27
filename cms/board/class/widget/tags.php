<?

class board_widget_tags extends tmp_widget {

    public function name() {
        return "Виджет тэгов доски";
    }

    public function execWidget() {
        tmp::exec("/board/widget/tags");
    }

}
