<?

admin::header("Отчет по выгрузке в Яндекс.Маркет");
echo "<div style='padding:40px;' >";

tmp::exec("info");
tmp::exec("bids");


// Распределение товаров по рпоизводителям
$table = reflex::virtual("eshop_item")->table()->prefixedName();
reflex_mysql::query("select `vendor`,count(*) as `count` from `$table` where `yandexMarket`=1 group by `vendor` order by count desc");
$data = reflex_mysql::get_array();
$chart = google_chart::create()->pieChart()->width(600)->height(400);
$chart->title("Распределение выгружаемых товаров по производителям");
$chart->col("Вендор","string");
$chart->col("Количество");
foreach($data as $row)
    $chart->row(
        eshop_vendor::get($row["vendor"])->title(),
        $row["count"]*1
    );
$chart->exec();

echo "</div>";
admin::footer();