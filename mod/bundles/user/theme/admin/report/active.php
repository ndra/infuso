<? 

<div>Активность пользователей (сколько зарегистрированных пользователей пользовалось сайтом)</div>

$daily = user::all()->gt("lastActivity",util::now()->shift(-3600*24))->count();
$weekly = user::all()->gt("lastActivity",util::now()->shift(-3600*24*7))->count();
$monthly = user::all()->gt("lastActivity",util::now()->shift(-3600*24*30))->count();
$yearly = user::all()->gt("lastActivity",util::now()->shift(-3600*24*365))->count();

<div>
    echo "За день: ".$daily;
</div>
<div>
    echo "За неделю: ".$weekly;
</div>
<div>
    echo "За месяц: ".$monthly;
</div>
<div>
    echo "За год: ".$yearly;
</div>

<br>