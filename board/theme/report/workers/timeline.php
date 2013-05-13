<?

<div class='tkkbt9zesp' >

$users = user::all()->like("roles","boardUser");

tmp::helper("<div>")
    ->addClass("workday")
    ->style("height",$users->count()*33)
    ->exec();
    
$time = util::now()->stamp() - util::now()->date()->stamp();

tmp::helper("<div>")
    ->addClass("now")
    ->style("height",$users->count()*33)
    ->style("left",$time / 3600 *32 + 33)
    ->exec();

foreach($users as $user) {
    tmp::exec("user",array(
        "user" => $user,
    ));
}

for($i=0;$i<24;$i++) {
    tmp::helper("<div class='date' >")
        ->style("position","absolute")
        ->style("left",32*$i+33)
        ->style("bottom",-20)
        ->param("content",$i)
        ->exec();
}

</div>