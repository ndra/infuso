<? 

$ret.= sizeof($result)." rows: <br/>";

$ret.= "<table style='width:100%;' >";
foreach($result as $n=>$row) {

    if($n==0) {
        $ret.="<tr>";
        foreach($row as $key=>$cell)
            $ret.="<td style='padding:5px;border:1px solid #ededed;font-weight:bold;' >$key</td>";
        $ret.="</tr>";
    }

    $ret.="<tr>";
    foreach($row as $cell)
        $ret.="<td style='padding:5px;border:1px solid #ededed;' >$cell</td>";
    $ret.="</tr>";
}
$ret.="</table>";
echo $ret;