<?

foreach(eshop::groups()->limit(0) as $group) {
    echo "<div style='margin-bottom:10px;' >";
    // Группа
    echo "<a style='font-size:18px;' href='{$group->url()}' >{$group->title()}</a> ";
    echo "({$group->numberOfItems()})";
    echo "<br/>";
    // Подгруппы    
    foreach($group->subgroups()->limit(0) as $group) {
        echo "<a href='{$group->url()}' >{$group->title()}</a> ";
        echo "({$group->numberOfItems()}) ";
    }
    echo "</div>";
}

?>