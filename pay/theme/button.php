<? 

$button = new tmp_helper_html();
$button->tag("a");
$button->attr("href",$href);
$button->begin();
    echo $text;
$button->end();