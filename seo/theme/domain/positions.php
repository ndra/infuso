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

<table class='yp2yr8yzq7' >

    <thead>
        <tr>
            <td>запрос</td>
            <td></td>
            <td>Продвигаемя страница</td>
            foreach($domain->engines() as $engine) {
                <td>{$engine->title()}</td>
            }
        </tr>
    </thead>

    foreach($domain->queries()->limit(0) as $q) {
    
        <tr>
            <td>{$q->title()}</td>
            <td><a href='{$q->editor()->url()}' target='_blank' >ред</a></td>
            
            // Продвигаемая страница
            $url = $domain->normalizeUrl($q->data("url"));            
            <td>
                tmp::exec("info",array(
                    "url" => $url,
                ));
            </td>
            
            foreach($domain->engines() as $engine) {
       
                // Объект текущей позиции
                $currentPosition = seo_query_position::all()
                    ->eq("date",$to)
                    ->eq("queryID",$q->id())
                    ->eq("engineID",$engine->id())
                    ->one();
                
                // Объект предыдущей позиции
                $lastPosition = seo_query_position::all()
                    ->eq("date",$from)
                    ->eq("queryID",$q->id())
                    ->eq("engineID",$engine->id())
                    ->one();
                
                // Изменение позиции    
                $d = - ($currentPosition->data("position") - $lastPosition->data("position"));
                
                <td>
                    <span class='position' >
                        echo $currentPosition->data("position");        
                        if($now) {
                            if($d>0) $d = "+$d";
                            if($d)  echo " <span style='color:gray' >$d</span>";
                        }
                    </span>
                    
                    if($currentPosition->data("url")) {
                        tmp::exec("info",array(
                            "url" => $currentPosition->data("url"),
                        ));
                    }
                    
                </td>
                
            </td>
        }    
    
        </tr>
    }

</table>
