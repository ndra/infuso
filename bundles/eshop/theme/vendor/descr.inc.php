<?

echo "<h1>{$p1->title()}</h1>";

$groups = eshop::items()->eq("vendor",$p1->id())->distinct("parent");
foreach(eshop_group::all()->eq("id",$groups)->limit(0) as $group) {
    echo "<div style='display:inline-block;width:200px;margin:0px 20px 4px 0px;' >";
    echo "<a href='{$group->url()}?eq_vendor={$p1->id()}'>{$group->title()}</a>";
    echo "</div>";
}

echo "<br/><br/>";
echo $p1->pdata("descr");

?>