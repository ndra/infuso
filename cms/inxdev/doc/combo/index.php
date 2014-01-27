<?

echo "<div id='log' ></div>";

inx::add(array(

	"type" => "inx.combo",
	"cmd" => "inxdev:example:combo",
    "value" => 8,
    "text" => "Начальный текст",
	"loader" => array(
		"cmd" => "inxdev_example:listLoader",
	),
    "listeners" => array(
        "change" => "$('#log').html(this.info('value'))",
    ),
    
));

