<?

inx::add(array(

    "type" => "inx.list",
  	"sortable" => true,
    "width" => 700,
    "height" => 500,
    "loader" => array(
        "cmd" => "inxdev:example:listLoader",
        "n" => 200
    ),
    "side" => array(
    	array(
    		"width" => 200,
    		region => right,
    		resizable => true,
		)
	),
    
));
