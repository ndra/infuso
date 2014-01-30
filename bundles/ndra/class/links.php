<?php

/**
 * Класс для работы с ссылками
 * 
 * @autor Petr Grishin <petr.grishin@grishini.ru>
 **/
class ndra_links extends mod_controller {
    
    
    public static function indexTest() {
    	return true;
    }
    
    public static function index() {
        tmp::header();
        
        self::addExternalJs();
        
        $url = mod_url::current()->url();
        
        echo "<a href=\"#\">test1</a><br /><br />";
        echo "<a href=\"/\">test2</a><br /><br />";
        
        echo "<a href=\"{$url}\">test3</a><br /><br />";
        echo "<a href=\"{$url}/site_test\">test4</a><br /><br />";
        
        echo "<a href=\"http://google.com\">test5</a><br /><br />";
        echo "<a href=\"mailto:a@a.ru\">test6</a><br /><br />";
        echo "<a href=\"\">test7</a><br /><br />";
        echo "<a>test8</a><br /><br />";

        tmp::footer();
    }
    
    /**
    * Добавляет JS который открывает все внешние ссылки в новом окне
    **/
    public static function addExternalJs() {
        tmp::jq();
        tmp::js("/ndra/res/links/external.js");
    }

}
