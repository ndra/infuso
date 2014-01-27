<?

$p = array (
    "type" => "inx.tabs",
    "width" => 600,
    height=>200,
    selectNew => false,    
    style=> array(
        padding => 0,
    ),
    "items" => array(),
    side => array(
        array(
            width => 100,
            resizable => true,
            region => right
        )
    )
);

for($i=0;$i<10;$i++)
    $p["items"][] = array(
        "title" => "Таб-".$i,
        lazy => true,
        type => "inx.tabs",
        closable => $i%2,
        items => array(
            array(
                title => "ololo",
                html => 123
            ),array(
                title => "ololo",
                height => 50,
                html => util_delirium::generate(),
                style=>array(
                    padding=>20
                ),
                vscroll=>true
            ),array(
                title => "ololo",
                style=>array(
                    padding => 1
                ),
                html => 123
            )
            
        )
    );

inx::add($p);