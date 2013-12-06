<? 

<div class='k6k070zxwy' >

    $max = 0;
    foreach($chart->colGroups() as $colGroup) {                    
        foreach($colGroup["cols"] as $col) {
            $max = max($max,$chart->colSum($col["name"]));
        }
    }    
    
    $chartWidth = 200; 
    
    <table>
        foreach($chart->colGroups() as $colGroup) {                    
            foreach($colGroup["cols"] as $col) {
                <tr>
                    <td>
                        echo $col["label"];
                    </td>
                    <td>
                        $value = round($chart->colSum($col["name"]),2);
                        $width =  $value / $max * $chartWidth;                    
                        $color = $col["color"];
                        <div style='width:{$width}px;background:{$color};height:16px;display:inline-block;' ></div>
                        <div class='col-sum' >{$value}</div>
                    </td>
                </tr>
            }  
        }
    </table>

</div>