<?

echo "Панель со случайным текстом:";
inx::add(array(
    "type" => "inx.panel",
    "width" => 400,
    "html" => util_delirium::generate(500),
));

echo "<br/><br/>";

echo "Тестируем html=0 (inx должен отличать этот случай от случая, когда html не указан и отображать 0 в панели)";
inx::add(array(
    "type" => "inx.panel",
    "width" => 400,
    "html" => 0,
));

