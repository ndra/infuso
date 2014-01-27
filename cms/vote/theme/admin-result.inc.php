<?

// Шаблон для отобрадения результатов голосования в каталоге
// Т.к. нет возможности подключить стили в каталог, то 
// их приходится писать непосредственно в style

echo "<b>{$p1->data('title')}</b>";
if($p1->data("active"))
    echo "<span style='background:green;padding:4px;color:white;' >Активно</span>";
echo "<br/>";
    
echo "<small>Создан ".util::date($p1->data("created"))->txt()."</small>";    
echo "<table>";
foreach($p1->resultData() as $a) {
    echo "<tr>";
    echo "<td style='border:1px solid #cccccc;padding:4px;width:300px;' >$a[text]</td>";
    echo "<td style='border:1px solid #cccccc;padding:4px;'>$a[answers]</td>";
    echo "<td style='border:1px solid #cccccc;padding:4px;'>$a[percent]%</td>";
    echo "</tr>";
}
echo "</table>";
echo "Всего проголосовало&nbsp;&mdash; ".$p1->answers()->count();

?>