<?


$tag = new tmp_helper_html();

$tag->tag("form");

$tag->params($form->params());

$tag->attr("id", "$id");

// Если не задан метод, используем POST
if(!$tag->attr("method")) {
    $tag->attr("method", "post");
}    
    
$tag->begin();

    foreach($form->blocks() as $block) {
        $block->exec();
    }

$tag->end();