<?

$p = array (
    "type" => "inx.panel",
    "width" => 500,
    "height" => 300,
    style=> array(
        padding => 20,
        vscroll => true,
	),
	tbar=>array(
		array(
	        type => "inx.button",
	        text => "default",
	        onclick => "this.owner().owner().style('layout','inx.layout.default')",
		),
	    array(
	        type => "inx.button",
	        text => "column",
	        onclick => "this.owner().owner().style('layout','inx.layout.column')",
		),
		array(
	        type => "inx.button",
	        text => "absolute",
	        onclick => "this.owner().owner().style('layout','inx.layout.absolute')",
		)
	),
    "side" => array(
        array (
            "width" => 200,
            "region" => "left",
            "resizable" => true,
            "html" => util_delirium::generate(),
        )
    ),
    "items" => array()
);

for($i=0;$i<10;$i++) {
	$p["items"][] = array(
	    "type" => "inx.panel",
	    "html" => util_delirium::generate(100),
		x => $i*10,
		y => $i*10
	);
}

inx::add($p);
