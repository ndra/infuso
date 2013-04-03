<? 

<div class='nvfha9mlps' >
    
    $n = user::all()->eq("verified",1)->count();
    <b>Всего пользователей:</b> $n<br/>
    
    $users = user::all()->eq("verified",1)->neq("roles","");
    <b>Имеют роли</b> {$users->count()}:
    echo " ";
    foreach($users->limit(100) as $user) {
        <a href='{$user->editor()->url()}' >{$user->title()}</a>
        echo " ";
    }
    
    
    $nn = array(1,7,30);
    
    foreach($nn as $n) {
        <div>
            $users = user::all()->eq("verified",1)->geq("lastActivity",util::now()->shift(-24*3600*$n));
            <b>Пользователей за $n дней:</b> {$users->count()}<br/>            
        </div>
    }

</div>