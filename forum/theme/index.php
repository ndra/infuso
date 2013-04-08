<? 

foreach(forum_group::all() as $group) {
    <a href='{$group->url()}' >{$group->title()}</a>
}