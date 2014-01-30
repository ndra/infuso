<?

/*css:
.gyx7lde1nt td{ padding:5px; }
*/

echo "<table class='gyx7lde1nt' >";
foreach(realty_flat::all() as $item) {
    echo "<tr>";
    echo "<td><b>{$item->pdata(type)->title()}</b> {$item->pdata(state)}</td>";
    echo "<td>{$item->data(totalArea)}</td>";
    echo "<td>{$item->data(livingArea)}</td>";
    echo "<td>{$item->data(kitchenArea)}</td>";
    echo "<td>{$item->data(price)} р.</td>";
    echo "<td>{$item->pdata(location)->title()}</td>";
    echo "</tr>";
}
echo "</table>";

?>