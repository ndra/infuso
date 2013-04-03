<?

inx::add(array(

	"type" => "inx.combo",
	"cmd" => "inxdev:example:combo",
    "value" => 5,
    "text" => "Начальный текст",
	"loader" => array(
		"cmd" => "inxdev_example:listLoader",
	),
    "listeners" => array(
        "change" => "inx.msg(arguments[0])"
    ),
    
));
