<?

$p = array(
    type => "inx.panel",
    items => array(),
    width => 600,
    height => 200,
    style => array(
    	vscroll => true,
    	padding=> 100,
	),
    side => array(
        array(
            html => util_delirium::generate(),
            resizable => true,            
            width => 100,
            region => left,
            style=>array(
                padding=>20,
                vscroll => true,
            ),
        )
    )    
);

for($i=0;$i<10;$i++)
    $p["items"][] = array(
        "height" => 100,
        html => util_delirium::generate(),
        style=>array(
            vscroll => true
		)
    );

inx::add($p);
