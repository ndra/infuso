<? 

tmp::reset();

$max = $chart->max();
$chartHeight = 150;
$colWidth = 25;

$chart->max();

<div class='mwiz6j92fe' >
    
    <table class='chart' >
        <tr>
            foreach($chart->rows() as $row) {
                <td>
                    foreach($chart->colGroups() as $colGroup) {
                    
                        $sum = 0;
                        foreach($colGroup["cols"] as $col) {
                            $sum+= $row[$col["name"]]["value"];
                        }  
                    
                        <div style='width:{$colWidth}px;display:inline-block;position:relative;' class='colgroup' >
                        
                            // Сумма по столбцу
                            if($sum > 0) {
                                <div class='sum' >
                                    echo $sum;
                                </div>
                            }
                            
                            foreach($colGroup["cols"] as $col) {                        
                                $val = $row[$col["name"]]["value"];
                                $height = round($val / $max * $chartHeight);
                                $background = $col["color"];
                                $title = $colGroup["label"]." / ".$col["label"]." / ".$val;
                                $onclick = util::str($row[$col["name"]]["onclick"])->esc();
                                if($height) {
                                    <div style='width:{$colWidth}px;height:{$height}px;background:{$background};' title='{$title}' onclick='{$onclick}' ></div>
                                }
                            }
                        </div>
                    }
                    <div style='position:relative;' >
                        <div class='col-label' >
                            echo $row["date"]["value"];
                        </div>
                    </div>
                </td>
            }
        </tr>
    </table>
    
    tmp::exec("sum");
    
</div>