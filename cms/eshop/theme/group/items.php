<?

$items = $p1;

tmp::exec("reflex:navigation",$items);

echo "<div>";
tmp::exec($items->viewMode()->val(),$p1);
echo "</div>";

