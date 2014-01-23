<?

/*css:
.f4f8c2go1y a, .f4f8c2go1y b{margin-right:5px;}
*/

echo "<div class='f4f8c2go1y' >";
foreach(lang::all() as $lang) {
    if(lang::active()->id()!=$lang->id())
        echo "<a href='{$lang->url()}' >{$lang->title()}</a>";
    else
        echo "<b>{$lang->title()}</b>";
}
echo "</div>";

?>