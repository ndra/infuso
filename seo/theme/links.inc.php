<?

echo "<div style='padding:40px;' >";


echo "<table class='framed' >";
foreach(seo_link::all()->eq("domain",$p1)->groupBy("donorDomain")->limit(0) as $link) {
    echo "<tr>";
    echo "<td><a href='{$link->donor()->title()}' >{$link->donor()->title()}</a></td>";
    echo "<td>{$link->url()}</td>";
    echo "<td>{$link->title()}</td>";
    echo "</tr>";
}
echo "</table>";

echo "<br/>";

echo "<br/>";
echo "Страниц в базе &mdash; ".seo_page::all()->count();
echo "<br/>";
echo "Ссылок в базе &mdash; ".seo_link::all()->count();

echo "</div>";

?>