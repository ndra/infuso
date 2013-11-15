<?php

/**
 * Класс процессора контента
 * Используется для преобразования текста из текстовых полей
 *
 * @author Petr.Grishin <petr.grishin@grishini.ru>
 **/
class reflex_content_defaultProcessor extends reflex_content_processor {

    private static $tags = array();

	public function defaultService() {
	    return "contentProcessor";
	}

    /**
     * Стандартный процессор контента
     * Вставляет виджеты в переданный html
     *
     * @todo В будущем, возможно, мы дополним этот метод
     * @humor Будущие наступило, я пришел и дописал. :)
     * @author Petr.Grishin <petr.grishin@grishini.ru>
     **/
    public function process($html) {

        //Разбираем виджеты
        $html = self::processorWidget($html);

        if($this->param("newlineToParagraph")) {
            $html = self::newlineToParagraph($html);
		}

        if($tmp = $this->param("useTemplate")) {
            $html = tmp::get($tmp)->param("content",$html)->rexec();
		}

        return $html;
    }

    public static function replaceTag($x) {
        $id = util::id();
        self::$tags[$id] = $x[0];
        return $id;
    }

    /**
     * Преобразует контент, заменяя переходы на новую строку на параграфы
     **/
    public static function newlineToParagraph($html) {

        self::$tags = array();

        // Заменяем скрипты плейсхолдерами (чтобы в скриптах не вставлялись <br/>)
        $html = preg_replace_callback("/\<script[^>]*\>.*\<\/script\>/is",array(self,"replaceTag"),$html);

        // Убираем пробелы около \n
        $html = explode("\n",$html);
        foreach($html as $key=>$val)
            $html[$key] = trim($val);
        $html = implode("\n",$html);

        // Убираем новые строки между тэгами
        $html = preg_replace("/\>\n*/",">",$html);
        $html = preg_replace("/\<\n*/","<",$html);

        // Заменяем две и более новых строки на две
        $html = preg_replace("/\n{2,}/","\n\n",$html);

        // Преобразуем два перехода на новую строку в параграф
        $html = explode("\n\n",$html);
        foreach($html as $key=>$val)
            $html[$key] = "<p>".$val."</p>";
        $html = implode("",$html);

        $html = strtr($html,array(
            "\n" => "<br/>",
        ));

        $html = strtr($html,self::$tags);

        return $html;
    }

    /**
     * Процессор виджетов
     *
     * @author Petr.Grishin <petr.grishin@grishini.ru>
     **/
     public static function processorWidget($html) {

         //Уровень вложенности
         $level = 0;

         //Буфер
         $content = array();

         //Разбиваем строку на куски
         $train = preg_split("/(<widget\s*[^>]*?[^>]*\/>|<widget\s*[^>]*?[^>]*>|<\/widget>)/", $html, -1, PREG_SPLIT_DELIM_CAPTURE);

         foreach ($train as $item) {

             if (preg_match("/<widget\s*[^>]*?[^>]*\/>/", $item)) {
                 //Виджет без контента
                 $content[$level] .= self::replaceWidget($item);

             } elseif (preg_match("/<widget\s*[^>]*?[^>]*>/", $item)) {
                 //Поднимаем уровень вложености и заполняем его данными
                 $level++;
                 $content[$level] .= $item;

             } elseif (preg_match("/<\/widget>/", $item)) {
                 //Заканчиваем данный уровень, пропускаем его через процессор и добавляем его на уровень ниже

                 $content[$level] .= $item;

                 $innerContent = $content[$level];

                 //Удаляем текущий уровень из буфера
                 unset($content[$level]);

                 //Пропускаем текущий уровень через Процессор и добавляем его на уровень ниже
                 $level--;
                 $content[$level] .= self::replaceWidget($innerContent);

             } else {
                 //Добавляем на текущий уровень
                 $content[$level] .= $item;
             }

         } //end foreach

         //Если $level не равен 0, значит в коде ошибка, например не закрыли тег <widget>
         //Пишем в лог об ошибке
         //И добавляем все уровни >0 в вывод
         if ($level > 0) {
             mod::trace("Ошибка в разборе Виджетов, в данном коде: \n"
                 . $html
                 . "\n\n"
             );

             while ($level > 0) {
                 $content[0] .= $content[$level];
                 unset($content[$level]);
                 $level--;
             }
         }

         //Таким образом весь результат находиться на 0 уровне
         return $content[0];
    }

    /**
     * Метод заменяет виджет на отложеную функцию
     * UPD: метод не рефакторился
     *
     * @author Александр Голиков <golikov.org@gmail.com>
     **/
    private static function replaceWidget($matches) {

        // Находим параметры виджета
        $widget = util::str($matches)->html()->body->widget;

        $params = array();
        foreach($widget->attributes() as $a)
            $params[$a->getName()] = $a."";

        // Находим контент виджета
        $params["content"] = preg_replace(array(
            "/\<widget[^>]*?\>/s",
            "/\<\/widget\>/s",
        ),"",$matches);

        $w = tmp_widget::get($params["name"]);
        foreach($params as $key=>$val)
            $w->param($key,$val);

        return $w->delayedMarker();
    }

} //END CLASS
