<?

/**
 * Класс для минуты кронтаба
 **/
class reflex_task_crontab_minute extends reflex_task_crontab_field {

    /**
     * Уменьшает дату на одну минуту
     **/
    public function incrementDate($date) {
        $date->shift(60);
    }

    protected function unitsInTimestamp($date) {
        return floor($date->stamp() / 60);
    }

    protected function unitsInRank($date) {
        return $date->minutes();
    }

}
