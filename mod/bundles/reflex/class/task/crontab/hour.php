<?

/**
 * Класс для часа кронтаба
 **/
class reflex_task_crontab_hour extends reflex_task_crontab_field {

    /**
     * Уменьшает дату на одну минуту
     **/
    public function incrementDate($date) {
        $date->shift(3600);
    }

    protected function unitsInTimestamp($date) {
        return floor($date->stamp() / 3600);
    }

    protected function unitsInRank($date) {
        return $date->hours();
    }

}
