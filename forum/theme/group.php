<? 

<h1>{$group->title()}</h1>

foreach($group->topics() as $topic) {
    <div>
        <a href='{$topic->url()}' >{$topic->title()}</a>
    </div>
}