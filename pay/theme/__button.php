<?

// Шаблон кнопки, используется везде в модуле pay
// Перепишите этот шаблон чтобы стилизовать все кнопки на сайте 

$button = new tmp_helper_html();

if($href) {
    $button->tag("a");
    $button->attr("href",$href);
} else {
    $button->tag("button");
    $button->param("type","submit");
}

$button->begin();
    echo $text;
$button->end();