<? 

$items = reflex_log::all()
    ->eq("type","reflex/mysql-admin-query");

foreach($items->distinct("text") as $item) {
    <div>{$item}</div>
}