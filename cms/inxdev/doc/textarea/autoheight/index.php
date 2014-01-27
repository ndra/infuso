<?

$p = array (
	"type" => "inx.panel",
	"style" => array(
	    "width" => 800,
	    "border" => 1,
	    "padding" => 10,
	),
	"side" => array(
		array(
		    "width" => 200,
		    "resizable" => true,
		    "region" => "right",
		),
	),
	"items" => array(
	    array(
			type => "inx.textarea",
			height => "content",
		)
	),
);

inx::add($p);

