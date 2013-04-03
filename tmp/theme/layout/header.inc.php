<?

echo "<div style='background:#ededed;padding:20px;' >";
echo "<div style='margin:0px auto;width:950px;' >";

echo "<table><tr>";
echo "<td style='padding:0px 20px 0px 0px;' >";
echo "<span style='margin:0px 40px 0px 0px;font-size:2em;' >Мой магазин</span>";
tmp::exec("reflex:search");
echo "</td>";

echo "<td style='width:250px;' >";
tmp::exec("user:loginForm");
echo "</td>";

echo "<td>";
tmp::exec("lang:selector");
echo "</td>";

echo "</tr></table>";

echo "</div>";
echo "</div>";

?>