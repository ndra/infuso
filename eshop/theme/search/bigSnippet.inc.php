<?

/*css:
.bu9uumbulh td{padding:0px 20px 0px 0px;}
*/
echo "<table class='bu9uumbulh' ><tr>";
$preview = $p1->photo()->preview(150,150);
echo "<td><a href='{$p1->url()}' ><img src='$preview' style='border:1px solid #cccccc;' /></a></td>";
echo "<td>";

echo "<div style='padding:0px 0px 7px 0px;' >";
foreach($p1->parents() as $parent)
    echo "<a style='margin-right:10px;' href='{$parent->url()}' >{$parent->title()}</a>"; 
echo "</div>";

// Заголовок
echo "<div><a href='{$p1->url()}' >{$p1->title()}</a></div>";

echo "<br/>";
tmp::exec("eshop:layout.buy",$p1,true);

echo "</td>";
echo "</tr></table>";

?>