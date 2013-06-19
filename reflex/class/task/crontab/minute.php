<?

/**
 * Класс для минуты кронтаба
 **/
class reflex_task_crontab_minute extends reflex_task_crontab_field {

    /**
     * Уменьшает дату на одну минуту
     **/
    public function incrementDate(&$timestamp) {
        $timestamp = $timestamp + 60;
    }

    protected function unitsInTimestamp($timestamp) {
        return floor($timestamp / 60);
    }

    protected function unitsInRank($timestamp) {
        return (int) date("i",$timestamp);
    }

}
