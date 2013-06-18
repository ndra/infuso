<?

/**
 * Класс поля день месяца
 **/
abstract class reflex_task_crontab_field extends mod_component {

    private $pattern = null;

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

        $class = get_class($this);

        // Сколько единиц всего
        $timestampUnits = $class::unitsInTimestamp($timestamp);
        // Сколько единиц в разряде
        $rankUnits = $class::unitsInRank($timestamp);

        // Если в шаблоне число, сравниваем его со значением
        if(preg_match("/^\d+$/",$this->pattern)) {
            return (int)$rankUnits == (int)$this->pattern;
        }

        // Если в шаблон типа */n
        if(preg_match("/^\*\/(\d+)$/",$this->pattern,$matches)) {
            $n = $matches[1];
            return $rankUnits % $n == 0;
        }

        return false;

    }

    /**
     * Возвращает количество единиц в метке времени
     * Определяется в классе-реализации поля
     * Например, для месяцев методж вернет сколько месяцев прошло с timestamp=0
     **/
    abstract protected function unitsInTimestamp($timestamp);

    abstract protected function unitsInRank($timestamp);

    /**
     * Уменьшает дату на единицу (минута, час, день или месяц)
     **/
    abstract public function decrementDate(&$timestamp);

}
