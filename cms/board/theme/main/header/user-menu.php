<? 

$user = user::active();
$emails = user_mail::all()->eq("userID",$user->id());
$emails->eq("read",0);

<div class='jmi8th58od' >

    $userpick = user::active()->userpick()->preview(32,32)->crop();
    <a class='profile' href='#conf-profile' >
        <img src='{$userpick}' />
    </a>

    <a class='messages' href='#messages' >
        echo $emails->count();
    </a>

</div>