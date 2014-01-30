<? 

foreach(forum_group::all() as $group) {
    <div>
        <a href='{$group->url()}' >{$group->title()}</a>
    </div>
}