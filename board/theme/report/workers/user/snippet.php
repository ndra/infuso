<? 

<div class='bderkdyt73' >

    $spentMonth = board_task_log::all()
        ->eq("userID",$user->id())
        ->gt("created",util::now()->shift(-3600*24*30))
        ->sum("timeSpent");
    
    $daily = round($spentMonth / 20,1);
        
    <span class='factor' title='Сколько часов работает в день' >$daily</span>

</div>