<?

/**
 * Класс для подключения стандартных библиотек
 **/
class tmp_lib {

	public function path() {
	    return mod::service("classmap")->getClassBundle(get_class())->path()."/res/";
	}

    /**
     * Подключает less-библиотеку components
     **/
    public function components() {
        tmp::css(self::path()."/components.less");
    }

    public function jq() {
        tmp::singleJS(self::path()."/jquery-1.8.2.min.js",-1000);
    }

    /**
     * @todo Сделать чтобы параметром можно было бы передавать название темы
     **/
    public function jqui() {
        self::jq();
        tmp::js("http://yandex.st/jquery-ui/1.10.3/jquery-ui.min.js");
        tmp::css("http://yandex.st/jquery-ui/1.10.3/themes/base/jquery-ui.min.css");
    }
    
    public function jqcolor() {
        self::jq();
        tmp::js(self::path()."/jquery.color-2.1.0.min.js");
    }
    
    public function jsplumb() {
        self::jq();
        tmp::singlejs(self::path()."/jquery.jsPlumb-1.3.16-all-min.js");
    }
    
    public function highlightjs() {
        self::jq();
        tmp::singlejs(self::path()."/highlightjs/js/highlight.pack.js");
        tmp::singlecss(self::path()."/highlightjs/css/default.css");
    }

    public function reset() {
        tmp::css(self::path()."/reset.css",-1000);
    }

}
