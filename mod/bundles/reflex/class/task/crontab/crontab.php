<?

/**
 * Парсер crontab для reflex_task
 **/
class reflex_task_crontab {

    public function nextDate($pattern) {

        $pattern = util::splitAndTrim($pattern," ");
        $date = util::now()->seconds(0)->shift(60);

        for($j=0;$j<1000;$j++) {

            for($i=0;$i<5;$i++) {

                $field = self::fieldFactory($i,$pattern[$i]);

                if(!$field->match($date)) {
                    $field->incrementDate($date);
                    break;
                }

                if($i==4) {
                    return $date;
                }
            }
        }
    }

    private static function fieldFactory($n,$pattern) {

        $classNames = array(
            0 => "reflex_task_crontab_minute",
            1 => "reflex_task_crontab_hour",
            2 => "reflex_task_crontab_monthDay",
            3 => "reflex_task_crontab_month",
            4 => "reflex_task_crontab_weekDay",
        );
        $class = $classNames[$n];
        return new $class($pattern);

    }

}
