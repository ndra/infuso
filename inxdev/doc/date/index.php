<?

echo "Если не вызвать компонент inx.date без параметра value, то в качестве значения будет использована текущая дата";
inx::add(array(
    "type" => "inx.date",
    "time" => true,
));
echo "<br/>";

echo "Если не вызвать компонент inx.date с value=\"\" (пустая строка), null или false, то компонент считает что дата не указана";
inx::add(array(
    "type" => "inx.date",
    "value" => false,
    "time" => true,

));
echo "<br/>";

echo "Компонент inx.date без указания времени";
inx::add(array(
    "type" => "inx.date",
    "value" => $date,
    "listeners" => array(
        "change" => "inx.msg(this.info('value'));",
    )
));
echo "<br/>";

echo "Событие afdtercalendare - вызывается после выбора даты в календаре. В примере мы реагируем на это событие и меняем часы месяцы и секунды на 23:59:59";
inx::add(array(
    "type" => "inx.date",
    "value" => $date,
    "time" => true,
    "listeners" => array(
        "aftercalendar" => "this.cmd('setHours',23);this.cmd('setMinutes',59);this.cmd('setSeconds',59)",
        "change" => "inx.msg(this.info('value'));",
    )
));

