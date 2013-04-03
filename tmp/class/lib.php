<?

/**
 * Класс для подключения стандартных библиотек
 **/
class tmp_lib {

    /**
     * Подключает less-библиотеку components
     **/
    public function components() {
        tmp::css("/tmp/res/components.less");
    }

    public function jq() {
        tmp::singleJS("/tmp/res/jquery-1.8.2.min.js",-1000);
    }

    /**
     * @todo Сделать чтобы параметром можно было бы передавать название темы
     **/
    public function jqui() {
        self::jq();
        tmp::js("http://yandex.st/jquery-ui/1.9.0/jquery-ui.min.js");
        tmp::css("http://yandex.st/jquery-ui/1.9.0/themes/base/jquery-ui.min.css");
    }
    
    public function jqcolor() {
        self::jq();
        tmp::js("/tmp/res/jquery.color-2.1.0.min.js");
    }
    
    public function jsplumb() {
        self::jq();
        tmp::singlejs("/tmp/res/jquery.jsPlumb-1.3.16-all-min.js");
    }
    
    public function highlightjs() {
        self::jq();
        tmp::singlejs("/tmp/res/highlightjs/js/highlight.pack.js");
        tmp::singlecss("/tmp/res/highlightjs/css/default.css");
    }

    public function reset() {
        tmp::css("/tmp/res/reset.css",-1000);
    }

}
