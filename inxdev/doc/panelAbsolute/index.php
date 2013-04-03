<?

inx::inc("inx.layout.absolute");

$p = array(
    "type" => "inx.panel",
    "width" => 600,
    height => 300,
    layout => "inx.layout.absolute",
    items => array(),
    style => array(
        vscroll => true,
        padding => 50,
	),
);

for($i=0;$i<800;$i+=20)
	$p["items"][] = array(
	    "x" => $i,
	    "y" => 200 + cos($i/200)*180,
	    "type" => "inx.date",
	    "text" => "***",
	    "air" => true
	);

inx::add($p);
