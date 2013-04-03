<? 

$items = $p1;
if(!$items->count())
    return;

echo "<table class='k0belatrod' ><tr>";

if($items->pages()>1) {
    echo "<td>";
    tmp::exec("pager",$items);
    echo "</td>";
}

echo "<td>";
$count = $p1->count();
echo "найдено элементов &mdash; $count";
echo "</td>";

echo "<td>";
tmp::exec("select",$p1->limitModes());
echo "</td>";

echo "<td>";
tmp::exec("select",$p1->sortModes());
echo "</td>";

echo "<td>";
tmp::exec("select",$p1->viewModes());
echo "</td>";

echo "</tr></table>";