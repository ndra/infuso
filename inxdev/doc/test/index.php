<?

$p = array (
	"type" => "inx.panel",
	width => 500,
	style => array(
		"height" => "content",
		padding => 3,
	),
	
	
	tbar => array(
		array(
		    "text" => 13,
		)
	),
	
	items => array(
		array(
			"type" => "inx.textarea",
			"listeners" => array(
				 "focus" => "this.owner().axis('tbar').cmd('show');",
			),
			"height" => "content",
		)
	),
	
	listeners => array(
	    "render" => "inx(this).axis('tbar').cmd('hide')",
	)
);

inx::add($p);
