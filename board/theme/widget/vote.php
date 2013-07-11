<? 

<div class='qmky1dx6t' >
    
    <a href='/board/#vote/id/{$task->id()}' target='_top' >
        <img class='edit' data:task='{$task->id()}' src='/board/res/img/icons16/vote.png' >
    </a>
    
    foreach($task->votes()->distinct("ownerID") as $ownerID) {
        $user = user::get($ownerID);
        $userpick = user::get($ownerID)->userpick()->preview(16,16);
        
        $title = array();
        foreach($task->votes()->eq("ownerID",$ownerID) as $vote) {
            $title[] = $vote->criteria()->title().": ".$vote->data("score");
        }
        $title = implode(", ",$title);
        
        <img src='{$userpick}' title='{$title}' />
    }

</div>