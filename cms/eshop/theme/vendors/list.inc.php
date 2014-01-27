<?

foreach(eshop_vendor::all()->limit(0) as $vendor) {
    echo "<div style='display:inline-block;width:200px;margin:0px 20px 4px 0px;' >";
    echo "<a href='{$vendor->url()}'>{$vendor->title()}</a>";
    echo "</div>";
}

?>