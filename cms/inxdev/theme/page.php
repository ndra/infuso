<?


foreach($p1->children() as $child) {
    <div>
        <a href='{$child->url()}'>{$child->title()}</a>    
    </div>
}

$p1->inc();
    