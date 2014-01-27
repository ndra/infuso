<?

echo "<div>";

echo "<h2>{$p1->data('title')}</h2>";
echo "Спасибо за голосование. Вот как распределились результаты:";
echo "<table>";
foreach($p1->options()->desc("count") as $option) {
    echo "<tr>";
    echo "<td>{$option->title()}</td>";
    echo "<td>{$option->count()}</td>";
    echo "<td>{$option->percent()}%</td>";
    echo "</tr>";
}
echo "</table>";
echo "Всего проголосовало&nbsp;&mdash; ".$p1->answers()->count();

echo "</div>";