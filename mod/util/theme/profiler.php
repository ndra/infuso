<?

tmp::jq();
mod_profiler::addMilestone("done");

<div class='nw5bny9hxyu6' >
    <table>
    
        <tr class='group' >
            <td class='title' >Основное</td>
            <td>
                tmp::exec("main");
            </td>
        <tr>
        
        foreach(mod_profiler::log() as $group=>$items) {
            <tr>
    
                <td class='title'>$group</td>
    
                <td>
                    foreach($items as $operation=>$params) {
        
                        $time = number_format($params["time"],6);                    
                        $keys = $params["keys"];
                        
                        $n = 0;
                        foreach($keys as $key) {
                            $n += $key["count"];
                        }              
        
                        <div class='a' >
                            <span>$operation</span>
                            <span>$n</span>
                            <span>$time sec.</span>
                        </div>
                        
                        <div class='b' >
                            foreach($keys as $key=>$val) {
                                <span style='display:inline-block;width:30px;' >{$val[count]}</span>
                                $time = round($val["time"],6);
                                <span style='display:inline-block;width:60px;' >{$time}</span>
                                echo " — $key<br/>";
                            }
                        </div>
                    }
                </td>
                
            </tr>
    
        }
        
        <tr>
            <td class='title' >Milestones</td>
            <td>
                tmp::exec("milestones");
            </td>
        </tr>
        
        <tr>
            <td class='title' >MySQL</td>
            <td>
                //tmp::exec("mysql");
            </td>
        </tr>
        
    </table>
</div>