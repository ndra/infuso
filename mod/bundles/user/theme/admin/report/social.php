<? 

<div class='be466lsda' >

    <div class='comment' >
        echo "Последние регистрации через соцсети:";
    </div>

    foreach(user_social::all() as $item) {
        <a href='{$item->identity()}' class='photo' >
            <img src='{$item->userpick()}' />
        </a>
    }

</div>