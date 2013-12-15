<? 

$domain = $p1;

<div style='padding:20px;' >

    <h2>{$domain->title()}</h2>
    
    <div style='margin-bottom:20px;' >
        tmp::exec("filter", array("domain"=>$domain));
    </div>
    
    <div>
        $day = $_GET["date"];
        tmp::exec("positions",$domain,$day);
    </div>
    
    <div>
        //tmp::exec("chart",$domain,$day);
    </div>
    
</div>

util::profiler();