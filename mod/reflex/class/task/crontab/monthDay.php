<?

/**
 * Класс для дня месяца кронтаба
 **/
class reflex_task_crontab_monthDay extends reflex_task_crontab_field {

    /**
     * Уменьшает дату на одну минуту
     **/
    public function incrementDate($date) {
        $date->shiftDay(1)->hours(0)->minutes(0);
    }

    protected function unitsInTimestamp($date) {
        return floor($date->stamp() / 3600 / 24);
    }

    protected function unitsInRank($date) {
        return $date->day();
    }

}
