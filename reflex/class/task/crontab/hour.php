<?

/**
 * Класс для часа кронтаба
 **/
class reflex_task_crontab_hour extends reflex_task_crontab_field {

    /**
     * Уменьшает дату на одну минуту
     **/
    public function incrementDate(&$timestamp) {
        $timestamp = $timestamp + 3600;
    }

    protected function unitsInTimestamp($timestamp) {
        return floor($timestamp / 3600);
    }

    protected function unitsInRank($timestamp) {
        return date("G",$timestamp);
    }

}
