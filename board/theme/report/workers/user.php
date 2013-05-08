<?

<table class='dg13nhbck2' > 
    <tr>
        <td>
        
            $userpick = $user->userpick()->preview(100,100);
            <img src='{$userpick}' />
            <div>{$user->title()}</div>
        
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