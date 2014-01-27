<?

namespace infuso\core;

class profiler {

    public static $stack = array();

    public static $log = array();

    private static $variables = array();

    private static $milestones = array();

    /**
     * Открывает новую операцию
     **/
    public static function beginOperation($group,$operation,$key) {

        if(!superadmin::check())
            return;

        if(!mod::debug())
            return;

        $key.="";

        self::$stack[] = array(
            $group,
            $operation,
            $key,
            microtime(1),
        );

    }

    /**
     * Открывает новую операцию
     **/
    public static function updateOperation($group,$operation,$key) {

        if(!mod_superadmin::check())
            return;

        if(!mod::debug())
            return;

        $key.="";

        $n = sizeof(self::$stack)-1;
        $time = self::$stack[$n][3];

        self::$stack[$n] = array(
            $group,
            $operation,
            $key,
            $time
        );

    }

    /**
     * Закрывает операцию
     **/
    public static function endOperation() {

        if(!superadmin::check())
            return;

        if(!mod::debug())
            return;

        $item = array_pop(self::$stack);
        $time = microtime(true) - $item[3];
        self::$log[$item[0]][$item[1]]["time"] += $time;
        self::$log[$item[0]][$item[1]]["keys"][$item[2]]["count"] ++;
        self::$log[$item[0]][$item[1]]["keys"][$item[2]]["time"] += $time;

    }

    public function addMilestone($name) {

        if(!mod::debug()) {
            return;
        }

        self::$milestones[] = array(
            $name,
            microtime(true),
        );
    }

    public function setVariable($key,$val) {
        self::$variables[$key] = $val;
    }

    public function getVariable($key) {
        return self::$variables[$key];
    }

    public function getMilestones() {
        return self::$milestones;
    }

    public static function sortLog($a,$b) {
        $r = $b["time"] - $a["time"];
        if($r>0) return 1;
        if($r<0) return -1;
        return 0;
    }

    public function log() {

        foreach(self::$log as $k1=>$a) {
            foreach($a as $k2=>$b) {

                $keys = $b["keys"];
                uasort($keys,array(self,"sortLog"));
                self::$log[$k1][$k2]["keys"] = $keys;
            }
        }
        return self::$log;
    }

    public function hlog() {

        ob_start();

        echo "generated: ".round(microtime(1)-$GLOBALS["infusoStarted"],2)." sec.\n";
        echo "classload: ".round($GLOBALS["infusoClassTimer"],4)." sec.\n";
        echo "Page size : ".util::bytesToSize1000(mod_profiler::getVariable("contentSize"))."\n";
        echo "Peak memory: ".util::bytesToSize1000(memory_get_peak_usage())." / ".ini_get("memory_limit")."\n";
        echo "\n";

        foreach(self::log() as $group =>$items) {
            echo $group.":\n";

            foreach($items as  $operation => $params) {
                echo $operation." | ".$params["time"]." s.\n";
            }

             echo "\n";

        }

        echo "--------------------------------------";

        foreach(self::getMilestones() as $s) {

            $time = $s[1] - $t;
            echo $s[0].": ".number_format($time,5);

            $t = $s[1];
            echo "\n";
        }


        return ob_get_clean();

    }

}
