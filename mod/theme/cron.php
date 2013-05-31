<? 

admin::header("Лог крона");

$log = reflex_log::all()
    ->limit(0)
    ->eq("type","cron")
    ->eq("date(datetime)",util::now()->date());
$data = $log->groupby("time")->select("`datetime` - interval (MINUTE(`datetime`)*60 + second(`datetime`)) second as `time`, count(*) as `count`, round(min(`p1`),1) as `min`, round(max(`p1`),1) as `max`, round(avg(`p1`),1) as `avg`");

<div style='padding:20px;' >
    <table class='x618enltmoz' >
    
        <thead>
            <tr>
                <td>Время</td>
                <td>min</td>
                <td>max</td>
                <td>avg</td>
                <td>count</td>
            </tr>
        </thead>
    
        foreach($data as $row) {
            <tr>
                $time = util::date($row["time"])->txt();
                <td>{$time}</td>
                <td>{$row[min]}</td>
                <td>{$row[max]}</td>
                <td>{$row[avg]}</td>
                <td>{$row[count]}</td>
            </tr>        
        }
    </table>
</div>

admin::footer();