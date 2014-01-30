<?

echo "<select>";
foreach($p1 as $type) {
    echo "<option>{$type->title()}</option>";
}
echo "</select>";

?>