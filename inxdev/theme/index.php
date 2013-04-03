<? 

foreach(inxdev::get("")->children() as $child) {
    <div>
        <a href='{$child->url()}'>{$child->title()}</a>
    </div>
}