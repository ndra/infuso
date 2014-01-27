<? 

// Модуль
echo "<h2 style='position:relative;margin-bottom:10px;font-size:18px;' >";
echo "<div style='position:absolute;top:10px;height:1px;width:100%;background:#cccccc;'></div>";
echo "<span style='position:relative;background:white;padding:0px 10px;margin-left:20px;' >$p1</span>";
echo "</h2>";

foreach($p2 as $data) {
    $name = $data["id"];
    $evalue = htmlspecialchars(mod::conf($name),ENT_QUOTES);
    echo "<div style='margin:0px 0px 10px 0px;' >";
    echo "<table class='conftable' ><tr>";
    $title = $data["type"]!="checkbox" ? $data[title] : "";
    echo "<td><div style='width:200px;'><span class='conf-help' infuso:id='$n' style='border-bottom:1px dotted #cccccc;cursor:pointer;' >$title</span></div></td>";
    echo "<td>";
    
    switch($data["type"]) {
        default:
            echo "<input name='$name' value='$evalue' />";
            break;
        case "textarea":
            echo "<textarea style='width:400px;height:50px;' name='$name'>$evalue</textarea>";
            break;
        case "checkbox":
            $iid = $data["id"];
            $inject = $evalue ? "checked='checked' " : "";
            echo "<input type='checkbox' id='$iid' name='$name' value='1' $inject /><label style='display:inline-block;vertical-align:top;' for='$iid' >$data[title]</label> <span class='conf-help' infuso:id='$n' style='vertical-align:top;border-bottom:1px dotted #cccccc;cursor:pointer;' >?</span>";
            break;
        case "select":
            echo "<select name='$name' />";
            foreach($data["values"] as $valueKey=>$valueName) {
                $inject = $valueKey == mod::conf($name) ? "selected" : "";
                echo "<option value='$valueKey' $inject>$valueName</option>";
            }
            echo "</select>";
            break;
            
    }    
    echo "</td></tr></table></div>";

    // Описание настройки
    echo "<div id='help-$n' style='background:#ededed;padding:10px;width:400px;position:relative;margin-bottom:20px;display:none;' >";
    echo "<div style='position:absolute;right:10px;cursor:pointer;' onclick='$(this).parent().hide(200)' >&times; Закрыть</div>";
    echo "Ключ: <b>{$data[id]}</b><br/>";
    echo $data["descr"];
    echo "</div>";

    $n++;
            
}

    
    