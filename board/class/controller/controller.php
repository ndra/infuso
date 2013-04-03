<?

class board_controller extends mod_controller {

    public static function postTest() {
        return user::active()->exists();
    }
    


    private static function sortColors($a,$b) {
        return strcmp($a,$b);
    }




    public static function applyTaskSearch($tasks,$q) {
        $q = reflex_mysql::escape($q);
        $q = mb_strtolower($q,"utf-8");
        $tasks->where("lower(`board_task`.`text`) like '%$q%' or lower(`board_project`.`title`) like '%$q%' or `board_task`.`id`='$q' ");
    }

    // ----------------------------------------------------------------------------- Отчеты

    public static function post_reportLog($p) {

        $ret = array();
        if(board_security::test("board:showReportLog")) {

            $log = board_task_log::all()->limit(100);
            if($taskID = $p["taskID"])
                $log->eq("taskID",$taskID);

            $lastDate = "";

            foreach($log as $item) {

                $date = $item->pdata("created")->notime()->txt();
                if($lastDate!=$date) {
                    $txt = "";
                    $txt.= "<div><span style='font-size:18px;' >".$date."</span>";
                    $txt.= "Потрачено ".board_task_log::all()->eq("created",$item->pdata("created")->notime(),"date")->sum("timeSpent")." ч.";
                    $txt.= "</div>";
                    $ret[] = array(
                        "text" => $txt,
                    );
                }
                $lastDate = $date;

                $txt = "<table><tr>";
                $txt.= "<td><div style='font-size:11px;opacity:.5;display:inline-block;width:50px;' >".$item->pdata("created")->time()."</div></td>";
                $txt.= "<td><div style='font-size:11px;opacity:.5;display:inline-block;width:150px;' >".$item->user()->title()."</div></td>";

                $msg = util::str($item->data("text"))->ellipsis(100);
                if($item->data("blah")) {
                    $msg = "<div style='border-radius:10px;border:1px solid rgba(0,0,0,.3);padding:10px;' >".$msg."</div>";
                }

                $txt.= "<td><div style='display:inline-block;width:300px;padding-right:20px;' >{$msg}</div></td>";

                $title = $item->task()->project()->title()."&nbsp;/ ".util::str($item->task()->title())->ellipsis(50);
                $txt.= "<td><div style='display:inline-block;width:250px;opacity:.5;font-size:11px;' >{$title}</div></td>";

                if($t = $item->data("timeSpent"))
                    $txt.= "<td><div style='display:inline-block;width:50px;opacity:.5;font-size:11px;' >$t ч.</div></td>";
                $txt.= "</tr></table>";

                $ret[] = array(
                    "text" => $txt,
                );
            }
        }
        return $ret;
    }

    public static function post_reportLogForTask($p) {

        $ret = array();
        if(user::active()->checkAccess("board:showReportLog")) {

            $log = board_task_log::all()->eq("taskID",$p["taskID"])->limit(100);

            foreach($log as $item) {

                $txt = "";
                $txt.= "<div style='font-size:11px;opacity:.5;' >".$item->pdata("created")->txt()." ";
                $txt.= $item->user()->title();
                $txt.= "</div>";

                $msg = nl2br(util::str($item->data("text")));
                if($item->data("blah")) {
                    $msg = "<div style='border-radius:10px;border:1px solid rgba(0,0,0,.3);padding:10px;' >".$msg."</div>";
                }

                $txt.= "<div>{$msg}</div>";

                $ret[] = array(
                    "text" => $txt,
                );
            }
        }
        return $ret;
    }

    public static function post_reportDone($p) {

        mod_cmd::meta("cols",array(
            "month" => array("title"=>"Месяц"),
            "completed" => array("title"=>"Закрыто задач"),
            "paid" => array("title"=>"Платные работы"),
            "bonus" => array("title"=>"Бонус"),
        ));

         $ret = array();

         $projectID = $p["projectID"];

         if(board_security::test("board:showReportDone"))
            for($year = util::now()->year()-1;$year<=util::now()->year();$year++)
                for($month=1;$month<=12;$month++) {

                    $t = board_task::all()->eq("status",3)->eq("changed",$year,"year")->eq("changed",$month,"month");
                    if($projectID!="*")
                        $t->eq(projectID,$projectID);

                    $tasks = $t->copy()->eq("bonus",0);
                    $tasksBonus = $t->copy()->eq("bonus",1);

                    $paid = $tasks->sum("timeSpent")." / ".$tasks->sum("timeSceduled");
                    $bonus = $tasksBonus->sum("timeSpent")." / ".$tasksBonus->sum("timeSceduled");
                    if($paid == "0 / 0")
                        $paid = "<span style='color:#cccccc;' >&mdash;</span>";
                    if($bonus == "0 / 0")
                        $bonus = "<span style='color:#cccccc;' >&mdash;</span>";

                    $ret[] = array(
                        "month" => str_pad($month,2,0,STR_PAD_LEFT).".".$year,
                        "completed" => $tasks->count(),
                        "paid" => $paid,
                        "bonus" => $bonus,
                    );
                }
        return $ret;
    }

    public static function post_reportDoneForProject($p) {

        $project = board_project::get($p["projectID"]);
        if(!$project->exists() && board_project::visible()->count()==1)
            $project = board_project::visible()->one();

        $tasks = $project->tasks()->eq("status",3)->eq("bonus",0);
        $year1 = $tasks->min("year(changed)");
        $year2 = $tasks->max("year(changed)");

        $html = "";

        $html.= "<style>.cuinyuiq {width:600px;}</style>";
        $html.= "<style>.cuinyuiq td{padding:4px;border:1px solid #ededed;}</style>";

        for($year=$year1;$year<=$year2;$year++)
            for($month=1;$month<=12;$month++) {

                $tt = $tasks->copy()->eq("year(changed)",$year)->eq("month(changed)",$month);
                if(!$tt->count())
                    continue;

                $html.= "<div style='font-size:18px;padding:20px 0px;' >".$year.".".$month."</div>";

                $html.= "<table class='cuinyuiq' >";
                foreach($tt as $task) {
                    $html.= "<tr>";
                    $html.= "<td>{$task->text()}</td>";
                    $html.= "<td style='text-align:right;white-space:nowrap;' >{$task->data(timeSceduled)} ч.</td>";
                    $html.= "</tr>";
                }


                $html.= "<tr style='font-weight:bold;' >";
                $html.= "<td>Итого за месяц</td>";
                $html.= "<td style='text-align:right;white-space:nowrap;' >{$tt->sum(timeSceduled)} ч.</td>";
                $html.= "</tr>";

                $html.= "</table>";
            }

        return $html;
    }

    public static function post_reportInfograph($p) {

        /*ob_start();
        foreach(user::all() as $user)
            if($user->check("board:access")) {
                echo "<div style='width:200px;margin:20px;display:inline-block;' >";
                echo "<div style='font-size:18px;' >".$user->title()."</div>";

                $user->addBehaviour("board_user");
                echo "За сутки: ".$user->boardLog24H()->sum("timeSpent")." ч.<br/>";
                echo "За неделю: ".$user->boardLogWeek()->sum("timeSpent")." ч.<br/>";
                echo "За месяц: ".$user->boardLogMonth()->sum("timeSpent")." ч.<br/>";

                echo "Задач закрыто: ".$user->completedTasks()->count()."<br/>";

                echo "</div>";
            }
        return ob_get_clean();
        */
    }

}
