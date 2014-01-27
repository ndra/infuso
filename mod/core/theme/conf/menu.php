<? 

$menu = array(
    (string) mod::action("mod_conf_controller","componentsVisual") => "Визуально",
    (string) mod::action("mod_conf_controller","components") => "Текстом",
);

<div class='x6gjmowhi6s' >
    foreach($menu as $url => $item) {
        $html = "<a href='{$url}' >{$item}</a>";
        if(trim(mod_url::current()->path(),"/") == trim($url,"/")) {
            $html = "<b>{$html}</b>";            
        }
        echo $html;
    }
</div>