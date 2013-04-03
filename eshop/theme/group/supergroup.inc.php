<?

foreach($p1->subgroups() as $sub) {
    echo "<div>";
    echo "<a href='{$sub->url()}' >{$sub->title()}</a>";
    echo "</div>";
}

?>