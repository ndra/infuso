<? 

$domain = $p1;

echo "<div style='padding:20px;' >";

echo "<h2>".$domain->title()."</h2>";

<div>
    tmp::exec("filter", array("domain"=>$domain));
</div>

echo "<div>";
$day = $_GET["date"];
tmp::exec("positions",$domain,$day);
echo "</div>";

echo "<div>";
tmp::exec("chart",$domain,$day);
echo "</div>";

echo "<div>";
tmp::exec("liveinternet",$domain);
echo "</div>";

echo "</div>";

?>