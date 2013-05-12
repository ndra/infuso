<?

<table class='dg13nhbck2' > 
    <tr>
        <td>
        
            $userpick = $user->userpick()->preview(100,100);
            
            <a href='{$url}' >
                <img src='{$userpick}' />
            </a>
            
            $url = mod::action("board_controller_report","worker",array(
                "id" => $user->id(),
            ));
            <div><a href='{$url}' >{$user->title()}</a></div>
            tmp::exec("snippet");
        
        </td>
        <td>

            $chart = google_chart::create();
            $chart->columnChart();
            $chart->width(400);
            $chart->col("день","string");
            $chart->col("Потрачено времени");
            
            for($i=60;$i>=0;$i--) {
            
                $date = util::now()->shiftDay(-$i)->date();
            
                $log = board_task_log::all()
                    ->eq("userID",$user->id())
                    ->eq("date(created)",$date);
                
                $chart->row($date->txt(),$log->sum("timeSpent"));
            }
            
            $chart->exec();
        </td>
    </tr>
</table>