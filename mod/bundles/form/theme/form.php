<?


$tag = new tmp_helper_html();

$tag->tag("form");

$tag->params($form->params());

$tag->attr("id", "$id");
$tag->attr("method", "post");
    
    
$tag->begin();

    foreach($form->blocks() as $block) {
        $block->exec();
    }

$tag->end();