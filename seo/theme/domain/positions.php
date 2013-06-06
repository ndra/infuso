<?

$domain = $p1;
if(!$day = $p2) $day = util::now()->notime()."";


$current = mod_action::current();
$params = $current->params();
$from = $params["from_date"];
$to = $params["to_date"];



if(!$from){    
    $from = util::date($day)->shift(-60*60*24);  
}

if(!$to) {
    $to = util::date($day); 
}



echo "<table class='framed' >";
echo "<tr>";
echo "<td></td>";
    foreach($domain->engines() as $engine)
        echo "<td>{$engine->title()}</td>";
echo "</tr>";

foreach($domain->queries()->limit(0) as $q) {
    echo "<tr>";
    echo "<td>{$q->title()}</td>";
    foreach($domain->engines() as $engine) {

        $currentPosition = seo_query_position::all()->eq("date",$to)->eq("queryID",$q->id())->eq("engineID",$engine->id())->one();
        $now = $currentPosition->data("position");
        $lazd = seo_query_position::all()->eq("date",$from)->eq("queryID",$q->id())->eq("engineID",$engine->id())->one()->data("position");
        $d = -($now-$lazd);
        echo "<td>";
        echo $now;

        if($now) {
            if($d>0) $d = "+$d";
            if($d)  echo " <span style='color:gray' >$d</span>";
        }

        echo "<div style='font-size:9px;color:gray;' >{$currentPosition->data(url)}</div>";

        echo "</td>";
    }


    /*

    // Ссылки
    echo "<td style='font-size:9px;' >";
    $links = seo_link::all()->eq("domain",$domain->title())->where("lower('{$q->title()}')=lower(`title`)")->groupBy("donorDomain");
    foreach($links as $link)
        echo $link->donor()->title()."<br/>";
    echo "</td>";

    */

    echo "<td>{$q->pdata(update)}</td>";

    echo "</tr>";
}

echo "</table>";

?>
