<?

/**
 * Класс для дня месяца кронтаба
 **/
class reflex_task_crontab_month extends reflex_task_crontab_field {

    /**
     * Уменьшает дату на одну минуту
     **/
    public function incrementDate($date) {
        $date->shiftMonth(1);
        $date->day(1);
        $date->hours(0);
        $date->minutes(0);
    }

    protected function unitsInTimestamp($date) {
        return (int)date("Y",$date->stamp()*12 + (int)date("n",$date->stamp()));
    }

    protected function unitsInRank($date) {
        return $date->month();
    }

}
