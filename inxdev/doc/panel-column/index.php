<?

$p = array(
    "type" => "inx.panel",
    "width" => 600,
    autoHeight => true,
    style => array(
        padding => 0,
    ),
    tbar => array(),
    layout => "inx.layout.column",
    items => array(),
    side => array(
        array(
            "width" => 30,
            resizable => true,
            region => left
        )
    )
);

for($i=0;$i<100;$i++)
    $p["items"][] = array(
        "width" => rand()%50+3,
        "height" => rand()%30+3,
        "type" => "inx.panel",
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
