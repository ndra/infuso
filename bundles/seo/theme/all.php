<?

/*css:
.u7zk057dj0 td{padding:4px 10px;}
*/

echo "<div style='padding:20px;' >";
echo "<table class='u7zk057dj0' >";
foreach(seo_domain::all() as $domain) {
    echo "<tr>";
    echo "<td><a href='/seo/domain/id/{$domain->id()}/'>{$domain->title()}</a></td>";

    echo "<td>";
    echo $domain->queriesInTop()->count();
    echo " / ";
    echo $domain->queries()->count();
    echo "</td>";

    echo "<td>";
    echo $domain->primaryEngine()->title();
    echo "</td>";

    echo "</tr>";
}
echo "</table>";

$all = seo_query::all()->count();
$scanned = seo_query::all()->eq("date(update)",util::now()->notime())->count();
$percent = floor($scanned / $all*100);

echo "<br/><br/>";
echo "Сканировано ".$percent."%";

echo "</div>";
