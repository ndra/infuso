<? 

<div class='ha8ddmy4vr' >

    <div>{$user->title()}</div>
    foreach(board_task_vote_criteria::all() as $criteria) {
        echo $criteria->title().": ";
        
        $votes = board_task_vote::all()
            ->eq("criteriaID",$criteria->id())
            ->eq("subjectID",$user->id());
            
        echo round($votes->avg("score"),2);
        echo " (".$votes->count().")";
            
        echo "<br/>";
    }

</div>