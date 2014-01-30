<? 

foreach($task->tags() as $tag) {
    echo $tag->title();
    echo ", ";
}