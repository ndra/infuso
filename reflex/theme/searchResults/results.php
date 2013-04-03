<?

if($p1->count()) {

    if($p1->pages()>1) {
        tmp::exec("tmp:layour.pager",$p1);
        echo "<br/><br/>";
    }
    
    foreach($p1 as $item) {
        echo "<div style='padding-bottom:20px;' >";
        echo $item->item()->reflex_bigSearchSnippet();
        echo "</div>";    
    }
    
    if($p1->pages()>1) {
        echo "<br/><br/>";
        tmp::exec("tmp:layour.pager",$p1);
    }

} else {
    echo "По вашему запросу ничего не найдено.";
}