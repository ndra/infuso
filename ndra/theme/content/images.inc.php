 <?

/*css:
.ah3f5h7fv-div {float:left;}
.ah3f5h7fv {display:inline-block;margin:0px 40px 40px 0px;width:150px;vertical-align:top;text-align:center;font-size:10px;color:gray;text-decoration:none;}
.ah3f5h7fv img{display:block;width:150px;height:150px;margin-bottom:10px;}
*/

echo "<br clear='both' />";
echo "<div class='ah3f5h7fv-div' >";
foreach($p1 as $img) {
    $preview = file::get($img["src"])->preview(150,150)->crop();
    echo "<a class='ah3f5h7fv fancybox' title='$img[alt]' href='$img[src]' rel='ndragallery' >";
    echo "<img src='$preview' alt='$img[alt]' />";
    echo $img["alt"];
    echo "</a>";
}
echo "</div>";

?>
