<?

/**
 * Класс поля день месяца
 **/
class reflex_task_crontab_monthDay extends mod_component {

    private $pattern;

    public function __construct($pattern) {
        $this->pattern = $pattern;
    }

    /**
     * Проверяет, подходит ли дата к шаблону
     **/
    public function match($timestamp) {

        // Если в шаблоне звездочка, проверку пройдет любая дата
        if($this->pattern=="*") {
            return true;
        }

        $monthDay = date("j",$timestamp);
        $day = floor($timestamp/3600/24);

        // Если в шаблоне число, сравниваем его со значением
        if(preg_match("/^\d+$/",$this->pattern)) {
            return (int)$monthDay == (int)$this->pattern;
        }

        // Если в шаблон типа */n
        if(preg_match("/^\*\/(\d+)$/",$this->pattern,$matches)) {
            $n = $matches[1];
            return $day % $n == 0;
        }

    }

    /**
     * Уменьшает дату на один день
     **/
    public function decrementDate(&$timestamp) {
        $timestamp = $timestamp - 3600 * 24;
    }

}
