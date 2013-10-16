<? 

$user = user::active();
$emails = user_mail::all()->eq("userID",$user->id());
$emails->eq("read",0);

<div class='jmi8th58od' >

    <a class='messages' href='#messages' >
        echo $emails->count();
    </a>

</div>