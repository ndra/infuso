<?

$page = $p1->page();
$pages = $p1->pages();

if($pages!=1) {

    echo "<div class='pager' >";
    if($page>1) {
        $url = $p1->url(1);
        echo "<a style='font-weight:normal;' href='$url'>&laquo;</a>";
        $url = $p1->url($page-1);
        echo "<a style='font-weight:normal;' href='$url' >&lsaquo;</a>";
    }
    for($i=$page-10;$i<=$page+10;$i++)
        if($i>=1 & $i<=$pages) {
            $href = $p1->url($i);;
            if($page==$i)
                echo "<a style='color:red;' href='$href'>$i</a>";
            else
                echo "<a href='$href'>$i</a>";
        }
    if($page<$pages) {
        $url = $p1->url($page+1);
        echo "<a style='font-weight:normal;' href='$url' >&rsaquo;</a>";
        $url = $p1->url($pages);
        echo "<a style='font-weight:normal;' href='$url' >&raquo;</a>";
    }
    echo "</div>";
}