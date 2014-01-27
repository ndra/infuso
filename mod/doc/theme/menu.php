<? 

foreach(doc_page::all()->limit(0) as $page) {
    <div>
        <a href='{$page->url()}' >{$page->title()}</a>
    </div>
}








