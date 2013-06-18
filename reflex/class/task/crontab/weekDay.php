<?

/**
 * Класс для дня месяца кронтаба
 **/
class reflex_task_crontab_weekDay extends reflex_task_crontab_field {

    /**
     * Уменьшает дату на одну минуту
     **/
    public function decrementDate(&$timestamp) {
        $timestamp = $timestamp - 3600 * 24;
    }

    protected function unitsInTimestamp($timestamp) {
        return floor($timestamp / 3600 / 24);
    }

    protected function unitsInRank($timestamp) {
        return date("N",$timestamp);
    }

}
