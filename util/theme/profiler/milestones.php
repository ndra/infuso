<? 

$t = $GLOBALS["infusoStarted"];

foreach(mod_profiler::getMilestones() as $s) {

    $time = $s[1] - $t;
    echo $s[0].": ".number_format($time,5);
    
    $t = $s[1];
    echo "<br/>";
}