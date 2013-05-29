<?

/**
 * Контроллер крона
 **/
class mod_cron extends mod_controller {

    public static function indexTest() {
        return true;
    }

    public static function index() {
        self::checkTimeAndprocess();
    }

    /**
     * Проверяет, когда был запущен последний раз крон
     * (Предотаращает слишком частый запуск крона)
     * Выполняет задачу
     **/
    public function checkTimeAndprocess() {

        $file = file::get("/mod/service/cron.php");
        $time = $file->time();

        // Читаем статус из файла
        $status = $file->contents();

        // Если в статусе написано, что крон завершился,
        // Определяем время работы крона, умножаем на 10
        // Следующий запуск крона возможен не раньше, чем длительность предыдущего запуска * 10 + 10
        // Т.е. если скрипт выполнялся 8 с., то следующий запуск возможен через 8*10+10 = 90 с.
        if(preg_match("/done:\s*(\d+)/",$status,$matches)) {
            $s = $matches[1];
            $delay = $s*10+10;
        } else {
            $delay = 3600;
        }

        $left = $time->stamp() + $delay - util::now()->stamp();

        // Рарзрешаем обработчикам крона запуститься только если устек кулдаун или мы в режиме суперадмина
        if($left<0 || mod_superadmin::check()) {
            $file->put("processing");
            $t1 = util::now()->stamp();
            self::process();
            $t2 = util::now()->stamp();
            $time = $t2 - $t1;
            $file->put("done: ".$time);
        }

        // Выводим инфу
        if(mod_superadmin::check()) {            
            tmp::header();
            
            echo "<div style='padding:100px;' >";
            echo "Time to next launch ".$left." sec.";
            echo "</div>";
            
            tmp::reset();
            util::profiler();
            tmp::footer();

            if(array_key_exists("loop",$_GET)) {
                echo "<script>window.location.reload();</script>";
            }

        }

    }

    /**
     * Выполняет задачу без дополнительных проверок
     **/
    private function process() {
        mod::fire("mod_cron");
    }

}
