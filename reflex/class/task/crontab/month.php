<?

/**
 * Класс для дня месяца кронтаба
 **/
class reflex_task_crontab_month extends reflex_task_crontab_field {

    /**
     * Уменьшает дату на одну минуту
     **/
    public function incrementDate(&$timestamp) {
        $timestamp = strtotime("+1 month",$timestamp);
    }

    protected function unitsInTimestamp($timestamp) {
        return (int)date("Y",$timestamp)*12 + (int)date("n",$timestamp);
    }

    protected function unitsInRank($timestamp) {
        return (int)date("n",$timestamp);
    }

}
