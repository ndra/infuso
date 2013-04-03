<?

$items = $p1 ? $p1->children() : doc::all()->eq("parentID",0);
foreach($items as $item) {
    echo "<div>";
    $link = "<a href='{$item->url()}' >{$item->title()}</a>";
    if($item==tmp::obj()) $link = "<b>$link</b>";
    echo $link;
    echo "<div style='margin-left:10px;' >";
    tmp::exec("doc:article.menu",$item);
    echo "</div>";
    echo "</div>";
}

?>