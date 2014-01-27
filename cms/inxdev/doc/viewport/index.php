<?

inx::add(array(
    "type" => "inx.viewport",
    items => array(
        array (
	        "type" => "inx.tabs",
	        height => "parent",
	        selectNew =>false,
			items => array(
	            array(
	                title => 121212,
	                style=>array(
	                    vscroll => 1,
					),
					height => "parent",
	                html => util_delirium::generate(1000000)."***********************8".util_delirium::generate(100000).util_delirium::generate(1000000).util_delirium::generate(1000000).util_delirium::generate(1000000),
				),
			),
		),
	),
    
));
