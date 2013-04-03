<?
// Модуль
echo "<h2 style='position:relative;margin-bottom:10px;font-size:18px;' >";
echo "<div style='position:absolute;top:10px;height:1px;width:100%;background:#cccccc;'></div>";
echo "<span style='position:relative;background:white;padding:0px 10px;margin-left:20px;' >SEO-настройки</span>";
echo "</h2>";
echo "<table class='conftable' ><tr>";
    echo "<td><div style='width:200px;'><span class='conf-help'>Текст robots.txt</span></div></td>";
    echo "<td>";
        $robotstxt = file::get("/robots.txt")->contents();
        $evalue = htmlspecialchars($robotstxt,ENT_QUOTES);
        echo "<textarea style='width:400px;height:50px;' name='robots'>$evalue</textarea>";
    echo "</td>";
echo "</td></tr></table></div>";
echo "<table class='conftable' ><tr>";
    echo "<td><div style='width:200px;'><span class='conf-help'>favicon.ico</span></div></td>";
    echo "<td>";
        echo "<input type='file' style='width:200px;' name='favicon' >";
    echo "</td>";
echo "</td></tr></table></div>";

?>