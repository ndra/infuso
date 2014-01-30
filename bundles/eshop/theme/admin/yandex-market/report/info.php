<?

$items = eshop_item::all()->eq("yandexMarket",1);
$n = $items->count();
echo "Сейчас в Яндекс.Маркет выгружается <b>$n</b> предложений<br/>";

$time = file::get("/eshop/yandex/yandexmarket.xml")->time();
echo "Файл выгрузки сгенерирован ".$time->left()."<br/>";

$n = $items->copy()->gt("bid1",0)->count();
echo "Найдены рекомендованые цены для <b>$n</b> предложений<br/>";

$t1 = util::date($items->copy()->min("bidGrabbed"))->txt();
$t2 = util::date($items->copy()->max("bidGrabbed"))->txt();
echo "Интервал сканирования рекомендаций: ".$t1." &mdash; ".$t2."<br/>";

$maxBid = $items->copy()->max("bid");
$maxCBid = $items->copy()->max("cbid");
echo "Робот сделал следующие максимальные ставки: поиск &mdash; {$maxBid}$, карточка модели &mdash; {$maxCBid}$<br/>";

$max = $items->copy()->max("bid1");
echo "Максимальная рекомендованая Яндекс.Маркетом ставка {$max}$";
echo "<br/><br/>";