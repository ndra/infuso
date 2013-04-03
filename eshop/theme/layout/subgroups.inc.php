<?

foreach($p1->subgroups() as $folder) {
    $link = "<a href='{$folder->url()}' >{$folder->title()}</a><br/>";
    if($folder->id()==tmp::param("activeGroupID"))
        $link = "<b>$link</b>";
    echo $link;
    
    echo "<div style='padding-left:20px;' >";
    tmp::exec("eshop:layout.subgroups",$folder);    
    echo "</div>";
    
}

?>