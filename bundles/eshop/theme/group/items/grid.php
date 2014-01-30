<? 

foreach($p1 as $item) {

    echo "<div class='pejhat ib' >";
    
    // Фотограяи товара
    $preview = $item->photo()->preview(150,150);
    echo "<a href='{$item->url()}' ><img src='$preview' style='border:1px solid #ededed;padding:2px;' /></a>";
    
    // Название товара
    echo "<div class='pejhat-title' ><a href='{$item->url()}' >{$item->title()}</a></div>";
    
    tmp::exec("eshop:layout.buy",$item);
        
    echo "</div>";
    
}