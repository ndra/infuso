<?php

class ndra_carousel extends mod_controller {

    private $selector = "";
    private static $included = false;

    public function __construct($selector=null) {
        $this->selector = $selector;
    }

    /**
     * Создает билдер карусели
     * @param string css-селектор
     **/
    public function create($selector) {
        return new self($selector);
    }

    /**
     * Устанавливает минимальную ширину элемента карусели
     **/
    public function minWidth($w) {
        $this->param("minWidth",$w);
        return $this;
    }

    /**
     * Устанавливает высоту карусели
     **/
    public function height($h) {
        $this->param("height",$h);
        return $this;
    }

    /**
     * Устанавливает расстояние между элементамикарусели
     **/
    public function spacing($s) {
        $this->param("spacing",$s);
        return $this;
    }

    /**
     * Устанавливает на сколько элементов перематывают кнопки назад-вперед
     * Если передть стрку "auto" - карусель перемотает на количество видимых элементов
     **/
    public function offset($val) {
        $this->param("offset",$val);
        return $this;
    }

    /**
     * Включает/выключает зацикливание карусели
     **/
    public function cycle($val=true) {
        $this->param("cycle",$val);
        return $this;
    }
    
    /**
     * Задает css-селектор для стрелки "Вперед"
     **/
    public function next($next=true,$nextDisabled=null) {
        $this->param("next",$next);
        $this->param("nextDisabled",$nextDisabled);
        return $this;
    }
    
    /**
     * Задает css-селектор для стрелки "Назад"
     **/
    public function prev($prev,$prevDisabled=null) {
        $this->param("prev",$prev);
        $this->param("prevDisabled",$prevDisabled);
        return $this;
    }

    /**
     * Задает css-селектор для контейнера элементов
     **/
    public function container($val) {
        $this->param("container",$val);
        return $this;
    }
    
    /**
     * Задает css-селектор для контейнера навигации
     **/
    public function navigation($navigation,$navigationActive=null) {
        $this->param("navigation",$navigation);
        $this->param("navigationActive",$navigationActive);
        return $this;
    }

    /**
     * Устанавливает таймер авоматической перемотки карусели (в секундах)
     **/
    public function delay($val=true) {
        $this->param("delay",$val);
        return $this;
    }
    
    /**
     * Устанавливает вертикальную карусель
     **/
    public function vertical() {
        $this->param("vertical", true);
        return $this;
    }
    

    public function exec() {
    
        if(!self::$included) {
            mod::coreJS();
            tmp::jq();
            tmp::js("/ndra/res/carousel/carousel.js");
            self::$included = true;
        }
        
        $params = json_encode($this->params());
        tmp::script("$(function(){ ndra.carousel.create('{$this->selector}',$params) });");
        tmp::head("<style>".$this->selector."{opacity:0;}</style>");
        tmp::script("\$(function() {\$('{$this->selector}').animate({opacity:1},500);})");
    }

}
