<?

//if($img = $p1->reflex_renderImg()) {
    echo "<table class='uxhgw3k1uem' ><tr>";
    $preview = file::get($img)->preview(32,32);
    echo "<td><img src='$preview' /></td>";
    echo "<td><div class='uxhgw3k1uem-title' ><a href='{$p1->url()}'>{$p1->title()}</a></div></td>";
    echo "</tr></table>";
//} else {
   // echo "<a href='{$p1->url()}'>{$p1->title()}</a>";
//}