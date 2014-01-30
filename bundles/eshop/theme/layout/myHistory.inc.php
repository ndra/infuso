<?

$items = eshop_item::history();
if(!$items->count()) return;

echo "<b>История просмотра:</b><br/><br/>";
echo "<table class='akay5gznaw' >";
foreach($items as $item) {
    echo "<tr>";
    $preview = $item->photo()->preview(32,32);
    echo "<td><img src='$preview' /></td>";
    echo "<td><a href='{$item->url()}' >{$item->title()}</a></td>";
    echo "</tr>";
}
echo "</table>";

?>