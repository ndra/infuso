<?

$p = array(
    "type" => "inx.panel",
    "width" => 600,
    autoHeight => true,
    tbar => array(),
    items => array(
        array(
            "type" => "inx.button",
            text => "hide tbar",
            onclick => "this.owner().axis('tbar').cmd('hide')"
        )
    )
);
    
for($i=0;$i<30;$i++) {

    $x = rand()%3;

    switch($x) {
        case 0:
            $p["tbar"][] = array(
                "type" => "inx.button",
                "text" => rand(),
            );
            break;
        case 1:
            $p["tbar"][] = array(
                width => rand()%50+10,
                "type" => "inx.select",
            );
            break;
        case 2:
            $p["tbar"][] = "|";
            break;
    }
}

inx::add($p);