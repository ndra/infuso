<? 

<div class='qyquars52p' >

    echo "Регистрации пользователей за последние три года:";
    
    $chart = new google_chart();
    $chart->columnChart();
    $chart->width("100%");
    $chart->col("Месяц","string");
    $chart->col("Регистрации");

    $n = util::now()->year()*12 + util::now()->month() - 38;

    for($i=0;$i<=36;$i++) {
    
        $n++;
        $year = floor($n/12);
        $month = $n - $year*12 + 1;
        
        $d = user::all()
            ->eq("year(registrationTime)",$year)
            ->eq("month(registrationTime)",$month)
            ->count();
            
        $chart->row($year.".".$month,$d*1);
    }

    $chart->exec();

</div>