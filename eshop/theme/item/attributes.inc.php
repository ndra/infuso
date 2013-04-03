<?

$item = $p1;
echo "<table>";
foreach($item->attributes()->limit(0) as $attr) {
    echo "<tr>";
    echo "<td>".$attr->title()."</td>";
    echo "<td>".$attr->value()."</td>";
    echo "</tr>";
}
echo "</table>";

?>