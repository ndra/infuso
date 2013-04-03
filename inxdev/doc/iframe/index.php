<?

$p = array (
	"type" => "inx.iframe",
	tbar => array(
	    array(
	        "text" => 1234,
		),
	),
    "width" => 800,
    "height" => 800,
    style=>array(
        padding => 2,
        spacing => 30,
        hscroll => true,
        vscroll => true,
	),side=>array (

	    array (
	        region=>right,
	        width => 50,
	        resizable => true,
		),array(
	        region=>bottom,
	        height => "content",
	        "html" => util_delirium::generate(200),
		)
	)
);

inx::add($p);
