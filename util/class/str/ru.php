<?

/**
 * Поведение к классу util_str, реализующее специфические для русского языка методы
 **/

class util_str_ru extends mod_behaviour {

    public function addToClass() {
        return "util_str";
    }

    /**
    * Функция возвращает окончание для множественного числа слова на основании числа и массива окончаний
    * @param  $number Integer Число на основе которого нужно сформировать окончание
    * @param  $endingsArray  Array Массив слов или окончаний для чисел (1, 4, 5),
    *         например array('яблоко', 'яблока', 'яблок')
    * @return String
    */
    function numEnding($number, $endingArray) {
    
        array_unshift($endingArray,$this->component()."");
    
        $number = $number % 100;
        if ($number>=11 && $number<=19) {
            $ending=$endingArray[2];
        }
        else {
            $i = $number % 10;
            switch ($i)
            {
                case (1): $ending = $endingArray[0]; break;
                case (2):
                case (3):
                case (4): $ending = $endingArray[1]; break;
                default: $ending=$endingArray[2];
            }
        }
        return new util_str($ending);
    }
    
    /**
     * Возвращает склонение (падеж) слова
     * ИСпользует яндекс.сколнятор
     **/
    public function inflect($inflection) {
    
        $word = $this."";
        $xml = simplexml_load_file("http://export.yandex.ru/inflect.xml?name=$word");
        
        foreach($xml->inflection as $inf) {
            $ret[$inf["case"]*1] = $inf."";
        }
            
        $ret = $ret[$inflection];
        
        if(!$ret) {
            $ret = $word;
        }
        
        return $ret;
    }
    
	/**
	 * Обрезает окончание слова
	 **/
    public function stem() {
        $s = new util_str_stemer();
        $s = $s->stemString($this);
        return new self($s);
    }
    

}
