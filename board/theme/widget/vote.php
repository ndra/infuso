<? 

<div class='qmky1dx6t' >
    
    <a href='/board/#vote/id/{$task->id()}' target='_top' >
        <img class='edit' data:task='{$task->id()}' src='/board/res/img/icons16/vote.gif' >
    </a>
    
    foreach($task->votes()->distinct("ownerID") as $ownerID) {
        $user = user::get($ownerID);
        $userpick = user::get($ownerID)->userpick()->preview(16,16);
        <img src='{$userpick}' />
    }

</div>