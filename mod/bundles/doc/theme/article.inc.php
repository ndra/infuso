<? admin::header("Документация");

$doc = $p1;

echo "<div style='padding:20px;' >";
echo "<table><tr>";

echo "<td style='padding-right:40px;width:200px;' >";
tmp::exec("doc:article.menu");

echo "</td>";

echo "<td>";
foreach($doc->parents() as $parent)
    echo "/<a style='margin:0px 5px;' href='{$parent->url()}' >{$parent->title()}</a>";
echo "<h1>{$doc->title()}</h1>";

// Пересобиралка документации
if(mod_superadmin::check()) {
    inx::add(array(
        "type" => "inx.button",
        "icon" => "refresh",
        "text" => "Пересобрать документацию",
        "onclick" => "this.call({cmd:'doc:generate'},function(){window.location.reload();})"        
    ));
    echo "<br/>";
}

echo "<div style='font-weight:bold;margin-bottom:30px;' >";
foreach($doc->ancors() as $key=>$val)
    echo "<a href='#$key' >$val</a><br />";
echo "</div>";

tmp::exec("doc:article.html",$doc);

echo "</td>";

echo "</tr></table>";
echo "</div>";

admin::footer(); ?>