<?

var_export($p2);

foreach($p2 as $key=>$val) {
    echo "<div><label>";
    $name = "eq_".$p1;
    //$checked = in_array($key,explode(",",$_GET[$name]));
    $inject = $checked ? " checked='on' " : "";
    echo "<input class='w6orymz2tv-in' name='$name' value='$key' type='checkbox' $inject />";
    echo $val;
    echo "</label></div>";
}

?>