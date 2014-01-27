<?

$button = new tmp_helper_html();
if($href) {
    $button->tag("a");
    $button->attr("href",$href);
} else {
    $button->tag("input");
    $button->attr("type","submit");
    $button->attr("value",$text);
}
$button->begin();
    echo $text;
$button->end();