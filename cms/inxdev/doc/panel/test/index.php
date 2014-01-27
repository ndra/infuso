<?

$p = array(
    "type" => "inx.panel",
    "width" => 800,
    vscroll => true,
    style => array(
        padding => 40,
        spacing => 10,
    ),
    side => array(
        array(
       		region => "right",
	        width => 100,
	        resizable => true
		)
	),
    items => array()
); 

for($i=0;$i<10;$i++)
    $p["items"][] = array(
        height => 40,
        title => $i%2==0 ? util_delirium::generate(50) : ""
    );
   
inx::add($p);

